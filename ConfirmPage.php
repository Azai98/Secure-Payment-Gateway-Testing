<?php
    session_start();
    include('./config/settings.php');
    require_once('./config/function.php');
    include('./config/session.php'); 
?>
<!DOCTYPE html>
<html>
    <head>
       	<!-- Main Stylesheet -->
           <link rel="stylesheet" href="css/loginstyle.css"> <!--Import CSS-->
     <style>
      th{
        text-align: left;
        width: 150px;
      }
    </style> 
    </head>

    <?php
        
        //Get Token from INPUT
        $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);

        //Check CSRF Token
        if (!$token || $token !== $_SESSION['token']) {
            echo "token is not valid. Request has been rejected.";
            unset($_SESSION['token']);
            goto2("Login.php","Bad request, login to try again.");
            exit;
        } else {
            //Calculate Total Price
            $cheesepizza = (int)$_POST['cheesepizza'] * 10;
            $pepperonipizza = (int)$_POST['pepperonipizza'] * 12;
            $greekpizza = (int)$_POST['greekpizza'] * 15;
            $NeapolitanPizza = (int)$_POST['NeapolitanPizza'] * 18;
            $SicilianPizza = (int)$_POST['SicilianPizza'] * 20;
            $DetroitPizza = (int)$_POST['DetroitPizza'] * 22;
            $setA = (int)$_POST['SET-A'] * 50;
            $setB = (int)$_POST['SET-B'] * 60;
            $setC = (int)$_POST['SET-C'] * 70;
            $setD =(int) $_POST['SET-D'] * 80; 

            $cheesepizza_name = "";
            $pepperonipizza_name = "";
            $greekpizza_name = "";
            $NeapolitanPizza_name = "";
            $SicilianPizza_name = "";
            $DetroitPizza_name = "";
            $setA_name = "";
            $setB_name = "";
            $setC_name = "";
            $setD_name = "";

            $cheesepizza_qty = (int)$_POST['cheesepizza'];
            if($cheesepizza_qty>0){
                $cheesepizza_name = 'Cheese pizza'; 
            }
            $pepperonipizza_qty = (int)$_POST['pepperonipizza'];
            if($pepperonipizza_qty>0){
                $pepperonipizza_name = 'Pepperoni pizza'; 
            }
            $greekpizza_qty = (int)$_POST['greekpizza'];
            if($cheesepizza_qty>0){
                $greekpizza_name = 'Greek pizza'; 
            }
            $NeapolitanPizza_qty = (int)$_POST['NeapolitanPizza'];
            if($NeapolitanPizza_qty>0){
                $NeapolitanPizza_name= 'Neapolitan Pizza'; 
            }
            $SicilianPizza_qty = (int)$_POST['SicilianPizza'];
            if($cheesepizza_qty>0){
                $SicilianPizza_name = 'Sicilian Pizza'; 
            }
            $DetroitPizza_qty = (int)$_POST['DetroitPizza'];
            if($DetroitPizza_qty>0){
                $DetroitPizza_name = 'Detroit Pizza'; 
            }
            $setA_qty = (int)$_POST['SET-A'];
            if($setA_qty>0){
                $setA_name = 'Pizza family Set A (2 Greekpizza, 1 Pepperoni pizza, 2 bottle Pepsi)'; 
            }
            $setB_qty = (int)$_POST['SET-B'];
            if($setB_qty>0){
                $setB_name = 'Pizza family Set B (2 Detroit, 1 Greekpizza pizza, 2 bottle Pepsi)'; 
            }
            $setC_qty = (int)$_POST['SET-C'];
            if($setC_qty>0){
                $setC_name = 'Pizza family Set C (3 Greekpizza, 2 Pepperoni pizza, 2 bottle Pepsi)'; 
            }
            $setD_qty = (int)$_POST['SET-D'];
            if($setD_qty>0){
                $setD_name = 'Pizza family Set D (3 Detroit, 3 Greekpizza pizza, 2 bottle Pepsi)'; 
            }

            $total_items = array($cheesepizza, $pepperonipizza, $greekpizza, $NeapolitanPizza, $SicilianPizza, $DetroitPizza, $setA, $setB, $setC, $setD);
            $total_items_details = array(array($cheesepizza_qty, 10, $cheesepizza_name), array($pepperonipizza_qty, 12, $pepperonipizza_name), array($greekpizza_qty, 15, $greekpizza_name), 
                                array($NeapolitanPizza_qty, 18, $NeapolitanPizza_name), array($SicilianPizza_qty, 20, $SicilianPizza_name),  array($DetroitPizza_qty, 22, $DetroitPizza_name), 
                                array($setA_qty, 50, $setA_name), array($setB_qty, 60, $setB_name), array($setC_qty, 70, $setC_name), array($setD_qty, 80, $setD_name),);
            $totalprice = 0;

            foreach ($total_items as $value) {
                $totalprice += $value;
            }
            
            //save into session
            $_SESSION['total_item_details'] = $total_items_details;

            //select database as default
            mysqli_select_db($conn,"SP_Assignment"); 

            //Connect PDO Database
            $pdo = new PDO('mysql:host=localhost;dbname=SP_Assignment', 'root',"");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            //Get Data from Session
            $userID = $_SESSION['userID'];
            $userName = $_SESSION['userName'];
            $sessionID = $_SESSION['sessionID'];
            $orderID = uniqid();

            //Sanitize Data to prevent CSS Attack
            $clean_userID = filter_var($userID, FILTER_SANITIZE_STRING);
            $clean_sessionID = filter_var($sessionID, FILTER_SANITIZE_STRING);
            $clean_userName = filter_var($userName, FILTER_SANITIZE_STRING);
            $clean_orderID = filter_var($orderID, FILTER_SANITIZE_STRING);

            //Use parameterized SQL Statement to prevent SQL Injection Attack
            $sanitized_userID = mysqli_real_escape_string($conn, $clean_userID);
            $sanitized_sessionID = mysqli_real_escape_string($conn, $clean_sessionID);
            $sanitized_userName  = mysqli_real_escape_string($conn, $clean_userName);
            $sanitized_orderID = mysqli_real_escape_string($conn, $clean_orderID);
            $sanitized_totalprice = mysqli_real_escape_string($conn, $totalprice);

            $queryInsert = $pdo->prepare("INSERT INTO temporary_order_tbl (`session_id`, `order_price`, `userID`, `username`, `orderID`) 
                           VALUES(:sessionID, :totalprice, :userID, :username, :orderID)");
            $queryUpdate = $pdo->prepare("UPDATE temporary_order_tbl SET `session_id` = :sessionID, `order_price` = :totalprice , 
                           `orderID` = :orderID  , `username` = :username WHERE `userID` = :userID");

            $queryInsert->bindParam(':sessionID', $sanitized_sessionID);
            $queryInsert->bindParam(':totalprice', $sanitized_totalprice);
            $queryInsert->bindParam(':userID', $sanitized_userID);
            $queryInsert->bindParam(':orderID', $sanitized_orderID);
            $queryInsert->bindParam(':username', $sanitized_userName);
            
            $queryUpdate->bindParam(':sessionID', $sanitized_sessionID);
            $queryUpdate->bindParam(':totalprice', $sanitized_totalprice);
            $queryUpdate->bindParam(':userID', $sanitized_userID);
            $queryUpdate->bindParam(':orderID', $sanitized_orderID);
            $queryUpdate->bindParam(':username', $sanitized_userName);

            $queryCheck = $pdo->prepare("SELECT * FROM temporary_order_tbl WHERE `userID` = :userID ");
            $queryCheck->execute([':userID' => $sanitized_userID]);

            //Validate the userID in database

            if(count($queryCheck->fetchAll()) > 0){
                $queryUpdate->execute();
            }
            else{
                $queryInsert->execute();
            }
        }
    ?>

    <body>
         <div class = "header">
            <img src="images\studiologo.png" width="100px" height="90px" alt="logo" class="logo">
            <h2>Confirm Page</h2>
         </div>
         <div class = "box-2">
                <div class="box-title">
                        <h2>Pizza Confirmation Page</h2>
                </div>
                <div class="message">
                <form action="PaymentPage.php" method="POST">
                    <!-- CSRF Token -->
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">
                    <?php echo '<p> Your order is RM'.$totalprice.'</p>' ?>
                    <!-- Use session ID replace price to prevent client state manipulation -->
                    <input type="hidden" name="sessionID" value=<?=$_SESSION['sessionID']?>>
                    <p> Do you want to confirm your order ? </p>
                    <div>
                        <button type="submit" style="width:50px;" class="button1" name="submit" id="submit" value="true"> Yes </button>
                        <button type="submit" style="width:50px; margin-left:20px;" class="button1" name="submit" id="submit" value="false"> No </button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </body>
</html>