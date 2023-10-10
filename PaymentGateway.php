<?php
    session_start();
    include('./config/settings.php');
    require_once('./config/function.php');
    include('./config/session.php'); 
?>
<?php
    
    //Get Token from INPUT
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);

    //Check CSRF Token
    if (!$token || $token !== $_SESSION['token']) {
        echo "token is not valid. Request has been rejected.";
        unset($_SESSION['token']);
        goto2("Login.php","Bad request, login to try again.");
    } else {
        $card_number = $_POST['card_number'];
        $cvv_number = $_POST['cvv_number'];
        $sessionID = $_SESSION['sessionID'];
        $userName = $_SESSION['userName'];
        $userID = $_SESSION['userID'];

        //select database as default
        mysqli_select_db($conn,"SP_Assignment"); 

        //Connect PDO Database
        $pdo = new PDO('mysql:host=localhost;dbname=SP_Assignment', 'root',"");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //Sanitize Card Number to prevent CSS Attack
        $clean_card_number = filter_var($card_number, FILTER_SANITIZE_STRING);
        $clean_cvv_number  = filter_var($cvv_number, FILTER_SANITIZE_STRING);

        //Use parameterized SQL Statement to prevent SQL Injection Attack
        $sanitized_cardNumber = mysqli_real_escape_string($conn, $clean_card_number);
        $sanitized_cvvNumber = mysqli_real_escape_string($conn, $clean_cvv_number);

        $query = $pdo->prepare("SELECT * FROM creditcard_table WHERE `creditcard_number` = :cardNumber AND `creditcard_cvv` = :cvvNumber");
        $query->execute([':cardNumber' => $sanitized_cardNumber, ':cvvNumber' => $sanitized_cvvNumber]);

        //check If the credit card Number Correct
        if(count($query->fetchAll()) > 0){
            
            //retrieve data from table credit card
            $sql = "SELECT * FROM `creditcard_table` WHERE `creditcard_number`= ? AND `creditcard_cvv` = ? ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', $sanitized_cardNumber, $sanitized_cvvNumber);
            $stmt->execute();
            $numOrderRows = $stmt->get_result();
            $row = $numOrderRows->fetch_array(MYSQLI_ASSOC);
            $creditcard_number = $row['creditcard_number'];
            $creditcard_cvv =  $row['creditcard_cvv'];
            $creditcard_amount = $row['creditcard_amount'];

            // $sql = "SELECT * FROM `creditcard_table` WHERE `creditcard_number`='".$sanitized_cardNumber."' AND `creditcard_cvv` = '".$sanitized_cvvNumber."' ";
            // $query = mysqli_query($conn,$sql);
            // $row = mysqli_fetch_assoc($query);

            //Resanitized again to update all data back to table to prevent XSRF and XSS
            $clean_card_number = filter_var($creditcard_number, FILTER_SANITIZE_STRING);
            $clean_cvv_number  = filter_var($creditcard_cvv, FILTER_SANITIZE_STRING);
            $clean_userName = filter_var($userName, FILTER_SANITIZE_STRING);
            $clean_userID = filter_var($userID, FILTER_SANITIZE_STRING);
            $clean_sessionID = filter_var($sessionID, FILTER_SANITIZE_STRING);

            $sanitized_cardNumber = mysqli_real_escape_string($conn, $clean_card_number);
            $sanitized_cvvNumber = mysqli_real_escape_string($conn, $clean_cvv_number);
            $sanitized_userName = mysqli_real_escape_string($conn, $clean_userName);
            $sanitized_userID= mysqli_real_escape_string($conn, $clean_userID);
            $sanitized_sessionID = mysqli_real_escape_string($conn, $clean_sessionID);

            //retrieve data from order table to get the price
            $sql = "SELECT * FROM `temporary_order_tbl` WHERE `session_id`= ? AND `username` = ? ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', $sanitized_sessionID, $sanitized_userName);
            $stmt->execute();
            $numOrderRows = $stmt->get_result();
            $row = $numOrderRows->fetch_array(MYSQLI_ASSOC);

            // $sql = "SELECT * FROM `temporary_order_tbl` WHERE `session_id`='".$sanitized_sessionID."' AND `username` = '".$sanitized_userName."' ";
            // $query = mysqli_query($conn,$sql);
            // $row = mysqli_fetch_assoc($query);
            $totalprice = $row['order_price'];
            $orderID = $row['orderID'];

            $clean_totalprice = filter_var($totalprice, FILTER_SANITIZE_STRING);
            $sanitized_totalprice = mysqli_real_escape_string($conn, $clean_totalprice);
            //update payment now
            if($totalprice > $creditcard_amount){
                goto2("OrderPage.php", "Payment Failed! Your amount of credit card is not sufficient!");
            }else{

            $creditcard_amount = $creditcard_amount - $totalprice;
            $clean_creditcard_amount = filter_var($creditcard_amount, FILTER_SANITIZE_STRING);    
            $sanitized_creditcard_amount = mysqli_real_escape_string($conn, $clean_creditcard_amount);
            $queryUpdate = $pdo->prepare("UPDATE creditcard_table SET `creditcard_amount` = :creditcard_amount, 
                        `last_used` = :last_used, `last_payment` = :last_payment WHERE `creditcard_cvv` = :creditcard_cvv
                         AND creditcard_number = :creditcard_number");
            
            $queryUpdate->bindParam(':creditcard_amount', $sanitized_creditcard_amount);
            $queryUpdate->bindParam(':last_used', $sanitized_userName);
            $queryUpdate->bindParam(':last_payment', $sanitized_totalprice);
            $queryUpdate->bindParam(':creditcard_cvv', $sanitized_cvvNumber);
            $queryUpdate->bindParam(':creditcard_number', $sanitized_cardNumber);
            $queryUpdate->execute();

            //order saved into order table
            $queryInsert = $pdo->prepare("INSERT INTO order_tbl (`orderID`, `order_totalprice`, `userID`, `username`, `order_time`, `isdelivered`) 
            VALUES(:orderID, :order_totalprice, :userID, :username, :order_time, :isdelivered)");  

            $status = 'N';
            date_default_timezone_set("Asia/Kuala_Lumpur");
            $curDate = date("Y-m-d H:i:s");
            $clean_orderID = filter_var($orderID, FILTER_SANITIZE_STRING);
            $clean_curDate = filter_var($curDate, FILTER_SANITIZE_STRING);
            $sanitized_orderID = mysqli_real_escape_string($conn, $clean_orderID);
            $sanitized_curDate= mysqli_real_escape_string($conn, $clean_curDate);
            
            $queryInsert->bindParam(':orderID', $sanitized_orderID);
            $queryInsert->bindParam(':order_totalprice', $sanitized_totalprice);
            $queryInsert->bindParam(':userID', $sanitized_userID);
            $queryInsert->bindParam(':username', $sanitized_userName);
            $queryInsert->bindParam(':order_time', $sanitized_curDate);
            $queryInsert->bindParam(':isdelivered', $status);
            
            //insert into order_tbl
            $queryInsert->execute();    
            
            //order details saved into order details tbl
            $total_items_details = $_SESSION['total_item_details'];        
            $queryInsertDetail = $pdo->prepare("INSERT INTO order_details_tbl (`orderID`, `item_subtotal`, `item_price`, `order_qty`, `item_name`) 
            VALUES(:orderID, :item_subtotal, :item_price, :order_qty, :item_name)");  

            foreach ($total_items_details as $value) {
                if($value[0]>0){
                    $itemQty = $value[0];
                    $itemprice = $value[1];
                    $itemName = $value[2];
                    $item_subtotal = $itemQty * $itemprice;

                    $clean_itemQty = filter_var($itemQty, FILTER_SANITIZE_STRING);
                    $clean_itemprice = filter_var($itemprice, FILTER_SANITIZE_STRING);
                    $clean_itemName = filter_var($itemName, FILTER_SANITIZE_STRING);
                    $clean_item_subtotal = filter_var($item_subtotal, FILTER_SANITIZE_STRING);
                    $sanitized_itemQty = mysqli_real_escape_string($conn, $clean_itemQty);
                    $sanitized_itemprice = mysqli_real_escape_string($conn, $clean_itemprice);
                    $sanitized_itemName = mysqli_real_escape_string($conn, $clean_itemName);
                    $sanitized_item_subtotal= mysqli_real_escape_string($conn, $clean_item_subtotal);

                    $queryInsertDetail->bindParam(':orderID', $sanitized_orderID);
                    $queryInsertDetail->bindParam(':item_subtotal', $sanitized_item_subtotal);
                    $queryInsertDetail->bindParam(':item_price', $sanitized_itemprice);
                    $queryInsertDetail->bindParam(':order_qty', $sanitized_itemQty);
                    $queryInsertDetail->bindParam(':item_name', $sanitized_itemName);
                    
                    //insert into order_details_tbl
                    $queryInsertDetail->execute();  
                }  
            }    

            goto2("OrderPage.php", "Payment Successfully! Your amount of credit card is deducted and your order will be processed soon!");
            }
        }
        else{
            goto2("OrderPage.php", "Payment Failed! Invalid Card Number and CVV Number!");
        }
    }
?>