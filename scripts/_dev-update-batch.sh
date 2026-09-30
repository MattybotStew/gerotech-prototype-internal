#!/usr/bin/env bash
set -euo pipefail
REPO="$(cd "$(dirname "$0")/.." && pwd)"
SSH_KEY="$HOME/Library/Application Support/Local/ssh/wpe-connect"
HOST="gerotechdev@gerotechdev.ssh.wpengine.net"
REMOTE="/nas/content/live/gerotechdev"
S="$REMOTE/_gerotech-scripts"

rsync -avz --delete -e "ssh -i \"$SSH_KEY\"" "$REPO/wp-content/themes/gerotech-child/" "$HOST:$REMOTE/wp-content/themes/gerotech-child/"

ssh -i "$SSH_KEY" -o BatchMode=yes "$HOST" "find $REMOTE/wp-content/themes/gerotech-child -type d -exec chmod 755 {} +; find $REMOTE/wp-content/themes/gerotech-child -type f -exec chmod 644 {} +; mkdir -p $S"

rsync -avz -e "ssh -i \"$SSH_KEY\"" \
  "$REPO/scripts/set-card-image.php" \
  "$REPO/scripts/set-lineup-panel-photo.php" \
  "$REPO/scripts/replace-gallery-media.php" \
  "$HOST:$S/"

ssh -i "$SSH_KEY" -o BatchMode=yes "$HOST" bash <<'REMOTE'
set -euo pipefail
REMOTE="/nas/content/live/gerotechdev"
S="$REMOTE/_gerotech-scripts"
cd "$REMOTE"

wp eval-file "$S/set-lineup-panel-photo.php" "Rotaries & Indexers" assets/images/lineup-rotaries-indexers.jpg
wp eval-file "$S/set-lineup-panel-photo.php" "Haas Automation" assets/images/lineup-haas-automation.jpg

wp eval-file "$S/set-card-image.php" unique-applications-for-standard-machines app_cards "Tooling Recommendation" assets/images/app-tooling-cart.jpg

wp eval-file "$S/replace-gallery-media.php" unique-applications-for-standard-machines app_collections "Tooling Recommendation" \
  "image | assets/images/app-tooling-cart.jpg | | Haas Tooling.com red mobile cart with tool holders and pegboard display | Haas Tooling.com cart"

wp eval-file "$S/replace-gallery-media.php" automated-system ai_collections "Pre-Engineered Solutions" \
  "image | assets/images/pre-engineered-card.jpg | | Open grey control cabinet with blue wiring, red terminals, and a VFD | Pre-Engineered Solutions · control cabinet" \
  "image | assets/images/pre-engineered-gallery.jpg | | Open dual-door control cabinet on the shop floor beside a Haas machine | Control enclosure · shop floor"

wp page-cache flush
wp cdn-cache flush
echo "Dev batch update complete."
REMOTE
