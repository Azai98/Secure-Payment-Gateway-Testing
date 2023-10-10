<!DOCTYPE html>
<?php session_start();
include('./config/settings.php');
include('./config/session.php'); 
?>
<html>
    <head>
       	<!-- Main Stylesheet -->
           <link rel="stylesheet" href="css/orderstyle.css"> <!--Import CSS-->
     <style>
      th{
        text-align: left;
      }
      table {
        border-spacing: 10px;
      }
      
      #table_detail tr:hover {
            background-color: #F2F2F2;
      }
    </style> 
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>  
    <script type="text/javascript">  
    $(document).ready(function () {  
            $('tr.parent')  
                .css("cursor", "pointer")  
                .attr("title", "Click to expand/collapse")  
                .click(function () {  
                    $(this).siblings('.child-' + this.id).toggle();  
                });  
            $('tr[@class^=child-]').hide().children('td');  
    });  
    </script>   
    </head>
    <body>
            <div class = "header">
                <div class = "header-title">
                <img src="images\studiologo.png" width="100px" height="90px" alt="logo" class="logo">
                <h2>Azai&Chen Pizza.co</h2>
                </div>
            </div>
            <div class = "box-3">
                <div class="box-title">
                        <h2>Orders History</h2>
                </div>
                    <?php
                          mysqli_select_db($conn,"SP_Assignment"); 
                          $pdo = new PDO('mysql:host=localhost;dbname=SP_Assignment', 'root',"");
                          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                          $userID = $_SESSION['userID'];
                          $isdelivered = 'Y';
                          $clean_userID = filter_var($userID, FILTER_SANITIZE_STRING);
                          $sanitized_userID = mysqli_real_escape_string($conn, $clean_userID);
                          $sql = 'SELECT orderID FROM order_tbl
                          WHERE `isdelivered` = ? AND `userID` = ?
                          ORDER BY orderID ASC';
                          $stmt = $conn->prepare($sql);
                          $stmt -> bind_param("ss", $isdelivered, $sanitized_userID);
                          $stmt -> execute();
                          $stmt -> store_result();
                          $stmt -> fetch();
                          $numberofrows = $stmt->num_rows;
                  
                        if($numberofrows == 0){
                            ?>
                             <p class="fill">"Currently you have no orders history. Create your new order now!" </p>
                            <?php 
                        }else{
                        ?>
                            <table style="margin-left:auto; margin-right:auto" width="100%" id="table_detail" >
                            <tr>
                                <th>No</th>
                                <th>Order ID</th>
                                <th>Total Price (RM)</th>
                                <th>Order Time</th>
                                <th>Credit Card</th>
                            </tr>    
                        <?php    
                        mysqli_select_db($conn,"SP_Assignment"); 
                        $no=1;
                        $isdelivered = 'Y';
                        $sql = 'SELECT order_tbl.orderID, order_tbl.order_totalprice, order_tbl.order_time, creditcard_table.creditcard_number
                        FROM order_tbl INNER JOIN creditcard_table
                        ON order_tbl.username = creditcard_table.last_used
                        WHERE order_tbl.userID = ? AND order_tbl.isdelivered = ?
                        ORDER BY order_tbl.userID ASC';
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('ss', $sanitized_userID, $isdelivered);
                        $stmt->bind_result($orderID, $order_totalprice, $order_time, $creditcard_number); 
                        $stmt->execute();
                        $numOrderRows = $stmt->get_result();

                        while ($value = $numOrderRows->fetch_array(MYSQLI_ASSOC)){ 
                        ?>  
                        <tbody> 
                    <tr class="parent" id="row123" title="Click to expand/collapse" style="cursor: pointer;">
                        <td><?php echo  $no; ?></td>
                        <td><?php echo $value['orderID'] ; ?></td>
                        <td>$<?php echo $value['order_totalprice'] ; ?></td>
                        <td><?php echo $value['order_time'] ; ?></td>
                        <td><?php echo $value['creditcard_number'] ; ?></td>
                    </tr>

                    <?php $no++;
                        $orderID = $value['orderID'];
                        $clean_orderID= filter_var($orderID, FILTER_SANITIZE_STRING);
                        $sanitized_orderID= mysqli_real_escape_string($conn, $clean_orderID);

                        $sql = 'SELECT item_name, item_price, item_subtotal, order_qty
                        FROM order_details_tbl
                        WHERE orderID = ?
                        ORDER BY orderID ASC';
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('s', $sanitized_orderID);
                        $stmt->bind_result($item_name, $item_price, $item_subtotal, $order_qty); 
                        $stmt->execute();
                        $numRows = $stmt->get_result(); 
                        $count = 1;
                    ?>
                    
                        <tr class="child-row123" style="display: none;">
                        <th></th>
                        <th><p style="font-size: 10px;"> Item name </p></th>
                        <th><p style="font-size: 10px;"> Item price (RM)</p></th>
                        <th><p style="font-size: 10px;"> Quantity </p></th>
                        <th><p style="font-size: 10px;"> Subtotal (RM)</p></th>
                        </tr>    
                        <tr class="child-row123" style="display: table-row;">
                        
                    <?php
                        while ($row = $numRows->fetch_array(MYSQLI_ASSOC)){  
                    ?>  
                        <tr class="child-row123" style="display: none;">
                        <td></td>
                        <td style="padding-left: 3px" >
                            <p style="font-size: 10px;">
                            &ensp; <?php echo  $count; ?>. &ensp;
                            <?php echo $row['item_name'] ; ?>
                            </p>
                        </td>

                        <td style="padding-left: 3px">
                            <p style="font-size: 10px;">
                                $<?php echo $row['item_price'] ; ?>
                            </p>
                        </td>    
                        
                        <td style="padding-left: 3px">
                            <p style="font-size: 10px;">
                                <?php echo $row['order_qty'] ; ?>
                            </p>
                        </td> 
                        
                        <td style="padding-left: 3px">
                            <p style="font-size: 10px;">
                                $<?php echo $row['item_subtotal'] ; ?>
                            </p>
                        </td> 
                
                        </tr>   
                                    
                    <?php
                        $count++;
                        }   
                        ?> </tr> <?php
                    } }?>
                     </tbody>  
                    </table>
            </div>
    </body>
</html>

