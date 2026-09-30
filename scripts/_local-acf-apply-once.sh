#!/usr/bin/env bash
set -euo pipefail
PHP="$HOME/Library/Application Support/Local/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php"
WP="$HOME/Local Sites/goodshepherd/_tools/wp-cli.phar"
SITE="$HOME/Local Sites/gerotech/app/public"
SOCK="$HOME/Library/Application Support/Local/run/VjZ_PwL-d/mysql/mysqld.sock"
REPO="$(cd "$(dirname "$0")/.." && pwd)"
S="$REPO/scripts"

wp() {
  "$PHP" -d "mysql.default_socket=$SOCK" -d "mysqli.default_socket=$SOCK" "$WP" --path="$SITE" --url=https://gerotech.local "$@"
}

cd "$SITE"
wp option get siteurl >/dev/null

wp eval-file "$S/set-card-image.php" modification-of-standard-machine-tools mcs_cards "Custom Workholding" assets/images/custom-workholding.jpg
wp eval-file "$S/set-page-image.php" modification-of-standard-machine-tools mcs_cta_image assets/images/cta-mcs-cell.jpg
wp eval-file "$S/add-app-gallery-items.php" Demo \
  "assets/images/app-gallery-demo-dc1.jpg|Gerotech instructor demonstrating a Haas DC-1 to customers in the showroom|Demo · Haas DC-1" \
  "assets/images/app-gallery-demo-showroom-group.jpg|Customers touring a Haas UMC machining center in the Gerotech showroom|Showroom · customer demo"
wp eval-file "$S/set-page-image.php" automated-system ai_hero_image assets/images/automation-hero.jpg
wp eval-file "$S/replace-gallery-media.php" automated-system ai_collections "Robot EOAT – Ancillary Material Handling" \
  "image | assets/images/robot-eoat.jpg | | Custom dual-gripper end-of-arm tooling | Custom end-of-arm tooling" \
  "image | assets/images/automation-gallery/eoat-vacuum-suction.jpg | | Vacuum suction end-of-arm tooling with orange cups on an aluminum frame | Vacuum EOAT · suction cups" \
  "image | assets/images/automation-gallery/eoat-gripper-pair.jpg | | Custom dual end-of-arm gripper tooling with pneumatic fittings on a workbench | Dual gripper EOAT"
wp eval-file "$S/replace-gallery-media.php" automated-system ai_collections "Automation Cell Design" \
  "image | assets/images/automation-cell-design.jpg | | FANUC M-20iD/25 tending a Haas ST-10 in a guarded cell | FANUC M-20iD/25 · Haas ST-10" \
  "image | assets/images/automation-gallery/automation-cell-lab.jpg | | Automation training lab with control cabinet, teach pendant, dual yellow robots on pedestals, EOAT tree, and CNC machines in the background | Training lab · dual robots · EOAT tree" \
  "image | assets/images/automation-gallery/automation-cell-guarded.jpg | | Guarded yellow robot cell with wire-mesh safety enclosure, vertical control cabinet with HMI, and floor controller | Guarded robot cell · control cabinet" \
  "image | assets/images/automation-gallery/automation-cell-vision.jpg | | Keyence overhead machine vision system with four green LED ring lights on a diamond mounting plate | Keyence vision · ring lights"

AI=$(wp post list --post_type=page --name=automated-system --field=ID --format=csv)
MCS=$(wp post list --post_type=page --name=modification-of-standard-machine-tools --field=ID --format=csv)
wp eval "update_field('ai_gallery_eyebrow', 'On Our Floor', (int) '$AI');"
wp eval "update_field('mcs_gallery_eyebrow', 'On Our Floor', (int) '$MCS');"

wp eval-file "$S/set-lineup-panel-photo.php" "Rotaries & Indexers" assets/images/lineup-rotaries-indexers.jpg
wp eval-file "$S/set-lineup-panel-photo.php" "Haas Automation" assets/images/lineup-haas-automation.jpg
wp eval-file "$S/replace-gallery-media.php" unique-applications-for-standard-machines app_collections "Tooling Recommendation" \
  "image | assets/images/app-tooling-cart.jpg | | Haas Tooling.com red mobile cart with tool holders and pegboard display | Haas Tooling.com cart"
wp eval-file "$S/replace-gallery-media.php" automated-system ai_collections "Pre-Engineered Solutions" \
  "image | assets/images/pre-engineered-card.jpg | | Open grey control cabinet with blue wiring, red terminals, and a VFD | Pre-Engineered Solutions · control cabinet" \
  "image | assets/images/pre-engineered-gallery.jpg | | Open dual-door control cabinet on the shop floor beside a Haas machine | Control enclosure · shop floor"

echo "Local ACF apply complete (pages $AI, $MCS)."
