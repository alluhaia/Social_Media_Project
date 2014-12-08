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


$array=array();

  



// loop through each profile 

foreach ($getProfiles as $profile) {

    $id=$profile['VH_id'];
    $URL=file_get_contents("https://app.viralheat.com/social/api/monitoring/facebook/mentions?days=1&profile_id=".$id."&api_key=".$api_key."&organization_id=".$organization_id);
      // turn the result from JSON into array
    $decode=json_decode($URL,true);

 


    // if there is result
    if ($decode['error']==null) {

        // loop through mentions
        $facebook_mentions=$decode['mentions'];
        foreach($facebook_mentions as $mention) { 



            $mention_record = $mention['data_record']; 
            $date=$mention_record['date']; //get the date
            $author=$mention_record['author']; // get the author
            $authorURL=$mention_record['authorURL']; // get the authorURL
            $comment=$mention_record['comment']; // get the comment
            $user_id=$mention_record['profile_id']; // get the profile_id

            $is_post=$mention_record ['is_post']; // true OR false
            $negative_score= $mention_record ['negative_score']; // decimal 
            $positive_score= $mention_record['positive_score']; // decimal
            $post_url= $mention_record['post_url'];

           
            // save the variables into array
            $array=array('date'=>$date,'source_id'=>$resource_id,'profile_id'=>$id, 'author'=>$author,'authorURL'=>$authorURL,
                'comment'=> $comment,'user_id'=>$user_id, 'negative_score'=>  $negative_score,'positive_score'=>$positive_score ,
                'is_post'=>$is_post, 
                'post_url'=>$post_url);

         
          
            // insert the record
            $insertMentions=$facebook_trend_dbClass->insertMention($array);


        }



    }
}


?>