<?php 

$resource_id=2; // Twitter resource id


// call needed files
include "C:\wamp\www\SJR_1\keys.php";
include "C:\wamp\www\SJR_1\db_connect.php";
include "C:\wamp\www\SJR_1\db_classes\profile_db.php";
include "C:\wamp\www\SJR_1\db_classes/twitter_db.php";

// define needed classes
$profile_dbClass=new Profiles($con);
$twitter_trend_dbClass=new Trend_Twitter($con);
$getProfiles=$profile_dbClass->getProfiles();




// loop through each profile 

foreach ($getProfiles as $profile) {

	$id=$profile['VH_id'];
	$URL=file_get_contents("https://app.viralheat.com/social/api/monitoring/twitter/stats?days=1&profile_id=".$id."&api_key=".$api_key."&organization_id=".$organization_id);
	// create array to save the variables
	$array=array();


      // turn the result from JSON into array

	$decode=json_decode($URL,true);

	 // if there is result
    if ($decode['error']==null) {


		$twitter_stats=$decode['stats'];


		$total_tweets=$twitter_stats    ['total_tweets'];
		$average_tweets=$twitter_stats   ['average_tweets'];
		$active_day=$twitter_stats   ['active_day'];
		$total_unique_authors=$twitter_stats   ['total_unique_authors'];


		$popular_author=$twitter_stats   ['popular_author'];
		$positive_sentiment=$twitter_stats   ['positive_sentiment'];
		$negative_sentiment=$twitter_stats   ['negative_sentiment'];
		$neutral_sentiment=$twitter_stats   ['neutral_sentiment'];


		$retweets=$twitter_stats    ['retweets'];
		$percent_retweets=$twitter_stats    ['percent_retweets'];
		$total_urls=$twitter_stats    ['total_urls'];
		$percent_urls=$twitter_stats    ['percent_urls'];


		$top_language=$twitter_stats    ['top_language'];
		$profile_twitter_attention=$twitter_stats    ['profile_twitter_attention'];
		$top_authority=$twitter_stats    ['top_authority'];
		$top_authority_followers=$twitter_stats    ['top_authority_followers'];
		$total_leads=$twitter_stats    ['total_leads'];

            // save the variables into array

			$array=array('source_id'=>$resource_id,'profile_id'=>$id,'total'=>$total_tweets,'average'=>$average_tweets,
				'active_day'=>$active_day, 'unique_author'=>$total_unique_authors, 'popular_author'=>$popular_author, 'positive_sentiment'=>$positive_sentiment,
				'negative_sentiment'=>$negative_sentiment, 'neutral_sentiment'=>$neutral_sentiment, 'retweets'=>$retweets,
				'percent_retweets'=>$percent_retweets,'total_urls'=>$total_urls,'percent_urls'=>$percent_urls,
				'top_language'=>$top_language,'profile_twitter_attention'=>$profile_twitter_attention,
				'top_authority'=>$top_authority,'top_authority_followers'=>$top_authority_followers,
				'total_leads'=>$total_leads,'top_authority_followers'=>$top_authority_followers);
			// insert the record
			$insertTrends=$twitter_trend_dbClass->insertStats($array);
	}
}

?>