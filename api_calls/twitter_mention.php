<?php 

$resource_id=2; // Twitter resource id


// call needed files
include "../keys.php";
include "../db_connect.php";
include "../db_classes/profile_db.php";
include "../db_classes/twitter_db.php";


// define needed classes
$profile_dbClass=new Profiles($con);
$twitter_trend_dbClass=new Trend_Twitter($con);
$getProfiles=$profile_dbClass->getProfiles();


$array=array();

  



// loop through each profile 

foreach ($getProfiles as $profile) {

    $id=$profile['VH_id'];
    $URL=file_get_contents("https://app.viralheat.com/social/api/v2/monitoring/twitter/mentions?days=1&profile_id=".$id."&api_key=".$api_key."&organization_id=".$organization_id);

      // turn the result from JSON into a variable
    $decode=json_decode($URL,true);

 


 // if there is result
    if ($decode['error']==null) {

        // loop through mentions
        $facebook_mentions=$decode['mentions'];
        foreach($facebook_mentions as $mention) { 


            $date=$mention['date'];  // date
            $label=$mention['label'];
            $original_platform=$mention['platform_name'];
            $sentiment=$mention['sentiment'];

            $lead_action_name=$mention['lead_action_name'];
            $like_status=$mention['like_status'];

            $comments=$mention['comments'];
            $removable=$mention['removable'];
            $user_link_behavior=$mention['user_link_behavior'];
            $user_followed=$mention['user_followed'];
            $lead=$mention['lead'];
            $message_content= $mention['message_content'];
            $message_link =$mention['message_link'];
            $author=$mention['user_name'];
            $user_id=$mention['user_id'];
            $user_name= $mention['user_screen_name'];


            
            $liked=$mention['liked'];
            $like_status=$mention['like_status'];
            $like_action=$mention['like_action'];
            $follow_status=$mention['follow_status'];
            $likes=$mention['likes'];
            $shared=$mention['shared'];
            $is_rich=$mention['is_rich'];
            $mention_var=$mention['mention'];
       

            $post=$mention['post'];
            $content=$post['account'];
         

            $user_profile= $post['user_link'];//user_link
            $tracked=$content['tracked'];
            $reply_to=$post['reply_to'];
            $twitter_conversations_url=$mention['twitter_conversations_url'];
            

                       // save the variables into array

                $array=array('date'=>$date,'source_id'=>$resource_id,'profile_id'=>$id, 
                    'author'=>$author,'label'=>$label,
                     'original_platform'=>$original_platform,'sentiment'=>$sentiment,
                     'lead_action_name'=>$lead_action_name,'follow_status'=>$follow_status,
                     'comments'=>$comments,'removable'=>$removable,
                     'user_link_behavior'=>$user_link_behavior,'user_followed'=>$user_followed,
                     'lead'=>$lead,'message_content'=>$message_content,
                     'message_link'=>$message_link,'user_id'=>$user_id,
                       'user_name'=>$user_name,'user_profile'=>$user_profile,
                       'liked'=>$liked,'like_status'=>$like_status,
                       'like_action'=>$like_action,'likes'=>$likes,
                        'shared'=>$shared,'is_rich'=>$is_rich,
                        'mention_var'=>$mention_var,'tracked'=>$tracked,
                        'reply_to'=>$reply_to,'twitter_conversations_url'=>$twitter_conversations_url);




                        // insert the record

            $insertMentions=$twitter_trend_dbClass->insertMention($array);


        }


    }
}


?>