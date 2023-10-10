<?php
    include('./config/settings.php');
    require_once('./config/function.php');
    session_start();
?>        
<!DOCTYPE HTML>
<html>
    <head>
          <meta charset="utf-8"/>
               <title>ResetPassword</title>
                    <link rel="stylesheet" href="css/resetpassword.css"/>   <!--Import CSS-->
                </head>
                <body>
                 <div class="header">
                    <h3>Azai&Chen Pizza.co</h3>
                </div>
                  <div class="title">
                     <h2>Password Recovery<h2>
                 </div>
                <div class="box">
                     <div class="resetlogo">
                     <img src="images\newpassword.jpg" width="60px" height="60px" alt="logo"> 
              </div>
<?php
    if (isset($_GET["token"]) && isset($_GET["email"]) && ($_GET["action"]=="reset")){
        if(isset($_SESSION["token"]) && ($_GET["tokenkey"]==$_SESSION["token"])) { 
            $token = $_GET["token"];
            $email = $_GET["email"];
            date_default_timezone_set("Asia/Kuala_Lumpur");
            $curDate = date("Y-m-d H:i:s");
            mysqli_select_db($conn,"SP_Assignment"); ///select database as default

            $pdo = new PDO('mysql:host=localhost;dbname=SP_Assignment', 'root',"");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $clean_token = filter_var($token, FILTER_SANITIZE_STRING);
            $clean_email = filter_var($email, FILTER_SANITIZE_STRING);
            $sanitized_token = mysqli_real_escape_string($conn, $clean_token);
            $sanitized_email = mysqli_real_escape_string($conn, $clean_email);
            
            $queryCheck = $pdo->prepare("SELECT expDate FROM tbl_password_reset WHERE `email` = :email AND `token` = :token");
            $queryCheck->execute([':email' => $sanitized_email, ':token' => $sanitized_token]);    

            if(count($queryCheck->fetchAll()) == 0){
                echo '<div class=invalid-text> <h2>Invalid Link</h2>
                <p>The reset token is invalid/expired. Either you did not reset the password in time, 
                or you have already used the key in which case it is deactivated.</p> </div>';
                goto2("Login.php","Bad request, login to try again.");
                exit;
            }
            else{
                $queryCheck->execute([':email' => $sanitized_email, ':token' => $sanitized_token]);  
                $expDate = $queryCheck->fetch(PDO::FETCH_ASSOC)['expDate'];
                if ($expDate >= $curDate){
                ?>
                     <form method="post" action="" name="update">
                     <input type="hidden" name="action" value="update" />
                     <br />
                     <label><strong>Enter New Password:</strong></label><br />
                     <input type="password" name="pass1" maxlength="15" placeholder="New Password" class="input-box" required />
                     <br />
                     <label><strong>Re-Enter New Password:</strong></label><br />
                     <input type="password" name="pass2" maxlength="15" placeholder="Confirm Password" class="input-box" required/>
                     <input type="hidden" name="email" value="<?php echo $email;?>"/>
                     <button type="submit" value="Reset Password">Reset Password</button>
                     </form>
                     <?php
                }else{
                        echo  '<div class=invalid-text><h2>Link Expired</h2>
                        <p>The link is expired. Please request again in order to change your password.<br /><br /></p> </div>';
                        goto2("Login.php","Bad request, login to try again.");
                        exit;
                    }
                }
            }else{
                echo "token is not valid. Request has been rejected.";
                unset($_SESSION['token']);
                goto2("Login.php","Bad request, login to try again.");
                exit;
            }
            }//done validate

                if(isset($_POST["email"]) && isset($_POST["action"]) && ($_POST["action"]=="update")){
                    $error = "";
                    $pass1 = mysqli_real_escape_string($conn,$_POST["pass1"]);
                    $pass2 = mysqli_real_escape_string($conn,$_POST["pass2"]);
                    $email = $_POST["email"];
                if ($pass1!=$pass2){
                    $error.= "<p class='warning-text'>Password do not match, <br />both password should be same.<br /></p>";
                }
                if($error!=""){
                    echo "<div class='error'>".$error."</div><br />";
                }else{
                
                //retrieve user unique salt for calculating new password
                mysqli_select_db($conn,"sp_assignment");
                $pdo = new PDO('mysql:host=localhost;dbname=SP_Assignment', 'root',"");
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $clean_email = filter_var($email, FILTER_SANITIZE_STRING);
                $sanitized_email = mysqli_real_escape_string($conn, $clean_email);
                
                $queryCheck = $pdo->prepare("SELECT userID FROM tbluser WHERE `email` = :email");
                $queryCheck->execute([':email' => $sanitized_email]);    
                $userID = $queryCheck->fetch(PDO::FETCH_ASSOC)['userID'];

                $hashpass = md5($pass2.$userID);  //recalculate hashpassword            
                $clean_hashpass = filter_var($hashpass, FILTER_SANITIZE_STRING);
                $clean_userID = filter_var($userID, FILTER_SANITIZE_STRING);
                $sanitized_hashpass = mysqli_real_escape_string($conn, $clean_hashpass);    
                $sanitized_userID = mysqli_real_escape_string($conn, $clean_userID);    

                $queryGetSignature = $conn->prepare("SELECT digital_signature, vector FROM tbl_user_signature WHERE `userID` = ? ");
                $queryGetSignature->bind_param('s', $sanitized_userID);
                $queryGetSignature->bind_result($signature, $vector); 
                $queryGetSignature->execute();
                $rows = $queryGetSignature->get_result();
                $value = $rows->fetch_array(MYSQLI_ASSOC);
            
                $signature = $value['digital_signature'] ?? '';
                $vector = $value['vector'] ?? '';

                $ciphering = "AES-128-CTR";
                $iv_length = openssl_cipher_iv_length($ciphering);
                $options = 0;
                $encryption = openssl_encrypt($sanitized_hashpass, $ciphering, $signature, $options, $vector);

                $clean_encryption= filter_var($encryption, FILTER_SANITIZE_STRING);
                $sanitized_encryption = mysqli_real_escape_string($conn, $clean_encryption);

                
                $queryUpdate = $pdo->prepare("UPDATE `tbluser` SET `password`= :upassword WHERE `email`=:email");
                $queryDelete =  $pdo->prepare("DELETE FROM `tbl_password_reset` WHERE `email` = :email");

                $queryUpdate->bindParam(':upassword', $sanitized_encryption);
                $queryUpdate->bindParam(':email', $sanitized_email);
                $queryDelete->bindParam(':email', $sanitized_email);
                
                $queryUpdate->execute();
                $queryDelete->execute();

                goto2("login.php","Congratulations! Your password has been updated successfully.");
                    }		
                }
                ?>
    </div>
</body>
</html>
        