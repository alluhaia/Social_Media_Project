<?php 


$resource_id=4; // instagram resource id


// call needed files
include "C:\wamp\www\SJR_1\keys.php";
include "C:\wamp\www\SJR_1\db_connect.php";
include "C:\wamp\www\SJR_1\db_classes\profile_db.php";
include "C:\wamp\www\SJR_1\db_classes\instagram_db.php";


// define needed classes
$profile_dbClass=new Profiles($con);
$instagram_trend_dbClass=new Trend_instagram($con);
$getProfiles=$profile_dbClass->getProfiles();




// loop through each profile 

foreach ($getProfiles as $profile) {

    $id=$profile['VH_id'];
    $URL=file_get_contents("https://app.viralheat.com/social/api/monitoring/instagram/mentions?days=1&profile_id=".$id."&api_key=".$api_key."&organization_id=".$organization_id);
    // create array to save the variables
    $array=array();

  // turn the result from JSON into a variable

        $decode=json_decode($URL,true);

        // if there is result
    if ($decode['error']==null) {

            $instgram_mentions=$decode['mentions'];


            // loop through mentions
            foreach($instgram_mentions as $mention) { 
             

           //$mention_account=$mention['date']; //get the date
                $mention_detail = $mention['data_record']; //get the date

                $date= $mention_detail['date']; //get the label
                $authorName= $mention_detail ['authorName'];

                $message= $mention_detail ['message'];

                $link= $mention_detail ['messageLink'];
                $negative= $mention_detail ['negative_score'];

                
                $positive= $mention_detail['positive_score']; //get the label

                
                $totalComments = $mention_detail['totalComments']; //comments

                $totalLikes=$mention_detail['totalLikes'];

                $authorDisplayName=$mention_detail['authorDisplayName'];



                $original_platform = $mention_detail['platform']; //message
                   
                    // save the variables into array
                        $array=array('date'=>$date,'source_id'=>$resource_id,'profile_id'=>$id,
                            'username'=>$authorName,'message'=>$message,
                        'link'=>$link, 'negative'=>$negative, 'positive'=>$positive,
                        'totalComments'=>$totalComments, 'totalLikes'=>$totalLikes,
                        'authorDisplayName'=> $authorDisplayName, 'original_platform'=> $original_platform
                        );


                    // insert the record
                    $insertTrends=$instagram_trend_dbClass->insertMention($array);

            }
        }

}




?>