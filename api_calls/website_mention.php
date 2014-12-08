<?php 


$resource_id=7; // website resource id

// call needed files
include "C:\wamp\www\SJR_1\keys.php";
include "C:\wamp\www\SJR_1\db_connect.php";
include "C:\wamp\www\SJR_1\db_classes\profile_db.php";
include "C:\wamp\www\SJR_1\db_classes\website_db.php";



// define needed classes
$profile_dbClass=new Profiles($con);
$website_trend_dbClass=new Trend_Website($con);
$getProfiles=$profile_dbClass->getProfiles();




// loop through each profile 

foreach ($getProfiles as $profile) {

    $id=$profile['VH_id'];
    $URL=file_get_contents("https://app.viralheat.com/social/api/monitoring/website/mentions?days=1&profile_id=".$id."&api_key=".$api_key."&organization_id=".$organization_id);
    // create array to save the variables
    $array=array();

  // turn the result from JSON into array

    $decode=json_decode($URL,true);


    // if there is result
    if ($decode['error']==null) {


        $website_mentions=$decode['mentions'];

        // loop through mentions
        foreach($website_mentions as $mention) { 
            



                $mention_detail = $mention['data_record']; //get the date

                
                $date= $mention_detail['date']; //get the label

                
                $post = $mention_detail['original_url']; //post

                $title = $mention_detail['title']; //content
                $domain= $mention_detail['domain']; //text
                $link = $mention_detail['url']; //link
              
                $country= $mention_detail['country']; //link
                
                $city = $mention_detail['city']; //link
                $original_platform = $mention_detail['_original_platform']; //message
                
                                // save the variables into array

                        $array=array('date'=>$date,'source_id'=>$resource_id,'profile_id'=>$id,
                            'post'=>$post,'title'=>$title,
                        'domain'=>$domain, 'link'=>$link, 'country'=>$country,
                        'city'=>$city, 'original_platform'=>$original_platform);
                    
                    // insert the record
                    $insertTrends=$website_trend_dbClass->insertMention($array);


            }

    }

}




?>