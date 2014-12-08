<?php 

$resource_id=7; // Website resource id


// call needed files
include "C:\wamp\www\SJR_1\keys.php";
include "C:\wamp\www\SJR_1\db_connect.php";
include "C:\wamp\www\SJR_1\db_classes\profile_db.php";
include "C:\wamp\www\SJR_1\db_classes\website_db.php";


// define needed classes
$profile_dbClass=new Profiles($con);
$website_trend_dbClass=new Trend_Website($con);
$getProfiles=$profile_dbClass->getProfiles();


// loop through each profile 

foreach ($getProfiles as $profile) {

	$id=$profile['VH_id'];
	$URL=file_get_contents("https://app.viralheat.com/social/api/monitoring/website/stats?days=1&profile_id=".$id."&api_key=".$api_key."&organization_id=".$organization_id);
	// create array to save the variables


	$array=array();

      // turn the result from JSON into array

	$decode=json_decode($URL,true);

	 // if there is result
    if ($decode['error']==null) {



		$video_stats=$decode['stats'];

		$total_sites= $video_stats['total_sites'];

		$average_sites= $video_stats['average_sites'];
		$total_sites_today= $video_stats['total_sites_today'];
		$top_country= $video_stats['top_country'];
		$total_country= $video_stats['total_country'];

		$top_domain= $video_stats['top_domain'];
		$top_domain_mentions= $video_stats['top_domain_mentions'];
		$top_domain_url= $video_stats['top_domain_url'];
		$top_link= $video_stats['top_link'];

		$top_link_url= $video_stats['top_link_url'];
		$top_link_mentions= $video_stats['top_link_mentions'];
		$period_high= $video_stats['period_high'];
		$period_low= $video_stats['period_low'];


            // save the variables into array

			$array=array('source_id'=>$resource_id,'profile_id'=>$id,'total'=>$total_sites,'average'=>$average_sites,
				'total_sites_today'=>$total_sites_today, 'top_country'=>$top_country, 'total_country'=>$total_country, 
				'top_domain'=>$top_domain,
				'top_domain_mentions'=>$top_domain_mentions,'top_domain_url'=>$top_domain_url,
				'top_link_url'=> $top_link_url,'top_link'=>$top_link,'top_link_mentions'=> $top_link_mentions,


				'period_high'=>$period_high,'period_low'=>$period_low);
			// insert the record
			$insertTrends=$website_trend_dbClass->insertStats($array);
	}
}





?>