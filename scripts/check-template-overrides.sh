#!/usr/bin/env bash
#
# check-template-overrides.sh — refuse to ship a template override that drops a form.
#
# THE BUG THIS PREVENTS
# ---------------------
# A child theme wins over its parent for any template with the same filename.
# Ours shipped `page-service.php`, `page-contact.php`, `rotary-repair.php`,
# `preventive-maintenance.php` and `page-support.php`, and NONE of them rendered
# the page's Gravity Form — the parent's originals do, via
# `do_shortcode('[gravityform id="N" …]')`. Activating the child therefore
# silently replaced four working forms with Contact Form 7 forms that deliver
# to a `@…wpenginepowered.com` hostname. Nothing errored. The page just quietly
# lost its form.
#
# WHAT IT DOES
# ------------
# For every template present in BOTH themes, it compares the form shortcodes
# each one renders. Any form the parent renders that the child does not is a
# failure, with the file and the missing id.
#
# Usage
#   scripts/check-template-overrides.sh --env dev|stg
#   scripts/check-template-overrides.sh --env stg --quiet     # exit code only
#
# Exit: 0 clean, 1 a template would drop a form, 2 usage error.
#
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
THEME_SLUG="gerotech-child"
PARENT_SLUG="gerotech"

SSH_KEY="${GEROTECH_SSH_KEY:-$HOME/Library/Application Support/Local/ssh/wpe-connect}"
SSH_ARGS=(-i "$SSH_KEY" -o BatchMode=yes -o ConnectTimeout=20)
PRODUCTION_MARKER="gerotech.ssh.wpengine.net"

ENV=""
QUIET=0

usage() { sed -n '3,25p' "${BASH_SOURCE[0]}" | sed 's/^# \{0,1\}//'; exit 2; }

while [ $# -gt 0 ]; do
	case "$1" in
		--env)   ENV="${2:?--env needs a value}"; shift ;;
		--quiet) QUIET=1 ;;
		--help|-h) usage ;;
		*) printf 'unknown argument: %s\n' "$1" >&2; usage ;;
	esac
	shift
done

# An allow-list, exactly as in deploy-stage.sh: production has no entry.
case "$ENV" in
	dev) INSTALL="gerotechdev" ;;
	stg) INSTALL="gerotechstg" ;;
	*)   printf 'x --env must be "dev" or "stg"\n\n' >&2; usage ;;
esac

HOST="$INSTALL@$INSTALL.ssh.wpengine.net"
case "$HOST" in
	*"$PRODUCTION_MARKER"*) printf 'x refusing: that is production\n' >&2; exit 2 ;;
esac

[ -f "$SSH_KEY" ] || { printf 'x SSH key not found: %s\n' "$SSH_KEY" >&2; exit 2; }

[ "$QUIET" -eq 1 ] || printf 'template-override guard — %s (%s)\n' "$ENV" "$INSTALL"

OUT="$(
	{
		printf 'set -euo pipefail\n'
		printf 'ROOT=%s\n' "$(printf '%q' "/nas/content/live/$INSTALL")"
		printf 'CHILD_SLUG=%s\n' "$(printf '%q' "$THEME_SLUG")"
		printf 'PARENT_SLUG=%s\n' "$(printf '%q' "$PARENT_SLUG")"
		cat <<'REMOTE'
THEMES="$ROOT/wp-content/themes"
CHILD="$THEMES/$CHILD_SLUG"
PARENT="$THEMES/$PARENT_SLUG"

if [ ! -d "$CHILD" ]; then
	echo "SKIP: $CHILD_SLUG is not installed on this environment"
	exit 0
fi
if [ ! -d "$PARENT" ]; then
	echo "SKIP: the parent theme $PARENT_SLUG is not present"
	exit 0
fi

fail=0
checked=0

# Overrides that INTENTIONALLY replace the parent's shortcode output. These are
# design decisions, not accidents, so they are named here and reported rather
# than failed:
#   header.php — the design replaces the legacy Smart Slider entirely.
#   footer.php — the design ships its own mailing-list signup, so the legacy
#                CF7 102/233 forms are deliberately not rendered.
INTENTIONAL="header.php footer.php"

# Form shortcodes the legacy templates render. Anything here that the child
# override does not also render is content silently lost to the parent.
ids() {
	grep -ohE 'gravityform id="[0-9]+"|contact-form-7 id="[0-9]+"' "$1" 2>/dev/null \
		| sort -u || true
}

for child_file in "$CHILD"/*.php; do
	[ -e "$child_file" ] || continue
	name="$(basename "$child_file")"
	parent_file="$PARENT/$name"
	[ -f "$parent_file" ] || continue

	checked=$((checked + 1))

	case " $INTENTIONAL " in
		*" $name "*) continue ;;
	esac

	missing="$(comm -23 <(ids "$parent_file") <(ids "$child_file") || true)"

	if [ -n "$missing" ]; then
		fail=1
		printf 'FAIL  %s overrides the parent and drops:\n' "$name"
		printf '%s\n' "$missing" | sed 's/^/          /'
	fi
done

for name in $INTENTIONAL; do
	[ -f "$CHILD/$name" ] || continue
	missing="$(comm -23 <(ids "$PARENT/$name") <(ids "$CHILD/$name") || true)"
	if [ -n "$missing" ]; then
		printf 'note  %s intentionally replaces the parent shortcode output (design)\n' "$name"
	fi
done

echo "TEMPLATES_CHECKED=$checked"
exit $fail
REMOTE
	} | ssh "${SSH_ARGS[@]}" "$HOST" 'bash -s'
)" || {
	printf '\n%s\n' "$OUT" >&2
	printf 'REFUSING: a template override would drop a form shortcode.\n' >&2
	exit 1
}

CHECKED="$(printf '%s\n' "$OUT" | sed -n 's/^TEMPLATES_CHECKED=//p')"

if [ "$QUIET" -eq 0 ]; then
	printf '%s\n' "$OUT" | grep -v '^TEMPLATES_CHECKED=' | grep -v '^$' || true
	printf '  %s template(s) overridden by the child were compared\n' "${CHECKED:-0}"
	printf '  OK   no override drops a form shortcode\n'
fi

exit 0
