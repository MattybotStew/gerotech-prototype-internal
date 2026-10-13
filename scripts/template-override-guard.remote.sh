#!/usr/bin/env bash
#
# template-override-guard.remote.sh — runs ON the WP Engine server.
#
# Read from stdin by:  ssh <host> "bash -s -- <install>" < this-file
#
# Kept as a real file rather than a heredoc inside the calling script because
# macOS ships bash 3.2, which mis-parses a quoted heredoc inside a command
# substitution as soon as the body contains an apostrophe. See the note in
# check-template-overrides.sh.
#
# THE BUG THIS DETECTS
# --------------------
# A child theme wins over its parent for any template with the same filename.
# If the child's version renders fewer form shortcodes than the parent's
# original, the page silently loses that form. That is exactly how four working
# Gravity Forms were replaced by Contact Form 7 forms that deliver nowhere.
#
set -euo pipefail

INSTALL="${1:?usage: bash -s -- <install-name>}"
ROOT="/nas/content/live/${INSTALL}"
THEMES="${ROOT}/wp-content/themes"
CHILD="${THEMES}/gerotech-child"
PARENT="${THEMES}/gerotech"

if [ ! -d "${CHILD}" ]; then
	echo "SKIP: gerotech-child is not installed on this environment"
	exit 0
fi
if [ ! -d "${PARENT}" ]; then
	echo "SKIP: the parent theme gerotech is not present"
	exit 0
fi

# Overrides that INTENTIONALLY replace the parent shortcode output. These are
# design decisions, not accidents, so they are reported rather than failed:
#   header.php  - the design replaces the legacy Smart Slider entirely.
#   footer.php  - the design ships its own mailing-list signup, so the legacy
#                 CF7 102/233 forms are deliberately not rendered.
INTENTIONAL="header.php footer.php"

# Form shortcodes the legacy templates render.
ids() {
	grep -ohE 'gravityform id="[0-9]+"|contact-form-7 id="[0-9]+"' "$1" 2>/dev/null \
		| sort -u || true
}

fail=0
checked=0

for child_file in "${CHILD}"/*.php; do
	[ -e "${child_file}" ] || continue
	name="$(basename "${child_file}")"
	parent_file="${PARENT}/${name}"
	[ -f "${parent_file}" ] || continue

	checked=$((checked + 1))

	case " ${INTENTIONAL} " in
		*" ${name} "*) continue ;;
	esac

	missing="$(comm -23 <(ids "${parent_file}") <(ids "${child_file}") || true)"

	if [ -n "${missing}" ]; then
		fail=1
		printf 'FAIL  %s overrides the parent and drops:\n' "${name}"
		printf '%s\n' "${missing}" | sed 's/^/          /'
	fi
done

for name in ${INTENTIONAL}; do
	[ -f "${CHILD}/${name}" ] || continue
	missing="$(comm -23 <(ids "${PARENT}/${name}") <(ids "${CHILD}/${name}") || true)"
	if [ -n "${missing}" ]; then
		printf 'note  %s intentionally replaces the parent shortcode output (design)\n' "${name}"
	fi
done

echo "TEMPLATES_CHECKED=${checked}"
exit "${fail}"
