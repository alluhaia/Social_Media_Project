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
    $URL=file_get_contents("https://app.viralheat.com/social/api/monitoring/pinterest/mentions?days=1&profile_id=".$id."&api_key=".$api_key."&organization_id=".$organization_id);
    // create array to save the variables
    $array=array();


  // turn the result from JSON into a variable

    $decode=json_decode($URL,true);

    // if there is result
    if ($decode['error']==null) {

        $pinterest_mentions=$decode['mentions'];

        // loop through mentions
        foreach($pinterest_mentions as $mention) { 
            $content = $mention['data_record']; //get the date
           
            $date=$content['date'];
            $description= $content['description']; //text
          

            $title= $content['title']; //link


            $user_id= $content['userID']; //user_id
            $username= $content['author']; //user_name
         
            $likes= $content['likes']; // number
            $pins= $content['pins']; // number


            $is_repin= $content['isRepin']; // true/false
            $message_link= $content['pin_link']; // link

            // save the variables into array

            $array=array('date'=>$date,'source_id'=>$resource_id,'profile_id'=>$id,'description'=>$description,'title'=>$title,
            'user_id'=>$user_id, 'username'=>$username, 'likes'=>$likes, 'pins'=>$pins,
            'is_repin'=>$is_repin, 'message_link'=>$message_link);
        
            // insert the record
            $insertTrends=$pinterest_trend_dbClass->insertMention($array);


        }
    }

}




?>