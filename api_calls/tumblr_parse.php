<?php 

$resource_id=5; // Tumblr resource id

// call needed files
include "C:\wamp\www\SJR_1\keys.php";
include "C:\wamp\www\SJR_1\db_connect.php";
include "C:\wamp\www\SJR_1\db_classes\profile_db.php";
include "C:\wamp\www\SJR_1\db_classes/tumblr_db.php";

// define needed classes
$profile_dbClass=new Profiles($con);
$tumblr_trend_dbClass=new Trend_Tumblr($con);
$getProfiles=$profile_dbClass->getProfiles();




// loop through each profile 

foreach ($getProfiles as $profile) {

	$id=$profile['VH_id'];
	$URL=file_get_contents("https://app.viralheat.com/social/api/monitoring/tumblr/stats?days=1&profile_id=".$id."&api_key=".$api_key."&organization_id=".$organization_id);
	// create array to save the variables
	$array=array();

      // turn the result from JSON into array

	$decode=json_decode($URL,true);

 // if there is result
    if ($decode['error']==null) {

		$tumblr_stats=$decode['stats'];


		$total_posts=$tumblr_stats    ['total_posts'];
		$average_posts=$tumblr_stats   ['average_posts'];
		$period_high=$tumblr_stats   ['period_high'];
		$period_low=$tumblr_stats   ['period_low'];

		$top_day=$tumblr_stats   ['top_day'];
		$positive_sentiment=$tumblr_stats   ['positive_sentiment'];
		$negative_sentiment=$tumblr_stats   ['negative_sentiment'];
		$neutral_sentiment=$tumblr_stats   ['neutral_sentiment'];


		$top_influencer_all=$tumblr_stats    ['top_influencer_all'];
		$top_influencer_by_answer=$tumblr_stats    ['top_influencer_by_answer'];
		$top_influencer_by_audio=$tumblr_stats    ['top_influencer_by_audio'];
		$top_influencer_by_chat=$tumblr_stats    ['top_influencer_by_chat'];


		$top_influencer_by_link=$tumblr_stats    ['top_influencer_by_link'];
		$top_influencer_by_photo=$tumblr_stats    ['top_influencer_by_photo'];
		$top_influencer_by_quote=$tumblr_stats    ['top_influencer_by_quote'];
		$top_influencer_by_text=$tumblr_stats    ['top_influencer_by_text'];
		$top_influencer_by_video=$tumblr_stats    ['top_influencer_by_video'];
		$top_hashtag=$tumblr_stats    ['top_hashtag'];
		
           // save the variables into array
				$array=array('source_id'=>$resource_id,'profile_id'=>$id,'total'=>$total_posts,'average'=>$average_posts,
				'max'=>$period_high,'min'=>$period_low, 'top_day'=>$top_day,'positive'=>$positive_sentiment, 'negative'=>$negative_sentiment	,'neutral'=>$neutral_sentiment, 'top_influencer_all'=>$top_influencer_all, 
				'top_influencer_by_answer'=>$top_influencer_by_answer, 'top_influencer_by_audio'=>$top_influencer_by_audio,'top_influencer_by_chat'=>$top_influencer_by_chat,
				'top_influencer_by_link'=>$top_influencer_by_link, 'top_influencer_by_photo'=>$top_influencer_by_photo, 'top_influencer_by_quote'=>$top_influencer_by_quote,
				'top_influencer_by_text'=>$top_influencer_by_text, 'top_influencer_by_video'=>$top_influencer_by_video, 'top_hashtag'=>$top_hashtag);

				// insert the record
		$insertTrends=$tumblr_trend_dbClass->insertStats($array);
	}
}

?>