<?php
$servername="localhost";
$username="root";
$password="sarthak@123?";
$db_name="criminal_database";
$conn=new mysqli($servername,$username,$password,$db_name);

if($conn->connect_error){
    die("Connection failed".$conn->connect_error);
}

echo "";

?>