<?php
/**
 * Round 2 of the Tristien Bridges Basecamp round (2026-10-07, Cursor pass):
 * push the images, gallery changes, engineering mailto and homepage CTA that are
 * in the prototype + theme defaults into the stored ACF rows.
 *
 * Stored ACF beats the template default, so every one of these needs a DB write
 * on both Local and Dev. Idempotent.
 *
 * Run (Local): wp eval-file scripts/apply-tristien-round2-2026-10.php
 * Run (Dev):   rsync to _gerotech-scripts/, `wp eval-file`, then delete.
 *
 * @package GerotechChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run this through WordPress.\n" );
	exit( 1 );
}
if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
	echo "ACF is not loaded — aborting.\n";
	return;
}

$uri = get_stylesheet_directory_uri();

/* ── Helpers ─────────────────────────────────────────────── */

function gx2_page_id( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? (int) $page->ID : 0;
}

function gx2_reset() {
	$store = acf_get_store( 'values' );
	if ( $store ) {
		$store->reset();
	}
}

function gx2_set( $post_id, $key, $value, $label ) {
	gx2_reset();
	$current = get_field( $key, $post_id );
	$same    = ( is_string( $current ) && is_string( $value ) )
		? ( trim( $current ) === trim( $value ) )
		: ( $current === $value );
	if ( $same ) {
		echo "  = {$label}: unchanged\n";
		return;
	}
	update_field( $key, $value, $post_id );
	echo "  ~ {$label}: updated\n";
}

/**
 * Point a service-card image at a bundled theme asset (imported to media).
 */
function gx2_set_card_image( $slug, $field, $title, $asset_rel ) {
	$post_id = gx2_page_id( $slug );
	if ( ! $post_id ) {
		echo "  ! page '{$slug}' not found\n";
		return;
	}
	$asset = get_theme_file_path( $asset_rel );
	if ( ! file_exists( $asset ) ) {
		echo "  ! missing theme asset {$asset_rel}\n";
		return;
	}

	$field_key = $field;
	if ( function_exists( 'acf_get_field' ) ) {
		$f = acf_get_field( $field );
		if ( $f && ! empty( $f['key'] ) ) {
			$field_key = $f['key'];
		}
	}

	gx2_reset();
	$rows = get_field( $field, $post_id );
	if ( ! is_array( $rows ) ) {
		echo "  ! no rows in {$field}\n";
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
		echo "  ! '{$title}' not in {$field}\n";
		return;
	}

	$current_id = isset( $rows[ $target ]['image'] ) ? $rows[ $target ]['image'] : 0;
	if ( is_array( $current_id ) && isset( $current_id['ID'] ) ) {
		$current_id = (int) $current_id['ID'];
	}
	$current_id = (int) $current_id;
	if ( $current_id ) {
		$attached = (string) get_post_meta( $current_id, '_wp_attached_file', true );
		if ( basename( $attached ) === basename( $asset_rel ) ) {
			printf( "  = %s / %s: already %s\n", $slug, $title, basename( $asset_rel ) );
			return;
		}
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$tmp = wp_tempnam( basename( $asset_rel ) );
	if ( ! $tmp || ! copy( $asset, $tmp ) ) {
		echo "  ! could not stage {$asset_rel}\n";
		return;
	}
	$new_id = media_handle_sideload(
		array( 'name' => basename( $asset_rel ), 'tmp_name' => $tmp ),
		$post_id,
		$title
	);
	if ( file_exists( $tmp ) ) {
		@unlink( $tmp );
	}
	if ( is_wp_error( $new_id ) ) {
		echo '  ! import failed: ' . $new_id->get_error_message() . "\n";
		return;
	}
	$rows[ $target ]['image'] = $new_id;
	update_field( $field_key, $rows, $post_id );
	printf( "  ~ %s / %s -> #%d (%s)\n", $slug, $title, $new_id, basename( $asset_rel ) );
}

/* ── 1. Card images ──────────────────────────────────────── */

echo "Card images\n";
gx2_set_card_image( 'modification-of-standard-machine-tools', 'mcs_cards', 'Custom Workholding', 'assets/images/custom-workholding-block.jpg' );
gx2_set_card_image( 'modification-of-standard-machine-tools', 'mcs_cards', 'Process Engineering', 'assets/images/process-engineering-workflow.jpg' );
gx2_set_card_image( 'unique-applications-for-standard-machines', 'app_cards', 'Process Troubleshooting', 'assets/images/lathe-turning.jpg' );
gx2_set_card_image( 'automated-system', 'ai_cards', 'Electrical – Controls Solutions', 'assets/images/pre-engineered-card.jpg' );
gx2_set_card_image( 'automated-system', 'ai_cards', 'Automation Cell Design', 'assets/images/automation-cell-controls.jpg' );
gx2_set_card_image( 'automated-system', 'ai_cards', 'Pre-Engineered Solutions', 'assets/images/human-robot-automation-interface.jpg' );

/* ── 2. Drop Layered Controls Solutions from ai_cards ────── */

echo "Automation cards\n";
$ai_id = gx2_page_id( 'automated-system' );
if ( $ai_id ) {
	gx2_reset();
	$ai_cards = get_field( 'field_ai_cards', $ai_id );
	if ( is_array( $ai_cards ) ) {
		$filtered = array_values(
			array_filter(
				$ai_cards,
				function ( $card ) {
					$title = strtolower( html_entity_decode( isset( $card['title'] ) ? $card['title'] : '', ENT_QUOTES, 'UTF-8' ) );
					return false === strpos( $title, 'layered' );
				}
			)
		);
		if ( count( $filtered ) !== count( $ai_cards ) ) {
			update_field( 'field_ai_cards', $filtered, $ai_id );
			echo "  ~ Layered Controls Solutions removed from ai_cards\n";
		} else {
			echo "  = Layered Controls Solutions: already absent\n";
		}
	}
}

/* ── 3. Footer CTA button URLs -> engineering mailto ─────── */

echo "Engineering mailto\n";
$engineering = function_exists( 'gerotech_engineering_mailto' )
	? gerotech_engineering_mailto()
	: 'mailto:Engineeringproposals@gerotech.com?subject=' . rawurlencode( 'Gerotech Quote Request' );

foreach ( array(
	'engineered-solutions'                  => 'field_es_cta_button_url',
	'modification-of-standard-machine-tools' => 'field_mcs_cta_button_url',
	'unique-applications-for-standard-machines' => 'field_app_cta_button_url',
	'automated-system'                      => 'field_ai_cta_button_url',
) as $slug => $key ) {
	$pid = gx2_page_id( $slug );
	if ( $pid ) {
		gx2_set( $pid, $key, $engineering, $slug );
	}
}

/* ── 4. Homepage CTA ─────────────────────────────────────── */

echo "Homepage CTA\n";
$home_id = (int) get_option( 'page_on_front' );
if ( $home_id ) {
	gx2_set( $home_id, 'field_home_cta_subhead', "Let's Talk Through It. Prefer Email?", 'subhead' );
	gx2_set( $home_id, 'field_home_cta_body', 'Tell us about your machine, part, process, and project goals, and include any drawings, photos, or specifications that may help. This will help our team come prepared to discuss your application.', 'body' );
	gx2_set( $home_id, 'field_home_cta_button_label', 'Talk to an Engineer', 'button label' );
	gx2_set( $home_id, 'field_home_cta_button_url', $engineering, 'button URL' );
	gx2_set( $home_id, 'field_home_cta_call_number', '', 'call number (removed)' );

	// Haas Automation lineup: Bar Feeders -> the lathes bar-feeders page.
	gx2_reset();
	$panels = get_field( 'field_home_lineup_panels', $home_id );
	if ( is_array( $panels ) ) {
		$changed = false;
		foreach ( $panels as $i => $panel ) {
			if ( isset( $panel['tab_label'] ) && 'Haas Automation' === $panel['tab_label'] && ! empty( $panel['tags'] ) ) {
				$new = str_replace(
					'https://www.haascnc.com/machines/automation-systems/automation-models.html#barfeeder',
					'https://haascnc.com/machines/lathes/bar-feeders',
					$panel['tags']
				);
				if ( $new !== $panel['tags'] ) {
					$panels[ $i ]['tags'] = $new;
					$changed = true;
				}
			}
		}
		if ( $changed ) {
			update_field( 'field_home_lineup_panels', $panels, $home_id );
			echo "  ~ Haas Automation Bar Feeders URL updated\n";
		} else {
			echo "  = Haas Automation Bar Feeders URL: unchanged\n";
		}
	}
}

/* ── 5. Galleries ────────────────────────────────────────── */

echo "Galleries\n";

// Applications: Process Troubleshooting collection -> lathe-turning.jpg.
$app_id = gx2_page_id( 'unique-applications-for-standard-machines' );
if ( $app_id ) {
	gx2_reset();
	$app_cols = get_field( 'field_app_collections', $app_id );
	if ( is_array( $app_cols ) ) {
		$changed = false;
		foreach ( $app_cols as $i => $col ) {
			if ( isset( $col['title'] ) && false !== stripos( $col['title'], 'troubleshooting' ) ) {
				$want = "image | {$uri}/assets/images/lathe-turning.jpg | | Coolant spray on a lathe turning a part | Process Troubleshooting · lathe turning";
				if ( trim( (string) $col['media'] ) !== $want ) {
					$app_cols[ $i ]['media'] = $want;
					$changed = true;
				}
			}
		}
		if ( $changed ) {
			update_field( 'field_app_collections', $app_cols, $app_id );
			echo "  ~ Applications gallery: Process Troubleshooting -> lathe-turning.jpg\n";
		} else {
			echo "  = Applications gallery: unchanged\n";
		}
	}
}

// Automation: replace the whole collection list with the template default so it
// matches the prototype exactly (Electrical first, new HMI set, updated covers).
if ( $ai_id ) {
	$ai_collections = array(
		array(
			'title' => 'Electrical – Controls Solutions',
			'meta'  => '',
			'media' => "image | {$uri}/assets/images/pre-engineered-card.jpg | | Open grey control cabinet with blue wiring, red terminals, and a VFD | Electrical – Controls Solutions · control cabinet",
		),
		array(
			'title' => 'HMI Design',
			'meta'  => '',
			'media' => implode( "\n", array(
				"image | {$uri}/assets/images/automation-gallery/hmi-edit-program.png | | HMI edit program screen | Edit Program Screen",
				"image | {$uri}/assets/images/automation-gallery/hmi-program-load.png | | HMI program load screen | Program Load Screen",
				"image | {$uri}/assets/images/automation-gallery/hmi-diag-safety-inputs.png | | HMI diagnostics safety inputs | Diag > Safety Inputs",
				"image | {$uri}/assets/images/automation-gallery/hmi-blowoff-1-main.png | | HMI BlowOff 1 main screen | BlowOff 1 Main",
				"image | {$uri}/assets/images/automation-gallery/hmi-outfeed-1-main.png | | HMI Outfeed 1 main screen | Outfeed 1 Main Screen",
				"image | {$uri}/assets/images/automation-gallery/hmi-robot-1-eoat.png | | HMI Robot 1 EOAT screen | Robot 1 EOAT",
				"image | {$uri}/assets/images/automation-gallery/hmi-mill-1-main.png | | HMI Mill 1 main screen | Mill 1 Main Screen",
				"image | {$uri}/assets/images/automation-gallery/hmi-blowoff-1-main-2.png | | HMI BlowOff 1 main screen, second view | BlowOff 1 Main",
				"image | {$uri}/assets/images/automation-gallery/hmi-operator-screen-1.png | | HMI operator screen 1 | Operator Screen 1",
				"image | {$uri}/assets/images/automation-gallery/hmi-hydraulic-valves.jpg | | HMI hydraulic valves split view | Hydraulic Valves",
				"image | {$uri}/assets/images/automation-gallery/hmi-autodoor.jpg | | HMI autodoor split view | Autodoor",
				"image | {$uri}/assets/images/automation-gallery/hmi-diag-enet-devices.png | | HMI diagnostics Ethernet devices | Diag > Enet Devices",
				"image | {$uri}/assets/images/automation-gallery/hmi-diag-all-io.png | | HMI diagnostics all I/O | Diag > All IO",
			) ),
		),
		array(
			'title' => 'Layered Controls Solutions',
			'meta'  => '',
			'media' => "image | {$uri}/assets/images/automation-gallery/layered-controls-diagram.jpg | | Layered controls diagram: machine tool, machine tool controls, and automation cells | Layer 01 machine tool · Layer 02 controls · Layer 03 cells",
		),
		array(
			'title' => 'Automation Cell Design',
			'meta'  => '',
			'media' => "image | {$uri}/assets/images/automation-cell-controls.jpg | | CAD render of a long automation cell with red enclosures, yellow guarding, and an overhead rail | Automation Cell Design · controls layout\n"
				. "image | {$uri}/assets/images/automation-gallery/automation-cell-lab.jpg | | Automation training lab with control cabinet, teach pendant, dual yellow robots on pedestals, EOAT tree, and CNC machines in the background | Training lab · dual robots · EOAT tree\n"
				. "image | {$uri}/assets/images/automation-gallery/automation-cell-guarded.jpg | | Guarded yellow robot cell with wire-mesh safety enclosure, vertical control cabinet with HMI, and floor controller | Guarded robot cell · control cabinet\n"
				. "image | {$uri}/assets/images/automation-gallery/automation-cell-vision.jpg | | Keyence overhead machine vision system with four green LED ring lights on a diamond mounting plate | Keyence vision · ring lights",
		),
		array(
			'title' => 'Robot EOAT – Ancillary Material Handling',
			'meta'  => '',
			'media' => "image | {$uri}/assets/images/robot-eoat.jpg | | Custom dual-gripper end-of-arm tooling | Custom end-of-arm tooling\n"
				. "image | {$uri}/assets/images/automation-gallery/eoat-vacuum-suction.jpg | | Vacuum suction end-of-arm tooling with orange cups on an aluminum frame | Vacuum EOAT · suction cups\n"
				. "image | {$uri}/assets/images/automation-gallery/eoat-gripper-pair.jpg | | Custom dual end-of-arm gripper tooling with pneumatic fittings on a workbench | Dual gripper EOAT",
		),
		array(
			'title' => 'Pre-Engineered Solutions',
			'meta'  => '',
			'media' => "image | {$uri}/assets/images/human-robot-automation-interface.jpg | | Yellow robot arm and a hand meeting under an Automation label | Pre-Engineered Solutions · human-robot interface",
		),
	);

	gx2_reset();
	$current = get_field( 'field_ai_collections', $ai_id );
	$current_titles = array();
	if ( is_array( $current ) ) {
		foreach ( $current as $c ) {
			$current_titles[] = isset( $c['title'] ) ? $c['title'] : '';
		}
	}
	$want_titles = array();
	foreach ( $ai_collections as $c ) {
		$want_titles[] = $c['title'];
	}
	if ( $current_titles === $want_titles ) {
		echo "  = Automation gallery: titles already match\n";
	} else {
		update_field( 'field_ai_collections', $ai_collections, $ai_id );
		echo "  ~ Automation gallery: rebuilt (Electrical first, new HMI set, new covers)\n";
	}
}

gx2_reset();
wp_cache_flush();
echo "\nDone.\n";
