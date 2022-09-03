<?php
//echo'<pre>'; print_r($data['']); die;
?>
<!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                <meta http-equiv="X-UA-Compatible" content="ie=edge">
                <style>
                * {
                    font-size: 15px;
                    font-family: "Arial";
                }
                
                .centered {
                    text-align: center;
                    align-content: center;
                }
                table, th, td {
                    border: 1px solid;
                    border-collapse: collapse;
                   
                  }
                .ticket {
                    width:108mm;
                    height:108mm;
                }
                .imu{ 
                    width:350px;
                }
                img {
                  display: block;
                  margin-left: auto;
                  margin-right: auto;
                }
                .ff{
                    margin-top: 7px;
                    font-size: 28px;
                    margin-bottom: 2px;
                }
                .kk{
                    margin-top: 4px;
                    margin-bottom: 2px;
                    font-size: 20px;
                }
                .address{
                    font-size: 19px;
                    margin-top: 2px;
                }
                
                @media print {
                    .hidden-print,
                    .hidden-print * {
                        display: none !important;
                    }
                    @page { margin: 5px 19px; }
                }
                </style>
            </head>
            <body onload="window.print()">
                <div class="ticket">
                    <table style="width:100%" style="background-color: aquamarine;">
                        <tr>
                            <td colspan="3" style="text-align:center;">
                                <img src="/assets/img/logo_se.jpg" class="imu">
                            </td>
                            
                        </tr>
                        <tr>
                            <td width="30%" ><b style="margin-left: 8px; ">LR No.</b></td>
                            <td colspan ="2" style="text-align:center;"><h3 class="ff"><?php echo $data['id'] ?></h3></td>
                        </tr>
                        <tr>
                            <td width="30%"><b style="margin-left: 8px;">Order ID:</b></td>
                            <?php if(empty($data['order_id'])){
                                foreach($data['consignment_items'] as $order)
                                    {
                                        $orders[] = $order['order_id'];
                                        $invoices[] = $order['invoice_no'];
                                    }
                                    // echo'<pre>';print_r($orders[0]); die;
                                        // $order_item = implode(',', $orders);
                                        // $invoic_no = implode(',', $invoices);
                                    ?>
                            <td colspan ="2" style="text-align:center;"><b><?php echo $orders[0] ?></b></td>
                            <?php }else{ ?>
                                <td colspan ="2" style="text-align:center;"><b><?php echo $data['order_id'] ?></b></td>
                                <?php } ?>
                        </tr>
                        <tr>
                            <td width="30%"><b style="margin-left: 8px;">Invoice No:</b></td>
                            <?php if(empty($data['invoice_no'])){ ?>
                            <td colspan ="2" style="text-align:center;"><b><?php echo $invoices[0] ?></b></td>
                            <?php }else{ ?>
                                <td colspan ="2" style="text-align:center;"><b><?php echo $data['invoice_no'] ?></b></td>
                                <?php } ?>
                        </tr>
                        <tr>
                            <td width="30%"><b style="margin-left: 8px;">Client:</b></td>
                            <td colspan ="2" style="text-align:center;"><b><?php echo $baseclient ?></b></td>
                        </tr>
                        <tr>
                            <td width="30%"><b style="margin-left: 8px;">NO Of Boxes:</b></td>
                            <td colspan ="2" style="text-align:center;"><p style="font-size: 36px;margin: 1px;font-weight:bold;position: relative;top: 8px;"><?php echo $data['total_quantity'] ?><p></td>
                        </tr>
                        <tr>
                            <td width="30%" style="text-align:center;"><img src="/assets/img/barcode.png" style="width: 80px;"></td>
                            <td colspan ="2">
                                <div class="row" style="margin-left: 8px;">
                                <p  style="font-weight:bold">Ship to: <br/><?php echo $data['shipto_detail']['nick_name'].','. $data['shipto_detail']['address_line1'].','. $data['shipto_detail']['address_line2']; ?></p>
                                </div>
                            </td>
                        </tr>
                        <tr>
                           <th>PIN Code</th>
                           <th>Zone</th>
                           <th>Delivery Station</th>
                        </tr>
                        <tr>
                            <td style="text-align:center;"><h3 class="kk"><?php echo $data['shipto_detail']['postal_code'] ?></h3></td>
                            <td style="text-align:center;"><h3 class="kk"></h3></td>
                            <td style="text-align:center;"><h3 class="kk"><?php echo $data['shipto_detail']['city'] ?></h3></td>
                        </tr>
                        
                    </table>
                </div>
                <script type="text/javascript">
      window.onload = function() { window.print(); }
 </script>
            </body>       
</html>