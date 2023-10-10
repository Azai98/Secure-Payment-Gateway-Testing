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

    //Get Token from INPUT and Sanitize again
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);

    //Check CSRF Token
    if (!$token || $token !== $_SESSION['token']) {
        echo "token is not valid. Request has been rejected.";
        unset($_SESSION['token']);
        goto2("Login.php","Bad request, login to try again.");
    } else {
        $submit = $_POST['submit'];
        $sessionID = $_POST['sessionID'];
        
        if($submit == 'true'){
                ?>
                <body>
                    <div class = "header">
                        <img src="images\studiologo.png" width="100px" height="90px" alt="logo" class="logo">
                        <h2>Payment Page</h2>
                    </div>
                    <div class = "box-2">
                        <div class="box-title">
                            <h2>Ordering Payment Page</h2>
                        </div>
                        <div class="position"> 
                            <form action="PaymentGateway.php" method="POST">
                                <table>
                                   
                                <!-- CSRF Token -->   
                                <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? ''?>">
                                <tr>
                                <td>   
                                    <p > Credit Card </p>
                                </td>    
                                <td>
                                    <input type="text"  name="card_number" id="card_number" placeholder="Type your Card's number">
                                </td>     
                                </tr>
                                <tr>
                                <td>   
                                <p > CVV Number </p>
                                </td>    
                                <td>
                                    <input type="text"  name="cvv_number" id="card_number" placeholder="Type you Card's CVV"> 
                                </td>  
                                </tr>
        
                                </table> 
                                <button type="submit" class="button1" name="submit" id="submit" value="true"> Pay now </button>
                            </form>
                            </div>
                        </div>
                    </div>
                </body>
               <?php 
            }    
            else{
                goto2("OrderPage.php","You are redirected back to the order page.");
            }
    }
    ?>
</html>