<?php
/**
 * Template Name: Calendar
 *
 * Calendar (Scheduled Training) — legacy body ported from the dev site.
 *
 * The parent theme's markup is styled by its stylesheet, which the child loads
 * scoped to `.legacy` (assets/css/legacy.css) so it cannot leak into the new
 * header/footer. Wrapped in `<div class="legacy">` for exactly that reason —
 * without the wrapper every rule in legacy.css misses (they are all
 * `.legacy …`-prefixed) and the calendar renders as an unstyled table.
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
			<div class="sub_crumb"><a href="/training/">TRAINING</a></div>
			<h1><?php the_title(); ?></h1>
		</div>
    </section>
	
    <section class="bkg_grey_shapes">
		<div class="row_970 clearfix centered">
		
			<? the_content(); ?>
		
		</div>
    </section>
	
	<section>
		<div class="row clearfix pb70">

<?		
date_default_timezone_set('America/New_York');

/* date settings */
$month = (int) ($_GET['mn'] ? $_GET['mn'] : date('m'));
$year = (int)  ($_GET['yr'] ? $_GET['yr'] : date('Y'));

			
/* "next month" control */
$next_month_link = '?mn='.($month != 12 ? $month + 1 : 1).'&yr='.($month != 12 ? $year : $year + 1);
$next_month_num = ($month != 12 ? $month + 1 : 1);
$dateObj_next   = DateTime::createFromFormat('!m', $next_month_num);
$next_month_name = $dateObj_next->format('F') . ' ';
//$next_month_name .= ($month != 12 ? $year : $year + 1);

/* "previous month" control */
$previous_month_link = '?mn='.($month != 1 ? $month - 1 : 12).'&yr='.($month != 1 ? $year : $year - 1);
$previous_month_num = ($month != 1 ? $month - 1 : 12);
$previous_year_num = ($month != 1 ? $year : $year - 1);
$dateObj_previous   = DateTime::createFromFormat('!m-Y', $previous_month_num.'-'.$previous_year_num);
$previous_month_name = $dateObj_previous->format('F') . ' ';
//$previous_month_name .= ($month != 1 ? $year : $year - 1);
$num_days_in_previous_month = $dateObj_previous->format('t');

//$dateObj   = DateTime::createFromFormat('!m', $month);
$dateObj   = DateTime::createFromFormat('!n-Y', $month.'-'.$year);

$monthName = $dateObj->format('F');
$last_day_in_current_month = $dateObj->format('t');

$current_month_year = $monthName." ".$year;

$first_timestamp = $year.'-'.$dateObj->format('m').'-01 00:00:00';
$first_timestamp_new = $first_timestamp;
$first_timestamp = strtotime($first_timestamp);
$last_timestamp = $year.'-'.$dateObj->format('m').'-'.$last_day_in_current_month.' 23:59:59';
$last_timestamp_new = $last_timestamp;
$last_timestamp = strtotime($last_timestamp);



// MAKE ARRAY OF SESSIONS

$tss = array();
$tssx = 0;

$args = array(
	'post_type' => 'training-session',
	'posts_per_page' => -1,
	'post__not_in' => array(344,575)
);
$uts = new WP_Query( $args );
if ( $uts->have_posts() ) : 
	while ( $uts->have_posts() ) :
	
		$uts->the_post(); // run the loop

		if( have_rows('training_session')) {
			while ( have_rows('training_session') ) : the_row();

				$tss[$tssx]['class'] = get_the_title();
				$tss[$tssx]['date_for_cal'] = get_sub_field('training_date', false);
				$tss[$tssx]['location'] = get_sub_field('training_location');
				$tss[$tssx]['time_range'] = get_sub_field('training_time_range');
				$tss[$tssx]['reg_link'] = get_the_permalink();
				
				$tssx = $tssx + 1;
				
			endwhile;
		}

	endwhile;
	wp_reset_postdata();
endif;

//print_r($tss);


	

/* draws a calendar */
function draw_calendar($month,$year){
	
	global $num_days_in_previous_month;
	global $tss;

	/* draw table */
	$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

	/* table headings */
	$headings = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();
	
	$start_with_day = $num_days_in_previous_month - ($running_day - 1);

	/* row for week one */
	$calendar.= '<tr class="calendar-row">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
		$bad_date = $start_with_day + $x;
		$calendar.= '<td class="calendar-day-np"> <div class="day-number-nope"> '.$bad_date.' </div> </td>';
		$days_in_this_week++;
	endfor;

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
	
		if ($list_day < 10) { $list_day_two = '0'.$list_day; } else { $list_day_two = $list_day; }
		if ($month < 10) { $month_two = '0'.$month; } else { $month_two = $month; }
		$date_to_compare = $year.$month_two.$list_day_two;
		$date_to_compare = (string)$date_to_compare;
		
		$whatnow = date('Ymd');
		
		//$class_on_date = array_search($date_to_compare, array_column($tss, 'date_for_cal'));
		$class_on_date = 0;
		$date_sessions = '';


		
		for ($i=0; $i < count($tss); $i++) {
			if ($date_to_compare == $tss[$i]['date_for_cal']) {
				$date_sessions .= '<div class="cal_ts">'.$tss[$i]['class'].'</div>';
				$date_sessions .= $tss[$i]['location'].'<br />';
				$date_sessions .= $tss[$i]['time_range'].'<br />';
				
				if ($tss[$i]['date_for_cal'] > $whatnow) {
				$date_sessions .= '<a href="'.$tss[$i]['reg_link'].'" class="cal_reg">REGISTER</a>';
				}
			
				$class_on_date = 1;
			}
		}
	
		$calendar.= '<td class="calendar-day';
		if ($class_on_date) { $calendar.= ' has_training '; }
			if ($list_day == date('j') && $month == date('m') && $year == date('Y')) { $calendar.= ' current-day '; }
			$calendar.= '">';

			/* add in the day number */
			$calendar.= '<div class="day-number">'.$list_day.'</div>';

			$calendar.= $date_sessions;

		$calendar.= '</td>';
		if($running_day == 6):
			$calendar.= '</tr>';
			if(($day_counter+1) != $days_in_month):
				$calendar.= '<tr class="calendar-row">';
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
		endif;
		$days_in_this_week++; $running_day++; $day_counter++;
	endfor;

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8 && $days_in_this_week!=1):
		for($x = 1; $x <= (8 - $days_in_this_week); $x++):
			$calendar.= '<td class="calendar-day-np"> <div class="day-number-nope">'.$x.'</div> </td>';
		endfor;
	endif;

	/* final row */
	$calendar.= '</tr>';

	/* end the table */
	$calendar.= '</table>';
	
	/* all done, return result */
	return $calendar;
}

/* draws a calendar */
function draw_calendar_mobile($month,$year){
	
	global $num_days_in_previous_month;
	global $tss;

	/* draw table */
	$calendar = '<table cellpadding="0" cellspacing="0" class="mobile-calendar">';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();
	
	$start_with_day = $num_days_in_previous_month - ($running_day - 1);

	/* row for week one */
	$calendar.= '<tr class="mobile-calendar-row">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
		$bad_date = $start_with_day + $x;
		$calendar.= '<td class="mobile-calendar-day-np"> <!--<div class="day-number-nope"> '.$bad_date.' </div>--> </td>';
		$days_in_this_week++;
	endfor;

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
	
		if ($list_day < 10) { $list_day_two = '0'.$list_day; } else { $list_day_two = $list_day; }
		if ($month < 10) { $month_two = '0'.$month; } else { $month_two = $month; }
		$date_to_compare = $year.$month_two.$list_day_two;
		$date_to_compare = (string)$date_to_compare;

		$class_on_date = 0;
		$date_sessions = '';
		
		for ($i=0; $i < count($tss); $i++) {
			if ($date_to_compare == $tss[$i]['date_for_cal']) {
				$class_on_date = 1;
			}
		}

		$calendar.= '<td class="mobile-calendar-day';
			if ($list_day == date('j') && $month == date('m') && $year == date('Y')) { $calendar.= ' current-day '; }
			
				if ($class_on_date) {$calendar.= ' perf_today '; }


				for ($i=0; $i < count($tss); $i++) {
					if ($date_to_compare == $tss[$i]['date_for_cal']) {
						$date_sessions .= '<div class="cal_ts">'.$tss[$i]['class'].'</div>';
						$date_sessions .= $tss[$i]['location'].'<br />';
						$date_sessions .= $tss[$i]['time_range'].'<br />';
						
						if ($tss[$i]['date_for_cal'] > $whatnow) {
						$date_sessions .= '<a href="'.$tss[$i]['reg_link'].'" class="cal_reg">REGISTER</a>';
						}
						
						$class_on_date = 1;
					}
				}

			$calendar.= '"';
			if ($class_on_date) { $calendar.= ' data-date="d'.$date_to_compare.'"'; }
			$calendar.= '>';

			/* add in the day number */
			$calendar.= '<div class="day-number">'.$list_day.'</div>';
	
			if ($date_sessions) {
				$this_days_events .= '<div style="display: none;" id="d'.$date_to_compare.'" class="mobile-event clearfix">';
				$this_days_events .= '<div class="perf_today_close"><img src="/wp-content/themes/gerotech/images/burger_exit.png" srcset="/wp-content/themes/gerotech/images/burger_exit.png 1x, /wp-content/themes/gerotech/images/burger_exit_2x.png 2x" /></div>';
				$this_days_events .= $date_sessions;
				$this_days_events .= '</div>';
			}
			

		$calendar.= '</td>';
		if($running_day == 6):
			$calendar.= '</tr>';
			if(($day_counter+1) != $days_in_month):
				$calendar.= '<tr class="mobile-calendar-row">';
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
		endif;
		$days_in_this_week++; $running_day++; $day_counter++;
	endfor;

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8 && $days_in_this_week!=1):
		for($x = 1; $x <= (8 - $days_in_this_week); $x++):
			$calendar.= '<td class="mobile-calendar-day-np"> <!--<div class="day-number-nope">'.$x.'</div>--> </td>';
		endfor;
	endif;

	/* final row */
	$calendar.= '</tr>';

	/* end the table */
	$calendar.= '</table>';
	
	$calendar.= $this_days_events;
	
	/* all done, return result */
	return $calendar;
}

echo '<div class="calendar_desktop hide_on_mobile">';
echo '<div class="calendar_month_year_and_nav">';
echo '<div class="cal_nav clearfix"><a href="'.$previous_month_link.'" style="margin-right:40px;" class="cal_btn_prev">'.$previous_month_name.'</a>';
echo '<a href="'.$next_month_link.'" class="cal_btn_next">'.$next_month_name.'</a></div>';
echo '<h1 class="pb40">'.$current_month_year.'</h1>';
echo '</div>';
echo draw_calendar($month,$year);
echo '</div>';

echo '<div class="calendar_mobile hide_on_desktop">';
echo '<div class="cal_nav clearfix">';
echo '<div style="float: right;"><a href="'.$next_month_link.'">'.$next_month_name.' <span style="font-size: 1.3em;">&raquo;</span></a></div>';
echo '<a href="'.$previous_month_link.'"><span style="font-size: 1.3em;">&laquo;</span> '.$previous_month_name.'</a>';
echo '</div>';
echo '<h1 class="pb40">'.$current_month_year.'</h1>';
echo draw_calendar_mobile($month,$year);
echo '</div>';

?>


		
		</div>
	</section>

</div><!-- /.legacy -->

<?php

		endwhile;
	endif;

	get_footer();
?>