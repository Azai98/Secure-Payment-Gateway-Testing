<?php 
    include('./config/settings.php');
    require_once('./config/function.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Registration</title>
    <link rel="stylesheet" href="css/registerstyle.css"/>   <!--Import CSS-->
</head>
<script>
    function checkField() //check fields
    {
        //checkEmail
        var checkEmail = document.forms["register"]["email"].value;

        if(checkEmail != ""){
        if (/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(checkEmail))
        {
            return (true);
        }
            return (false);
        }
    }
</script>
<body>
<?php
session_start();

//CSRF Token
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = md5(uniqid(mt_rand(), true));
}
    // When form submitted, insert values into the database.
    if (isset($_POST['submit'])) {
        if($_POST['token'] == $_SESSION['token']) { 
        $password    = $_POST['password'];
        $name    = $_POST['uname'];
        $email = $_POST['email'];
        $salt = uniqid(rand(0, 1000000)); //Unique salt for userID
        $hashpass = md5($password.$salt); //Strengthen the password and ensure that there will never have the same password pattern in a system

        //select database as default
        mysqli_select_db($conn,"SP_Assignment");

        //Connect PDO Database
        $pdo = new PDO('mysql:host=localhost;dbname=SP_Assignment', 'root',"");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $queryInsert = $pdo->prepare("INSERT into `tbluser` (`userID` , `username`, `email`, `password`)
        VALUES (:salt, :username , :email , :upassword)");
        $queryInsertSignature = $pdo->prepare("INSERT into `tbl_user_signature` (`userID` , `digital_signature`, `vector`)
        VALUES (:userID , :user_signature, :vector)");  

        //create digital signature for user for assymetric encryption
        $signature = generateRandomString(30);
        $vector = generateRandomString(16);
        $ciphering = "AES-128-CTR";

        // Use OpenSSl Encryption method
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;
       
        // Store the encryption key
        $encryption_key = $signature;
        $encryption_iv = $vector;
        $encryption = openssl_encrypt($hashpass, $ciphering, $encryption_key, $options, $encryption_iv);

        //Sanitize Data to prevent CSS Attack
        $clean_name = filter_var($name, FILTER_SANITIZE_STRING);
        $clean_email = filter_var($email, FILTER_SANITIZE_STRING);
        $clean_userID = filter_var($salt, FILTER_SANITIZE_STRING);
        $clean_signature = filter_var($signature, FILTER_SANITIZE_STRING);
        $clean_encryption_iv= filter_var($encryption_iv, FILTER_SANITIZE_STRING);
        $clean_encryption= filter_var($encryption, FILTER_SANITIZE_STRING);
                
        //Use parameterized SQL Statement to prevent SQL Injection Attack
        $sanitized_name = mysqli_real_escape_string($conn, $clean_name);
        $sanitized_email = mysqli_real_escape_string($conn, $clean_email);
        $sanitized_userID = mysqli_real_escape_string($conn, $clean_userID);
        $sanitized_signature= mysqli_real_escape_string($conn, $clean_signature);
        $sanitized_encryption_iv = mysqli_real_escape_string($conn, $encryption_iv);
        $sanitized_encryption = mysqli_real_escape_string($conn, $clean_encryption);

        $queryInsert->bindParam(':salt', $sanitized_userID);
        $queryInsert->bindParam(':username', $sanitized_name);
        $queryInsert->bindParam(':email', $sanitized_email);
        $queryInsert->bindParam(':upassword', $sanitized_encryption);

        $queryInsertSignature->bindParam(':userID', $sanitized_userID);
        $queryInsertSignature->bindParam(':user_signature', $sanitized_signature);
        $queryInsertSignature->bindParam(':vector', $sanitized_encryption_iv);

        $sucessmsg = "Account successfully created";
        $failmsg = "Something wrong, please try again";

        
        $queryCheck = $pdo->prepare("SELECT * FROM tbluser WHERE `email` = :email ");
        $queryCheck->execute([':email' => $sanitized_email]);
        
         //Validate the email is used in database
        
        if(count($queryCheck->fetchAll()) > 0){
                echo $failmsg;
                goto2("Login.php","Email has been registered, please use another email and register again.");
                }
        else{
                $queryInsert->execute();
                $queryInsertSignature->execute();
                ?>
                <br>
                <p> &emsp; <?php echo $sucessmsg ?> </p>
                <p> &emsp; Test your account now! <a href="login.php">Login here!</a></p>
                <?php
            }
        }else{
            echo "token is not valid. Request has been rejected.";
            unset($_SESSION['token']);
            goto2("Login.php","Bad request, login to try again.");
            exit;
        }    
    } else {
?> 
    <div class="header">
        <h2>Azai&Chen Pizza.co</h2>
    </div>
    <form class="form" name="register" action="" method="post" autocomplete="off"  enctype="multipart/form-data">
        <!-- CSRF Token and its hidden by input type-->
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">
        <h1 class="register-title">Registration</h1>
    <div class="register-container">
        <label for="uname" class="login-label"><b>Username*</b></label>
        <input type="text" class="login-input" name="uname" placeholder="Username" required size=30> <br>
        <label for="email" class="login-label"><b>Email*</b></label>
        <input type="text" class="login-input" name="email" placeholder="Email" required size=30> <br>
        <label for="password" class="login-label"><b>Password*</b></label>
        <input type="password" class="login-input" name="password" placeholder="Password" autocomplete="password" required size=30> <br>
       </select>
        <input type="submit" name="submit" value="Register" class="register-button" onClick="checkField();">
        <p class="link">Already have an account? <a href="login.php">Login here!</a></p>
    </div>   
    </form>
            
<?php
    }
?>
</body>
</html>
