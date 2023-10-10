<?php
if (file_exists('config/function.php')==1){ 
  require_once('config/function.php');
}
else{
  require_once('./config/function.php');
}

//check userID and CSRF token
if (isset($_SESSION['userID']) && isset($_SESSION['token'])){
   //goto2("./orderpage.php","You have login.");
}else{
   goto2("./login.php","Please log on before using.");
}
?>

