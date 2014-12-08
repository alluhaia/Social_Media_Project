<?php
/*
@purpose: This file is to establish the connection to mysql DB 

@Author: Ala

@Date created: 30/10/2014

@Version: 1

*/


$con = new mysqli("localhost","root","", "social_media_DB");

if ($con->connect_errno)
  {
  	die('Could not connect: ' . $con->connect_error);
  }

?>