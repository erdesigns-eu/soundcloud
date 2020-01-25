<?php

/**********************************************************************************/
/*										  */
/*				Demo.php (Soundcloud class demo)  		  */
/*										  */
/*				Author	: Ernst Reidinga 			  */
/*				Date 	: 25/01/2020 20:00			  */
/*				Version	: 1.0					  */
/*										  */
/**********************************************************************************/

include_once 'soundcloud.php';
$soundcloud = new soundCloud('3UT1QkKC2kBqMLmSnbLbIps1suqeSlRs');

// Download mp3 from soundcloud url -> https://soundcloud.com/djpaulelstak/i-love-beatz-mixtape-3
if (isset($_GET['url'])) {
	$res = $soundcloud->filter_mp3_downloads([$soundcloud->convert_url(urldecode($_GET['url']))['data'][0]]);
	$soundcloud->download_mp3($res[0]);
}

// Search for keyword (artist, song, etc) - and list downloadable tracks (first 200), use search_next for the next 200 etc etc..
if (isset($_GET['q'])) {
	$res = $soundcloud->filter_mp3_downloads($soundcloud->search($_GET['q']));
	echo "Total found: ".$soundcloud->search_total."<br>";
	echo "Total Downloadable from first 200 results: ".count($res)."<br><br>";
	foreach ($res as $d) {
		echo "Filename: ".$d['filename']."<br>";
		echo "Download: ".$d['download']."<br><br>";
	}
}
