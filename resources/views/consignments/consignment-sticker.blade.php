<?php
//echo "<pre>";print_r($data);die;?>
<!DOCTYPE html>
        <html lang="en">
            <head>
                <style>
                * {
                    font-size: 12px;
                    font-family: "sans-serif";
                    margin:1px;
                    font-weight:400;
                }
                
                table, th, td {
                    border: 1px solid;
                    border-collapse: collapse;
                   
                  }
                .ticket {
                    width:245pt;
                    height:310pt;
                }

                img {
                  display: block;
                  margin-left: auto;
                  margin-right: auto;
                }

                .kk{
                    margin-top: 4px;
                    margin-bottom: 2px;
                    font-size: 16px;
                    font-weight:bold;
                }
                .address{
                    font-size: 19px;
                    margin-top: 2px;
                }
                .logo{
                    height:72px;
                }
                
                </style>
            </head>
            <body>
            @for ($i = 1; $i <= $boxes; $i++)
                <div class="ticket">
                    <div class="logo"></div>
                    <table style="width:100%">
                        <tr>
                            <td width="30%" ><b style="margin-left: 8px;padding:5px 0px">LR No.</b></td>
                            <td colspan ="2" style="text-align:center; padding:5px 0px"><p style="font-size: 36px;font-weight:bold;"><?php echo $data['id'] ?></p></td>
                        </tr>
                        <tr>
                            <td width="30%"><b style="margin-left: 8px;padding:5px 0px">Order ID:</b></td>
                            <?php if(empty($data['order_id'])){
                                foreach($data['consignment_items'] as $order)
                                    {
                                        $orders[] = $order['order_id'];
                                        $invoices[] = $order['invoice_no'];
                                    }
                                     
                                        // $order_item = implode(',', $orders);
                                        // $invoic_no = implode(',', $invoices);
                                    ?>
                                <td colspan ="2" style="text-align:center;padding:5px 0px;"><b><?php echo $orders[0] ?></b></td>

                              <?php }else{ ?>
                                <td colspan ="2" style="text-align:center;padding:5px 0px"><b><?php echo $data['order_id'] ?></b></td>
                           <?php } ?>
                        </tr>
                        <tr>
                            <td width="30%"><b style="margin-left: 8px;padding:5px 0px">Invoice no:</b></td>
                            <?php if(empty($data['invoice_no'])){ ?>
                            <td colspan ="2" style="text-align:center;padding:5px 0px"><b><?php echo $invoices[0] ?></b></td>
                            <?php }else{ ?>
                                <td colspan ="2" style="text-align:center;padding:5px 0px"><b ><?php echo $data['invoice_no'] ?></b></td>
                                <?php } ?>
                        </tr>
                        <tr>
                            <td width="30%"><b style="margin-left: 8px;padding:5px 0px">Client:</b></td>
                            <td colspan ="2" style="text-align:center;padding:5px 0px"><b style="font-size: 18px;font-weight:bold;"><?php echo $baseclient ?></b></td>
                        </tr>
                        <tr>
                            <td width="30%"><b style="margin-left: 8px;">No Of Box:</b></td>
                            <td colspan ="2" style="text-align:center;"><p style="font-size: 18px;font-weight:bold;"><?php echo $i?> of <?php echo $data['total_quantity'];?><p></td>
                        </tr>
                        <tr>
                            <td width="30%" style="text-align:center;"></td>
                            <td colspan ="2">
                                <div class="row" style="margin-left: 8px;">
                                <p style="font-size: 12px;text-decoration:underline;">Ship to<p><p style="font-size: 16px;font-weight:bold;"> <?php echo $data['shipto_detail']['nick_name'].','. $data['shipto_detail']['address_line1'].','. $data['shipto_detail']['address_line2']; ?></p>
                                </div>
                            </td>
                        </tr>
                        <tr>
                           <th>PIN Code</th>
                           <th colspan="2">Delivery Station</th>
                        </tr>
                        <tr>
                            <td style="text-align:center;"><h3 class="kk"><?php echo $data['shipto_detail']['postal_code'] ?></h3></td>
                            <td colspan="2" style="text-align:center;"><h3 class="kk"><?php echo $data['shipto_detail']['city'] ?></h3></td>
                        </tr>
                        
                    </table>
                </div>
                @endfor
            </body>       
</html>
