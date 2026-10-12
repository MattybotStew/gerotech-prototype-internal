<?php
/**
 * Client review round — 2026-10-12.
 *
 * Three changes, all of them in STORED ACF (a theme push does not apply fields):
 *
 *   1. Applications › Process Troubleshooting — the body now reads
 *      "our talented Application Engineers are here to assist"
 *      (was "our talented team of Application Engineers is here to assist").
 *
 *   2. Automation › ai_cards — Layered Controls Solutions is RESTORED, directly
 *      after HMI Design. It was dropped on 2026-10-07 from the printed markup
 *      ("marked Remove"); the client asked for it back with its four "+" sections.
 *      The "Standardized Software Design Methodology" block, which had been moved
 *      onto Pre-Engineered Solutions when Layered was dropped, is removed from
 *      Pre-Engineered — it belongs to the Layered card (handoff/content-map.md row 34).
 *
 *   3. CTA destinations — the footer CTA bands keep Engineeringproposals@gerotech.com
 *      (those are the "prefer email?" CTAs). Every other Engineering-page CTA now
 *      points at the contact form instead of sales@:
 *        · ES hero / why / FANUC / capability buttons
 *        · the service-card pop-up button (Shared Content › card_cta_url)
 *        · the Engineered Solutions nav button (nav_es_cta_url)
 *
 * Idempotent — safe to re-run on any environment.
 *
 *   wp eval-file scripts/apply-client-review-2026-10-12.php
 *
 * ⚠ scripts/ is NOT part of a theme-only push. Copy it to the server first, e.g.
 *   rsync ... scripts/apply-client-review-2026-10-12.php gerotechdev@…:_gerotech-scripts/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Reset the ACF value store so a re-read reflects what we just wrote. */
function gcr_reset() {
	$store = acf_get_store( 'values' );
	if ( $store ) {
		$store->reset();
	}
}

/** Normalise a title for comparison: entities decoded, lowercased, trimmed. */
function gcr_norm( $s ) {
	return strtolower( trim( html_entity_decode( (string) $s, ENT_QUOTES, 'UTF-8' ) ) );
}

/** Page id by the real WP slug. */
function gcr_page_id( $slug ) {
	$q = get_page_by_path( $slug );
	return $q ? (int) $q->ID : 0;
}

$contact = '/contact/';

/* ── The canonical Layered Controls Solutions detail ───────────────────────
 * Recovered verbatim from git 033a91f. Four "+" sections, Layer 1 open. */
$layered_detail = '<p>Our Layered Controls approach organizes automation into three integrated levels, each building on the last to deliver a complete, coordinated manufacturing system.</p>'
	. '<details open><summary>Layer 1 — Machine Tool</summary><p>The OEM CNC control remains responsible for the machine\'s core manufacturing functions, including axis motion, spindle control, tool changes, and machining cycles. For applications requiring additional functionality, we specialize in implementing targeted enhancements to the existing control system, extending the machine\'s capabilities while preserving the OEM control architecture.</p></details>'
	. '<details><summary>Layer 2 — Machine Tool Automation</summary><p>Our Machine Automation package extends the capabilities of the CNC machine with features that are specific to your manufacturing process.</p><p><strong>Typical extended capabilities include:</strong></p><ul><li>Automatic door control</li><li>Part presence verification</li><li>Machine status monitoring</li><li>Custom I/O integration</li><li>Safety interfaces</li><li>Pneumatic and hydraulic systems</li><li>Coolant and chip management</li><li>Operator interfaces</li><li>Process-specific automation</li></ul><p><strong>Machine Tool Control Packages:</strong> Rather than designing every system from the ground up, we offer a family of pre-engineered automation solutions that can be configured to match your application\'s requirements. From cost-effective machine automation packages to fully featured control systems, each solution is designed to provide the right balance of functionality, performance, and investment.</p><p>Every platform is built on proven software, standardized engineering practices, and years of real-world manufacturing experience, allowing us to deliver custom solutions with reduced engineering time, lower project risk, and faster implementation.</p><ul><li>Reduced engineering time</li><li>Faster project delivery</li><li>Lower project risk</li><li>Proven, reliable software</li><li>Consistent operator experience</li><li>Flexible architecture that adapts to a wide range of machine types and applications</li><li>Simplified future enhancements and support</li></ul></details>'
	. '<details><summary>Layer 3 — Automation Cells</summary><p>The Cell Controller coordinates the entire manufacturing system by managing communication between machines, robots, conveyors, vision systems, and peripheral equipment.</p><p><strong>Responsibilities include:</strong></p><ul><li>Robot coordination</li><li>Part routing</li><li>Cell sequencing</li><li>Production scheduling</li><li>Vision integration</li><li>Data collection</li><li>Fault recovery</li><li>System diagnostics</li></ul></details>'
	. '<details><summary>Standardized Software Design Methodology</summary><p>Our automation solutions are developed using a standardized software design methodology that has been refined through years of real-world manufacturing applications. This proven approach provides a consistent programming structure, operator experience, and diagnostic philosophy across our automation platforms.</p><p>By developing from a common software foundation and adapting it to the selected control platform, we can deliver custom automation solutions more efficiently while maintaining proven functionality, consistent operation, and high-quality software.</p><p><strong>Key Benefits:</strong></p><ul><li>Proven software foundation</li><li>Standardized programming methodology</li><li>Consistent HMI navigation and operator experience</li><li>Common alarms, diagnostics, and fault recovery</li><li>Faster project development</li><li>Reduced project risk</li><li>Simplified troubleshooting and maintenance</li><li>Easier operator training</li><li>Flexible deployment across multiple control platforms</li><li>Scalable design for future expansion</li></ul></details>';

/* ── Card order for the Automation grid ──────────────────────────────────── */
$card_order = array( 'electrical', 'hmi design', 'layered', 'automation cell', 'robot eoat', 'pre-engineered' );
$rank = function ( $card ) use ( $card_order ) {
	$title = gcr_norm( isset( $card['title'] ) ? $card['title'] : '' );
	foreach ( $card_order as $i => $needle ) {
		if ( false !== strpos( $title, $needle ) ) {
			return $i;
		}
	}
	return PHP_INT_MAX;
};

echo "Client review 2026-10-12\n";

/* ─── 1. Applications › Process Troubleshooting wording ─────────────────── */
echo "\n1. Process Troubleshooting wording\n";
$app_id = gcr_page_id( 'unique-applications-for-standard-machines' );
if ( $app_id ) {
	gcr_reset();
	$cards = get_field( 'field_app_cards', $app_id );
	if ( is_array( $cards ) ) {
		$hit = false;
		foreach ( $cards as $i => $card ) {
			if ( 'process troubleshooting' !== gcr_norm( isset( $card['title'] ) ? $card['title'] : '' ) ) {
				continue;
			}
			$old = "our talented team of Application Engineers is here to assist";
			$new = "our talented Application Engineers are here to assist";
			$detail = isset( $card['detail'] ) ? (string) $card['detail'] : '';
			if ( false !== strpos( $detail, $new ) ) {
				echo "  = already reworded\n";
				$hit = true;
				break;
			}
			if ( false === strpos( $detail, $old ) ) {
				echo "  ! expected phrase not found — left alone\n";
				$hit = true;
				break;
			}
			$cards[ $i ]['detail'] = str_replace( $old, $new, $detail );
			update_field( 'field_app_cards', $cards, $app_id );
			echo "  ~ reworded\n";
			$hit = true;
			break;
		}
		if ( ! $hit ) {
			echo "  ! Process Troubleshooting row not found\n";
		}
	} else {
		echo "  ! app_cards empty\n";
	}
} else {
	echo "  ! Applications page not found\n";
}

/* ─── 2. Automation › ai_cards: restore Layered, clean Pre-Engineered ───── */
echo "\n2. Automation service cards\n";
$ai_id = gcr_page_id( 'automated-system' );
if ( $ai_id ) {
	gcr_reset();
	$cards = get_field( 'field_ai_cards', $ai_id );
	if ( is_array( $cards ) && $cards ) {
		$changed = false;

		// 2a. Layered — insert (or repair) the row.
		$layered_at = null;
		foreach ( $cards as $i => $card ) {
			if ( false !== strpos( gcr_norm( isset( $card['title'] ) ? $card['title'] : '' ), 'layered' ) ) {
				$layered_at = $i;
				break;
			}
		}
		$layered_row = array(
			'title'  => 'Layered Controls Solutions',
			'image'  => 'assets/images/layered-controls.jpg',
			'detail' => $layered_detail,
		);
		if ( null === $layered_at ) {
			// Insert immediately after HMI Design; fall back to just after the leader.
			$insert_at = 1;
			foreach ( $cards as $i => $card ) {
				if ( false !== strpos( gcr_norm( isset( $card['title'] ) ? $card['title'] : '' ), 'hmi design' ) ) {
					$insert_at = $i + 1;
					break;
				}
			}
			array_splice( $cards, $insert_at, 0, array( $layered_row ) );
			$changed = true;
			echo "  ~ Layered Controls Solutions inserted at position " . ( $insert_at + 1 ) . "\n";
		} else {
			// Present already: only repair blanks, never clobber hand edits.
			if ( empty( $cards[ $layered_at ]['image'] ) ) {
				$cards[ $layered_at ]['image'] = $layered_row['image'];
				$changed = true;
			}
			if ( empty( $cards[ $layered_at ]['detail'] ) ) {
				$cards[ $layered_at ]['detail'] = $layered_row['detail'];
				$changed = true;
			}
			echo "  = Layered Controls Solutions already present\n";
		}

		// 2b. Pre-Engineered — drop the Standardized Software block, now that Layered owns it.
		foreach ( $cards as $i => $card ) {
			if ( false === strpos( gcr_norm( isset( $card['title'] ) ? $card['title'] : '' ), 'pre-engineered' ) ) {
				continue;
			}
			$detail = isset( $card['detail'] ) ? (string) $card['detail'] : '';
			$stripped = preg_replace(
				'/<details>\s*<summary>Standardized Software Design Methodology<\/summary>.*?<\/details>/s',
				'',
				$detail
			);
			if ( $stripped !== $detail ) {
				$cards[ $i ]['detail'] = $stripped;
				$changed = true;
				echo "  ~ removed the duplicated Standardized Software block from Pre-Engineered Solutions\n";
			} else {
				echo "  = Pre-Engineered Solutions: no duplicate block\n";
			}
			break;
		}

		// 2c. Enforce the card order.
		$before = array();
		foreach ( $cards as $card ) {
			$before[] = isset( $card['title'] ) ? $card['title'] : '?';
		}
		usort( $cards, function ( $a, $b ) use ( $rank ) {
			return $rank( $a ) <=> $rank( $b );
		} );
		$after = array();
		foreach ( $cards as $card ) {
			$after[] = isset( $card['title'] ) ? $card['title'] : '?';
		}
		if ( $before !== $after ) {
			$changed = true;
		}

		if ( $changed ) {
			update_field( 'field_ai_cards', $cards, $ai_id );
		}
		echo "  · order: " . implode( ' | ', $after ) . "\n";
	} else {
		echo "  ! ai_cards empty\n";
	}
} else {
	echo "  ! Automation page not found\n";
}

/* ─── 3. CTA destinations ───────────────────────────────────────────────── */
echo "\n3. CTA destinations -> {$contact}\n";

// Service-card pop-up button (Shared Content).
$card_cta = get_option( 'options_card_cta_url' );
if ( $contact === $card_cta ) {
	echo "  = card_cta_url already {$contact}\n";
} else {
	update_field( 'field_card_cta_url', $contact, 'option' );
	echo "  ~ card_cta_url -> {$contact}\n";
}

// Engineered Solutions nav button.
$nav = get_option( 'options_nav_es_cta_url' );
if ( is_array( $nav ) && isset( $nav['url'] ) && $contact === $nav['url'] ) {
	echo "  = nav_es_cta_url already {$contact}\n";
} else {
	update_field( 'field_nav_es_cta_url', array( 'url' => $contact, 'title' => '', 'target' => '' ), 'option' );
	echo "  ~ nav_es_cta_url -> {$contact}\n";
}

// ES page buttons (footer band es_cta_button_url is deliberately left as the engineering mailto).
$es_id = gcr_page_id( 'engineered-solutions' );
if ( $es_id ) {
	$fields = array(
		'field_es_hero_cta1_url'  => array( 'es_hero_cta1_url', 'hero' ),
		'field_es_why_cta_url'    => array( 'es_why_cta_url', 'why section' ),
		'field_es_fanuc_cta1_url' => array( 'es_fanuc_cta1_url', 'FANUC' ),
		'field_es_cap_cta1_url'   => array( 'es_cap_cta1_url', 'capability band' ),
	);
	gcr_reset();
	foreach ( $fields as $key => $meta ) {
		list( $name, $label ) = $meta;
		$current = get_field( $key, $es_id );
		if ( $contact === $current ) {
			echo "  = {$label} ({$name}) already {$contact}\n";
			continue;
		}
		update_field( $key, $contact, $es_id );
		echo "  ~ {$label} ({$name}) -> {$contact}\n";
	}
} else {
	echo "  ! Engineered Solutions page not found\n";
}

/* ─── Verify by reading back through a reset store ──────────────────────── */
echo "\nVerification (ACF store reset before each read)\n";

gcr_reset();
$ai = get_field( 'field_ai_cards', $ai_id );
$titles = array();
foreach ( (array) $ai as $card ) {
	$titles[] = isset( $card['title'] ) ? $card['title'] : '?';
}
echo "  ai_cards: " . count( $titles ) . " rows\n";
echo "    " . implode( "\n    ", $titles ) . "\n";
$layered_ok = false;
$preeng_ok  = true;
foreach ( (array) $ai as $card ) {
	$t = gcr_norm( isset( $card['title'] ) ? $card['title'] : '' );
	if ( false !== strpos( $t, 'layered' ) ) {
		$layered_ok = 4 === substr_count( (string) $card['detail'], '<details' );
	}
	if ( false !== strpos( $t, 'pre-engineered' ) ) {
		$preeng_ok = false === strpos( (string) $card['detail'], 'Standardized Software' );
	}
}
echo "  Layered has 4 drawers: " . ( $layered_ok ? 'yes' : 'NO' ) . "\n";
echo "  Pre-Engineered free of the duplicate block: " . ( $preeng_ok ? 'yes' : 'NO' ) . "\n";

gcr_reset();
$app = get_field( 'field_app_cards', $app_id );
$wording_ok = false;
foreach ( (array) $app as $card ) {
	if ( 'process troubleshooting' === gcr_norm( isset( $card['title'] ) ? $card['title'] : '' ) ) {
		$d = (string) $card['detail'];
		$wording_ok = false !== strpos( $d, 'Application Engineers are here to assist' )
			&& false === strpos( $d, 'team of Application Engineers is here' );
	}
}
echo "  Process Troubleshooting reworded: " . ( $wording_ok ? 'yes' : 'NO' ) . "\n";

gcr_reset();
echo "  card_cta_url  = " . get_field( 'field_card_cta_url', 'option' ) . "\n";
gcr_reset();
$nav_read = get_field( 'field_nav_es_cta_url', 'option' );
echo "  nav_es_cta_url = " . ( is_array( $nav_read ) && isset( $nav_read['url'] ) ? $nav_read['url'] : '(empty)' ) . "\n";
if ( $es_id ) {
	foreach ( array( 'field_es_hero_cta1_url', 'field_es_why_cta_url', 'field_es_fanuc_cta1_url', 'field_es_cap_cta1_url', 'field_es_cta_button_url' ) as $key ) {
		gcr_reset();
		echo "  {$key} = " . get_field( $key, $es_id ) . "\n";
	}
}

echo "\nDone. Reminder: run scripts/update-es-nav-order.php so the stored nav picks up\n";
echo "the restored Layered Controls Solutions link.\n";
