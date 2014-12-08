<?php 



$resource_id=3; // pinterest resource id

// call needed files
include "C:\wamp\www\SJR_1\keys.php";
include "C:\wamp\www\SJR_1\db_connect.php";
include "C:\wamp\www\SJR_1\db_classes\profile_db.php";
include "C:\wamp\www\SJR_1\db_classes\pinterest_db.php";

// define needed classes
$profile_dbClass=new Profiles($con);
$pinterest_trend_dbClass=new Trend_Pinterest($con);
$getProfiles=$profile_dbClass->getProfiles();




// loop through each profile 

foreach ($getProfiles as $profile) {

	$id=$profile['VH_id'];
	$URL=file_get_contents("https://app.viralheat.com/social/api/monitoring/pinterest/stats?days=1&profile_id=".$id."&api_key=".$api_key."&organization_id=".$organization_id);
	// create array to save the variables
	$array=array();


      // turn the result from JSON into array

		$decode=json_decode($URL,true);

		 // if there is result
    if ($decode['error']==null) {


		$pinterest_stats=$decode['stats'];



		$total_pins=$pinterest_stats ['total_pins'];
		$average_posts=$pinterest_stats ['avg_pins'];
		$total_repins=$pinterest_stats['total_repins'];
		$total_boards=$pinterest_stats['total_boards'];
		$total_likes=$pinterest_stats['total_likes'];
		$total_reach=$pinterest_stats['total_reach'];


		$total_unique_authors=$pinterest_stats ['unique_pinners'];
		$max_posts=$pinterest_stats ['period_high'];
		$min_posts=$pinterest_stats ['period_low'];


		$average_daily_pins=$pinterest_stats ['average_daily_pins'];
		$average_pins_per_pinner=$pinterest_stats ['average_pins_per_pinner'];
		$average_reach_per_pinner=$pinterest_stats ['average_reach_per_pinner'];

		$average_likes_per_pinner=$pinterest_stats ['average_likes_per_pinner'];
		$average_likes_per_pin=$pinterest_stats ['average_likes_per_pin'];
		$average_repins_per_pin=$pinterest_stats ['average_repins_per_pin'];




		// get Top influencers by impact & volume
		$influencers_stats=$decode['influencers'];

		$by_impact=$influencers_stats['by_impact'];

		$by_volume=$influencers_stats['by_volume'];

			foreach ($by_impact as $item) {
			   var_dump($item);
			// the influncer & number

			}

			foreach ($by_volume as $item) {
			   var_dump($item);
			// the influncer & number

			}



            // save the variables into array
			$array=array('source_id'=>$resource_id,'profile_id'=>$id,'total_pins'=>$total_pins,'avg_pins'=>$average_posts,
					'total_repins'=>$total_repins, 'total_boards'=>$total_boards, 'total_likes'=>$total_likes, 'total_reach'=>$total_reach,
					'max'=>$max_posts, 'min'=>$min_posts, 'unique_pinners'=>$total_unique_authors,
					'average_daily_pins'=>$average_daily_pins,'average_pins_per_pinner'=>$average_pins_per_pinner,'average_reach_per_pinner'=>$average_reach_per_pinner,
					'average_likes_per_pinner'=>$average_likes_per_pinner,
					'average_likes_per_pin'=>$average_likes_per_pin,'average_repins_per_pin'=>$average_repins_per_pin);
				
			// insert the record
				$insertTrends=$pinterest_trend_dbClass->insertStats($array);
	}
}



?>