localist-events-list
====================
<pre>PHP implementation of Localist API to display event list on our site

 UMHB Events List - Pulls list of events from Localist API and formats them to our liking
 List of department IDs can be found at http://YOURLOCALISTPLATFORM/api/2/events/filters
 
 Usage: events-feed.php?hide_minical=1&hide_building=1&show_description=1&department=20429&sort=date&show_num=10
 All parameters optional
    - hide_minical - Set to 1 to hide the default minical that has the month on top and the day on bottom
    - hide_building - Set to 1 to hide the event building and room. Default is on
    - show_description - Set to 1 to show the description 
    - show_photo - Set to 1 to show the Localist event photo, not shown by default.
    - show_social - Set to 1 to show the social links (download event iCal, share to Twitter, and share to Facebook)
    - show_num - Set to an integer of the number of items you want shown. Default is 10.
    - department - Set to the ID of the department you seek, found at the URL above
    - sort - Set to date to sort results by date, otherwise they will be sorted by Localist trending data
</pre>
