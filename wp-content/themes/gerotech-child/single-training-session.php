<?php
/**
 * Single Training Session (`/training/<course>/`) — legacy body ported from the
 * dev site. `custom-classes` and `advanced-training` take the request-form
 * branch; every other course page renders the Haas/Other registration forms.
 *
 * Wrapped in `<div class="legacy">` so the parent theme's stylesheet (loaded by
 * the child as assets/css/legacy.css, scoped to `.legacy`) applies to this
 * markup and nowhere else. The class-date boxes (`#classes_fr` / `#classes_gr`
 * / `#classes_mcc`) are filled by the parent's `ts_script.js`, which the child
 * keeps enqueued for this post type via `gerotech_child_enqueue_assets()`.
 *
 * @package GerotechChild
 */

	get_header('general');
	
	if ( have_posts() ):
		while ( have_posts() ): the_post();
			$slug = get_post( $post )->post_name;
?>

<div class="legacy">

	<section id="page_title">
		<div class="row clearfix">
		
			<div class="calendar_button"><a href="/scheduled-training/"><img src="/wp-content/themes/gerotech/images/b_calendar.png" alt="View Training Calendar" /></a></div>
		
			<div class="sub_crumb"><a href="/training/">TRAINING</a></div>
			<h1><?php the_title(); ?></h1>
			
		</div>
    </section>
	
	<?php if ($slug != 'custom-classes' && $slug != 'advanced-training') { ?>
	
	
	
		
		<?php

			if( have_rows('training_session') ):
				while ( have_rows('training_session') ) : the_row();
					if (get_sub_field('training_location') == "Flat Rock") {

						$datey = get_sub_field('training_date', false, false);
						$nowey = date('Ymd');
						$nowey_less_three = $nowey - 3;
					
						if ($datey > $nowey_less_three) {
							$fr = $fr . '<div class="choose_datetime"><input type="radio" name="date_time" class="date_time" value="'. get_the_title() .' - ' . get_sub_field('training_location') . ' - '. get_sub_field('training_dates') . ', ' . get_sub_field('training_time_range') . '" />' . get_sub_field('training_dates') . ', ' . get_sub_field('training_time_range') . '</div>';
						}
						
					}
				endwhile;
			endif;

			if( have_rows('training_session') ):
				while ( have_rows('training_session') ) : the_row();
					if (get_sub_field('training_location') == "Grand Rapids") {
		
						$datey = get_sub_field('training_date', false, false);
						$nowey = date('Ymd');
						$nowey_less_three = $nowey - 3;
						
						if ($datey > $nowey_less_three) {
							$gr = $gr . '<div class="choose_datetime"><input type="radio" name="date_time" class="date_time" value="'. get_the_title() .' - ' . get_sub_field('training_location') . ' - '. get_sub_field('training_dates') . ', ' . get_sub_field('training_time_range') . '" />' . get_sub_field('training_dates') . ', ' . get_sub_field('training_time_range') . '</div>';
						}
					}
				endwhile;
			endif;

			if( have_rows('training_session') ):
				while ( have_rows('training_session') ) : the_row();
					if (get_sub_field('training_location') == "Macomb CC") {
		
						$datey = get_sub_field('training_date', false, false);
						$nowey = date('Ymd');
						$nowey_less_three = $nowey - 3;
						
						if ($datey > $nowey_less_three) {
							$mcc = $mcc . '<div class="choose_datetime"><input type="radio" name="date_time" class="date_time" value="'. get_the_title() .' - ' . get_sub_field('training_location') . ' - '. get_sub_field('training_dates') . ', ' . get_sub_field('training_time_range') . '" />' . get_sub_field('training_dates') . ', ' . get_sub_field('training_time_range') . '</div>';
						}
					}
				endwhile;
			endif;

			if (!$fr) { $fr = "None at this time."; }
			if (!$gr) { $gr = "None at this time."; }
			if (!$mcc) { $mcc = "None at this time."; }

			// echo $datey . ' - ' . $nowey_less_three;

		?>

		<script>
			var fr_classes = '<?php echo $fr; ?>';
			var gr_classes = '<?php echo $gr; ?>';
			var mcc_classes = '<?php echo $mcc; ?>';
		</script>

		<section class="bkg_grey_shapes">
			<div class="row_970 clearfix">

				<?php the_field('training_intro'); ?>

				<!--
				<div class="training_options clearfix">
					<a class="to to_active" id="to_haas">HAAS</a>
					<a class="to to_not_active" id="to_other">ALL OTHERS</a>
				</div>
				-->
				
			</div>
		</section>

		<section class="bkg_white">
			<div class="row_970 clearfix">
			
				<div id="tf_haas" class="clearfix" >

					<h2>Haas Specific Training</h2>
					
					<?php if (get_field('training_outline')) { ?>
						<a href="<?php the_field('training_outline'); ?>" class="download_outline" target="_blank" onClick="ga('send', 'event', 'Download', '<?php the_title(); ?>', 'Download the Course Outline');">Download the Course Outline.</a>
						<div class="orange_divider"></div>
					<?php } ?>
					
					<?php the_field('haas_general_registration_text', 'options'); ?>

					<?php echo do_shortcode('[contact-form-7 id="333" title="Haas Training"]'); ?>

				</div>
				
				<div id="tf_other" class="clearfix" style="display: none;">

					<h2>Other Training</h2>
					
					<?php the_field('other_general_registration_text', 'options'); ?>
					
					<?php echo do_shortcode('[contact-form-7 id="334" title="Other Training"]'); ?>
				
				</div>
				
			</div>
		</section>

	<?php } ?>
	
	
	
	
	
	
	
	
	<?php if ($slug == 'custom-classes') { ?>

		<section class="bkg_grey_shapes">
			<div class="row_970 clearfix">

				<?php the_field('training_intro'); ?>
				
			</div>
		</section>
		
		<section class="bkg_white">
			<div class="row_970 clearfix" id="tf_other">
			
				<?php echo str_replace('Registration', 'Custom Training Request', get_field('other_general_registration_text', 'options')); ?>
					
				<?php echo do_shortcode('[contact-form-7 id="334" title="Other Training"]'); ?>
			
			</div>
		</section>
	
	<?php } ?>
	
	
	
	
	
	<?php if ($slug == 'advanced-training') { ?>

		<section class="bkg_grey_shapes">
			<div class="row_970 clearfix">

				<?php the_field('training_intro'); ?>
				
			</div>
		</section>
		
		<section class="bkg_white">
			<div class="row_970 clearfix" id="tf_other">
			
				<?php echo str_replace('Registration', 'Advanced Training Request', get_field('other_general_registration_text', 'options')); ?>
					
				<?php echo do_shortcode('[contact-form-7 id="577" title="Advanced Training"]'); ?>
			
			</div>
		</section>
	
	<?php } ?>
	
	
	
	

</div><!-- /.legacy -->

<?php
		endwhile;
	endif;

	get_footer();
?>