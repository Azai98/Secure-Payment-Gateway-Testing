<?php
    include('./config/settings.php');
    require_once('./config/function.php');
    session_start();

    //CSRF Token
    if (empty($_SESSION['token'])) {
        $_SESSION['token'] = md5(uniqid(mt_rand(), true));
    }
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Password Recovery</title>
    <link rel="stylesheet" href="css/forgetpassword.css"/>   <!--Import CSS-->
</head>
<body>
    <div class="header">
        <h2>Azai&Chen Pizza.co</h2>
    </div>
    <div class="title">
    <h2>Password Recovery<h2>
    </div>
    <div class="box">
    <form action="" method="POST" name="reset">
        <!-- CSRF Token and its hidden by input type-->
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">
        <div class="resetlogo">
            <img src="images\resetlogo.jpg" width="65px" height="60px" alt="logo"> 
        </div>
        <table cellspacing='5' align='center'>
        <tr>
            <td class="box-title">Enter your Email Address:</td>
        </tr>
        <tr>
            <td><input type='text' name='email' placeholder="Email Address" class="input-box" required/></td>
        </tr>
        <tr>
            <td><button type='submit' name='submit' value='Submit'>Submit</button></td>
        </tr>
        </table>
    </form>
<?php

if(isset($_POST["email"]) && (!empty($_POST["email"]))){
    if($_POST['token'] == $_SESSION['token']) { 
    $email = $_POST['email'];
    $tokenkey = $_POST['token'];
    mysqli_select_db($conn,"sp_assignment"); ///select database as default
    //Connect PDO Database
    $pdo = new PDO('mysql:host=localhost;dbname=SP_Assignment', 'root',"");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $clean_email = filter_var($email, FILTER_SANITIZE_STRING);
    $sanitized_email = mysqli_real_escape_string($conn, $clean_email);
    
    $queryCheck = $pdo->prepare("SELECT userID FROM tbluser WHERE `email` = :email ");
    $queryCheck->execute([':email' => $sanitized_email]);

    if(count($queryCheck->fetchAll()) > 0){
            $expFormat = mktime(date("H"), date("i")+5, date("s"), date("m") ,date("d"), date("Y"));
            date_default_timezone_set("Asia/Kuala_Lumpur");
            $expDate = date("Y-m-d H:i:s",$expFormat);
            $token = generateRandomString($length = 10);

            $queryInsert = $pdo->prepare("INSERT into `tbl_password_reset` (`email` , `token`, `expDate`)
            VALUES (:email , :token , :expDate)");

            //Sanitize Data to prevent CSS Attack
            $clean_expDate = filter_var($expDate, FILTER_SANITIZE_STRING);
            $clean_token = filter_var($token, FILTER_SANITIZE_STRING);
                    
            //Use parameterized SQL Statement to prevent SQL Injection Attack
            $sanitized_expDate = mysqli_real_escape_string($conn, $clean_expDate);
            $sanitized_token = mysqli_real_escape_string($conn, $clean_token);

            $queryInsert->bindParam(':email', $sanitized_email);
            $queryInsert->bindParam(':token', $sanitized_token);
            $queryInsert->bindParam(':expDate', $sanitized_expDate);

            // Insert Temp Table
            $queryInsert->execute();

            //Shows link to reset password
            $output='<p class="link-text">User found, please click on the following link to reset your password within 5 minutes.</p>';
            $output.='<p class="link-text"><a href="resetpassword.php?token='.$sanitized_token.'&email='.$sanitized_email.'&tokenkey='.$tokenkey.'&action=reset">Reset Now, click on me!</a></p>';		
            echo $output;
    }else{
		goto2("forgetpassword.php","The email address is not exists, please try again.");
	}
   }else{
        echo "token is not valid. Request has been rejected.";
        unset($_SESSION['token']);
        goto2("Login.php","Bad request, login to try again.");
        exit;
    }    
}?>

    </div>
</body>
</html>





