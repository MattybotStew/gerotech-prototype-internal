<?php
/**
 * Replace a gallery collection's media with exactly the given lines.
 *
 * Stored repeater rows beat template defaults. add-app-gallery-items.php only
 * appends, so a one-photo swap needs this.
 *
 *   wp eval-file scripts/replace-gallery-media.php "<page slug>" "<repeater>" "<title>" "image | assets/images/file.jpg | | alt | caption"
 *
 * @package GerotechChild
 */

if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
	echo "ACF is not loaded — aborting.\n";
	return;
}

$args  = is_array( $args ) ? array_values( $args ) : array();
$slug  = isset( $args[0] ) ? trim( (string) $args[0] ) : '';
$field = isset( $args[1] ) ? trim( (string) $args[1] ) : '';
$title = isset( $args[2] ) ? trim( (string) $args[2] ) : '';
$lines = array_slice( $args, 3 );

if ( '' === $slug || '' === $field || '' === $title || ! $lines ) {
	echo "Usage: wp eval-file scripts/replace-gallery-media.php \"<slug>\" \"<repeater>\" \"<title>\" \"image | assets/... | | alt | caption\"\n";
	return;
}

$page = get_page_by_path( $slug );
if ( ! $page ) {
	echo "Page '{$slug}' not found.\n";
	return;
}
$post_id = (int) $page->ID;

$field_key = $field;
if ( function_exists( 'acf_get_field' ) ) {
	$f = acf_get_field( $field );
	if ( $f && ! empty( $f['key'] ) ) {
		$field_key = $f['key'];
	}
}

$rows = get_field( $field, $post_id );
if ( ! is_array( $rows ) ) {
	echo "No rows in '{$field}'.\n";
	return;
}

$target = null;
foreach ( $rows as $i => $row ) {
	if ( isset( $row['title'] ) && $title === $row['title'] ) {
		$target = $i;
		break;
	}
}
if ( null === $target ) {
	echo "No '{$title}' row.\n";
	return;
}

$out = array();
foreach ( $lines as $line ) {
	$line = trim( (string) $line );
	if ( '' === $line ) {
		continue;
	}
	if ( preg_match( '/^(image|video) \| assets\//', $line ) ) {
		$line = preg_replace( '/^(image|video) \| assets\//', '$1 | ' . GEROTECH_CHILD_URI . '/assets/', $line, 1 );
	}
	$out[] = $line;
}

$rows[ $target ]['media'] = implode( "\n", $out );
update_field( $field_key, $rows, $post_id );
echo "  '{$title}' media replaced (" . count( $out ) . " item(s)).\n";
