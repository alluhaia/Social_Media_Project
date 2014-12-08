<?php 



$resource_id=1; // facebook resource id


// call needed files
include "C:\wamp\www\SJR_1\keys.php";
include "C:\wamp\www\SJR_1\db_connect.php";
include "C:\wamp\www\SJR_1\db_classes\profile_db.php";
include "C:\wamp\www\SJR_1\db_classes/facebook_db.php";


// define needed classes
$profile_dbClass=new Profiles($con);
$facebook_trend_dbClass=new Trend_Facebook($con);
$getProfiles=$profile_dbClass->getProfiles();




// loop through each profile 

foreach ($getProfiles as $profile) {

	$id=$profile['VH_id'];
	$URL=file_get_contents("https://app.viralheat.com/social/api/monitoring/facebook/stats?days=1&profile_id=".$id."&api_key=".$api_key."&organization_id=".$organization_id);
	// create array to save the variables
	$array=array();


      // turn the result from JSON into array

		$decode=json_decode($URL,true);

		 // if there is result
    if ($decode['error']==null) {


			$facebook_stats=$decode['stats'];

			$total_posts=$facebook_stats['total_posts'];
			$average_posts=$facebook_stats['average_posts'];
			$total_unique_authors=$facebook_stats['total_unique_authors'];
			$max_posts=$facebook_stats['max_posts'];
			$min_posts=$facebook_stats['min_posts'];
			$total_fans=$facebook_stats['total_fans'];
			$total_pages=$facebook_stats['total_pages'];
				
			
			$positive_sentiment=$facebook_stats['positive_sentiment'];
			$negative_sentiment=$facebook_stats['negative_sentiment'];
			$neutral_sentiment=$facebook_stats['neutral_sentiment'];

			$top_page_by_fan_title=$facebook_stats['top_page_by_fan_title'];
			$top_page_by_fan_url=$facebook_stats['top_page_by_fan_url'];
			$top_page_fans=$facebook_stats['top_page_fans'];

			$top_page_by_posts_title=$facebook_stats['top_page_by_posts_title'];
			$top_page_by_posts_url=$facebook_stats['top_page_by_posts_url'];
			$top_page_posts=$facebook_stats['top_page_posts'];


            // save the variables into array

			$array=array('source_id'=>$resource_id,'profile_id'=>$id,'total'=>$total_posts,'average'=>$average_posts,
				'unique_author'=>$total_unique_authors, 'max'=>$max_posts, 'min'=>$min_posts, 'total_fan'=>$total_fans,
				'total_page'=>$total_pages, 'positive_sentiment'=>$positive_sentiment, 'negative_sentiment'=>$negative_sentiment,
				'neutral_sentiment'=>$neutral_sentiment,'top_page_by_fan_title'=>$top_page_by_fan_title,'top_page_by_fan_url'=>$top_page_by_fan_url,
				'top_page_fans'=>$top_page_fans,
				'top_page_by_posts_title'=>$top_page_by_posts_title,'top_page_by_posts_url'=>$top_page_by_posts_url, 'top_page_posts'=>$top_page_posts);
			
			// insert the record
			$insertTrends=$facebook_trend_dbClass->insertStats($array);

		// // store into database
		}

	}
	







?>