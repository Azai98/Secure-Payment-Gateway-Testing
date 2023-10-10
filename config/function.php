<?php


function goto2 ($to,$Message){
	echo "<script language=\"JavaScript\">alert(\"".$Message."\") \n window.location = \"".$to."\"</script>";
}

function alert1 ($str){
	print "<script>alert(\"".$str."\")</script>";
}

function logincheck($userID, $p, $signature, $vector){
    include('variable.php');
    $conn=new mysqli($servername,$user,$passw);
    mysqli_select_db($conn,"sp_assignment");
    
    //Connect PDO Database
    $pdo = new PDO('mysql:host=localhost;dbname=SP_Assignment', 'root',"");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Store the decryption key
    $ciphering = "AES-128-CTR";
    $options = 0;
    $decryption_key = $signature;
    $decryption_iv = $vector;
    
    // Use openssl_decrypt() function to decrypt the data
    $queryObtainEncryptedText = $pdo->prepare("SELECT password FROM tbluser WHERE `userID` = :userID");
    $queryObtainEncryptedText->execute([':userID' => $userID]);
    $encryption = $queryObtainEncryptedText->fetch(PDO::FETCH_ASSOC)['password'] ?? '';
    $decryption=openssl_decrypt ($encryption, $ciphering, $decryption_key, $options, $decryption_iv);

    if ($decryption == $p){
        return 1;
    } 
    else {
        return 0;
    }
}

function generateRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateRandomIV($length) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateId($i){
    $numbers = preg_replace('/[^0-9]/', '', $i);
    $letters = preg_replace('/[^a-zA-Z]/', '', $i);

    if ($numbers<10) {
        return $letters.'0'.$numbers+1;
    } else {
        return $letters.$numbers+1;}
}


function displayselectedinfoID(){
    include('variable.php');
    $conn=new mysqli($servername,$user,$passw);
    $sql ="select infoID from webcontents";  // sql command
    mysqli_select_db($conn,"webproject"); ///select database as default
    $result=mysqli_query($conn,$sql);  // command allow sql cmd to be sent to mysql
    $row=mysqli_fetch_assoc($result);

    if(mysqli_num_rows($result) < 1){
      return "";
    }
    else{
      return $row['infoID'];
    }
}

function combineword($r,$t){
    $c=$r." ".$t;

    return $c;

}

function div1($r,$t){
    $c=$r/$t;
    return $c;
}


?>