<?php 



include "C:\wamp\www\SJR_1\db_connect.php";
include "C:\wamp\www\SJR_1\db_classes\profile_db.php";



$organization_id="7a92e74007"; // SJR organization ID
$api_key="U042nuQJj4xcwGM4ebYA"; // the api key


$array=array();// to store information



$URL=file_get_contents("https://app.viralheat.com/social/api/v2/monitoring/profiles?api_key=".$api_key."&organization_id=".$organization_id);


$decode=json_decode($URL,true);

$profiles=$decode['profiles'];

foreach ($profiles as $profile) {

	$array[]=array('id'=> $profile['id'], 'category'=>$profile['category'], 'created_at'=>$profile['created_at'], 'expression'=> $profile['expression'],'name'=>$profile['name'], 'tracked'=>$profile['tracked']);




}

// call the function to insert into DB

$profile_dbClass=new Profiles($con);
$insertProfiles=$profile_dbClass->insertProfile ($array);
$getProfiles=$profile_dbClass->getProfiles();
		
print_r($getProfiles);


?>