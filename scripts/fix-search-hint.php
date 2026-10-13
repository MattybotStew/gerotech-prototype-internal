<?php
/**
 * Replace the client-facing "Prototype site search …" hint.
 *
 * The header search modal's line under the heading was authored while this was
 * a prototype and ended up in the stored options on Local and Dev:
 *
 *   "Prototype site search — browse by section:"
 *
 * The word "Prototype" must not ship. This rewrites the stored option only when
 * it still contains "Prototype", so an editor's own wording is never clobbered
 * and re-running is a no-op. The template default in inc/global-content.php is
 * changed separately so a fresh database is correct too.
 *
 *   wp eval-file scripts/fix-search-hint.php
 *
 * ⚠ scripts/ is NOT part of a theme-only push. Copy it to the server first.
 *
 * @package GerotechChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$new = 'Browse by section:';

$current = get_field( 'header_search_hint', 'option' );

if ( null === $current || '' === trim( (string) $current ) ) {
	echo "header_search_hint is blank — the template default renders. Nothing to change.\n";
	echo "Default is now: {$new}\n";
	exit;
}

if ( false === strpos( (string) $current, 'Prototype' ) ) {
	echo "header_search_hint already client-safe: \"" . $current . "\" — nothing to change.\n";
	exit;
}

update_field( 'field_header_search_hint', $new, 'option' );

// Prove the write landed rather than trusting the return value.
$store = acf_get_store( 'values' );
if ( $store ) {
	$store->reset();
}
$after = get_field( 'header_search_hint', 'option' );

echo "was: \"" . $current . "\"\n";
echo "now: \"" . $after . "\"\n";
echo ( false === strpos( (string) $after, 'Prototype' ) ) ? "OK — no \"Prototype\" left.\n" : "FAILED — value unchanged.\n";
