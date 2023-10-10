<?php

$conn=new mysqli($servername,$user,$passw);

//if ($conn->connect_error){
if (!$conn){
    //die("Connection failed".$conn->connect_error);  
    die("Connection failed".mysqli_connect_error());
}
// echo (" Connection is a success");
?>
<!-- need to have a form to type the name of the database --> 
<!-- delete that database -->