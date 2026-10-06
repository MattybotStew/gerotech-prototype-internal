<?php
/**
 * Every external link opens in a new tab.
 *
 * The header, navigation, footer and search modal already do this through
 * gerotech_link_row(), but links rendered directly by page templates (hero and
 * CTA buttons, course flyers, job postings) and inside legacy post content did
 * not. Rather than remember a per-link toggle — or touch every template — one
 * pass over the finished front-end document adds `target="_blank"` and a
 * `rel="noopener noreferrer"` to any anchor that leaves the site.
 *
 * "Leaves the site" is decided by gerotech_is_external_url() (inc/global-content.php),
 * so `mailto:`, `tel:`, anchors and relative links are untouched, and
 * gerotech.com counts as this site on every environment.
 *
 * @package GerotechChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ensure a rel token list contains every required token.
 *
 * @param string $rel Existing rel attribute value.
 * @return string Space-separated token list.
 */
function gerotech_merge_rel_tokens( $rel ) {
	$tokens = preg_split( '/\s+/', trim( (string) $rel ), -1, PREG_SPLIT_NO_EMPTY );
	$tokens = array_merge( (array) $tokens, array( 'noopener', 'noreferrer' ) );

	return implode( ' ', array_unique( $tokens ) );
}

/**
 * Rewrite a single opening `<a …>` tag for an external URL.
 *
 * Fallback used only when WP_HTML_Tag_Processor is unavailable (pre-WP 6.2).
 *
 * @param string $tag Opening anchor tag.
 * @return string
 */
function gerotech_external_anchor_fallback( $tag ) {
	if ( ! preg_match( '/\shref\s*=\s*"([^"]*)"/i', $tag, $m ) ) {
		return $tag;
	}
	if ( ! gerotech_is_external_url( html_entity_decode( $m[1], ENT_QUOTES ) ) ) {
		return $tag;
	}

	if ( preg_match( '/\starget\s*=\s*"[^"]*"/i', $tag ) ) {
		$tag = preg_replace( '/\starget\s*=\s*"[^"]*"/i', ' target="_blank"', $tag );
	} else {
		$tag = preg_replace( '/>$/', ' target="_blank">', $tag );
	}

	if ( preg_match( '/\srel\s*=\s*"([^"]*)"/i', $tag, $rm ) ) {
		$rel = gerotech_merge_rel_tokens( $rm[1] );
		$tag = preg_replace( '/\srel\s*=\s*"[^"]*"/i', ' rel="' . $rel . '"', $tag );
	} else {
		$tag = preg_replace( '/>$/', ' rel="noopener noreferrer">', $tag );
	}

	return $tag;
}

/**
 * Add target/rel to external anchors in the finished front-end document.
 *
 * @param string $html Buffered response body.
 * @return string
 */
function gerotech_external_links_filter( $html ) {
	if ( '' === $html || false === stripos( $html, '<a ' ) ) {
		return $html;
	}

	// Only full HTML documents: skip feeds, sitemaps and JSON/XML responses.
	if ( false === stripos( $html, '<html' ) && false === stripos( $html, '<!doctype' ) ) {
		return $html;
	}

	if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return preg_replace_callback( '#<a\b[^>]*>#i', 'gerotech_external_anchor_fallback', $html );
	}

	$processor = new WP_HTML_Tag_Processor( $html );
	while ( $processor->next_tag( array( 'tag_name' => 'a' ) ) ) {
		$href = $processor->get_attribute( 'href' );
		if ( ! is_string( $href ) || ! gerotech_is_external_url( $href ) ) {
			continue;
		}

		$processor->set_attribute( 'target', '_blank' );

		$rel = $processor->get_attribute( 'rel' );
		$processor->set_attribute( 'rel', gerotech_merge_rel_tokens( is_string( $rel ) ? $rel : '' ) );
	}

	return $processor->get_updated_html();
}

/**
 * Start buffering the front-end response so anchors can be rewritten.
 *
 * Hooked late so early 301 redirects (which exit before any HTML exists) are
 * unaffected. WordPress flushes registered buffers on shutdown, which is when
 * the callback runs.
 */
function gerotech_start_external_links_buffer() {
	if ( is_admin() || is_feed() || is_robots() || is_trackback() || is_embed() ) {
		return;
	}

	ob_start( 'gerotech_external_links_filter' );
}
add_action( 'template_redirect', 'gerotech_start_external_links_buffer', 20 );
