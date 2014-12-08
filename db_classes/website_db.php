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



class  Trend_Website {
// define a golabal variable for connection
	var $con;
/*
 *This function is called immediately upon instatiating theclass
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

				$item['date']=explode("T",$item['date']); // take the date from datetime

				// make sure that the date data doesn't exist to avoid dublicate entry

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
				// check for sentiment

		foreach ($sentiment as $item) {



				$item['date']=explode("T",$item['date']); // take the date from datetime
				$query=" SELECT *
						FROM trends 
						WHERE DATE='".$item['date'][0]."' AND source_id=".$item['source_id']." AND profile_id=".$item['profile_id'];

						 // first we need to make sure if trend is already exist and avoid dublicate key message  
			
				$result = $this->con->query($query) OR die("database error" . $this->con->error);
				
				$raw=$result->fetch_assoc();
			

				if (is_null($raw['date'])==true) {  // if no record exist-> insert it
			 		

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
* @ return array $result : the array of stats array  (id, date, source_id , profile_id , total, average,
								total_sites_today, top_country, top_domain, top_domain_mentions, top_domain_url, 
								top_link, top_link_url, top_link_mentions,total_country, max, min )  
*
*/

	public function getStats ($startDate,$endDate,$resource,$profile) {

	// define an empty array to store result
		$result=array();
		
				$query=" SELECT *
						FROM website_stats"; 
						

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
*This function is to insert a stats into DB
*
* @ param array $array : the array of stats  (source_id , profile_id , total, average,
								total_sites_today, top_country, top_domain, top_domain_mentions, top_domain_url, 
								top_link, top_link_url, top_link_mentions,total_country, max, min )  
*
*
*/
	public function insertStats ($array) {


				$item['date']=explode(" ",date('Y-m-d H:i:s')); // get the date from datetime

				$query=" SELECT *
						FROM website_stats 
						WHERE CONVERT (DATE, date)='".$item['date'][0]."' AND source_id=".$array['source_id']." AND profile_id=".$array['profile_id'];

						 // first we need to make sure if trend is already exist and avoid dublicate key message  
			
				$result = $this->con->query($query) OR die("database error" . $this->con->error);
				$raw=$result->fetch_assoc();

		
				if (is_null($raw['date'])==true) { 

						$query ='INSERT INTO website_stats 
							( date , source_id , profile_id , total, average,
								total_sites_today, top_country, top_domain, top_domain_mentions, top_domain_url, 
								top_link, top_link_url, top_link_mentions,total_country, max, min ) 
							VALUES ( "'.date('Y-m-d H:i:s') .'",'. $array['source_id'] .','.$array['profile_id'] .','.$array['total'] .
								','. $array['average'] .','.$array['total_sites_today'] .',"'.$array['top_country'] 
								 .'",'.$array['total_country'] .',"'.$array['top_domain'] .'",'. $array['top_domain_mentions']
								 .',"'.$array['top_domain_url'].'","'. $array['top_link'].'","'. $array['top_link_url'].'",'.$array['top_link_mentions']
								 .','.$array['period_high'].','.$array['period_low'].')';



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

	public function getMentions ($startDate,$endDate,$resource,$profile) {


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
* @ param array $item : the array of mentions
						( date , source_id , profile_id , title, message_link,
								domain, link, country, city, original_platform) 
	
*
*
*/

	public function insertMention ($item) {




						$query ='INSERT INTO mentions 
							( date , source_id , profile_id , title, message_link,
								domain, link, country, city, original_platform) 
							VALUES ( "'.$item['date'] .'",'. $item['source_id'] .','.$item['profile_id'] .',"'.mysqli_real_escape_string($this->con,$item['title']) .
								'","'.$item['post'] .
								'","'. $item['domain'] .'","'. mysqli_real_escape_string($this->con,$item['link']) .'","'.$item['country'] 
								 .'","'.$item['city'] .'","'. $item['original_platform'] .'") ' ;



			 			$result = $this->con->query($query) OR die("database error" . $this->con->error);
			 		
			 		


		}

}

  // end of website_DB calss


