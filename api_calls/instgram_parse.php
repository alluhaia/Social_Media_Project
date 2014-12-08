<?php 



$resource_id=4; // instagram resource id

// call needed files
include "C:\wamp\www\SJR_1\keys.php";
include "C:\wamp\www\SJR_1\db_connect.php";
include "C:\wamp\www\SJR_1\db_classes\profile_db.php";
include "C:\wamp\www\SJR_1\db_classes\instagram_db.php";

// define needed classes
$profile_dbClass=new Profiles($con);
$instagram_trend_dbClass=new Trend_Instagram($con);
$getProfiles=$profile_dbClass->getProfiles();




// loop through each profile 

foreach ($getProfiles as $profile) {

	$id=$profile['VH_id'];
	$URL=file_get_contents("https://app.viralheat.com/social/api/monitoring/instagram/stats?days=1&profile_id=".$id."&api_key=".$api_key."&organization_id=".$organization_id);
	// create array to save the variables
	$array=array();


      // turn the result from JSON into array

		$decode=json_decode($URL,true);

		 // if there is result
    if ($decode['error']==null) {


		$instgram_stats=$decode['stats'];


		$total_posts=$instgram_stats    ['total_posts'];
		$average_posts=$instgram_stats   ['average_posts'];
		$period_high=$instgram_stats   ['period_high'];
		$period_low=$instgram_stats   ['period_low'];

		$top_day=$instgram_stats   ['top_day'];
		$positive_sentiment=$instgram_stats   ['positive_sentiment'];
		$negative_sentiment=$instgram_stats   ['negative_sentiment'];
		$neutral_sentiment=$instgram_stats   ['neutral_sentiment'];


		$top_influencer_by_impact=$instgram_stats    ['top_influencer_by_impact'];
		$top_influencer_by_volume=$instgram_stats    ['top_influencer_by_volume'];

            // save the variables into array

		$array=array('source_id'=>$resource_id,'profile_id'=>$id,'total'=>$total_posts,'average'=>$average_posts,
			'max'=>$period_high, 'min'=>$period_low, 'top_day'=>$top_day, 'positive_sentiment'=>$positive_sentiment,
			 'negative_sentiment'=>$negative_sentiment,
			'neutral_sentiment'=>$neutral_sentiment,'top_influencer_by_impact'=>$top_influencer_by_impact
			,'top_influencer_by_volume'=>$top_influencer_by_volume);
		
		// insert the record
		$insertTrends=$instagram_trend_dbClass->insertStats($array);

		// // store into database

	}
}


?>