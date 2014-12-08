<?php 


$resource_id=6; // video resource id

// call needed files
include "C:\wamp\www\SJR_1\keys.php";
include "C:\wamp\www\SJR_1\db_connect.php";
include "C:\wamp\www\SJR_1\db_classes\profile_db.php";
include "C:\wamp\www\SJR_1\db_classes/video_db.php";


// define needed classes
$profile_dbClass=new Profiles($con);
$video_trend_dbClass=new Trend_Video($con);
$getProfiles=$profile_dbClass->getProfiles();




// loop through each profile 

foreach ($getProfiles as $profile) {

    $id=$profile['VH_id'];
    $URL=file_get_contents("https://app.viralheat.com/social/api/monitoring/video/mentions?days=1&profile_id=".$id."&api_key=".$api_key."&organization_id=".$organization_id);
    // create array to save the variables
    $array=array();

  // turn the result from JSON into a variable

    $decode=json_decode($URL,true);



    // if there is result
    if ($decode['error']==null) {

        $video_mentions=$decode['mentions'];

        // loop through mentions
        foreach($video_mentions as $mention) { 
           

            //$mention_account=$mention['date']; //get the date
                $mention_detail = $mention['data_record']; //get the date

                
                $date= $mention_detail['date']; //get the label

                
                $user_name = $mention_detail['author_name']; //post

                $user_link = $mention_detail['author_url']; //content
                $description=$mention_detail['description']; //description

                $platform=$mention_detail['platform']; //description

                $rating=$mention_detail['rating']; //rating

                $ratig_count=$mention_detail['rating_count']; //rating

                $title= $mention_detail['title']; //rating

                $link=$mention_detail['url']; //link'

                $view_count=$mention_detail['view_count']; //rating'


                $country_code=$mention_detail['country_code']; //linkcountry_code

                $user_id=$mention_detail['author_id']; //author_id
              
                $category=$mention_detail['category']; //category

                        // save the variables into array

                    $array=array('date'=>$date,'source_id'=>$resource_id,'profile_id'=>$id,
                            'user_id'=>$user_id,'username'=>$user_name,'user_link'=>$user_link, 'description'=>$description, 'platform'=>$platform,
                            'rating'=>$rating,  'link'=>$link,
                            'country_code'=>$country_code,
                        'title'=>$title,
                     'ratig_count'=>$ratig_count,
                    'view_count'=>$view_count, 
                    'rating'=>$rating,
                    'category'=>$category

                    );
                
                // insert the record
                $insertTrends=$video_trend_dbClass->insertMention($array);

            }
    }

}


?>