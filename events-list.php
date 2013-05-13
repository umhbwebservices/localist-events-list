<?php
/*
 * UMHB Events List - Pulls list of events from Localist API and formats them to our liking
 * List of department IDs can be found at http://YOURLOCALISTPLATFORM/api/2/events/filters
 * 
 * Usage: events-feed.php?hide_minical=1&hide_building=1&show_description=1&department=20429&sort=date&show_num=10
 * All parameters optional
 *    - hide_minical - Set to 1 to hide the default minical that has the month on top and the day on bottom
 *    - hide_building - Set to 1 to hide the event building and room. Default is on
 *    - show_description - Set to 1 to show the description 
 *    - show_photo - Set to 1 to show the Localist event photo, not shown by default.
 *    - show_social - Set to 1 to show the social links (download event iCal, share to Twitter, and share to Facebook)
 *    - show_num - Set to an integer of the number of items you want shown. Default is 10.
 *    - department - Set to the ID of the department you seek, found at the URL above
 *    - sort - Set to date to sort results by date, otherwise they will be sorted by Localist trending data
 * 
 */
 
require 'functions.php';

// PHP 5.2 includes json_encode and json_decode
// Earlier versions of PHP will require jsonwrapper http://www.boutell.com/scripts/jsonwrapper.html
// require '/path/to/jsonwrapper/jsonwrapper.php';

// Config Variables
$localist_platform = "http://events.umhb.edu/"; //include trailing slash

// Process URL Parameters
$hide_minical = $_GET['hide_minical'];
$hide_building = $_GET['hide_building'];
$show_description = $_GET['show_description'];
$show_photo = $_GET['show_photo'];
$show_social = $_GET['show_social'];
$the_department = $_GET['department'];
$show_num = $_GET['show_num'];
$the_sort = $_GET['sort'];

// Default sort to ranking, unless specified
if ($the_sort != 'date') { $the_sort='ranking'; }

// Defuault number to 10, unless specified
if (is_numeric($show_num)) { $show_num=$show_num-1; } else { $show_num=9; }

if ($the_department) {
  $url=$localist_platform."api/2/events?sort=".$the_sort."&days=31&type[]=".$the_department;
}
else {
	$url=$localist_platform."api/2/events?sort=".$the_sort."&days=31";	
}


$localist_data = json_decode(do_post_request($url));

echo "<div id='event-container'>\n";

if ($localist_data->events[0]->event->title) {
	for ($i=0; $i<=$show_num; $i++)
	  {
	
		$the_title = $localist_data->events[$i]->event->title;
		if (!$the_title) { continue; }
		$the_description = $localist_data->events[$i]->event->description;
		$the_url = $localist_data->events[$i]->event->localist_url;
		$the_slug = $localist_data->events[$i]->event->urlname;
		$the_date = $localist_data->events[$i]->event->event_instances[0]->event_instance->start;
		$the_building = $localist_data->events[$i]->event->location;
		$the_room = $localist_data->events[$i]->event->room_number;
		$the_photo = str_replace('/huge/','/big/',$localist_data->events[$i]->event->photo_url);
		if ($the_room) { $the_room=" - ".$the_room; }
		$is_featured = $localist_data->events[$i]->event->featured;
		$extraclass=""; if ($is_featured == 1) { $extraclass .= " featured-event"; }
		
		echo "  <div class='item item-".$i.$extraclass."'>\n";
		if ($show_photo == 1) {
			echo "   <div class='eventphoto hidemobile'>\n";
			echo "      <a class='no-underline' href='".$the_url."'><img src='".$the_photo."' width='150' height='113' alt='".$the_title."' /></a>\n";
			echo "   </div>\n";
		}
		if ($hide_minical !=1 && $show_photo !=1) {
			echo "   <a href='".$the_url."'><div class='cal'>\n";
			echo "      <div class='month'>".date("M",strtotime($the_date))."</div>\n";
			echo "      <div class='day'>".date("j",strtotime($the_date))."</div>\n";
			echo "   </div></a>\n";
		}
		echo "   <div class='itembody'>\n";
		if ($show_social == 1) {
			echo "      <div class='sociallinks'>\n";
			echo "         <a class='sb min download no-underline' href='".$localist_platform."event/".$the_slug.".ics' title='Download Event to Your Calendar'><span>iCal</span></a>\n";
			echo "         <a class='sb min twitter no-underline newWindow' title='Tweet this Event' href='https://twitter.com/intent/tweet?original_referer=http://www.umhb.edu&amp;url=".$the_url."&amp;text=".urlencode("Check out ".$the_title." @umhb")."'><span>Tweet</span></a>\n";
			echo "         <a class='sb min facebook no-underline newWindow' title='Share this Event on Facebook' href='https://www.facebook.com/sharer/sharer.php?u=".$the_url."'><span>Share</span></a>\n";
			echo "      </div>\n";
		}
		echo "      <div class='eventtitle'><a class='no-underline' href='".$the_url."'>".$the_title."</a></div>\n";
		echo "      <div class='eventdatetime'><span class='eventdate'>".date("F j",strtotime($the_date))."</span>";
		if (date("Gi",strtotime($the_date)) != '000') { echo "<span class='eventtime'>".date(" - g:i a",strtotime($the_date))."</span>"; }
		echo "</div>\n";
		if ($the_building && $hide_building != 1) {
			echo "      <div class='eventlocation'>".$the_building.$the_room."</div>\n";
		}
		if ($the_description && $show_description == 1) {
		echo "   <div class='eventdescription'>".character_limiter($the_description,200)."</div>\n";
		}
		echo "   </div>\n";
		echo "  </div>\n";
	  }
}
else {
	echo "There are currently no events.";
}
echo "</div>\n";

if ($the_department) {
	echo "<div style='float:right;margin-bottom:20px;'><a href='".$localist_platform."calendar/month?event_types%5B%5D=".$the_department."' class='button-text-yellow'>View more events &raquo;</a></div>";
}

// Uncomment below to see the raw PHP output of your Localist return. We have it in an HTML comment here so it's viewable in the HTML source when uncommented.
// echo "<!--"; print_r($localist_data); echo "-->";

?>
