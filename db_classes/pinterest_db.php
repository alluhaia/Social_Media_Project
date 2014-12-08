<?php
/*
*@Purpose: 
* In this class we include all the functionality that are related to inserting and retrivening from database
*Also we do some clean up and calculations to the output for the table 
* When the query submitted the code will take startDate, endDate, source_id, and profile_id

*@Author: Ala 

*@Date Created: 11/3/2014 

*@Version: 6

*/


class  Trend_Pinterest {
// define a golabal variable for connection
	var $con;
/*
 *This function is called immediately upon instatiating the class
 * @ param string $con : the connection to the database returned from DB script
 *
 * 
 */
	public function __construct($con) {
			$this->con = $con;
	}



/*
*This function is to get the trends
*
* @ param date : $date
* @ param int : $source_id
* @ param int : $profile_id
*
* @ return 	  array $result['result']:  the resulted array 
*		
* 	if no result found then return null
*
*/

	public function gettrends ($date, $source_id, $profile_id) {



	$query=" SELECT *
			FROM trends
			WHERE source_id=".$source_id
			." AND profile_id=".$profile_id
			." ORDER BY date";

		if (is_null($date)==false){ // we nneed to query certain date

			$query=" SELECT *
			FROM trends
			WHERE date='".$date
			."' source_id=".$source_id
			." AND profile_id=".$profile_id
			." ORDER BY date";

		}
		

	$res = $this->con->query($query) or die("database error" . $this->con->error);

	$result = array();  // to save the resulted rows to display in a table




		if (empty($res)) {   //here we deal with no result found
				return null;
		  }  else  {

			 
			while ($row = $res->fetch_assoc()) {
					$result[] = $row;
				}

				
	 	return $result;
		}
	 
	 }
 
 





/*
*This function is to insert a trend into DB
*
* @ param array $trend : the array of trends array (date,source_id, profile_id, count) 
*
* @ param array $sentiment : the array of trends array (date,source_id, profile_id, positive, negative, neutral) 
*
*/


	public function insertTrend ($trend, $sentiment) {



		
		foreach ($trend as $item) {

				$item['date']=explode("T",$item['date']);// take the date from datetime

			
				$query=" SELECT *
						FROM trends 
						WHERE DATE='".$item['date'][0]."' AND source_id=".$item['source_id']." AND profile_id=".$item['profile_id'];

						 // first we need to make sure if trend is already exist and avoid dublicate key message  
			
				$result = $this->con->query($query) OR die("database error" . $this->con->error);
				$raw=$result->fetch_assoc();
				if (is_null($raw['count'])==true) { 


					if (is_null($raw['date'])==true) { // if no count exist-> insert it
						$query ='INSERT INTO trends 
							( date , source_id , profile_id , count) 
							VALUES ( "'.$item['date'][0] .'",'. $item['source_id'] .','.$item['profile_id'] .','.$item['count'] .') ' ;
			 			$result = $this->con->query($query) OR die("database error" . $this->con->error);
			 		
			 		} else { // update the record

			 			$query ='UPDATE trends 
							   SET count= '.$item['count']. ' WHERE date="'.$item['date'][0].'" AND source_id='.$item['source_id'].
							   ' AND profile_id='.$item['profile_id'];
			 			$result = $this->con->query($query) OR die("database error" . $this->con->error);
			 		

			 		}


				} 

		}

		foreach ($sentiment as $item) {



				$item['date']=explode("T",$item['date']);// take the date from datetime
				
				// make sure that the date data doesn't exist to avoid dublicate entry

				$query=" SELECT *
						FROM trends 
						WHERE DATE='".$item['date'][0]."' AND source_id=".$item['source_id']." AND profile_id=".$item['profile_id'];

						 // first we need to make sure if trend is already exist and avoid dublicate key message  
			
				$result = $this->con->query($query) OR die("database error" . $this->con->error);
				
				$raw=$result->fetch_assoc();
			

				if (is_null($raw['date'])==true) { // if no count exist-> insert it
			 		

					$query ='INSERT INTO trends 
						( date , source_id , profile_id , positive, negative, neutral) 
						VALUES ( "'.$item['date'][0] .'",'. $item['source_id'] .','.$item['profile_id'] 
							.','.$item['positive'].','.$item['negative'].','.$item['neutral'].' ) ' ;
		 			$result = $this->con->query($query) OR die("database error" . $this->con->error);

				} else { // update the record

					$query ='UPDATE trends 
							   SET positive= '.$item['positive'].
							   		', negative='.$item['negative'].
							   		', neutral= '.$item['neutral'] 
							   		.' WHERE date="'.$item['date'][0].'" AND source_id='.$item['source_id'].
							   ' AND profile_id='.$item['profile_id'];
			 			$result = $this->con->query($query) OR die("database error" . $this->con->error);
			 		



				}

		}

	}


/*
*This function is to get stats from DB
*
* @ param date $startDate 
* @ param date $endDate 
* @ param int $resource 
* @ param int $profile 
*
* @ return array $result : the array of stats array   ( date , source_id , profile_id , total_pins, avg_pins,
								total_repins, total_boards, total_likes, total_reach, max, 
								min, unique_pinners, average_daily_pins,average_pins_per_pinner,
							    average_reach_per_pinner, average_likes_per_pinner, average_likes_per_pin,
							     average_repins_per_pin ) 
*
*/

	public function getStats ($startDate,$endDate,$resource,$profile) {

// define an empty array to store result
	$result=array();
		
				$query=" SELECT *
						FROM pinterest_stats"; 
						

						if (isset ($startDate) OR isset ($endDate) OR
						 isset($resource) OR isset($profile)) {

							
							$query=$query." WHERE ";
						

						}

						if (isset($startDate))  {

									if (strpos($query, ")") !== false) {
										   $query=$query . " AND ";
								 }


								$query=$query." DATE(date)>=".$startDate;
						}

							if (isset($endDate))  {

									if (strpos($query, ")") !== false) {
										   $query=$query . " AND ";
								 }


								$query=$query." DATE(date)<=".$endDate;
						}


							if (isset($resource))  {

								if (strpos($query, ")") !== false) {
										   $query=$query . " AND ";
								 }


								$query=$query." source_id=".$resource;
						}

								if (isset($profile))  {

									if (strpos($query, "=") !== false) {
										   $query=$query . " AND ";
								 }


								$query=$query." profile_id=".$profile;
						}

						$query=$query." ORDER BY DATE(date) ";


			
				$result = $this->con->query($query) OR die("database error" . $this->con->error);
				
				while ($row = $result->fetch_assoc()) { // save the result
					$result[] = $row;
				}
				

	 
		 return $result;
 
 
	}




/*
*This function is to insert into DB
*
* @ param array $profileArray : the array of profile
*
* @ return 	  true or false
*
*/

	public function insertStats ($array) {



			$item['date']=explode(" ",date('Y-m-d H:i:s'));

				$query=" SELECT *
						FROM pinterest_stats 
						WHERE CONVERT (DATE, date)='".$item['date'][0]."' AND source_id=".$array['source_id']." AND profile_id=".$array['profile_id'];

						 // first we need to make sure if trend is already exist and avoid dublicate key message  
			
				$result = $this->con->query($query) OR die("database error" . $this->con->error);
				$raw=$result->fetch_assoc();

		
				if (is_null($raw['date'])==true) { 

						$query ='INSERT INTO pinterest_stats 
							( date , source_id , profile_id , total_pins, avg_pins,
								total_repins, total_boards, total_likes, total_reach, max, 
								min, unique_pinners, average_daily_pins,average_pins_per_pinner,
							    average_reach_per_pinner, average_likes_per_pinner, average_likes_per_pin,
							     average_repins_per_pin ) 
							VALUES ( "'.date('Y-m-d H:i:s') .'",'. $array['source_id'] .','.$array['profile_id'] .','.$array['total_pins']
								.','. $array['avg_pins'] .','.$array['total_repins'] .','.$array['total_boards'] 
								 .','.$array['total_likes'] .','.$array['total_reach'] .','. $array['max']
								 .','.$array['min'].','. $array['unique_pinners'].','. $array['average_daily_pins']
								 .','.$array['average_pins_per_pinner'].','.str_replace(',', '', $array['average_reach_per_pinner'])
								  .','.$array['average_likes_per_pinner'].','.$array['average_likes_per_pin']
								  .','.$array['average_repins_per_pin'].')';


			 			$result = $this->con->query($query) OR die("database error" . $this->con->error);
			 		
			 		
			 }

		}


/*
*This function is to get mentions from DB
*
* @ param date $startDate 
* @ param date $endDate 
* @ param int $resource 
* @ param int $profile 
*
* @ return array $mention : the array of mentions array  (`id`, `date`, `source_id`, 
							`profile_id`, `label`, `description`, `link`, `message`, `title`,
							 `raw_content`, `user_id`, `username`, `user_profile`, `user_followed`,
							 `user_link_title`, `user_link_behavior`, `likes`, `liked`, `shared`, `removable`,
							  `comments`, `sentiment`, `lead`, `mention_var`, `is_rich`, `message_link`, `link_status`,
							   `lead_action_name`, `author`, `is_repin`, `pins`, `is_post`, `positive`, `negative`,
							    `city`, `country`, `domain`, `original_platform`, `country_code`, `view_count`, 
							    `rating_count`, `rating`, `category`, `reply_to`, `like_status`, 
							    `twitter_conversations_url`, `follow_status`, `like_action`, `tracked`) 

*
*/

	public function getMention ($startDate,$endDate,$resource,$profile) {

	// empty array to save result
	$result=array();
		
				$query=" SELECT *
						FROM mentions"; 
						

						if (isset ($startDate) OR isset ($endDate) OR
						 isset($resource) OR isset($profile)) {

							
							$query=$query." WHERE ";
						

						}

						if (isset($startDate))  {


								$query=$query." DATE(date)>=".$startDate;
						}




							if (isset($endDate))  {

								if (strpos($query, ")") !== false) {
										   $query=$query . " AND ";
								 }


								$query=$query." DATE(date)<=".$endDate;
						}


							if (isset($resource))  {


									if (strpos($query, ")") !== false) {
										   $query=$query . " AND ";
								 }

								$query=$query." source_id=".$resource;
						}

								if (isset($profile))  {

									if (strpos($query, "=") !== false) {
										   $query=$query . " AND ";
								 }	

								$query=$query." profile_id=".$profile;
						}

						$query=$query." ORDER BY DATE(date) ";


			
				$result = $this->con->query($query) OR die("database error" . $this->con->error);
				
				while ($row = $result->fetch_assoc()) {
					$result[] = $row;
				}
				

	 
		 return $result;
 
 
	}




/*
*This function is to insert into DB
*
* @ param array $array : the array of mentions
						(source_id , profile_id , description, title,
								user_id, username, likes, pins, is_repin, 
								message_link ) 
*
*
*/

	public function insertMention ($array) {



						$query ='INSERT INTO mentions 
							( date , source_id , profile_id , description, title,
								user_id, username, likes, pins, is_repin, 
								message_link ) 
							VALUES ( "'.date('Y-m-d H:i:s') .'",'. $array['source_id'] .','.$array['profile_id'] .',"'. mysqli_real_escape_string($this->con,$array['description']) .
								'","'. $array['title'] .'",'.$array['user_id'] .',"'.$array['username'] 
								 .'",'.$array['likes'] .','.$array['pins'] .',"'. $array['is_repin']
								 .'","'.$array['message_link'].'")';



			 			$result = $this->con->query($query) OR die("database error" . $this->con->error);
			 		
			 		


		}

}

 
  // end of pinterest_DB calss
