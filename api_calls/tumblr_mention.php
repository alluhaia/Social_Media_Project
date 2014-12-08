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
    $URL=file_get_contents("https://app.viralheat.com/social/api/monitoring/tumblr/mentions?days=1&profile_id=".$id."&api_key=".$api_key."&organization_id=".$organization_id);
    // create array to save the variables
    $array=array();

  // turn the result from JSON into a variable

        $decode=json_decode($URL,true);


    // if there is result
    if ($decode['error']==null) {

        $tumblr_mentions=$decode['mentions'];

        // loop through mentions
        foreach($tumblr_mentions as $mention) { 


            $mention_detail = $mention['data_record']; //get the date

            
            $date= $mention_detail['date']; //get the label

            $message=$mention_detail['message'];
            $post = $mention_detail['post_url']; //post

            $title = $mention_detail['blog_name']; //content

            $original_platform = $mention_detail['platform']; //message

            $positive_score=$mention_detail['positive_score'];
            $negative_score=$mention_detail['negative_score'];
            $user_id= $mention_detail['id']; //user_id

         
                    // save the variables into array


            $array=array('date'=>$date,'source_id'=>$resource_id,'profile_id'=>$id,
                'message'=> $message, 'post'=>$post, 

                  'title'=>$title,'positive_score'=>$positive_score,
            'negative_score'=>$negative_score, 'user_id'=>$user_id
            , 'original_platform'=>$original_platform);
        

            // insert the record
            $insertTrends=$tumblr_trend_dbClass->insertMention($array);


        }

    }
  

}




?>