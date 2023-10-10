<body>
<?php
include('./config/settings.php');
require_once('./config/function.php');
session_start();

//CSRF Token
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = md5(uniqid(mt_rand(), true));
}


if (!empty(isset($_POST['uname']))) {
    if($_POST['token'] == $_SESSION['token']) { 
    $userN = $_POST['uname'];
    $passW = $_POST['password'];

    mysqli_select_db($conn,"sp_assignment"); ///select database as default
    //Connect PDO Database
    $pdo = new PDO('mysql:host=localhost;dbname=SP_Assignment', 'root',"");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $clean_userN = filter_var($userN, FILTER_SANITIZE_STRING);
    $sanitized_userN = mysqli_real_escape_string($conn, $clean_userN);

    $queryCheck = $pdo->prepare("SELECT userID FROM tbluser WHERE `username` = :userN ");
    $queryCheck->execute([':userN' => $sanitized_userN]);
    $userID = $queryCheck->fetch(PDO::FETCH_ASSOC)['userID'] ?? '';


    $clean_userID = filter_var($userID, FILTER_SANITIZE_STRING);
    $sanitized_userID = mysqli_real_escape_string($conn, $clean_userID);
    $queryGetSignature = $conn->prepare("SELECT digital_signature, vector FROM tbl_user_signature WHERE `userID` = ? ");
    $queryGetSignature->bind_param('s', $sanitized_userID);
    $queryGetSignature->bind_result($signature, $vector); 
    $queryGetSignature->execute();
    $rows = $queryGetSignature->get_result();
    $value = $rows->fetch_array(MYSQLI_ASSOC);

    $signature = $value['digital_signature'] ?? '';
    $vector = $value['vector'] ?? '';

    //recalculate hashpassword
    $hashpass = md5($passW.$userID);  
    $clean_hashpass = filter_var($hashpass, FILTER_SANITIZE_STRING);
    $sanitized_hashpass = mysqli_real_escape_string($conn, $clean_hashpass);
    $status=logincheck(trim($sanitized_userID),trim($sanitized_hashpass),trim($signature),trim($vector));
    
    if ($status==1){
        $_SESSION['userID']=$userID;
        $_SESSION['userName']=$userN;
        //TO prevent client-state-manipulation
        $_SESSION['sessionID'] = session_create_id($userN.'orderpizza');
        $_SESSION['token'] = md5(uniqid(mt_rand(), true));
        goto2("OrderPage.php","Welcome to Pizza Ordering page");
    }
     else {
        echo "You have entered the wrong username or password. Please try again. <br>";
        ?>
        <a href="login.php" class="final"><b>Back</b></a> <br><br>
        <?php
    }
    }else{
        echo "token is not valid. Request has been rejected.";
        unset($_SESSION['token']);
        goto2("Login.php","Bad request, login to try again.");
        exit;
    }
} else{
    ?>
            <link rel="stylesheet" href="css/loginstyle.css"> <!--Import CSS-->
                <div class = "header">
                    <img src="images\studiologo.png" width="100px" height="90px" alt="logo" class="logo">
                    <h2>Azai&Chen Pizza.co</h2>
                </div>
                <form method="POST" name="loginform" autocomplete="off">
                   <!-- CSRF Token and its hidden by input type-->
                  <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">
                  <div class="box">
                      <div class="box-title">
                        <img src="images\loginlogo.png" width="40px" height="35px" alt="logo">
                        <h3>User Login</h3>
                      </div>
                        <label for="uname"><b>Username</b></label>
                        <input type="text" placeholder="Enter Username" name="uname" class="input-box"
                        value="<?php if (isset($cu)){ echo  $cu;} ?>" required>

                        <label for="password"> <b>Password</b></label>
                        <input type="password" placeholder="Enter Password" name="password" class="input-box" autocomplete="password"
                        value="<?php if (isset($cp)){ echo  $cp;} ?>" required>

                        <div class="container" style="backgroud-color:#f1f1f1">
                            <button type="submit" class="button1">Login</button>
                            <button type="reset" class="button1">Clear</button>
                        </div>
                    <a href="register.php" class="final">Not a user yet? Sign up now!</a> <br><br>
                    <a href="forgetpassword.php" class="final">Password recovery</a>
                </div>
                </form>
<?php } ?>

</body>
