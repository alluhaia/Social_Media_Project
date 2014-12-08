<?php 

$resource_id=6; // Video resource id

// call needed files
include "C:\wamp\www\SJR_1\keys.php";
include "C:\wamp\www\SJR_1\db_connect.php";
include "C:\wamp\www\SJR_1\db_classes\profile_db.php";
include "C:\wamp\www\SJR_1\db_classes/video_db.php";

// define needed classes
$profile_dbClass=new Profiles($con);
$video_trend_dbClass=new Trend_Video($con);
$getProfiles=$profile_dbClass->getProfiles();




// loop through each profile 

foreach ($getProfiles as $profile) {

	$id=$profile['VH_id'];
	$URL=file_get_contents("https://app.viralheat.com/social/api/monitoring/video/stats?days=1&profile_id=".$id."&api_key=".$api_key."&organization_id=".$organization_id);
	// create array to save the variables
	$array=array();

      // turn the result from JSON into array

	$decode=json_decode($URL,true);

 // if there is result
    if ($decode['error']==null) {


		$video_stats=$decode['stats'];


		$total_videos=$video_stats   ['videos'];
		$average_upload=$video_stats  ['average_upload'];
		$views=$video_stats  ['views'];
		$hottest_video=$video_stats ['hottest_video'];
		$hottest_video_url=$video_stats  ['hottest_video_url'];

		$hottest_video_views=$video_stats   ['hottest_video_views'];
		$videos_today=$video_stats   ['videos_today'];
		$percent_change=$video_stats   ['percent_change'];

		$top_day=$video_stats   ['top_day'];
		$period_high=$video_stats   ['period_high'];
		$period_low=$video_stats   ['period_low'];
		
            // save the variables into array

			$array=array('source_id'=>$resource_id,'profile_id'=>$id,'total'=>$total_videos,'average'=>$average_upload,
				'views'=>$views, 'hottest_video'=>$hottest_video, 'hottest_video_url'=>$hottest_video_url, 'hottest_video_views'=>$hottest_video_views,
				'videos_today'=>$videos_today, 'percent_change'=>$percent_change, 
				'top_day'=>$top_day,'period_high'=>$period_high,'period_low'=>$period_low);
			// insert the record
			$insertTrends=$video_trend_dbClass->insertStats($array);
	}
}


?>