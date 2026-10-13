#!/usr/bin/env bash
#
# deploy-stage.sh — the only supported way to push to STAGE (WP Engine "Dev").
#
# Why this exists
# ---------------
# Deploying by hand was ~7 remembered steps, and the failure modes are almost all
# SILENT. This script turns each one into either a refusal or a red error.
#
#   · rsync's default check is size+mtime, so a same-size edit is skipped silently
#     -> we verify with checksums (-c), never timestamps.
#   · `rsync -q src/ dst/` does not recurse, so it skips inc/ and template-parts/
#     -> we always pass -a and never pass -q.
#   · a missing chmod ships mode-600 files and WP Engine answers 403
#     -> chmod is part of the deploy, not a thing to remember.
#   · a same-named CSS/JS file keeps serving old bytes from the WPE CDN
#     -> both caches are flushed.
#   · --delete carries another agent's uncommitted work to stage
#     -> the tree must be clean and pushed, or you pass --allow-dirty knowingly.
#   · a typo'd host is Production
#     -> the host and remote path are hard-guarded for "gerotechdev".
#   · "the command exited 0" proves nothing
#     -> we assert the rendered page: HTTP status, markers present, markers absent.
#   · there was no rollback
#     -> every --apply snapshots the remote theme first.
#
# Usage
#   scripts/deploy-stage.sh --check                     # read-only. Start here.
#   scripts/deploy-stage.sh --check --scope home-nav
#   scripts/deploy-stage.sh --apply --scope theme --yes
#   scripts/deploy-stage.sh --list-snapshots
#   scripts/deploy-stage.sh --rollback --yes            # newest snapshot
#   scripts/deploy-stage.sh --rollback --snapshot gerotech-child-20261013-163000.tgz --yes
#
# Scope
#   theme      the whole child theme, with --delete (the unit you actually test)
#   home-nav   only the homepage + global chrome files (no --delete)
#
# Exit codes
#   0 ok   1 refused/failed   2 usage error
#
set -euo pipefail

# ─── Constants ────────────────────────────────────────────────────────────────

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
THEME_REL="wp-content/themes/gerotech-child"
LOCAL_THEME="$REPO_ROOT/$THEME_REL"
FILES_FROM_HOME_NAV="$REPO_ROOT/scripts/deploy-stage-home-nav.txt"

STAGE_HOST="gerotechdev@gerotechdev.ssh.wpengine.net"
STAGE_ROOT="/nas/content/live/gerotechdev"
STAGE_URL="https://gerotechdev.wpenginepowered.com"
LOCAL_URL="https://gerotech.local"
STAGE_MARKER="gerotechdev"

# Substring of the production host — a hard stop if it ever appears in the target.
PRODUCTION_MARKER="gerotech.ssh.wpengine.net"

SSH_KEY="${GEROTECH_SSH_KEY:-$HOME/Library/Application Support/Local/ssh/wpe-connect}"
# rsync runs this through a shell, so the key's spaces must stay quoted.
SSH_CMD="ssh -i \"$SSH_KEY\" -o BatchMode=yes -o ConnectTimeout=20"
SSH_ARGS=(-i "$SSH_KEY" -o BatchMode=yes -o ConnectTimeout=20)

REMOTE_THEME="$STAGE_ROOT/$THEME_REL"
# Snapshots MUST live inside the install: WPE's $HOME is EPHEMERAL per SSH
# session, so a snapshot written there is reported as created and then silently
# vanishes — the worst possible failure mode for a rollback point. Measured:
# $HOME and /tmp do not survive a new connection; only the install dir does.
REMOTE_SNAP_DIR="$STAGE_ROOT/_gerotech-scripts/snapshots"
KEEP_SNAPSHOTS=5
DEPLOY_COMMIT="$(git -C "$REPO_ROOT" rev-parse --short HEAD 2>/dev/null || echo unknown)"

WARNINGS=0
ASSERT_FAILED=0
SNAPSHOT_TAKEN=""

TMP_FILES=()
cleanup() { [ "${#TMP_FILES[@]}" -gt 0 ] && rm -rf "${TMP_FILES[@]}" || true; }
trap cleanup EXIT

# ─── Output ───────────────────────────────────────────────────────────────────

if [ -t 1 ]; then R=$'\033[31m'; G=$'\033[32m'; Y=$'\033[33m'; B=$'\033[1m'; N=$'\033[0m'
else R=''; G=''; Y=''; B=''; N=''; fi

step() { printf '\n%s== %s%s\n' "$B" "$*" "$N"; }
ok()   { printf '  %sOK%s   %s\n' "$G" "$N" "$*"; }
warn() { printf '  %sWARN%s %s\n' "$Y" "$N" "$*" >&2; WARNINGS=$((WARNINGS + 1)); }
fail() { printf '  %sFAIL%s %s\n' "$R" "$N" "$*" >&2; }
info() { printf '       %s\n' "$*"; }
die()  { printf '\n%sREFUSING TO CONTINUE:%s %s\n\n' "$R$B" "$N" "$*" >&2; exit 1; }

# A condition that must block --apply but is only diagnostic under --check, so
# you can inspect the environment before fixing it.
block_or_fail() {
	if [ "${MODE:-}" = "apply" ]; then
		die "$1"
	else
		fail "$1 (would block --apply)"; WARNINGS=$((WARNINGS + 1))
	fi
}

# ─── Args ─────────────────────────────────────────────────────────────────────

MODE=""
SCOPE="theme"
ASSUME_YES=0
ALLOW_DIRTY=0
ALLOW_UNPUSHED=0
SKIP_SNAPSHOT=0
SEED=0
AUDIT=0
SNAPSHOT_FILE=""

usage() {
	cat >&2 <<'TXT'
deploy-stage.sh — push to STAGE (WP Engine Dev) with guards.

  --check                 read-only: pre-flight, drift, dry run, chrome diff
  --apply                 snapshot, deploy, chmod, flush, verify, assert
  --verify                read-only: run the rendered assertions only (no SSH)
  --rollback              restore a snapshot (newest unless --snapshot given)
  --list-snapshots        what is available to roll back to

  --scope theme|home-nav  what to push (default: theme)
  --snapshot <file>       snapshot to restore on --rollback
  --seed                  run seed-acf-content.php on stage (idempotent)
  --audit                 run the ACF applied-state audit on stage (read-only)
  --yes                   skip the interactive confirmation
  --allow-dirty           permit a dirty working tree
  --allow-unpushed        permit commits that are not pushed
  --skip-snapshot         deploy without a rollback point (discouraged)

Exit: 0 ok, 1 refused/failed, 2 usage error.
TXT
	exit 2
}

while [ $# -gt 0 ]; do
	case "$1" in
		--check)          MODE="check" ;;
		--apply)          MODE="apply" ;;
		--verify)         MODE="verify" ;;
		--rollback)       MODE="rollback" ;;
		--list-snapshots) MODE="list" ;;
		--scope)          SCOPE="${2:?--scope needs a value}"; shift ;;
		--snapshot)       SNAPSHOT_FILE="${2:?--snapshot needs a value}"; shift ;;
		--seed)           SEED=1 ;;
		--audit)          AUDIT=1 ;;
		--yes|-y)         ASSUME_YES=1 ;;
		--allow-dirty)    ALLOW_DIRTY=1 ;;
		--allow-unpushed) ALLOW_UNPUSHED=1 ;;
		--skip-snapshot)  SKIP_SNAPSHOT=1 ;;
		--help|-h)        usage ;;
		*)                printf 'unknown argument: %s\n\n' "$1" >&2; usage ;;
	esac
	shift
done

[ -n "$MODE" ] || { printf 'nothing to do — pass --check, --apply, --verify, --rollback or --list-snapshots\n\n' >&2; usage; }

case "$SCOPE" in
	theme|home-nav) ;;
	*) printf 'x --scope must be "theme" or "home-nav" (got: %s)\n' "$SCOPE" >&2; exit 2 ;;
esac

need_confirmation() {
	[ "$ASSUME_YES" -eq 1 ] && return 0
	if [ -t 0 ]; then
		printf '\n%sAbout to %s on STAGE (%s).%s\n' "$Y$B" "$MODE" "$STAGE_HOST" "$N"
		printf 'Type "yes" to continue: '
		read -r reply
		[ "$reply" = "yes" ] || die "not confirmed."
	else
		die "--$MODE needs --yes when there is no terminal to confirm at."
	fi
}

# ─── Guards ───────────────────────────────────────────────────────────────────

assert_not_production() {
	case "$STAGE_HOST$STAGE_ROOT" in
		*"$PRODUCTION_MARKER"*) die "target looks like PRODUCTION. This script only ever pushes to stage." ;;
	esac
	case "$STAGE_HOST$STAGE_ROOT" in
		*"$STAGE_MARKER"*) ;;
		*) die "stage host/path do not contain '$STAGE_MARKER' — refusing to guess." ;;
	esac
}

# ─── Remote plumbing ──────────────────────────────────────────────────────────

# Exports constants into the remote shell, then runs the body supplied on stdin.
# Bodies are quoted heredocs so nothing is expanded locally; values arrive via the
# preamble, which keeps spaces and $ out of both command lines.
run_remote() {
	{
		printf 'set -euo pipefail\n'
		printf 'REMOTE_THEME=%s\n' "$(printf '%q' "$REMOTE_THEME")"
		printf 'REMOTE_ROOT=%s\n' "$(printf '%q' "$STAGE_ROOT")"
		printf 'REMOTE_SNAP_DIR=%s\n' "$(printf '%q' "$REMOTE_SNAP_DIR")"
		printf 'DEPLOY_COMMIT=%s\n' "$(printf '%q' "$DEPLOY_COMMIT")"
		printf 'KEEP_SNAPSHOTS=%d\n' "$KEEP_SNAPSHOTS"
		printf 'SNAP_FILE=%s\n' "$(printf '%q' "$SNAPSHOT_FILE")"
		cat
	} | ssh "${SSH_ARGS[@]}" "$STAGE_HOST" 'bash -s'
}

http_status() { curl -s -L -o /dev/null -w '%{http_code}' --max-time 25 "$1" 2>/dev/null || echo 000; }

mktmp() { local f; f="$(mktemp "${TMPDIR:-/tmp}/deploy-stage.XXXXXX")"; TMP_FILES+=("$f"); printf '%s' "$f"; }
mktmpd() { local d; d="$(mktemp -d "${TMPDIR:-/tmp}/deploy-stage.XXXXXX")"; TMP_FILES+=("$d"); printf '%s' "$d"; }

# Fetched pages are cached for the run. Assertions must grep a FILE, never a
# `curl | grep -q` pipeline: grep -q exits at the first match, curl takes
# SIGPIPE, and `pipefail` turns that into a non-zero pipeline — so the assertion
# silently reports a false negative most of the time (measured: 2 of 12 runs
# passed before this was fixed).
PAGE_CACHE_DIR="$(mktmpd)"

_page_key() { printf '%s' "$1" | tr -c 'A-Za-z0-9' '_'; }

page_fetch() {
	local path="$1" key file
	key="$(_page_key "$path")"
	file="$PAGE_CACHE_DIR/$key.html"
	if [ ! -f "$file" ]; then
		curl -s -L -o "$file" -w '%{http_code}' --max-time 25 "$STAGE_URL$path" > "$file.code" 2>/dev/null \
			|| echo 000 > "$file.code"
		[ -s "$file" ] || warn "empty body from $STAGE_URL$path — assertions on it are meaningless"
	fi
	printf '%s' "$file"
}

page_status() {
	page_fetch "$1" >/dev/null
	cat "$PAGE_CACHE_DIR/$(_page_key "$1").html.code" 2>/dev/null || echo 000
}

# ─── Pre-flight ───────────────────────────────────────────────────────────────

preflight_local() {
	step "Pre-flight — local"

	[ -d "$LOCAL_THEME" ] || die "child theme not found at $LOCAL_THEME"
	ok "theme present: ${LOCAL_THEME#"$REPO_ROOT/"}"

	[ -f "$SSH_KEY" ] || die "SSH key not found at: $SSH_KEY (set GEROTECH_SSH_KEY to override)"
	ok "SSH key present"

	command -v rsync >/dev/null || die "rsync not found locally"
	command -v curl >/dev/null || die "curl not found locally"
	ok "rsync + curl available"

	if [ "$SCOPE" = "home-nav" ]; then
		[ -f "$FILES_FROM_HOME_NAV" ] || die "scope home-nav needs $FILES_FROM_HOME_NAV"
		local n missing=0 f
		n="$(grep -cv '^[[:space:]]*$' "$FILES_FROM_HOME_NAV" || true)"
		while IFS= read -r f; do
			[ -n "$f" ] || continue
			[ -e "$LOCAL_THEME/$f" ] || { fail "listed but missing locally: $f"; missing=1; }
		done < "$FILES_FROM_HOME_NAV"
		[ "$missing" -eq 0 ] || die "the home-nav file list references files that do not exist."
		ok "scope home-nav: $n files, all present"
	fi

	if git -C "$REPO_ROOT" rev-parse --git-dir >/dev/null 2>&1; then
		local branch head porcelain upstream
		branch="$(git -C "$REPO_ROOT" rev-parse --abbrev-ref HEAD)"
		head="$(git -C "$REPO_ROOT" rev-parse HEAD)"
		porcelain="$(git -C "$REPO_ROOT" status --porcelain)"

		if [ -n "$porcelain" ]; then
			if [ "$ALLOW_DIRTY" -eq 1 ]; then
				warn "working tree is DIRTY (--allow-dirty given)"
			else
				printf '%s\n' "$porcelain" | sed 's/^/       /' >&2
				block_or_fail "working tree is dirty. Commit first, or pass --allow-dirty."
			fi
		else
			ok "working tree clean"
		fi

		if upstream="$(git -C "$REPO_ROOT" rev-parse --abbrev-ref --symbolic-full-name '@{u}' 2>/dev/null)"; then
			if [ "$head" = "$(git -C "$REPO_ROOT" rev-parse '@{u}')" ]; then
				ok "HEAD matches $upstream"
			elif [ "$ALLOW_UNPUSHED" -eq 1 ]; then
				warn "HEAD is ahead of $upstream (--allow-unpushed given)"
			else
				block_or_fail "HEAD is not pushed to $upstream. Push it, or pass --allow-unpushed."
			fi
		else
			warn "branch '$branch' has no upstream — cannot confirm it is pushed"
		fi

		[ "$branch" = "master" ] || warn "deploying from branch '$branch', not master"
	else
		warn "not a git repository — skipping tree/push checks"
	fi
}

preflight_remote() {
	step "Pre-flight — stage"

	assert_not_production
	ok "target is stage, not production"

	local out
	if ! out="$(run_remote <<'REMOTE'
command -v rsync >/dev/null || { echo "MISSING rsync"; exit 3; }
command -v wp    >/dev/null || { echo "MISSING wp";    exit 3; }
[ -d "$REMOTE_THEME" ] || { echo "MISSING theme dir";  exit 3; }
echo "siteurl=$(wp option get siteurl 2>/dev/null | head -1)"
echo "theme=$(wp theme list --status=active --field=name 2>/dev/null | head -1)"
echo "files=$(find "$REMOTE_THEME" -type f -not -name '.DS_Store' | wc -l | tr -d ' ')"
REMOTE
)"; then
		die "cannot reach stage, or stage failed pre-flight: ${out:-no output}"
	fi
	ok "SSH session established; $(printf '%s' "$out" | tr '\n' ' ')"

	case "$out" in *"siteurl=https://gerotechdev"*) ;; *)
		die "stage reports a siteurl that is not gerotechdev — refusing to touch it." ;; esac
	case "$out" in *"theme=gerotech-child"*) ;; *)
		die "the child theme is not the active theme on stage." ;; esac
}

# ─── Drift ────────────────────────────────────────────────────────────────────

manifest_local() {
	( cd "$LOCAL_THEME" && find . -type f -not -name '.DS_Store' -exec shasum -a 256 {} + ) \
		| sed 's|  \./|  |' | sort -k2
}

manifest_remote() {
	run_remote <<'REMOTE' | sed 's|  \./|  |' | sort -k2
cd "$REMOTE_THEME"
find . -type f -not -name '.DS_Store' -exec sha256sum {} +
REMOTE
}

show_drift() {
	local l="$1" r="$2"
	step "Drift — checksums, not timestamps"
	local lt rt add del chg same
	lt="$(mktmp)"; rt="$(mktmp)"
	cut -d' ' -f2- <<<"$l" > "$lt"
	cut -d' ' -f2- <<<"$r" > "$rt"
	add="$(comm -23 "$lt" "$rt" | wc -l | tr -d ' ')"
	del="$(comm -13 "$lt" "$rt" | wc -l | tr -d ' ')"
	local j; j="$(mktmp)"
	join -j2 <(sort -k2 <<<"$l") <(sort -k2 <<<"$r") > "$j"
	chg="$(awk '$2 != $3' "$j" | wc -l | tr -d ' ')"
	same="$(awk '$2 == $3' "$j" | wc -l | tr -d ' ')"

	info "repo files        : $(wc -l <<<"$l" | tr -d ' ')"
	info "stage files       : $(wc -l <<<"$r" | tr -d ' ')"
	info "would be added    : $add"
	info "would be deleted  : $del"
	info "would be replaced : $chg"
	info "byte-identical    : $same"

	[ "$chg" -gt 0 ] && awk '$2 != $3 {print "       ~ " $1}' "$j"
	[ "$del" -gt 0 ] && { warn "$del file(s) exist on stage but not in the repo:"; comm -13 "$lt" "$rt" | sed 's/^/       - /' >&2; }
	return 0
}

# ─── Deploy ───────────────────────────────────────────────────────────────────

do_rsync() {
	local dry="$1"
	local -a args=(-a --itemize-changes --exclude .DS_Store)
	[ "$dry" = dry ] && args+=(-n)

	step "rsync ($([ "$dry" = dry ] && echo 'dry run — nothing written' || echo writing))"
	if [ "$SCOPE" = "home-nav" ]; then
		args+=(--files-from "$FILES_FROM_HOME_NAV")
		info "scope home-nav — no --delete (with --files-from it would remove everything else)"
	else
		args+=(--delete)
		info "scope theme — with --delete"
	fi

	local out changed
	out="$(rsync "${args[@]}" -e "$SSH_CMD" "$LOCAL_THEME/" "$STAGE_HOST:$REMOTE_THEME/" 2>&1)" \
		|| { printf '%s\n' "$out" >&2; die "rsync failed"; }

	changed="$(printf '%s\n' "$out" | grep -cE '^[<>ch*]' || true)"
	changed="$(printf '%s' "$changed" | tr -d '[:space:]')"
	if [ "${changed:-0}" -eq 0 ]; then
		ok "no changes to transfer"
	else
		printf '%s\n' "$out" | grep -E '^[<>ch*]' | sed 's/^/       /'
		info "$changed item(s)"
	fi
	return 0
}

snapshot() {
	if [ "$SKIP_SNAPSHOT" -eq 1 ]; then warn "snapshot skipped — no rollback point"; return 0; fi
	step "Snapshot — the rollback point"
	local name
	name="$(run_remote <<'REMOTE'
mkdir -p "$REMOTE_SNAP_DIR"
stamp="$(date -u +%Y%m%d-%H%M%S)"
file="$REMOTE_SNAP_DIR/gerotech-child-$stamp-$DEPLOY_COMMIT.tgz"
cd "$REMOTE_ROOT/wp-content/themes"
tar -czf "$file" gerotech-child
ls -1t "$REMOTE_SNAP_DIR"/gerotech-child-*.tgz 2>/dev/null \
  | tail -n +$((KEEP_SNAPSHOTS + 1)) | xargs -r rm -f
# Prove it survived rather than trusting the tar exit code.
[ -s "$file" ] || { echo "SNAPSHOT-EMPTY" >&2; exit 1; }
basename "$file"
REMOTE
)" || die "could not create a snapshot on stage"
	case "$name" in *SNAPSHOT-EMPTY*) die "the snapshot came out empty" ;; esac
	ok "snapshot: $name"
	SNAPSHOT_TAKEN="$name"
}

remote_post_deploy() {
	step "Permissions + caches"
	run_remote <<'REMOTE'
find "$REMOTE_THEME" -type d -exec chmod 755 {} +
find "$REMOTE_THEME" -type f -exec chmod 644 {} +
echo "perms normalised"
REMOTE
	ok "755/644 — a mode-600 file is a 403 on WP Engine"

	run_remote <<'REMOTE'
wp page-cache flush >/dev/null 2>&1 || true
wp cdn-cache flush  >/dev/null 2>&1 || true
echo flushed
REMOTE
	ok "page cache + CDN cache flushed (the CDN serves theme CSS/JS by name)"
}

# ─── Assertions ───────────────────────────────────────────────────────────────

fetch_page() { page_fetch "$1"; }

assert_status() {
	local u code
	for u in "$@"; do
		code="$(page_status "$u")"
		if [ "$code" = 200 ]; then ok "200  $u"; else fail "$code  $u"; ASSERT_FAILED=1; fi
	done
}

assert_present() {
	if grep -qF -- "$2" "$(page_fetch "$1")"; then
		ok "present   $(printf '%-36s' "$2") on $1"
	else
		fail "MISSING   $2 on $1"; ASSERT_FAILED=1
	fi
}

assert_absent() {
	if grep -qF -- "$2" "$(page_fetch "$1")"; then
		fail "UNEXPECTED  $2 on $1"; ASSERT_FAILED=1
	else
		ok "absent    $(printf '%-36s' "$2") on $1"
	fi
}

# Not a failure: stage legitimately stores absolute gerotechdev URLs. It must be
# resolved before Production, so it is surfaced every run.
warn_if_present() {
	if grep -qF -- "$2" "$(page_fetch "$1")"; then
		warn "found \"$2\" on $1 — expected on stage, must be replaced before Production"
	fi
	return 0
}

assert_rendered() {
	step "Assertions — rendered stage"
	assert_status "/" "/engineered-solutions/" "/modification-of-standard-machine-tools/" \
	              "/unique-applications-for-standard-machines/" "/automated-system/" "/contact/"

	assert_present "/" "hero-slider--peek"
	assert_present "/" "mega-nav__machine-link"
	assert_present "/" "cta-band__actions"
	assert_present "/" "search-modal__hint"
	assert_present "/engineered-solutions/" "mega-nav__service-link"
	assert_present "/automated-system/" "layered-controls-solutions"

	assert_absent "/" "Prototype site search"

	local got
	got="$(grep -o 'mega-nav__machine-link' "$(page_fetch "/")" | wc -l | tr -d ' ' || true)"
	if [ "${got:-0}" -ge 30 ]; then ok "machine links rendered: $got"; else warn "machine links rendered: $got (expected >= 30)"; fi

	warn_if_present "/" "wpenginepowered.com/contact"
	return 0
}

chrome_diff_local() {
	step "Rendered chrome — Local vs stage (advisory)"
	if [ "$(http_status "$LOCAL_URL/")" != 200 ]; then
		warn "Local not reachable at $LOCAL_URL — skipping"; return 0
	fi
	command -v python3 >/dev/null || { warn "python3 unavailable — skipping"; return 0; }

	local a b
	a="$(mktmp)"; b="$(mktmp)"
	curl -s -L --max-time 25 "$LOCAL_URL/" > "$a"
	curl -s -L --max-time 25 "$STAGE_URL/" > "$b"

	if python3 - "$a" "$b" <<'PY'
import re,sys
def chrome(p):
    h=open(p,encoding='utf-8',errors='replace').read()
    parts=[]
    for t in ('header','footer'):
        m=re.search(r'<'+t+r'\b.*?</'+t+r'>',h,re.S)
        parts.append(m.group(0) if m else '')
    return '\n'.join(parts)
def norm(s):
    s=re.sub(r'https?://gerotechdev\.wpenginepowered\.com','HOST',s)
    s=re.sub(r'https?://gerotech\.local','HOST',s)
    s=re.sub(r'\?ver=[^"\'&]+','?ver=V',s)
    return re.sub(r'\s+',' ',s)
x,y=norm(chrome(sys.argv[1])),norm(chrome(sys.argv[2]))
if x==y:
    print('       chrome identical (%d chars)'%len(x)); sys.exit(0)
import difflib
for line in list(difflib.unified_diff(x.split('><'),y.split('><'),lineterm='',n=0))[:20]:
    print('       '+line)
sys.exit(1)
PY
	then ok "chrome matches Local"; else warn "chrome differs between Local and stage (see above)"; fi
	return 0
}

# ─── Content ──────────────────────────────────────────────────────────────────

content_stage() {
	if [ "$SEED" -eq 1 ]; then
		step "Seed ACF — idempotent, blanks only"
		run_remote <<'REMOTE'
if [ -f "$REMOTE_ROOT/_gerotech-scripts/seed-acf-content.php" ]; then
	wp eval-file "$REMOTE_ROOT/_gerotech-scripts/seed-acf-content.php"
else
	echo "  NOTE: seed-acf-content.php is not in _gerotech-scripts/ — skipped"
	echo "        stage it with: rsync -avz scripts/seed-acf-content.php <stage>:$REMOTE_ROOT/_gerotech-scripts/"
fi
REMOTE
	fi

	if [ "$AUDIT" -eq 1 ]; then
		step "ACF applied-state audit — read-only"
		local script="$REPO_ROOT/scripts/audit-acf-applied.php"
		if [ -f "$script" ]; then
			ssh "${SSH_ARGS[@]}" "$STAGE_HOST" "cd $STAGE_ROOT && wp eval-file -" < "$script" 2>&1 \
				| grep -v 'DISALLOW_FILE_EDIT' | tail -40 || true
		else
			warn "scripts/audit-acf-applied.php not found — skipped"
		fi
	fi
	return 0
}

# ─── Snapshots / rollback ─────────────────────────────────────────────────────

list_snapshots() {
	step "Snapshots on stage"
	run_remote <<'REMOTE'
if [ ! -d "$REMOTE_SNAP_DIR" ]; then echo "       (none yet)"; exit 0; fi
found=0
for f in "$REMOTE_SNAP_DIR"/gerotech-child-*.tgz; do
	[ -f "$f" ] || continue
	found=1
	printf '       %-8s %s\n' "$(du -h "$f" | cut -f1)" "$(basename "$f")"
done
[ "$found" -eq 1 ] || echo "       (none yet)"
REMOTE
}

do_rollback() {
	assert_not_production
	step "Rollback"

	local name="$SNAPSHOT_FILE"
	if [ -z "$name" ]; then
		name="$(run_remote <<'REMOTE'
ls -1t "$REMOTE_SNAP_DIR"/gerotech-child-*.tgz 2>/dev/null | head -1 | xargs -r basename
REMOTE
)"
	fi
	[ -n "$name" ] || die "no snapshot to roll back to — run --list-snapshots."

	info "restoring: $name"
	SNAPSHOT_FILE="$name"

	run_remote <<'REMOTE'
snap="$REMOTE_SNAP_DIR/$SNAP_FILE"
[ -f "$snap" ] || { echo "snapshot not found: $snap" >&2; exit 1; }
cd "$REMOTE_ROOT/wp-content/themes"
tmp="$(mktemp -d)"
trap 'rm -rf "$tmp"' EXIT
tar -xzf "$snap" -C "$tmp"
[ -d "$tmp/gerotech-child" ] || { echo "snapshot did not contain gerotech-child" >&2; exit 1; }
rm -rf gerotech-child.previous
mv gerotech-child gerotech-child.previous
mv "$tmp/gerotech-child" gerotech-child
rm -rf gerotech-child.previous
echo "restored"
REMOTE

	ok "restored $name"
	remote_post_deploy
}

# ─── Main ─────────────────────────────────────────────────────────────────────

printf '%sdeploy-stage%s  mode=%s scope=%s target=%s\n' "$B" "$N" "$MODE" "$SCOPE" "$STAGE_HOST"

case "$MODE" in
	list)
		assert_not_production
		list_snapshots
		;;

	rollback)
		need_confirmation
		do_rollback
		assert_rendered
		[ "$ASSERT_FAILED" -eq 0 ] || die "files restored, but the rendered assertions still fail."
		ok "rollback verified"
		[ "$WARNINGS" -eq 0 ] || info "$WARNINGS warning(s) — see above."
		;;

	verify)
		assert_not_production
		assert_rendered
		chrome_diff_local
		step "Verdict"
		[ "$ASSERT_FAILED" -eq 0 ] || die "the rendered assertions failed — stage is not rendering as expected."
		ok "stage renders as expected (read-only — nothing was written)"
		[ "$WARNINGS" -eq 0 ] || info "$WARNINGS warning(s) — see above."
		;;

	check)
		preflight_local
		preflight_remote
		local_l="$(mktmp)"; remote_l="$(mktmp)"
		manifest_local > "$local_l"
		manifest_remote > "$remote_l"
		show_drift "$(cat "$local_l")" "$(cat "$remote_l")"
		do_rsync dry
		chrome_diff_local
		step "Verdict"
		info "READ-ONLY — nothing was written to stage."
		[ "$WARNINGS" -gt 0 ] && info "$WARNINGS warning(s) above — read them before --apply." || ok "clean"
		;;

	apply)
		need_confirmation
		preflight_local
		preflight_remote
		snapshot
		do_rsync write
		remote_post_deploy

		step "Verify — checksums"
		if [ "$SCOPE" = "theme" ]; then
			local_l="$(mktmp)"; remote_l="$(mktmp)"
			manifest_local > "$local_l"
			manifest_remote > "$remote_l"
			if diff -q "$local_l" "$remote_l" >/dev/null; then
				ok "repo and stage are byte-identical ($(wc -l < "$local_l" | tr -d ' ') files)"
			else
				diff "$local_l" "$remote_l" | head -20 | sed 's/^/       /' >&2
				die "stage does not match the repo after the push."
			fi
		else
			info "scope home-nav — skipping the whole-tree manifest compare"
		fi

		content_stage
		assert_rendered
		chrome_diff_local

		step "Verdict"
		[ "$ASSERT_FAILED" -eq 0 ] \
			|| die "push completed but the rendered assertions FAILED. Roll back: scripts/deploy-stage.sh --rollback --yes"
		ok "deployed successfully${SNAPSHOT_TAKEN:+ (snapshot: $SNAPSHOT_TAKEN)}"
		[ "$WARNINGS" -eq 0 ] || info "$WARNINGS warning(s) — see above."
		;;
esac
