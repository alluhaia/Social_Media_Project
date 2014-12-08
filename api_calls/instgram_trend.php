<?php 

$resource_id=4; // facebook resource id

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
	$URL=file_get_contents("https://app.viralheat.com/social/api/monitoring/instagram/trend?days=1&profile_id=".$id."&api_key=".$api_key."&organization_id=".$organization_id);
	// create array to save the variables
	$trend=array();
	$sentiment=array();

	// $location_sentiment=strpos($URL, "sentiment_trend");

	// $imped_comma=substr($URL, 0,$location_sentiment-1).",".substr($URL, $location_sentiment-1);

	$decode=json_decode($URL,true);


	if ($decode['error']==null) {



		$facebook_trend=$decode['trend'];


		$array_keys=array_keys($facebook_trend);
		$count=count($facebook_trend);
		$i=0;
		while ($i<$count) {

			
			// get the date
			$date=$array_keys[$i];
			$post_content=$facebook_trend[$date];
			$datetime=$date;
			$post_count=$post_content['post_count'];
			$trend[]=array('source_id'=>$resource_id, 'profile_id'=>$id,'date'=>$datetime,'count'=>$post_count);
			$i++;

		}

		// get the sentoment trend
		$facebook_trend=$decode['sentiment_trend'];


		$array_keys=array_keys($facebook_trend);
		$count=count($facebook_trend);
		$i=0;
		while ($i<$count) {

			
			// get the date
			$date=$array_keys[$i];
			$post_content=$facebook_trend[$date];
			//$datetime=$post_content['date'];
			$positive=$post_content['positive'];
			$negative=$post_content['negative'];
			$neutral=$post_content['neutral'];
			$sentiment[]=array('source_id'=>$resource_id, 'profile_id'=>$id, 'date'=>$date,'positive'=>$positive,'negative'=>$negative, 'neutral'=>$neutral );
			$i++;

		}	




 		// insert the record
 		$insertTrends=$instagram_trend_dbClass->insertTrend($trend,$sentiment);


	}




}





?>