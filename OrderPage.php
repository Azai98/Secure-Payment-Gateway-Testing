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

    function checkOrder() //before send order, there must be item to be selected
    {
        var itemArray = [parseInt(document.getElementsByName("cheesepizza")[0].value), parseInt(document.getElementsByName("pepperonipizza")[0].value), 
                              parseInt(document.getElementsByName("greekpizza")[0].value), parseInt(document.getElementsByName("NeapolitanPizza")[0].value),
                              parseInt(document.getElementsByName("SicilianPizza")[0].value), parseInt(document.getElementsByName("DetroitPizza")[0].value),
                              parseInt(document.getElementsByName("SET-A")[0].value), parseInt(document.getElementsByName("SET-B")[0].value),
                              parseInt(document.getElementsByName("SET-C")[0].value), parseInt(document.getElementsByName("SET-D")[0].value)];
        var itemCheck = [10];
        var counter = 0;

        for(var i = 0; i<itemArray.length; i++){
            if(itemArray[i] == 0){
                itemCheck.push(false);
            }
            else{
                itemCheck.push(true);
            }
        }

        for(var i = 0; i<itemCheck.length; i++){
            if(itemCheck[i] == false){
               counter++;
            }
        }

        if(counter == 10){
            window.alert("You must HAVE select any of the item to proceed.");
            return false;
        }
        else{
            true
        }
    }   
    </script>   
    </head>
    <body>
            <div class = "header">
                <div class = "header-left">
                <h3><b>Welcome back, <?php echo  $_SESSION['userName'] ?></b></a></h3>
                </div>
                <div class = "header-title">
                <img src="images\studiologo.png" width="100px" height="90px" alt="logo" class="logo">
                <h2>Azai&Chen Pizza.co</h2>
                </div>
                <div class = "header-right">
                <h3><a data-scroll href="orderhistory.php"><b>Order History</b></a></h3>
                </div>
                <div class = "header-right">
                <h3><a data-scroll href="logout.php"><b>User Logout</b></a></h3>
                </div>
            </div>
            <div class = "pagegrid">
            <div class = "box-2">
                <div class="box-title">
                        <h2>Pizza Menu</h2>
                </div>
                <form action="ConfirmPage.php" method="POST">
                    <!-- CSRF Token and its hidden by input type-->
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">
                    <table width="500px">
                        <tr>
                            <th width="10%" text-align="left">No.</th>
                            <th width="55%" text-align="left">Pizza Name</th>
                            <th width="20%" text-align="left">Pizza Price (RM)</th>
                            <th width="15%" text-align="right">Qty</th>
                        </tr>
                        <tr>
                            <td>
                                1  
                            </td>
                            <td>
                                Cheese pizza     
                            </td>
                            <td>
                                $10.00     
                            </td>
                            <td>
                                <input type="number" name="cheesepizza" id="cheesepizza" min="0" value="0" style="width: 4em"/>
                            </td>
                        </tr>
                        <tr>    
                            <td>
                               2  
                            </td>
                            <td>
                                Pepperoni Pizza     
                            </td>
                            <td>
                                $12.00     
                            </td>
                            <td>
                                <input type="number" class="form-control" name="pepperonipizza" id="pepperonipizza" min="0" value="0" style="width: 4em"/>
                            </td>
                        </tr> 
                        <tr>   
                            <td>
                               3 
                            </td>
                            <td>
                                Greekpizza     
                            </td>
                            <td>
                                $15.00     
                            </td>
                            <td>
                                <input type="number" class="form-control" name="greekpizza" id="greekpizza" min="0" value="0" style="width: 4em"/>
                            </td>
                         </tr> 
                         <tr>   
                            <td>
                               4
                            </td>
                            <td>
                                 Neapolitan Pizza     
                            </td>
                            <td>
                                $18.00     
                            </td>
                            <td>
                                <input type="number" class="form-control" name="NeapolitanPizza" id="NeapolitanPizza" min="0" value="0" style="width: 4em"/>
                            </td>
                         </tr>  
                         <tr>   
                            <td>
                               5
                            </td>
                            <td>
                                Sicilian Pizza     
                            </td>
                            <td>
                                $20.00     
                            </td>
                            <td>
                                <input type="number" class="form-control" name="SicilianPizza" id="SicilianPizza" min="0" value="0" style="width: 4em"/>
                            </td>
                         </tr>  
                         <tr>   
                            <td>
                               6
                            </td>
                            <td>
                                Detroit Pizza 
                            </td>
                            <td>
                                $22.00     
                            </td>
                            <td>
                                <input type="number" class="form-control" name="DetroitPizza" id="DetroitPizza" min="0" value="0" style="width: 4em"/>
                            </td>
                         </tr>  
                         <tr>   
                            <td>
                               7
                            </td>
                            <td>
                                Pizza family Set A (2 Greekpizza, 1 Pepperoni pizza, 2 bottle Pepsi)   
                            </td>
                            <td>
                                $50.00     
                            </td>
                            <td>
                                <input type="number" class="form-control" name="SET-A" id="SET-A" min="0" value="0" style="width: 4em"/>
                            </td>
                         </tr>  
                         <tr>   
                            <td>
                                8
                            </td>
                            <td>
                                Pizza family Set B (2 Detroit, 1 Greekpizza pizza, 2 bottle Pepsi)     
                            </td>
                            <td>
                                $60.00     
                            </td>
                            <td>
                                <input type="number" class="form-control" name="SET-B" id="SET-B" min="0" value="0" style="width: 4em"/>
                            </td>
                         </tr>    
                         <tr>   
                            <td>
                                9
                            </td>
                            <td>
                                Pizza family Set C (3 Greekpizza, 2 Pepperoni pizza, 2 bottle Pepsi)     
                            </td>
                            <td>
                                $70.00     
                            </td>
                            <td>
                                <input type="number" class="form-control" name="SET-C" id="SET-C" min="0" value="0" style="width: 4em"/>
                            </td>
                         </tr>  
                         <tr>   
                            <td>
                                10
                            </td>
                            <td>
                                Pizza family Set D (3 Detroit, 3 Greekpizza pizza, 2 bottle Pepsi)     
                            </td>
                            <td>
                                $80.00     
                            </td>
                            <td>
                                <input type="number" class="form-control" name="SET-D" id="SET-D" min="0" value="0" style="width: 4em"/>
                            </td>
                         </tr>  
                    </table>   
                    <div class="button-wrapper">
                    <button type="submit"  name="submit" id="submit" class="button2" onclick="return checkOrder()"> Order </button>
                    <button type="reset" value="Reset order" name="reset" class="button2"> Clear </button>
                    </div>
                </form>
            </div>
            <div class = "box-3">
                <div class="box-title">
                        <h2>Current Orders</h2>
                </div>
                    <?php
                          mysqli_select_db($conn,"SP_Assignment"); 
                          $pdo = new PDO('mysql:host=localhost;dbname=SP_Assignment', 'root',"");
                          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                          $userID = $_SESSION['userID'];
                          $clean_userID = filter_var($userID, FILTER_SANITIZE_STRING);
                          $isdelivered = 'N';
                          $sanitized_userID = mysqli_real_escape_string($conn, $clean_userID);
                          $sql = 'SELECT orderID FROM order_tbl
                          WHERE `isdelivered` = :isdelivered AND `userID` = :userID
                          ORDER BY orderID ASC';
                          $stmt = $pdo->prepare($sql);
                          $stmt->bindParam(':isdelivered', $isdelivered);
                          $stmt->bindParam(':userID', $sanitized_userID);
                          $stmt->execute();

                        if(count($stmt->fetchAll()) == 0){
                            ?>
                             <p class="fill">"Currently you have no orders. Create your order now!" </p>
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
                        $isdelivered = 'N';
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
                        <th><p style="font-size: 10px;"> Item price (RM) </p></th>
                        <th><p style="font-size: 10px;"> Quantity </p></th>
                        <th><p style="font-size: 10px;"> Subtotal (RM) </p></th>
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
         </div>
    </body>
</html>

