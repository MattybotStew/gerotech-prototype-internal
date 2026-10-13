#!/usr/bin/env bash
#
# check-template-overrides.sh — refuse to ship a template override that drops a form.
#
# THE BUG THIS PREVENTS
# ---------------------
# A child theme wins over its parent for any template with the same filename.
# Ours shipped page-service.php, page-contact.php, rotary-repair.php,
# preventive-maintenance.php and page-support.php, and NONE of them rendered the
# page Gravity Form. The parent originals do, via
#   do_shortcode( '[gravityform id="N" ...]' ).
# Activating the child therefore silently replaced four working Gravity Forms
# with Contact Form 7 forms that deliver to a wpenginepowered hostname. Nothing
# errored - the pages just quietly lost their forms.
#
# WHAT IT DOES
# ------------
# Runs on the server, where both themes exist. For every template present in
# BOTH themes it compares the form shortcodes each renders. Any form the parent
# renders that the child does not is a failure, naming the file and the id.
#
# Usage
#   scripts/check-template-overrides.sh --env dev|stg
#   scripts/check-template-overrides.sh --env stg --quiet    # exit code only
#
# Exit: 0 clean, 1 a template would drop a form, 2 usage error.
#
# NOTE ON BASH 3.2 (macOS)
# ------------------------
# The remote logic lives in template-override-guard.remote.sh, a real file piped
# over stdin, rather than a heredoc inside a command substitution. macOS ships
# bash 3.2, which mis-parses a QUOTED heredoc inside $( ) the moment the body
# contains an odd number of single quotes - an apostrophe in a comment is enough
# to produce "unexpected EOF while looking for matching '". Reproduced on
# 3.2.57: a heredoc-wrapped body containing "parent's" fails to parse, while the
# identical body with no apostrophe parses. Keep this construct.
#
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
REMOTE_BODY="${REPO_ROOT}/scripts/template-override-guard.remote.sh"

SSH_KEY="${GEROTECH_SSH_KEY:-$HOME/Library/Application Support/Local/ssh/wpe-connect}"
SSH_ARGS=(-i "${SSH_KEY}" -o BatchMode=yes -o ConnectTimeout=20)
PRODUCTION_MARKER="gerotech.ssh.wpengine.net"

ENV=""
QUIET=0

usage() { sed -n '3,25p' "${BASH_SOURCE[0]}" | sed 's/^# \{0,1\}//'; exit 2; }

while [ $# -gt 0 ]; do
	case "$1" in
		--env)     ENV="${2:?--env needs a value}"; shift ;;
		--quiet)   QUIET=1 ;;
		--help|-h) usage ;;
		*)         printf 'unknown argument: %s\n' "$1" >&2; usage ;;
	esac
	shift
done

# An allow-list, exactly as in deploy-stage.sh: production has no entry.
case "${ENV}" in
	dev) INSTALL="gerotechdev" ;;
	stg) INSTALL="gerotechstg" ;;
	*)   printf 'x --env must be "dev" or "stg"\n\n' >&2; usage ;;
esac

HOST="${INSTALL}@${INSTALL}.ssh.wpengine.net"
case "${HOST}" in
	*"${PRODUCTION_MARKER}"*) printf 'x refusing: that is production\n' >&2; exit 2 ;;
esac

[ -f "${SSH_KEY}" ]   || { printf 'x SSH key not found: %s\n' "${SSH_KEY}" >&2; exit 2; }
[ -f "${REMOTE_BODY}" ] || { printf 'x missing %s\n' "${REMOTE_BODY}" >&2; exit 2; }
[ "${QUIET}" -eq 1 ] || printf 'template-override guard - %s (%s)\n' "${ENV}" "${INSTALL}"

OUT=""
STATUS=0
OUT="$(ssh "${SSH_ARGS[@]}" "${HOST}" "bash -s -- $(printf '%q' "${INSTALL}")" \
	< "${REMOTE_BODY}" 2>&1)" || STATUS=$?

CHECKED="$(printf '%s\n' "${OUT}" | sed -n 's/^TEMPLATES_CHECKED=//p')"

if [ "${STATUS}" -ne 0 ]; then
	printf '%s\n' "${OUT}" | grep -v '^TEMPLATES_CHECKED=' | sed '/^$/d' >&2
	printf 'REFUSING: a template override would drop a form shortcode.\n' >&2
	exit 1
fi

if [ "${QUIET}" -eq 0 ]; then
	printf '%s\n' "${OUT}" | grep -v '^TEMPLATES_CHECKED=' | sed '/^$/d' || true
	printf '  %s template(s) overridden by the child were compared\n' "${CHECKED:-0}"
	printf '  OK   no override drops a form shortcode\n'
fi

exit 0
