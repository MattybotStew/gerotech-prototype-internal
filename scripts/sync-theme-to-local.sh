#!/usr/bin/env bash
#
# Sync the repo's WordPress child theme into the running Local site.
#
#   One-way: repo wp-content/themes/gerotech-child/  ->  Local site theme dir
#
# The repo copy is the source of truth (it is what gets committed/pushed).
# The Local site has its own copy; edits made only in the repo will not appear
# locally until this runs.
#
# Usage:
#   ./scripts/sync-theme-to-local.sh            # copy repo theme -> Local site
#   ./scripts/sync-theme-to-local.sh --check    # report drift only
#
# Override the destination with GEROTECH_LOCAL_THEME=/path/to/theme.
#
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SRC="$REPO_ROOT/wp-content/themes/gerotech-child"
DEST="${GEROTECH_LOCAL_THEME:-$HOME/Local Sites/gerotech/app/public/wp-content/themes/gerotech-child}"

MODE="sync"
[ "${1:-}" = "--check" ] && MODE="check"

if [ ! -d "$SRC" ]; then
  echo "ERROR: repo theme not found at $SRC" >&2
  exit 1
fi
if [ ! -d "$DEST" ]; then
  echo "ERROR: Local theme not found at $DEST" >&2
  echo "       Set GEROTECH_LOCAL_THEME=/path/to/theme if the site moved." >&2
  exit 1
fi

if [ "$MODE" = "check" ]; then
  if diff -rq --exclude='.DS_Store' "$SRC" "$DEST" >/dev/null 2>&1; then
    echo "OK — Local theme matches the repo."
  else
    echo "DRIFT — Local theme differs from the repo:"
    diff -rq --exclude='.DS_Store' "$SRC" "$DEST" | sed 's/^/  /'
    exit 1
  fi
else
  rsync -a --delete --exclude '.DS_Store' "$SRC/" "$DEST/"
  # macOS ships openrsync, which has no --chmod; normalise explicitly so a
  # restrictive umask can never ship non-world-readable files (WP Engine -> 403).
  find "$DEST" -type d -exec chmod 755 {} + 2>/dev/null || true
  find "$DEST" -type f -exec chmod 644 {} + 2>/dev/null || true
  echo "Synced repo theme -> $DEST"
fi
