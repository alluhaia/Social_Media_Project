<?php
/*
*@Purpose: 
* In this class we include all the functionality that are related to inserting and retrivening Form database
*Also we do some clean up and calculations to the output for the table 
* When the query submitted the code will take startDate, endDate,  and profile_id

*@Author: Ala 

*@Date Created: 30/10/2014 

*@Version: 6

*/


class  Profiles {
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
*This function is to get the profiles (the companies keywords)
*
* @ return 	  array $result['result']:  the resulted array 
*		
* 	if no result found then return null
*
*/

/*
*/

	public function getProfiles () {

	$query=" SELECT *
			FROM profile 
			ORDER BY	VH_id";
		
		

	$res = $this->con->query($query) or die("database error" . $this->con->error);

	$result = array();  // to save the resulted rows to display in a table




		if (empty($res)) {   //here we deal with no result found
				return null;
		  }  else  {

			 
			while ($row = $res->fetch_assoc()) { // save the output from database
					$result[] = $row;
				}

				
	 	return $result;
		}
	 
	 }
 
 
/*
*This function is to insert into DB
*
* @ param array $profileArray : the array of profile array (createdDate, name, expression, tracked, viralheat_id, category)
*
*
*/
 
	public function insertProfile($array){

		// array to store result
		$result=array();

		//looop through the array and make sure that the profile has not been already there before inserting
		foreach ($array as $item) {

	

			$query ='SELECT * 
					FROM profile 
					WHERE VH_id='.$item['id'] ;


	 // first we need to make sure if profile is already exist and avoid dublicate key message  
			
			$result = $this->con->query($query) OR die("database error" . $this->con->error);
			
			if ($result->num_rows==0) {

					 $query ='INSERT INTO profile 
						( created_at , name , expression , tracked, VH_id, category ) 
						VALUES ( "'.$item['created_at'] .'","'. $item['name'] .'","'.$item['expression'] .'","'.$item['tracked'] .'",'.$item['id'].',"'.$item['category'] .'" ) ' ;
		 			$result = $this->con->query($query) OR die("database error" . $this->con->error);


			}



		}

	

	}

}

 

