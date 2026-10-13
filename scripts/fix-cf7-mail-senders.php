<?php
/**
 * Strip the WP Engine hostname out of Contact Form 7 *senders*.
 *
 * A domain search-replace left every form's `sender` as
 * `noreply@gerotechdev.wpenginepowered.com` — not a mailbox, and a domain the
 * form does not own, so SPF/DKIM fail and mail is junked. The recipients are
 * deliberately NOT touched here: the client still has to supply the real
 * mailboxes, and the audit below prints them so the list stays visible.
 *
 * Only the `sender` key of `_mail`, `_mail_2`, `_mail_3` … is rewritten, and
 * only when it actually contains a `*.wpenginepowered.com` host. Re-running
 * changes nothing.
 *
 *   wp eval-file scripts/fix-cf7-mail-senders.php
 *
 * ⚠ scripts/ is NOT part of a theme-only push. Copy it to the server first, e.g.
 *   rsync ... scripts/fix-cf7-mail-senders.php gerotechdev@…:_gerotech-scripts/
 *
 * @package GerotechChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$replacement = 'noreply@gerotech.com';

$forms = get_posts(
	array(
		'post_type'      => 'wpcf7_contact_form',
		'posts_per_page' => -1,
		'post_status'    => 'any',
		'orderby'        => 'ID',
		'order'          => 'ASC',
	)
);

$fixed    = 0;
$pending  = array();

foreach ( $forms as $form ) {
	$changed_here = false;

	foreach ( array( '_mail', '_mail_2', '_mail_3' ) as $meta_key ) {
		$mail = get_post_meta( $form->ID, $meta_key, true );

		if ( ! is_array( $mail ) || empty( $mail['sender'] ) ) {
			continue;
		}

		if ( false === strpos( (string) $mail['sender'], 'wpenginepowered.com' ) ) {
			continue;
		}

		$mail['sender'] = $replacement;
		update_post_meta( $form->ID, $meta_key, $mail );
		$changed_here   = true;
	}

	if ( $changed_here ) {
		$fixed++;
		echo "  sender fixed : {$form->ID}  {$form->post_title}\n";
	}

	// Report only — never written.
	$mail = get_post_meta( $form->ID, '_mail', true );
	if ( is_array( $mail ) && ! empty( $mail['recipient'] ) && false !== strpos( (string) $mail['recipient'], 'wpenginepowered.com' ) ) {
		$pending[] = sprintf( '%-5s %-34s %s', $form->ID, $form->post_title, $mail['recipient'] );
	}
}

echo "\nSenders fixed: {$fixed}\n";

echo "\nRecipients still awaiting a real mailbox from the client (" . count( $pending ) . "):\n";
foreach ( $pending as $row ) {
	echo '  ' . $row . "\n";
}

echo "\nDone.\n";
