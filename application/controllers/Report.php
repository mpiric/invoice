<?php

class Report extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
    }
    public function waiter_report()
    {
        $this->load->view('report/waiter_create');
        
        if (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '') {
            
            $this->load->model('order_model');
            
            $result = $this->order_model->waiter_report($_POST['waiter_id']);
            
            $response           = array();
            $response['status'] = "1";
            $response['data']   = $result;
            echo json_encode($response);
            die;
        }
        
    }
    
    public function waiter_list_all()
    {
        
        $this->load->model('order_model');
        
        $result             = $this->order_model->waiter_report_all();
        $response           = array();
        $response['status'] = "1";
        $response['data']   = $result;
        echo json_encode($response);
        die;
    }
    public function sales_list_all()
    {
        $this->load->model('order_model');
        $this->load->model('tax_main_model');
        $this->load->model('payment_model');
        
        $tax_name = $this->tax_main_model->tax_list_all();
        
        $result = $this->order_model->sales_report_all();

        $paymentTypeData = $this->payment_model->get_data();

        foreach ($paymentTypeData as $payment) {
            
            $paymentData[$payment['payment_id']] = array(
                                                        "name"=>$payment['payment_type'],
                                                        "id" => $payment['payment_id'],
                                                        "value" => 0
                                                    ); 
            
        }

        
        $details = array();
        
        $i = 0;
        $getTotalCover = $getSubtotal = $gettotalDiscountTotal = $getbill_amountTotal = $getRound = $getroundoffTotal = 0;
        foreach ($result as $order_data) {
            
            //print_r($order_data);die;
            
            $order_tax_data = $this->order_model->order_tax_data($order_data['order_id'], $order_data['sub_total'], $order_data['discount']);
            
            
            // if(!empty($order_tax_data))
            // {
            //     $order_tax_list = array();
            //     foreach ($order_tax_data as $tax_data) 
            //     {
            //          $order_tax_list[$tax_data['tax_id']] = $tax_data;
            //     }
            //     $details[$i]['order_tax'] =$order_tax_list;    
            // }
            
            $order_tax_list = array();
            
            
            foreach ($order_tax_data as $tax_data) {
                $order_tax_list[$tax_data['tax_id']] = $tax_data;
            }

            $taxSum = 0;
            
            $order_tax = $order_tax_data;
            
            foreach ($tax_name as $column) {
                $col_tax_id = $column['tax_id'];
                
                
                if (!empty($order_tax)) {
                    if (!empty($order_tax_list[$col_tax_id])) {
                        $taxSum += $order_tax_list[$col_tax_id]['tax_amount'];
                        
                    }
                }
                
            }

            
            $data['order_id'] = $order_data['order_id'];
            $data['created'] = $order_data['created'];
            $data['order_code'] = $order_data['order_code'];
            $data['table_no'] = $order_data['table_no'];
            $data['number_of_person'] = $order_data['number_of_person'];
            $data['sub_total'] = $order_data['sub_total'];
            if($order_data['orderType'] == 1){
                $data['orderType'] = "Table Order";
            } else if($order_data['orderType'] == 2){
                $data['orderType'] = "Delivery";
            } else {
                $data['orderType'] = "Parcel";
            }
            $data['paymentType'] = $order_data['paymentType'];
            $data['discount'] = ($order_data['sub_total'] * $order_data['discount']) / 100;
            $data['bill_amount'] = $order_data['bill_amount'];
            
            $data['total']       = round((float) ($order_data['bill_amount']));
            $data['roundoff_value'] = $data['total'] - ($data['bill_amount']);
            
            $details[$i] = $data;
            
            $details[$i]['order_tax'] = $order_tax_list;

            $getTotalCover += $data['number_of_person'];
            $getSubtotal += $data['sub_total'];
            $gettotalDiscountTotal += $data['discount'];
            $getbill_amountTotal += $data['bill_amount'];
            $getRound += $data['roundoff_value'];
            $getroundoffTotal += $data['total'];

            foreach ($paymentTypeData as $payment) {
                # code...
                if($order_data['payment_id'] == $payment['payment_id']){
                    $paymentData[$payment['payment_id']]["value"] += $data['total'];
                }
                
            }
            
            //$result['order_tax'] = $order_tax_list;
            $i++;
        }

        $response           = array();
        $response['status'] = "1";
        $response['data']   = $details;
        $response['getTotalCover'] += $getTotalCover;
        $response['getSubtotal'] += $getSubtotal;
        $response['gettotalDiscountTotal'] += $gettotalDiscountTotal;
        $response['getbill_amountTotal'] += $getbill_amountTotal;
        $response['getRound'] += $getRound;
        $response['getroundoffTotal'] += $getroundoffTotal;
        $response['paymentData'] = $paymentData;
        echo json_encode($response);
        die;
    }


    public function branch_report_all()
    {
        $this->load->model('order_model');
        
        $result = $this->order_model->branchwise_report_all();
        
        $details = array();
        $i       = 0;
        
        
        foreach ($result as $order_data) {
            $details[$i] = $order_data;
            //print_r($order_data);
            
            $order_tax_data = $this->order_model->order_tax_data($order_data['order_id'], $order_data['sub_total'], $order_data['discount']);
            //print_r($order_tax_data);
            
            $order_tax_list = array();
            if (!empty($order_tax_data)) {
                foreach ($order_tax_data as $tax_data) {
                    
                    $order_tax_list[$tax_data['tax_id']] = $tax_data;
                    
                }
                
                $details[$i]['order_tax'] = $order_tax_list;
                
            }
            //print_r($order_tax_list);
            
            //$result['order_tax'] = $order_tax_list;
            $i++;
        }
        $response           = array();
        $response['status'] = "1";
        $response['data']   = $details;
        echo json_encode($response);
        
    }
    
    public function waiter_report_byWaiter()
    {
        if (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '') {
            
            $this->load->model('order_model');
            
            $result             = $this->order_model->waiter_report_by_waiter($_POST['waiter_id']);
            $response           = array();
            $response['status'] = "1";
            $response['data']   = $result;
            echo json_encode($response);
            die;
        }
        
    }
    public function report_waiter()
    {
        //echo'<pre>';print_r($_POST);die;
        $waiter_id = '';
        $fromdate  = '';
        $todate    = '';
        $branch_id = '';
        
        if (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '' && isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
            $waiter_id = $_POST['waiter_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $branch_id = $_POST['branch_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $waiter_id = $_POST['waiter_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
            $waiter_id = $_POST['waiter_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
        } elseif (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['todate']) && $_POST['todate'] != '' && isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
            $waiter_id = $_POST['waiter_id'];
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
            $waiter_id = $_POST['waiter_id'];
        } elseif (isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            $todate   = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
            
        } else if (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $branch_id = $_POST['branch_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            
        } elseif (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $branch_id = $_POST['branch_id'];
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } else if (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $waiter_id = $_POST['waiter_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            
        } elseif (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $waiter_id = $_POST['waiter_id'];
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } elseif (isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
        } elseif (isset($_POST['todate']) && $_POST['todate'] != '') {
            $todate = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } else if (isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
        } else if (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '') {
            $waiter_id = $_POST['waiter_id'];
        }
        
        $this->load->model('order_model');
        
        $result             = $this->order_model->waiter_report($branch_id, $waiter_id, $fromdate, $todate);
        $response           = array();
        $response['status'] = "1";
        $response['data']   = $result;
        echo json_encode($response);
        die;
        
    }
    
    public function create_html()
    {
        //echo'<pre>';print_r($_POST);die;
        $waiter_id = '';
        $fromdate  = '';
        $todate    = '';
        $branch_id = '';
        
        if (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '' && isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
            $waiter_id = $_POST['waiter_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $branch_id = $_POST['branch_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $waiter_id = $_POST['waiter_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
            $waiter_id = $_POST['waiter_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
        } elseif (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['todate']) && $_POST['todate'] != '' && isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
            $waiter_id = $_POST['waiter_id'];
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
            $waiter_id = $_POST['waiter_id'];
        } elseif (isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            $todate   = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
            
        } else if (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $branch_id = $_POST['branch_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            
        } elseif (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $branch_id = $_POST['branch_id'];
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } else if (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $waiter_id = $_POST['waiter_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            
        } elseif (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $waiter_id = $_POST['waiter_id'];
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } elseif (isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
        } elseif (isset($_POST['todate']) && $_POST['todate'] != '') {
            $todate = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } else if (isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
        } else if (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '') {
            $waiter_id = $_POST['waiter_id'];
        }
        
        $this->load->model('order_model');
        
        $result = $this->order_model->waiter_report($branch_id, $waiter_id, $fromdate, $todate);
        
        $reportHtml = '<div> <table id="waiter_table" class="table table-condensed table-hover" cellpadding="10"> <thead> <tr> <th class="hidden-xs">Date</th> <th class="hidden-xs">Bill No.</th> <th class="hidden-xs">Waiter Commission (%)</th> <th class="hidden-xs">Waiter Commission Amount</th> </tr></thead> <tbody>';
        
        $total_commi_percent = 0;
        $total_commi_amount  = 0;
        
        foreach ($result as $row) {
            $total_commi_percent += $row['waiter_commision'];
            
            $commi_amount = ($row['total_amount'] * $row['waiter_commision'] / 100);
            $commi_amount = number_format($commi_amount, 2);
            
            $total_commi_amount += $commi_amount;
            
            $reportHtml .= '<tr> <td>' . $row['order_date_time'] . '</td><td>' . $row['order_code'] . '</td><td>' . $row['waiter_commision'] . '</td><td>' . $commi_amount . '</td></tr>';
        }
        
        $reportHtml .= '<tr> <td><b>Total:</b></td><td></td><td><b>' . $total_commi_percent . '</b></td><td><b>' . $total_commi_amount . '</b></td></tr></tbody> </table> </div>';
        
        echo $reportHtml;
        die;
    }
    
    public function sales_report()
    {
        $this->load->view('report/sales_create');
    }


    
    public function report_sales()
    {
        //echo'<pre>';print_r($_POST);die;
        $branch_id = '';
        $fromdate  = '';
        $todate    = '';
        
        if (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $branch_id = $_POST['branch_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            $todate   = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
            
        } else if (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $branch_id = $_POST['branch_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            
        } elseif (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $branch_id = $_POST['branch_id'];
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } elseif (isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
        } elseif (isset($_POST['todate']) && $_POST['todate'] != '') {
            $todate = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } else if (isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
        }
        
        
        $this->load->model('order_model');
        $this->load->model('tax_main_model');
        $this->load->model('payment_model');
        
        $tax_name = $this->tax_main_model->tax_list_all();
        
        $result = $this->order_model->sales_report($branch_id, $fromdate, $todate);
        $paymentTypeData = $this->payment_model->get_data();

        foreach ($paymentTypeData as $payment) {
            
            $paymentData[$payment['payment_id']] = array(
                                                        "name"=>$payment['payment_type'],
                                                        "id" => $payment['payment_id'],
                                                        "value" => 0
                                                    ); 
            
        }
        
        $details = array();
        
        $i = 0;
        $getTotalCover = $getSubtotal = $gettotalDiscountTotal = $getbill_amountTotal = $getRound = $getroundoffTotal = 0;
        foreach ($result as $order_data) {
            
            //print_r($order_data);die;
            
            $order_tax_data = $this->order_model->order_tax_data($order_data['order_id'], $order_data['sub_total'], $order_data['discount']);
            
            
            $order_tax_list = array();
            
            
            foreach ($order_tax_data as $tax_data) {
                $order_tax_list[$tax_data['tax_id']] = $tax_data;
            }
            
            $taxSum = 0;
            
            $order_tax = $order_tax_data;
            
            foreach ($tax_name as $column) {
                $col_tax_id = $column['tax_id'];
                
                
                if (!empty($order_tax)) {
                    if (!empty($order_tax_list[$col_tax_id])) {
                        $taxSum += $order_tax_list[$col_tax_id]['tax_amount'];
                        
                    }
                }
                
            }
            $data['order_id'] = $order_data['order_id'];
            $data['created'] = $order_data['created'];
            $data['order_code'] = $order_data['order_code'];
            $data['table_no'] = $order_data['table_no'];
            $data['number_of_person'] = $order_data['number_of_person'];
            $data['sub_total'] = $order_data['sub_total'];
            if($order_data['orderType'] == 1){
                $data['orderType'] = "Table Order";
            } else if($order_data['orderType'] == 2){
                $data['orderType'] = "Delivery";
            } else {
                $data['orderType'] = "Parcel";
            }
            $data['paymentType'] = $order_data['paymentType'];
            $data['discount'] = ($order_data['sub_total'] * $order_data['discount']) / 100;
            $data['bill_amount'] = $order_data['bill_amount'];
            
            $data['total']       = round((float) ($order_data['bill_amount']));
            $data['roundoff_value'] = $data['total'] - ($data['bill_amount']);
            
            $details[$i] = $data;
            
            $details[$i]['order_tax'] = $order_tax_list;

            $getTotalCover += $data['number_of_person'];
            $getSubtotal += $data['sub_total'];
            $gettotalDiscountTotal += $data['discount'];
            $getbill_amountTotal += $data['bill_amount'];
            $getRound += $data['roundoff_value'];
            $getroundoffTotal += $data['total'];

            foreach ($paymentTypeData as $payment) {
                # code...
                if($order_data['payment_id'] == $payment['payment_id']){
                    $paymentData[$payment['payment_id']]["value"] += $data['total'];
                }
                
            }

            $i++;
        }
        $response           = array();
        $response['status'] = "1";
        $response['data']   = $details;
        $response['getTotalCover'] += $getTotalCover;
        $response['getSubtotal'] += $getSubtotal;
        $response['gettotalDiscountTotal'] += $gettotalDiscountTotal;
        $response['getbill_amountTotal'] += $getbill_amountTotal;
        $response['getRound'] += $getRound;
        $response['getroundoffTotal'] += $getroundoffTotal;
        $response['paymentData'] = $paymentData;
        echo json_encode($response);
        die;
        
    }
    
    public function updateBillAmount()
    {
        $this->load->model('order_model');
        
        $result = $this->order_model->updateBillAmountOrdersQry();
        
        $i = 0;
        
        foreach ($result as $order_data) {
            $totalBillAmount = 0;
            
            $details[$i] = $order_data;
            //print_r($order_data);die;
            
            $order_tax_data = $this->order_model->order_tax_data($order_data['order_id'], $order_data['sub_total'], $order_data['discount']);
            
            if (!empty($order_tax_data)) {
                $order_tax_list = array();
                
                $totaTaxAmt = 0;
                
                foreach ($order_tax_data as $tax_data) {
                    $order_tax_list[$tax_data['tax_id']] = $tax_data;
                    
                    $totaTaxAmt += $tax_data['tax_amount'];
                }
                
                $totalBillAmountb4round = $order_data['sub_total'] - $order_data['discount'] + $totaTaxAmt;
                
                $totalBillAmount = (round($totalBillAmountb4round, 2));
                
                $updateData                 = array();
                $updateData['total_amount'] = $totalBillAmount;
                
                $this->order_model->updateBillAmountByOrderId($updateData, $order_data['order_id']);
                
                $order_tax_list['totalBillAmount'] = $totalBillAmount;
                
                $details[$i]['order_tax'] = $order_tax_list;
                
            }
            
            
            //$result['order_tax'] = $order_tax_list;
            $i++;
        }
        
        echo '<pre>';
        print_r($details);
        die;
    }
   
    public function branch_report()
    {
        $this->load->view('report/branch_create');
    }
    public function branchwise_waiter()
    {
        //print_r($_POST);
        if (isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $this->load->model('waiter_model');
            
            $response                          = array();
            $response['status']                = "1";
            $response['waiter_list_by_branch'] = $this->waiter_model->waiter_list_by_branch($_POST['branch_id']);
            
            echo json_encode($response);
        } else {
            $session_data = $this->session->userdata('logged_in');
            $branch_id    = $session_data['branch_id'];
            // get waiters by logged in branch
            $this->load->model('waiter_model');
            
            $response                          = array();
            $response['status']                = "1";
            $response['waiter_list_by_branch'] = $this->waiter_model->waiter_list_by_branch($branch_id);
            
            echo json_encode($response);
        }
    }
    public function branchwise_brand()
    {
        if (isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $this->load->model('brand_model');
            
            $response                         = array();
            $response['status']               = "1";
            $response['brand_list_by_branch'] = $this->brand_model->brand_list_by_branch_new($_POST['branch_id']);
            
            echo json_encode($response);
            die;
        }
    }
    
    public function branchwise_brand_item_wise_sales()
    {
        $this->load->model('brand_model');
        
        $response                         = array();
        $response['status']               = "1";
        $response['brand_list_by_branch'] = $this->brand_model->brand_list_by_branch_new_item_wise_sales();
        
        echo json_encode($response);
        die;
    }
    
    public function report_branch()
    {
        $brand_id  = '';
        $fromdate  = '';
        $todate    = '';
        $branch_id = '';
        //print_r($_POST);die;
        
        if (isset($_POST['brand_id']) && $_POST['brand_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '' && isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
            $brand_id  = $_POST['brand_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $branch_id = $_POST['branch_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['brand_id']) && $_POST['brand_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $brand_id = $_POST['brand_id'];
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
            $todate   = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['brand_id']) && $_POST['brand_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
            $brand_id  = $_POST['brand_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
        } elseif (isset($_POST['brand_id']) && $_POST['brand_id'] != '' && isset($_POST['todate']) && $_POST['todate'] != '' && isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
            $brand_id  = $_POST['brand_id'];
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['brand_id']) && $_POST['brand_id'] != '' && isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
            $brand_id  = $_POST['brand_id'];
        } elseif (isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            $todate   = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
            
        } else if (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $branch_id = $_POST['branch_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            
        } elseif (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $branch_id = $_POST['branch_id'];
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } else if (isset($_POST['brand_id']) && $_POST['brand_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $brand_id = $_POST['brand_id'];
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            
        } elseif (isset($_POST['brand_id']) && $_POST['brand_id'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $brand_id = $_POST['brand_id'];
            $todate   = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } elseif (isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
        } elseif (isset($_POST['todate']) && $_POST['todate'] != '') {
            $todate = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } else if (isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
        } else if (isset($_POST['brand_id']) && $_POST['brand_id'] != '') {
            $brand_id = $_POST['brand_id'];
        }
        
        $this->load->model('order_model');
        
        $result = $this->order_model->branch_sales_report($branch_id, $brand_id, $fromdate, $todate);
        
        $details = array();
        
        $i = 0;
        foreach ($result as $order_data) {
            $details[$i] = $order_data;
            //print_r($order_data);die;
            
            $order_tax_data = $this->order_model->order_tax_data($order_data['order_id'], $order_data['sub_total'], $order_data['discount']);
            
            if (!empty($order_tax_data)) {
                $order_tax_list = array();
                foreach ($order_tax_data as $tax_data) {
                    $order_tax_list[$tax_data['tax_id']] = $tax_data;
                }
                
                
                $details[$i]['order_tax'] = $order_tax_list;
                
            }
            
            
            //$result['order_tax'] = $order_tax_list;
            $i++;
        }
        $response           = array();
        $response['status'] = "1";
        $response['data']   = $details;
        echo json_encode($response);
        die;
        
    }
    
    public function item_wise_sales()
    {
        $this->load->view('report/item_wise_sales');
    }
    
    public function item_wise_sales_all()
    {
        $this->load->model('product_category_model');
        
        $result             = $this->product_category_model->get_all_items();
        $response           = array();
        $response['status'] = "1";
        $response['data']   = $result;
        echo json_encode($response);
        die;
    }
    
    public function item_wise_sales_data()
    {
        $fromdate     = '';
        $todate       = '';
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        
        if (isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
        } else {
            $fromdate = '';
        }
        
        if (isset($_POST['todate']) && $_POST['todate'] != '') {
            $todate = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } else {
            $todate = '';
        }
        if (isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
        } else if (isset($session_data['branch_id']) && $session_data['branch_id'] != '') {
            $branch_id = $session_data['branch_id'];
        } else {
            $branch_id = '';
        }
        if (isset($_POST['brand_id']) && $_POST['brand_id'] != '') {
            $brand_id = $_POST['brand_id'];
        } else {
            $brand_id = '';
        }
        
        // get all product categories
        $this->load->model('product_category_model');
        $product_category_list = $this->product_category_model->product_category_list_by_brand($brand_id);
        
        $details = array();
        
        $response = array();
        
        $finalQty = $finalTotal = 0;
        
        if (!empty($product_category_list)) {
            $i = 0;
            // get sold items of today by product_category_id
            foreach ($product_category_list as $category) {
                
                $product_category_id = $category['product_category_id'];
                
                $item_list = $this->product_category_model->get_daily_sold_items_by_product_category($product_category_id, $fromdate, $todate, $branch_id, $brand_id);
                
                //echo '<pre>';print_r($category);
                //echo '<pre>';print_r($item_list);
                
                if ($item_list) {
                    $details[$i] = $category;
                    
                    $item_data = array();
                    
                    $j            = 0;
                    $item_arr     = array();
                    $tempFinalQty = $tempFinalTotal = 0;
                    
                    foreach ($item_list as $item) {
                        
                        
                        //$item_data[$item['product_category_id']] = $item_arr;
                        $item_data[] = $item;
                        $tempFinalQty += $item['quantity'];
                        $tempFinalTotal += $item['total'];
                        $j++;
                    }
                    
                    $details[$i]['items'] = $item_data;
                    $finalQty += $tempFinalQty;
                    $finalTotal += $tempFinalTotal;
                }
                
                $i++;
            }
        }
        //print_r($details);
        
        $response['status']     = "1";
        $response['data']       = $details;
        $response['finalQty']   = $finalQty;
        $response['finalTotal'] = $finalTotal;
        echo json_encode($response);
        die;
    }
    
    public function item_wise_sales_data_pdf()
    {
        $fromdate = '';
        $todate   = '';
        
        
        if (isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            $todate   = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } elseif (isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
        } elseif (isset($_POST['todate']) && $_POST['todate'] != '') {
            $todate = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } else {
            $fromdate = date('Y-m-d H:i:s', strtotime(date("Y-m-d") . '00:00:00'));
            $todate   = date('Y-m-d H:i:s', strtotime(date("Y-m-d") . '23:59:59'));
        }
        
        $brand_id = "";
        
        if (isset($_POST['brand_id']) && $_POST['brand_id'] != '') {
            $brand_id = $_POST['brand_id'];
        }
        
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        
        if (isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
        }
        
        
        // get all product categories
        $this->load->model('product_category_model');
        //$product_category_list = $this->product_category_model->product_category_list();
        $product_category_list = $this->product_category_model->product_category_list_by_brand($brand_id);
        
        $details = array();
        
        $response = array();
        
        $reportHtml = '';
        
        
        if (!empty($product_category_list)) {
            
            $reportHtml .= '<div><table border="1" id="item_wise_sales_report" class="table table-condensed table-hover"><thead><tr><th colspan="2">Category Name</th><th>Quantity</th><th>Total</th></tr></thead><tbody>';
            
            $i = 0;
            // get sold items of today by product_category_id
            foreach ($product_category_list as $category) {
                
                $product_category_id = $category['product_category_id'];
                
                //$item_list = $this->product_category_model->get_daily_sold_items_by_product_category($product_category_id,$fromdate,$todate);
                $item_list = $this->product_category_model->get_daily_sold_items_by_product_category($product_category_id, $fromdate, $todate, $branch_id);
                
                
                if ($item_list) {
                    $reportHtml .= '<tr ng-repeat="row in item_wise_report" class="ng-scope"><td class="hidden-xs" colspan="4" style="text-align: center"> <b style="font-size:18px" class="ng-binding"> ' . $category["product_category_id"] . ' . ' . $category["name"] . ' </b><table class="table table-condensed table-hover" border="1" ><tbody>';
                    
                    $details[$i] = $category;
                    
                    $item_data = array();
                    
                    $j        = 0;
                    $item_arr = array();
                    
                    $item_total = 0;
                    
                    foreach ($item_list as $item) {
                        
                        //$item_data[$item['product_category_id']] = $item_arr;
                        
                        $reportHtml .= '<tr ng-repeat="item in row.items" class="ng-scope"><td>' . $item["product_code"] . '</td><td>' . $item["product_name"] . '</td><td>' . $item["quantity"] . '</td><td>' . $item["total"] . '</td></tr>';
                        
                        $item_total += $item["total"];
                        
                        $item_data[] = $item;
                        $j++;
                    }
                    $details[$i]['items'] = $item_data;
                    $reportHtml .= '<tr><td>Total:</td><td></td><td></td><td>' . $item_total . '</td></tr></tbody></table></td></tr>';
                }
                
                $i++;
            }
            
            $reportHtml .= '</tbody></table></div>';
            
        }
        
        // $reportHtml = '<div><table id="item_wise_sales_report" class="table table-condensed table-hover"><thead><tr><th colspan="2">Category Name</th><th>Quantity</th><th>Total</th></tr></thead><tbody><tr ng-repeat="row in item_wise_report" class="ng-scope"><td class="hidden-xs" colspan="4" style="text-align: center;"> <b style="font-size:18px" class="ng-binding"> 2 . ASSORTED BEVERAGES </b><table class="table table-condensed table-hover"><tbody><tr ng-repeat="item in row.items" class="ng-scope"><td>2</td><td>FRESH LIME SODA</td><td>3.00</td><td>195.00</td></tr><tr><td>Total:</td><td></td><td></td><td>195</td></tr></tbody></table></td></tr><tr ng-repeat="row in item_wise_report" class="ng-scope"><td class="hidden-xs" colspan="4" style="text-align: center;"> <b style="font-size:18px" class="ng-binding"> 2 . ASSORTED BEVERAGES </b><table class="table table-condensed table-hover"><tbody><tr ng-repeat="item in row.items" class="ng-scope"><td>2</td><td>FRESH LIME SODA</td><td>3.00</td><td>195.00</td></tr><tr><td>Total:</td><td></td><td></td><td>195</td></tr></tbody></table></td></tr></tbody></table></div>';
        
        
        $this->load->library('Pdf');
        
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('24x7Developers');
        $pdf->SetTitle('Item Wise Sales Report');
        $pdf->SetSubject('Item Wise Sales Report');
        
        $pdf->SetHeaderData('', '', 'Item Wise Sales Report', '', array(
            0,
            64,
            255
        ), array(
            0,
            64,
            128
        ));
        $pdf->setFooterData(array(
            0,
            64,
            0
        ), array(
            0,
            64,
            128
        ));
        
        // set header and footer fonts
        $pdf->setHeaderFont(Array(
            PDF_FONT_NAME_MAIN,
            '',
            PDF_FONT_SIZE_MAIN
        ));
        $pdf->setFooterFont(Array(
            PDF_FONT_NAME_DATA,
            '',
            PDF_FONT_SIZE_DATA
        ));
        
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }
        
        // set default font subsetting mode
        $pdf->setFontSubsetting(true);
        
        $pdf->SetFont('dejavusans', '', 10, '', true);
        
        
        $pdf->AddPage();
        
        // set text shadow effect
        $pdf->setTextShadow(array(
            'enabled' => true,
            'depth_w' => 0.2,
            'depth_h' => 0.2,
            'color' => array(
                196,
                196,
                196
            ),
            'opacity' => 1,
            'blend_mode' => 'Normal'
        ));
        
        // Set some content to print
        //     $html = <<<EOD
        //     <h1>Welcome to <a href="http://www.tcpdf.org" style="text-decoration:none;background-color:#CC0000;color:black;">&nbsp;<span style="color:black;">TC</span><span style="color:white;">PDF</span>&nbsp;</a>!</h1>
        //     <i>This is the first example of TCPDF library.</i>
        //     <p>This text is printed using the <i>writeHTMLCell()</i> method but you can also use: <i>Multicell(), writeHTML(), Write(), Cell() and Text()</i>.</p>
        //     <p>Please check the source code documentation and other examples for further information.</p>
        
        // EOD;
        
        
        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $reportHtml, 0, 1, 0, true, '', true);
        
        // ---------------------------------------------------------    
        
        $file_name = 'item_wise_sales_report.pdf';
        
        $pdf->Output($file_name, 'I');
        
        echo $file_name;
        die;
        
        //============================================================+
        // END OF FILE
        //============================================================+
        
        //print_r($details);
        
        // $response['status'] = "1";
        // $response['data'] = $details;
        // echo json_encode($response);
        // die;
    }
    
    public function waiter_rpt()
    {
        $this->load->view('report/waiter_report');
    }

    public function brandwisedailysales() {
        $this->load->view('report/brand_wise_daily_sales');
    }
    
    public function waiter_rpt_data()
    {
        //echo'<pre>';print_r($_POST);die;
        $waiter_id = '';
        $fromdate  = '';
        $todate    = '';
        
        if (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $waiter_id = $_POST['waiter_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            $todate   = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
            
        } else if (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $waiter_id = $_POST['waiter_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            
        } elseif (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $waiter_id = $_POST['waiter_id'];
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } elseif (isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
        } elseif (isset($_POST['todate']) && $_POST['todate'] != '') {
            $todate = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } else if (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '') {
            $waiter_id = $_POST['waiter_id'];
        }
        
        
        $this->load->model('order_model');
        
        $result             = $this->order_model->waiter_report($waiter_id, $fromdate, $todate);
        $response           = array();
        $response['status'] = "1";
        $response['data']   = $result;
        echo json_encode($response);
        die;
    }
    
    public function daily_sales()
    {
        $this->load->view('report/daily_sales');
    }
    public function all_daily_sales()
    {
        $this->load->model('order_model');
        
        $result             = $this->order_model->daily_sales_all();
        $response           = array();
        $response['status'] = "1";
        $response['data']   = $result;
        echo json_encode($response);
        die;
        
    }
    public function daily_sales_rpt()
    {
        
        $branch_id = '';
        $fromdate  = '';
        
        $year  = date("Y");
        $month = date("m");
        
        $list = array();
        
        for ($d = 1; $d <= 31; $d++) {
            $time = mktime(12, 0, 0, $month, $d, $year);
            if (date('m', $time) == $month)
                $list[] = date('Y-m-d', $time);
        }
        
        
        $this->load->model('order_model');
        
        $j = 0;
        
        $dailyDetails = array();
        
        foreach ($list as $cal_date) {
            
            $result = $this->order_model->dailySalesList($cal_date);
            
            //$response = $result;
            $response = array();
            
            
            $order_tax_list = array();
            
            $taxSum = 0;
            // find tax by order
            // get all orders of the given date
            $this->load->model('tax_main_model');
            $tax_list = $this->tax_main_model->tax_list_all();
            
            
            
            $round_off_value_total = 0;
            
            // calculation for round off
            $orderList = $this->order_model->getOrdersByDateAndBranchForAll($cal_date);
			
			
            
            foreach ($orderList as $order_data) {
                //getsub_total of order
                $orderItemDetails = $this->order_model->getOrderItemsByOrderId($order_data['order_id']);
                
                $order_tax_data = $this->order_model->order_tax_data($order_data['order_id'], $orderItemDetails['sub_total'], $orderItemDetails['discount']);
                
                
                $order_tax_list2 = array();
                
                foreach ($order_tax_data as $tax_data) {
                    $order_tax_list2[$tax_data['tax_id']] = $tax_data;
                }

				
                
                $taxSum2   = 0;
                $order_tax = $order_tax_data;
                
                foreach ($tax_list as $column) {
                    $col_tax_id = $column['tax_id'];
                    
                    if (!empty($order_tax)) {
                        if (!empty($order_tax_list2[$col_tax_id])) {
                            $taxSum2 += $order_tax_list2[$col_tax_id]['tax_amount'];
                            
                        }
                    }
                    
                }
				
                $billAmount    = (float) ($orderItemDetails['sub_total']) + $taxSum2 - ((float) ($orderItemDetails['discount']));
                $roundOff      = round((float) ($billAmount));
                $roundoffValue = number_format(($roundOff - (float) ($billAmount)), 2);
                
                $round_off_value_total += $roundoffValue;
                
                //$details[$i] = $order_data;
                // $i++;
                
            }
            ///
			
            
            foreach ($tax_list as $tax) {
                $tax_id   = $tax['tax_id'];
                // get tax data by date and tax_id
                $tax_data = $this->order_model->get_tax_data_by_date_and_tax_id_daily_sales($cal_date, $tax_id, $branch_id);
                
                $order_tax_list[$tax_id] = $tax_data;
                
                if (!empty($tax_data)) {
                    
                    if (!empty($order_tax_list[$tax_id])) {
                        $taxSum += $order_tax_list[$tax_id]['tax_amount'];
                    }
                }
            }
            
            $result['bill_amount']    = (float) ($result['sub_total']) + (float) ($taxSum) - ((float) ($result['discount']));
            $result['roundoff']       = (float) (round($result['bill_amount'] + $round_off_value_total));
            //$result['roundoff_value'] = number_format(($result['roundoff']-(float)($result['bill_amount'])),2);
            $result['roundoff_value'] = (float)$round_off_value_total;
            
            
            
            $response['roundoff_value'] = isset($result['roundoff_value']) ? (float)$result['roundoff_value'] : 0.00;
            $response['sub_total']      = isset($result['sub_total']) ? (float)$result['sub_total'] : 0;
            $response['tax_free']       = isset($result['tax_free']) ? (float)$result['tax_free'] : 0;
            $response['discount']       = isset($result['discount']) ? (float)$result['discount'] : 0;
            $response['bill_amount']    = isset($result['bill_amount']) ? (float)$result['bill_amount'] : 0;
            $response['roundoff']       = isset($result['roundoff']) ? (float)$result['roundoff'] : 0;
            $response['created']        = date('d-m-Y',strtotime($cal_date));
            
            // find tax by order
            // get all orders of the given date
            $this->load->model('tax_main_model');
            $tax_list = $this->tax_main_model->tax_list_all();
            
            // $order_tax_list = array();
            
            // foreach ($tax_list as $tax) {
            //     $tax_id = $tax['tax_id'];
            //     // get tax data by date and tax_id
            //     $tax_data = $this->order_model->get_tax_data_by_date_and_tax_id($cal_date,$tax_id);
            
            //      $order_tax_list[$tax_id] = $tax_data;
            
            // }
            
            $response['order_tax'] = $order_tax_list;
            
            $details[$j] = $response;
            
            $j++;
        }
        
        
        $response['status'] = "1";
        $response['data']   = $details;
        echo json_encode($response);
        die;
    }
    
    public function get_daily_sales_by_branch()
    {
        $branch_id = '';
        $fromdate  = '';
        
        $year  = date("Y");
        $month = date("m");
        
        
        if (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $branch_id = $_POST['branch_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            
            $from_date  = $_POST['fromdate'];
            $frdate_arr = explode('/', $from_date);
            
            if (!empty($frdate_arr)) {
                $year  = isset($frdate_arr[0]) ? $frdate_arr[0] : '';
                $month = isset($frdate_arr[1]) ? $frdate_arr[1] : '';
            }
            
            $year  = (int) $year;
            $month = (int) $month;
            
        }
        
        elseif (isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $from_date  = $_POST['fromdate'];
            $frdate_arr = explode('/', $from_date);
            
            if (!empty($frdate_arr)) {
                $year  = isset($frdate_arr[0]) ? $frdate_arr[0] : '';
                $month = isset($frdate_arr[1]) ? $frdate_arr[1] : '';
            }
            
            $year  = (int) $year;
            $month = (int) $month;
        }
        
        else if (isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
        }
        
        $list = array();
        
        for ($d = 1; $d <= 31; $d++) {
            $time = mktime(12, 0, 0, $month, $d, $year);
            if (date('m', $time) == $month)
                $list[] = date('Y-m-d', $time);
        }
        
        //echo '<pre>';print_r($list);die;
        
        
        $this->load->model('order_model');
        
        $j = 0;
        
        $dailyDetails = array();
        
        foreach ($list as $cal_date) {
            
            //$response = $result;
            $response = array();
            
            $order_tax_list = array();
            
            $taxSum = 0;
            $taxsum2 = 0;
            // find tax by order
            // get all orders of the given date
            $this->load->model('tax_main_model');
            $tax_list = $this->tax_main_model->tax_list_all();

            
            
            $result  = $this->order_model->get_daily_sales_by_branch($cal_date, $branch_id);
            $round_off_value_total = 0;

            
            // calculation for round off
            $orderList = $this->order_model->getOrdersByDateAndBranch($cal_date, $branch_id);
            
            
            foreach ($orderList as $order_data) {
                //getsub_total of order
                $orderItemDetails = $this->order_model->getOrderItemsByOrderId($order_data['order_id']);
                
                $order_tax_data = $this->order_model->order_tax_data($order_data['order_id'], $orderItemDetails['sub_total'], $orderItemDetails['discount']);
                
                
                $order_tax_list2 = array();
                
                
                foreach ($order_tax_data as $tax_data) {
                    $order_tax_list2[$tax_data['tax_id']] = $tax_data;
                }
                
                
                $order_tax = $order_tax_data;
                
                foreach ($tax_list as $column) {
                    $col_tax_id = $column['tax_id'];
                    
                    if (!empty($order_tax)) {
                        
						if(!empty($order_tax_list2[$col_tax_id])){
                            $taxSum2 += $order_tax_list2[$col_tax_id]['tax_amount'];
							
						}
                    }
                }

                
                $billAmount    = (float) ($orderItemDetails['sub_total']) + $taxSum2 - ((float) ($orderItemDetails['discount']));
                
                $roundOff      = round((float) ($billAmount));
                $roundoffValue = number_format(($roundOff - (float) ($billAmount)), 2);
                
                $round_off_value_total += $roundoffValue; 
                
                //$details[$i] = $order_data;
                // $i++;
                
            }
            ///
            
            foreach ($tax_list as $tax) {
                $tax_id   = $tax['tax_id'];
                // get tax data by date and tax_id
                $tax_data = $this->order_model->get_tax_data_by_date_and_tax_id_daily_sales($cal_date, $tax_id, $branch_id);
                
                $order_tax_list[$tax_id] = $tax_data;
                
                if (!empty($tax_data)) {
                    	if(!empty($order_tax_list[$tax_id])){
							$taxSum += $order_tax_list[$tax_id]['tax_amount'];
							
						}
                }
            }
            
            
            $result['bill_amount']    = (float) ($result['sub_total']) + (float) ($taxSum) - ((float) ($result['discount']));
            $result['roundoff']       = round($result['bill_amount'] + $round_off_value_total);
            //$result['roundoff_value'] = number_format(($result['roundoff']-(float)($result['bill_amount'])),2);
            $result['roundoff_value'] = $round_off_value_total;
            
            
            
            $response['roundoff_value'] = isset($result['roundoff_value']) ? $result['roundoff_value'] : 0.00;
            $response['sub_total']      = isset($result['sub_total']) ? $result['sub_total'] : 0;
            $response['tax_free']       = isset($result['tax_free']) ? $result['tax_free'] : 0;
            $response['discount']       = isset($result['discount']) ? $result['discount'] : 0;
            $response['bill_amount']    = isset($result['bill_amount']) ? $result['bill_amount'] : 0;
            //$response['roundoff'] = isset($result['roundoff']) ? $result['roundoff'] : 0;
            $response['roundoff']       = round($result['bill_amount'] + $result['roundoff_value']);
            $response['created']        = date('d-m-Y', strtotime($cal_date));
            
            $response['order_tax'] = $order_tax_list;
            
            $details[$j] = $response;
            
            $j++;
        }
        
        
        $response['status'] = "1";
        $response['data']   = $details;
        echo json_encode($response);
        die;
    }
    
    
    // access using this url : http://localhost/restaurant_management/index.php/report/days
    public function days()
    {
        $list  = array();
        $month = date("m");
        $year  = date("Y");
        
        for ($d = 1; $d <= 31; $d++) {
            $time = mktime(12, 0, 0, $month, $d, $year);
            if (date('m', $time) == $month)
                $list[] = date('Y-m-d', $time);
        }
        echo "<pre>";
        print_r($list);
        echo "</pre>";
        
        
        // use below qry in loop
        // SELECT CAST( SUM(  CASE 
        //                   WHEN (ROUND(o.total_amount)) < o.total_amount 
        //                          THEN ROUND(o.total_amount) - o.total_amount
        //                   WHEN (ROUND(o.total_amount)) > o.total_amount
        //                          THEN CONCAT("+", ROUND(o.total_amount) - o.total_amount) 
        //                   WHEN (ROUND(o.total_amount)) = o.total_amount
        //                          THEN  ROUND(o.total_amount) - o.total_amount
        //                      END ) AS DECIMAL(10,2) )  AS roundoff_value,
        //       SUM((o.sub_total * o.discount_amount/100)) AS discount,        
        //       SUM(o.total_amount) AS bill_amount,  
        //       SUM(ROUND(o.total_amount)) AS roundoff,
        //               (SELECT SUM(otx.tax_percent) FROM order_detail sod LEFT JOIN order_tax otx ON (sod.order_id = otx.order_id) WHERE sod.order_id=o.order_id ) AS totalTax
        //               FROM order_detail o                
        //               LEFT JOIN branch b ON b.branch_id = o.branch_id
        //               WHERE ( o.order_date_time >= '2016-10-26 00:00:00' AND o.order_date_time <= '2016-10-26 23:59:59'  )
        //               GROUP BY DATE(o.order_date_time)
    }
    
    
    public function report_waiter_pdf()
    {
        
        $waiter_id = '';
        $fromdate  = '';
        $todate    = '';
        $branch_id = '';
        
        if (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '' && isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
            $waiter_id = $_POST['waiter_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $branch_id = $_POST['branch_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $waiter_id = $_POST['waiter_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
            $waiter_id = $_POST['waiter_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
        } elseif (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['todate']) && $_POST['todate'] != '' && isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
            $waiter_id = $_POST['waiter_id'];
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
            $waiter_id = $_POST['waiter_id'];
        } elseif (isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            $todate   = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
            
        } else if (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $branch_id = $_POST['branch_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            
        } elseif (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $branch_id = $_POST['branch_id'];
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } else if (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $waiter_id = $_POST['waiter_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            
        } elseif (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $waiter_id = $_POST['waiter_id'];
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } elseif (isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
        } elseif (isset($_POST['todate']) && $_POST['todate'] != '') {
            $todate = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } else if (isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
        } else if (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '') {
            $waiter_id = $_POST['waiter_id'];
        }
        
        
        $this->load->model('order_model');
        
        $result = $this->order_model->waiter_report($branch_id, $waiter_id, $fromdate, $todate);
        
        $reportHtml = '<div> <table id="waiter_table" class="table table-condensed table-hover" cellpadding="10"> <thead> <tr> <th class="hidden-xs">Date</th> <th class="hidden-xs">Bill No.</th> <th class="hidden-xs">Waiter Commission (%)</th> <th class="hidden-xs">Waiter Commission Amount</th> </tr></thead> <tbody>';
        
        $total_commi_percent = 0;
        $total_commi_amount  = 0;
        
        foreach ($result as $row) {
            $total_commi_percent += $row['waiter_commision'];
            
            $commi_amount = ($row['total_amount'] * $row['waiter_commision'] / 100);
            $commi_amount = number_format($commi_amount, 2);
            
            $total_commi_amount += $commi_amount;
            
            $reportHtml .= '<tr> <td>' . $row['order_date_time'] . '</td><td>' . $row['order_code'] . '</td><td>' . $row['waiter_commision'] . '</td><td>' . $commi_amount . '</td></tr>';
        }
        
        $reportHtml .= '<tr> <td><b>Total:</b></td><td></td><td><b>' . $total_commi_percent . '</b></td><td><b>' . $total_commi_amount . '</b></td></tr></tbody> </table> </div>';
        
        // echo $reportHtml;
        // die;
        
        $this->load->library('Pdf');
        
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('24x7Developers');
        $pdf->SetTitle('Waiter Commission Report');
        $pdf->SetSubject('Waiter Commission Report');
        //$pdf->SetKeywords('TCPDF, PDF, example, test, guide');   
        
        // set default header data
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
        $pdf->SetHeaderData('', '', 'Waiter Commission Report', '', array(
            0,
            64,
            255
        ), array(
            0,
            64,
            128
        ));
        $pdf->setFooterData(array(
            0,
            64,
            0
        ), array(
            0,
            64,
            128
        ));
        
        // set header and footer fonts
        $pdf->setHeaderFont(Array(
            PDF_FONT_NAME_MAIN,
            '',
            PDF_FONT_SIZE_MAIN
        ));
        $pdf->setFooterFont(Array(
            PDF_FONT_NAME_DATA,
            '',
            PDF_FONT_SIZE_DATA
        ));
        
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }
        
        // ---------------------------------------------------------    
        
        // set default font subsetting mode
        $pdf->setFontSubsetting(true);
        
        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('dejavusans', '', 10, '', true);
        
        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage();
        
        // set text shadow effect
        $pdf->setTextShadow(array(
            'enabled' => true,
            'depth_w' => 0.2,
            'depth_h' => 0.2,
            'color' => array(
                196,
                196,
                196
            ),
            'opacity' => 1,
            'blend_mode' => 'Normal'
        ));
        
        // Set some content to print
        //     $html = <<<EOD
        //     <h1>Welcome to <a href="http://www.tcpdf.org" style="text-decoration:none;background-color:#CC0000;color:black;">&nbsp;<span style="color:black;">TC</span><span style="color:white;">PDF</span>&nbsp;</a>!</h1>
        //     <i>This is the first example of TCPDF library.</i>
        //     <p>This text is printed using the <i>writeHTMLCell()</i> method but you can also use: <i>Multicell(), writeHTML(), Write(), Cell() and Text()</i>.</p>
        //     <p>Please check the source code documentation and other examples for further information.</p>
        
        // EOD;
        
        
        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $reportHtml, 0, 1, 0, true, '', true);
        
        // ---------------------------------------------------------    
        
        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        
        $file_name = 'waiter_commision_report.pdf';
        
        $pdf->Output($file_name, 'I');
        
        echo $file_name;
        die;
        
        //============================================================+
        // END OF FILE
        //============================================================+
    }
    
    public function report_sales_pdf()
    {
        $branch_id = '';
        $fromdate  = '';
        $todate    = '';
        
        if (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $branch_id = $_POST['branch_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            $todate   = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
            
        } else if (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $branch_id = $_POST['branch_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            
        } elseif (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $branch_id = $_POST['branch_id'];
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } elseif (isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
        } elseif (isset($_POST['todate']) && $_POST['todate'] != '') {
            $todate = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } else if (isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
        }
        
        $this->load->model('order_model');
        $this->load->model('tax_main_model');
        
        if ($fromdate == "" && $todate == "") {
            $result = $this->order_model->sales_report_all();
        } else {
            $result = $this->order_model->sales_report($branch_id, $fromdate, $todate);
        }
        
        
        
        $details = array();
        
        $i          = 0;
        $reportHtml = '';
        
        $reportHtml .= ' <div class="ng-binding"><table id="sales_table" class="table table-condensed table-hover"><thead><tr><th class="hidden-xs">Created</th><th class="hidden-xs">Bill No.</th><th class="hidden-xs">Net Amt.</th><th class="hidden-xs">Tax Free</th><th class="hidden-xs">Discount</th> ';
        
        $tax_name = $this->tax_main_model->tax_list_all();
        
        $extratd    = '';
        $extrataxtd = '';
        
        foreach ($tax_name as $column) {
            if ($column['tax_percent'] == '') {
                $extrataxtd .= '<td></td>';
            }
            $extratd .= '<td></td>';
            $reportHtml .= '<th ng-repeat="column in tax_list_all" class="hidden-xs" >' . $column['tax_name'] . '(' . $column['tax_percent'] . '%)' . '</th>';
        }
        
        $reportHtml .= '<th class="hidden-xs">Bill Amount</th><th class="hidden-xs">Roundoff</th><th class="hidden-xs">Total</th></tr></thead><tbody>';
        
        $total_subtotal    = 0;
        $total_taxfree     = 0;
        $total_discount    = 0;
        $total_bill_amount = 0;
        $total_round       = 0;
        $total_roundoff    = 0;
        $start_bill_code   = 0;
        $end_bill_code     = 0;
        
        $totalOrders = count($result);
        
        $p = 0;
        
        foreach ($result as $order_data) {
            
            if ($p == 0) {
                $end_bill_code = $order_data['order_code'];
            }
            
            if ($p == ($totalOrders - 1)) {
                $start_bill_code = $order_data['order_code'];
            }
            
            $details[$i] = $order_data;
            //print_r($order_data);die;
            
            $order_tax_data = $this->order_model->order_tax_data($order_data['order_id'], $order_data['sub_total'], $order_data['discount']);
            
            $order_tax_list = array();
            
            foreach ($order_tax_data as $tax_data) {
                $order_tax_list[$tax_data['tax_id']] = $tax_data;
            }
            
            $taxSum = 0;
            
            $order_tax = $order_tax_data;
            
            foreach ($tax_name as $column) {
                $col_tax_id = $column['tax_id'];
                
                if (!empty($order_tax)) {
                    $taxSum += $order_tax_list[$col_tax_id]['tax_amount'];
                }
                
            }
            $order_data['bill_amount']    = (float) ($order_data['sub_total']) + $taxSum - ((float) ($order_data['discount']));
            $order_data['roundoff']       = round((float) ($order_data['bill_amount']));
            $order_data['roundoff_value'] = number_format(($order_data['roundoff'] - (float) ($order_data['bill_amount'])), 2);
            
            
            $total_subtotal += $order_data['sub_total'];
            $total_taxfree += $order_data['tax_free'];
            $total_discount += $order_data['discount'];
            $total_bill_amount += $order_data['bill_amount'];
            $total_round += $order_data['roundoff_value'];
            $total_roundoff += $order_data['roundoff'];
            
            
            $reportHtml .= '<tr ng-repeat="row in saleslist" class="ng-scope"><td class="hidden-xs ng-binding">' . $order_data['created'] . '</td><td class="hidden-xs ng-binding">' . $order_data['order_code'] . '</td><td class="hideen-xs ng-binding">' . $order_data['sub_total'] . '</td><td class="hidden-xs ng-binding">' . $order_data['tax_free'] . '</td><td class="hideen-xs ng-binding">' . $order_data['discount'] . '</td>';
            
            //if(!empty($order_tax_data))
            //{
            $order_tax_list = array();
            
            foreach ($tax_name as $column) {
                $reportHtml .= ' <td class="hidden-xs ng-binding ng-scope" ng-repeat="th in tax_list_all"> ';
                
                foreach ($order_tax_data as $tax_data) {
                    if ($tax_data['tax_id'] == $column['tax_id']) {
                        $reportHtml .= $tax_data['tax_amount'];
                    }
                }
                
                $reportHtml .= '</td>';
            }
            
            
            
            $details[$i]['order_tax'] = $order_tax_list;
            //}
            
            $reportHtml .= '<td class="hidden-xs ng-binding">' . $order_data['bill_amount'] . '</td><td class="hidden-xs ng-binding">' . $order_data['roundoff_value'] . '</td><td class="hidden-xs ng-binding">' . $order_data['roundoff'] . '</td>';
            
            $reportHtml .= '</tr>';
            
            //$result['order_tax'] = $order_tax_list;
            $i++;
            
            $p++;
        }
        
        $reportHtml .= '<tr><td><b>Total:</b></td><td></td><td><b class="ng-binding">' . $total_subtotal . '</b></td><td><b class="ng-binding">' . $total_taxfree . '</b></td><td id="subTotalTd"><b class="ng-binding">' . $total_discount . '</b></td>' . $extratd . '<td><b class="ng-binding">' . $total_bill_amount . '</b></td><td><b class="ng-binding">' . $total_round . '</b></td><td><b class="ng-binding">' . $total_roundoff . '</b></td><td></td></tr></tbody></table></div>';
        
        // $reportHtml .= ' <tr><td><b>Total:</b></td><td></td><td><b class="ng-binding">218891.6</b></td><td id="subTotalTd"><b class="ng-binding">16501.50</b></td><td></td><td></td><td></td><td></td><td><b class="ng-binding">254008.86</b></td><td><b class="ng-binding">-7.86</b></td><td><b class="ng-binding">254001</b></td><td></td></tr></tbody> </table> </div> ';
        
        $this->load->library('Pdf');
        
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('24x7Developers');
        $pdf->SetTitle('Sales Report');
        $pdf->SetSubject('Sales Report');
        //$pdf->SetKeywords('TCPDF, PDF, example, test, guide');   
        
        // get branch name by branch_id
        $this->load->model('branch_model');
        $branchDetails = $this->branch_model->get_branch_details_by_login_branch();
        
        $branch_name = ' Branch ';
        
        if (!empty($branchDetails)) {
            $branch_name = $branchDetails['name'] . ' Branch ';
        }
        $printDate = date('F Y', strtotime(end($list)));
        // set default header data
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
        $pdf->SetHeaderData('', '', 'Sales Report', $branch_name . ': bills from ' . $start_bill_code . ' to ' . $end_bill_code, array(
            0,
            64,
            255
        ), array(
            0,
            64,
            128
        ));
        $pdf->setFooterData(array(
            0,
            64,
            0
        ), array(
            0,
            64,
            128
        ));
        
        // set header and footer fonts
        $pdf->setHeaderFont(Array(
            PDF_FONT_NAME_MAIN,
            '',
            PDF_FONT_SIZE_MAIN
        ));
        $pdf->setFooterFont(Array(
            PDF_FONT_NAME_DATA,
            '',
            PDF_FONT_SIZE_DATA
        ));
        
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }
        
        // ---------------------------------------------------------    
        
        // set default font subsetting mode
        $pdf->setFontSubsetting(true);
        
        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('dejavusans', '', 8, '', true);
        
        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage();
        
        // set text shadow effect
        $pdf->setTextShadow(array(
            'enabled' => true,
            'depth_w' => 0.2,
            'depth_h' => 0.2,
            'color' => array(
                196,
                196,
                196
            ),
            'opacity' => 1,
            'blend_mode' => 'Normal'
        ));
        
        // Set some content to print
        //     $html = <<<EOD
        //     <h1>Welcome to <a href="http://www.tcpdf.org" style="text-decoration:none;background-color:#CC0000;color:black;">&nbsp;<span style="color:black;">TC</span><span style="color:white;">PDF</span>&nbsp;</a>!</h1>
        //     <i>This is the first example of TCPDF library.</i>
        //     <p>This text is printed using the <i>writeHTMLCell()</i> method but you can also use: <i>Multicell(), writeHTML(), Write(), Cell() and Text()</i>.</p>
        //     <p>Please check the source code documentation and other examples for further information.</p>
        
        // EOD;
        
        // $exp = '<div class="ng-binding"><table id="sales_table" class="table table-condensed table-hover"><thead><tr><th class="hidden-xs">Created</th><th class="hidden-xs">Bill No.</th><th class="hidden-xs">Net Amt.</th><th class="hidden-xs">Discount</th><th ng-repeat="column in tax_list_all" class="hidden-xs ng-binding ng-scope">Service Tax(5.60%)</th><th ng-repeat="column in tax_list_all" class="hidden-xs ng-binding ng-scope">SBC + KKC(0.40%)</th><th ng-repeat="column in tax_list_all" class="hidden-xs ng-binding ng-scope">productCatspe(23.00%)</th><th ng-repeat="column in tax_list_all" class="hidden-xs ng-binding ng-scope">protax2(2.50%)</th><th class="hidden-xs">Bill Amount</th><th class="hidden-xs">Roundoff</th><th class="hidden-xs">Total</th></tr></thead><tbody><tr ng-repeat="row in saleslist" class="ng-scope"><td class="hidden-xs ng-binding">2016-10-17 19:26:36</td><td class="hidden-xs ng-binding"></td><td class="hideen-xs ng-binding">0.00</td><td class="hideen-xs ng-binding">0.000000</td><td class="hidden-xs ng-binding ng-scope" ng-repeat="th in tax_list_all"> 0.00</td><td class="hidden-xs ng-binding ng-scope" ng-repeat="th in tax_list_all"> 0.00</td><td class="hidden-xs ng-binding ng-scope" ng-repeat="th in tax_list_all"></td><td class="hidden-xs ng-binding ng-scope" ng-repeat="th in tax_list_all"></td><td class="hidden-xs ng-binding">964.60</td><td class="hidden-xs ng-binding">+0.40</td><td class="hidden-xs ng-binding">965</td></tr><tr><td><b>Total:</b></td><td></td><td><b class="ng-binding">218891.6</b></td><td id="subTotalTd"><b class="ng-binding">16501.50</b></td><td></td><td></td><td></td><td></td><td><b class="ng-binding">254008.86</b></td><td><b class="ng-binding">-7.86</b></td><td><b class="ng-binding">254001</b></td><td></td></tr></tbody></table></div>';
        
        
        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $reportHtml, 0, 1, 0, true, '', true);
        
        // ---------------------------------------------------------    
        
        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        
        $file_name = 'sales_report.pdf';
        
        $pdf->Output($file_name, 'I');
        
        echo $file_name;
        die;
        
        //============================================================+
        // END OF FILE
        //============================================================+
        
    }
    
    public function taxTest()
    {
        $array = array(
            array(
                "tax_id" => "1"
            ),
            array(
                "tax_id" => "2"
            )
        );
        
        //initialize
        foreach ($array as $a) {
            $col                  = $a['tax_id'];
            $total_tax_col_{$col} = "0";
        }
        
        $str = '';
        
        //add
        foreach ($array as $a) {
            $col = $a['tax_id'];
            $total_tax_col_{$col} += 5;
        }
        
        //display
        foreach ($array as $a) {
            $col = $a['tax_id'];
            $str .= '<h1>' . $total_tax_col_{$col} . '</h1>';
        }
        
        echo $str;
        die;
    }
    
    public function get_daily_sales_by_branch_pdf()
    {
        $branch_id = '';
        $fromdate  = '';
        
        $year  = date("Y");
        $month = date("m");
        
        
        if (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $branch_id = $_POST['branch_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            
            $from_date  = $_POST['fromdate'];
            $frdate_arr = explode('/', $from_date);
            
            if (!empty($frdate_arr)) {
                $year  = isset($frdate_arr[0]) ? $frdate_arr[0] : '';
                $month = isset($frdate_arr[1]) ? $frdate_arr[1] : '';
            }
            
            $year  = (int) $year;
            $month = (int) $month;
            
        }
        
        elseif (isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $from_date  = $_POST['fromdate'];
            $frdate_arr = explode('/', $from_date);
            
            if (!empty($frdate_arr)) {
                $year  = isset($frdate_arr[0]) ? $frdate_arr[0] : '';
                $month = isset($frdate_arr[1]) ? $frdate_arr[1] : '';
            }
            
            $year  = (int) $year;
            $month = (int) $month;
        }
        
        else if (isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
        }

        if(empty($branch_id)){
            $session_data = $this->session->userdata('logged_in');
            $branch_id = $session_data['branch_id'];
        }
        
        $list = array();
        
        for ($d = 1; $d <= 31; $d++) {
            $time = mktime(12, 0, 0, $month, $d, $year);
            if (date('m', $time) == $month)
                $list[] = date('Y-m-d', $time);
        }
        
        //echo '<pre>';print_r($list);die;
        
        
        $this->load->model('order_model');
        $this->load->model('tax_main_model');
        
        $j = 0;
        
        $dailyDetails = array();
        
        $reportHtml = '';
        
        $reportHtml .= '<div class="ng-binding"><table id="daily_sales_table" class="table table-condensed table-hover"><thead><tr><th class="hidden-xs">Created</th><th class="hidden-xs">Net Amt.</th><th class="hidden-xs">Discount</th>';
        
        $tax_name = $this->tax_main_model->tax_list_all();
        
        $extratd = '';
        
        $total_tax_str = '';
        
        //initialize total tax variable    
        
        foreach ($tax_name as $column) {
            $reportHtml .= '<th ng-repeat="column in tax_list_all" class="hidden-xs" >' . $column['tax_name'] . '(' . $column['tax_percent'] . '%)' . '</th>';
            $extratd .= '<td></td>';
            
            $col                  = $column['tax_id'];
            $total_tax_col_{$col} = "0";
        }
        
        $reportHtml .= '<th class="hidden-xs">Bill Amount</th><th class="hidden-xs">Roundoff</th><th class="hidden-xs">Total</th></tr></thead><tbody>';
        
        $total_Subtotal         = 0;
        $total_Totaldiscount    = 0;
        $total_bill_amountTotal = 0;
        $total_Round            = 0;
        $total_roundoffTotal    = 0;
        $total_Tax              = 0;
        
        
        foreach ($list as $cal_date) {
            
            //$response = $result;
            $response       = array();
            $order_tax_list = array();
            $taxSum         = 0;
            // find tax by order
            
            
            $result = $this->order_model->get_daily_sales_by_branch($cal_date, $branch_id);
            
            // echo '<pre>';print_r($result);die;
            
            $round_off_value_total = 0;
            
            // calculation for round off
            $orderList = $this->order_model->getOrdersByDateAndBranch($cal_date, $branch_id);
            
            foreach ($orderList as $order_data) {
                //getsub_total of order
                $orderItemDetails = $this->order_model->getOrderItemsByOrderId($order_data['order_id']);
                
                $order_tax_data = $this->order_model->order_tax_data($order_data['order_id'], $orderItemDetails['sub_total'], $orderItemDetails['discount']);
                
                
                $order_tax_list2 = array();
                
                foreach ($order_tax_data as $tax_data) {
                    $order_tax_list2[$tax_data['tax_id']] = $tax_data;
                }
                
                $taxSum2   = 0;
                $order_tax = $order_tax_data;
                
                foreach ($tax_name as $column) //tax_name instead of tax_list
                    {
                    $col_tax_id = $column['tax_id'];
                    
                    if (!empty($order_tax)) {
                        $taxSum2 += $order_tax_list2[$col_tax_id]['tax_amount'];
                    }
                }
                $billAmount    = (float) ($orderItemDetails['sub_total']) + $taxSum2 - ((float) ($orderItemDetails['discount']));
                $roundOff      = round((float) ($billAmount));
                $roundoffValue = number_format(($roundOff - (float) ($billAmount)), 2);
                
                $round_off_value_total += $roundoffValue;
                
                //$details[$i] = $order_data;
                // $i++;
                
            }
            ///
            
            foreach ($tax_name as $tax) {
                $tax_id   = $tax['tax_id'];
                // get tax data by date and tax_id
                $tax_data = $this->order_model->get_tax_data_by_date_and_tax_id_daily_sales($cal_date, $tax_id, $branch_id);
                
                $order_tax_list[$tax_id] = $tax_data;
                
                if (!empty($tax_data)) {
                    $taxSum += $order_tax_list[$tax_id]['tax_amount'];
                }
            }
            
            
            $result['bill_amount']    = (float) ($result['sub_total']) + (float) ($taxSum) - ((float) ($result['discount']));
            $result['roundoff']       = round($result['bill_amount'] + $round_off_value_total);
            //$result['roundoff_value'] = number_format(($result['roundoff']-(float)($result['bill_amount'])),2);
            $result['roundoff_value'] = $round_off_value_total;
            
            
            
            $response['roundoff_value'] = isset($result['roundoff_value']) ? $result['roundoff_value'] : 0.00;
            $response['sub_total']      = isset($result['sub_total']) ? $result['sub_total'] : 0;
            $response['tax_free']       = isset($result['tax_free']) ? $result['tax_free'] : 0;
            $response['discount']       = isset($result['discount']) ? $result['discount'] : 0;
            $response['bill_amount']    = isset($result['bill_amount']) ? $result['bill_amount'] : 0;
            //$response['roundoff'] = isset($result['roundoff']) ? $result['roundoff'] : 0;
            $response['roundoff']       = round($result['bill_amount'] + $result['roundoff_value']);
            $response['order_code']     = isset($result['order_code']) ? $result['order_code'] : '';
            $response['created']        = $cal_date;
            
            $total_Subtotal += $response['sub_total'];
            $total_Totaldiscount += $response['discount'];
            $total_bill_amountTotal += $response['bill_amount'];
            $total_Round += $response['roundoff_value'];
            $total_roundoffTotal += $response['roundoff'];
            
            $reportHtml .= '<tr ng-repeat="row in saleslist" class="ng-scope"><td class="hidden-xs ng-binding">' . $response['created'] . '</td><td class="hideen-xs ng-binding">' . $response['sub_total'] . '</td><td class="hideen-xs ng-binding">' . $response['discount'] . '</td>';
            
            // find tax by order
            // get all orders of the given date
            $this->load->model('tax_main_model');
            $tax_list = $this->tax_main_model->tax_list_all();
            
            $order_tax_list = array();
            
            $tax_count = count($tax_list);
            $k         = 0;
            
            $tax_totalstr = '';
            
            foreach ($tax_list as $tax) {
                
                
                
                $tax_id   = $tax['tax_id'];
                // get tax data by date and tax_id
                //$tax_data = $this->order_model->get_tax_data_by_date_and_tax_id($cal_date,$tax_id);
                $tax_data = $this->order_model->get_tax_data_by_date_and_tax_id_daily_sales($cal_date, $tax_id, $branch_id);
                
                
                
                if (!empty($tax_data)) {
                    $order_tax_list[$tax_id] = $tax_data;
                    
                    $totalTaxAmount = (($response['sub_total'] - $response['tax_free'] - $response['discount']) * $tax['tax_percent']) / 100;
                    
                    
                    // for($k=1; $k<=$tax_count; $k++)
                    // {
                    //     $check = "tax_col_";
                    //     ${"check".$k} = "tax_col_";
                    
                    // }
                    
                    // if($k==0)
                    // {
                    //     $t1 += $totalTaxAmount;
                    // }
                    // if($k==1)
                    // {
                    //     $t2 += $totalTaxAmount;
                    // }
                    
                    
                    
                    $reportHtml .= '<td class="hidden-xs">' . $totalTaxAmount . '</td>';
                    
                    $col = $tax['tax_id'];
                    $total_tax_col_{$col} += $totalTaxAmount;
                }
                //$k++;                  
                
            }
            
            
            
            $response['order_tax'] = $order_tax_list;
            
            $details[$j] = $response;
            
            $reportHtml .= '<td class="hidden-xs ng-binding">' . $response['bill_amount'] . '</td><td class="hidden-xs ng-binding">' . $response['roundoff_value'] . '</td><td class="hidden-xs ng-binding">' . $response['roundoff'] . '</td></tr>';
            
            $j++;
        }
        
        foreach ($tax_name as $a) {
            $col = $a['tax_id'];
            $total_tax_str .= '<td><b>' . $total_tax_col_{$col} . '</b></td>';
        }
        
        //$tax_totalstr = '<td class="hidden-xs"><b>'.$t1.'</b></td><td class="hidden-xs"><b>'.$t2.'</b></td>';
        
        // check how many td we should generate
        
        
        $reportHtml .= '<tr><td><b>Total:</b></td><td><b class="ng-binding">' . $total_Subtotal . '</b></td><td id="subTotalTd"><b class="ng-binding">' . $total_Totaldiscount . '</b></td>' . $total_tax_str . '<td><b class="ng-binding">' . $total_bill_amountTotal . '</b></td><td><b class="ng-binding">' . $total_Round . '</b></td><td><b class="ng-binding">' . $total_roundoffTotal . '</b></td><td></td></tr></tbody></table></div>';
        
        
        // $response['status'] = "1";
        // $response['data'] = $details;
        // echo json_encode($response);die;
        
        // get branch name by branch_id
        $this->load->model('branch_model');
        $branchDetails = $this->branch_model->branch_details_by_id($branch_id);
        
        $branch_name = ' Branch ';
        
        if (!empty($branchDetails)) {
            $branch_name = $branchDetails['name'] . ' Branch ';
        }
        
        $printDate = date('F Y', strtotime(end($list)));
        $this->load->library('Pdf');
        
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('24x7Developers');
        $pdf->SetTitle('Daily Sales Report');
        $pdf->SetSubject('Daily Sales Report');
        
        $pdf->SetHeaderData('', '', 'Daily Sales Report - '.$printDate, $branch_name, array(
            0,
            64,
            255
        ), array(
            0,
            64,
            128
        ));
        $pdf->setFooterData(array(
            0,
            64,
            0
        ), array(
            0,
            64,
            128
        ));
        
        // set header and footer fonts
        $pdf->setHeaderFont(Array(
            PDF_FONT_NAME_MAIN,
            '',
            PDF_FONT_SIZE_MAIN
        ));
        $pdf->setFooterFont(Array(
            PDF_FONT_NAME_DATA,
            '',
            PDF_FONT_SIZE_DATA
        ));
        
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }
        
        // set default font subsetting mode
        $pdf->setFontSubsetting(true);
        
        $pdf->SetFont('dejavusans', '', 8, '', true);
        
        
        $pdf->AddPage();
        
        // set text shadow effect
        $pdf->setTextShadow(array(
            'enabled' => true,
            'depth_w' => 0.2,
            'depth_h' => 0.2,
            'color' => array(
                196,
                196,
                196
            ),
            'opacity' => 1,
            'blend_mode' => 'Normal'
        ));
        
        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $reportHtml, 0, 1, 0, true, '', true);
        
        // ---------------------------------------------------------    
        
        $file_name = 'item_wise_sales_report.pdf';
        
        $pdf->Output($file_name, 'I');
        
        echo $file_name;
        die;
        
        
    }
    public function report_branch_pdf()
    {
        $brand_id  = '';
        $fromdate  = '';
        $todate    = '';
        $branch_id = '';
        //print_r($_POST);die;
        
        if (isset($_POST['brand_id']) && $_POST['brand_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '' && isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
            $brand_id  = $_POST['brand_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $branch_id = $_POST['branch_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['brand_id']) && $_POST['brand_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $brand_id = $_POST['brand_id'];
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
            $todate   = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['brand_id']) && $_POST['brand_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
            $brand_id  = $_POST['brand_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . ' 00:00:00'));
        } elseif (isset($_POST['brand_id']) && $_POST['brand_id'] != '' && isset($_POST['todate']) && $_POST['todate'] != '' && isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
            $brand_id  = $_POST['brand_id'];
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . ' 23:59:59'));
        } elseif (isset($_POST['brand_id']) && $_POST['brand_id'] != '' && isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
            $brand_id  = $_POST['brand_id'];
        } elseif (isset($_POST['fromdate']) && $_POST['fromdate'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            $todate   = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
            
        } else if (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $branch_id = $_POST['branch_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            
        } elseif (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $branch_id = $_POST['branch_id'];
            $todate    = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } else if (isset($_POST['brand_id']) && $_POST['brand_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $brand_id = $_POST['brand_id'];
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            
        } elseif (isset($_POST['brand_id']) && $_POST['brand_id'] != '' && isset($_POST['todate']) && $_POST['todate'] != '') {
            $brand_id = $_POST['brand_id'];
            $todate   = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } elseif (isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
        } elseif (isset($_POST['todate']) && $_POST['todate'] != '') {
            $todate = date('Y-m-d H:i:s', strtotime($_POST['todate'] . '23:59:59'));
        } else if (isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
        } else if (isset($_POST['brand_id']) && $_POST['brand_id'] != '') {
            $brand_id = $_POST['brand_id'];
        }
        
        $this->load->model('order_model');
        $this->load->model('tax_main_model');
        
        $result = $this->order_model->branch_sales_report($branch_id, $brand_id, $fromdate, $todate);
        
        $details = array();
        
        $i          = 0;
        $reportHtml = '';
        
        $reportHtml .= ' <div class="ng-binding"><table id="sample-table-3" class="table table-condensed table-hover"><thead><tr><th class="hidden-xs">Date</th><th class="hidden-xs">Bill No.</th><th class="hidden-xs">Net Amt.</th><th class="hidden-xs">Discount</th> ';
        
        
        $tax_name = $this->tax_main_model->tax_list_all();
        
        $extratd = '';
        
        foreach ($tax_name as $column) {
            $extratd .= '<td></td>';
            $reportHtml .= '<th ng-repeat="column in tax_list_all" class="hidden-xs" >' . $column['tax_name'] . '(' . $column['tax_percent'] . '%)' . '</th>';
        }
        
        $reportHtml .= '<th class="hidden-xs">Bill Amount</th><th class="hidden-xs">Roundoff</th><th class="hidden-xs">Total</th></tr></thead><tbody>';
        
        $total_Subtotal         = 0;
        $total_Totaldiscount    = 0;
        $total_bill_amountTotal = 0;
        $total_Round            = 0;
        $total_roundoffTotal    = 0;
        
        $start_bill_code = 0;
        $end_bill_code   = 0;
        
        $totalOrders = count($result);
        
        $p = 0;
        
        foreach ($result as $order_data) {
            $details[$i] = $order_data;
            //print_r($order_data);die;
            
            if ($p == 0) {
                $start_bill_code = $order_data['order_code'];
            }
            
            if ($p == ($totalOrders - 1)) {
                $end_bill_code = $order_data['order_code'];
            }
            
            $order_tax_data = $this->order_model->order_tax_data($order_data['order_id'], $order_data['sub_total'], $order_data['discount']);
            
            $total_Subtotal += $order_data['sub_total'];
            $total_Totaldiscount += $order_data['discount'];
            $total_bill_amountTotal += $order_data['total_bill_amount'];
            $total_Round += $order_data['roundoff_value'];
            $total_roundoffTotal += $order_data['roundoff'];
            
            $reportHtml .= '<tr ng-repeat="row in branchwisereport" class="ng-scope"><td class="hidden-xs ng-binding">' . $order_data['created'] . '</td><td class="hidden-xs ng-binding">' . $order_data['order_code'] . '</td><td class="hideen-xs ng-binding">' . $order_data['sub_total'] . '</td><td class="hidden-xs ng-binding">' . $order_data['discount'] . '</td>';
            
            
            //if(!empty($order_tax_data))
            //{
            $order_tax_list = array();
            
            foreach ($tax_name as $column) {
                $reportHtml .= ' <td class="hidden-xs ng-binding ng-scope" ng-repeat="th in tax_list_all"> ';
                
                foreach ($order_tax_data as $tax_data) {
                    if ($tax_data['tax_id'] == $column['tax_id']) {
                        $reportHtml .= $tax_data['tax_amount'];
                    }
                }
                
                $reportHtml .= '</td>';
            }
            
            
            $details[$i]['order_tax'] = $order_tax_list;
            //}
            
            $reportHtml .= '<td class="hidden-xs ng-binding">' . $order_data['total_bill_amount'] . '</td><td class="hidden-xs ng-binding">' . $order_data['roundoff_value'] . '</td><td class="hidden-xs ng-binding">' . $order_data['roundoff'] . '</td>';
            
            $reportHtml .= '</tr>';
            
            //$result['order_tax'] = $order_tax_list;
            $i++;
            
            $p++;
        }
        
        
        $reportHtml .= '<tr><td><b>Total:</b></td><td></td><td><b class="ng-binding">' . $total_Subtotal . '</b></td><td id="subTotalTd"><b class="ng-binding">' . $total_Totaldiscount . '</b></td>' . $extratd . '<td><b class="ng-binding">' . $total_bill_amountTotal . '</b></td><td><b class="ng-binding">' . $total_Round . '</b></td><td><b class="ng-binding">' . $total_roundoffTotal . '</b></td><td></td></tr></tbody></table></div>';
        
        // $reportHtml .= ' <tr><td><b>Total:</b></td><td></td><td><b class="ng-binding">218891.6</b></td><td id="subTotalTd"><b class="ng-binding">16501.50</b></td><td></td><td></td><td></td><td></td><td><b class="ng-binding">254008.86</b></td><td><b class="ng-binding">-7.86</b></td><td><b class="ng-binding">254001</b></td><td></td></tr></tbody> </table> </div> ';
        
        $this->load->library('Pdf');
        
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('24x7Developers');
        $pdf->SetTitle('Branch Report');
        $pdf->SetSubject('Branch Report');
        //$pdf->SetKeywords('TCPDF, PDF, example, test, guide');   
        
        // get branch name by branch_id
        $this->load->model('branch_model');
        $branchDetails = $this->branch_model->get_branch_details_by_login_branch();
        
        $branch_name = ' Branch ';
        
        if (!empty($branchDetails)) {
            $branch_name = $branchDetails['name'] . ' Branch ';
        }
        
        // set default header data
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
        $pdf->SetHeaderData('', '', 'Branch Report', $branch_name . ': bills from ' . $start_bill_code . ' to ' . $end_bill_code, array(
            0,
            64,
            255
        ), array(
            0,
            64,
            128
        ));
        $pdf->setFooterData(array(
            0,
            64,
            0
        ), array(
            0,
            64,
            128
        ));
        
        // set header and footer fonts
        $pdf->setHeaderFont(Array(
            PDF_FONT_NAME_MAIN,
            '',
            PDF_FONT_SIZE_MAIN
        ));
        $pdf->setFooterFont(Array(
            PDF_FONT_NAME_DATA,
            '',
            PDF_FONT_SIZE_DATA
        ));
        
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }
        
        // ---------------------------------------------------------    
        
        // set default font subsetting mode
        $pdf->setFontSubsetting(true);
        
        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('dejavusans', '', 8, '', true);
        
        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage();
        
        // set text shadow effect
        $pdf->setTextShadow(array(
            'enabled' => true,
            'depth_w' => 0.2,
            'depth_h' => 0.2,
            'color' => array(
                196,
                196,
                196
            ),
            'opacity' => 1,
            'blend_mode' => 'Normal'
        ));
        
        // Set some content to print
        //     $html = <<<EOD
        //     <h1>Welcome to <a href="http://www.tcpdf.org" style="text-decoration:none;background-color:#CC0000;color:black;">&nbsp;<span style="color:black;">TC</span><span style="color:white;">PDF</span>&nbsp;</a>!</h1>
        //     <i>This is the first example of TCPDF library.</i>
        //     <p>This text is printed using the <i>writeHTMLCell()</i> method but you can also use: <i>Multicell(), writeHTML(), Write(), Cell() and Text()</i>.</p>
        //     <p>Please check the source code documentation and other examples for further information.</p>
        
        // EOD;
        
        // $exp = '<div class="ng-binding"><table id="sales_table" class="table table-condensed table-hover"><thead><tr><th class="hidden-xs">Created</th><th class="hidden-xs">Bill No.</th><th class="hidden-xs">Net Amt.</th><th class="hidden-xs">Discount</th><th ng-repeat="column in tax_list_all" class="hidden-xs ng-binding ng-scope">Service Tax(5.60%)</th><th ng-repeat="column in tax_list_all" class="hidden-xs ng-binding ng-scope">SBC + KKC(0.40%)</th><th ng-repeat="column in tax_list_all" class="hidden-xs ng-binding ng-scope">productCatspe(23.00%)</th><th ng-repeat="column in tax_list_all" class="hidden-xs ng-binding ng-scope">protax2(2.50%)</th><th class="hidden-xs">Bill Amount</th><th class="hidden-xs">Roundoff</th><th class="hidden-xs">Total</th></tr></thead><tbody><tr ng-repeat="row in saleslist" class="ng-scope"><td class="hidden-xs ng-binding">2016-10-17 19:26:36</td><td class="hidden-xs ng-binding"></td><td class="hideen-xs ng-binding">0.00</td><td class="hideen-xs ng-binding">0.000000</td><td class="hidden-xs ng-binding ng-scope" ng-repeat="th in tax_list_all"> 0.00</td><td class="hidden-xs ng-binding ng-scope" ng-repeat="th in tax_list_all"> 0.00</td><td class="hidden-xs ng-binding ng-scope" ng-repeat="th in tax_list_all"></td><td class="hidden-xs ng-binding ng-scope" ng-repeat="th in tax_list_all"></td><td class="hidden-xs ng-binding">964.60</td><td class="hidden-xs ng-binding">+0.40</td><td class="hidden-xs ng-binding">965</td></tr><tr><td><b>Total:</b></td><td></td><td><b class="ng-binding">218891.6</b></td><td id="subTotalTd"><b class="ng-binding">16501.50</b></td><td></td><td></td><td></td><td></td><td><b class="ng-binding">254008.86</b></td><td><b class="ng-binding">-7.86</b></td><td><b class="ng-binding">254001</b></td><td></td></tr></tbody></table></div>';
        
        
        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $reportHtml, 0, 1, 0, true, '', true);
        
        // ---------------------------------------------------------    
        
        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        
        $file_name = 'branch_report.pdf';
        
        $pdf->Output($file_name, 'I');
        
        echo $file_name;
        die;
        
    }
    
    public function testDate()
    {
        echo date("Y-m-d H:i:s");
        
        echo '<br>';
        
        echo date_default_timezone_get();
    }
    
    public function testOrders()
    {
        $this->load->model('order_model');
        $this->load->model('tax_main_model');
        //echo 'dq';die;
        $order_date = '2017-02-04';
        $branch_id  = '12';
        
        $orderList = $this->order_model->getOrdersByDateAndBranch($order_date, $branch_id);
        
        $tax_name = $this->tax_main_model->tax_list_all();
        
        
        $i       = 0;
        $details = array();
        
        $round_off_value_total = 0;
        
        foreach ($orderList as $order_data) {
            //getsub_total of order
            $orderItemDetails = $this->order_model->getOrderItemsByOrderId($order_data['order_id']);
            
            $order_tax_data = $this->order_model->order_tax_data($order_data['order_id'], $orderItemDetails['sub_total'], $orderItemDetails['discount']);
            
            
            $order_tax_list = array();
            
            foreach ($order_tax_data as $tax_data) {
                $order_tax_list[$tax_data['tax_id']] = $tax_data;
            }
            
            $taxSum    = 0;
            $order_tax = $order_tax_data;
            
            foreach ($tax_name as $column) {
                $col_tax_id = $column['tax_id'];
                
                if (!empty($order_tax)) {
                    $taxSum += $order_tax_list[$col_tax_id]['tax_amount'];
                }
            }
            $order_data['bill_amount']    = (float) ($order_data['sub_total']) + $taxSum;
            $order_data['roundoff']       = round((float) ($order_data['bill_amount']));
            $order_data['roundoff_value'] = number_format(($order_data['roundoff'] - (float) ($order_data['bill_amount'])), 2);
            
            $round_off_value_total += $order_data['roundoff_value'];
            
            
            $details[$i] = $order_data;
            $i++;
            
        }
        echo $round_off_value_total;
        
        echo '<pre>';
        print_r($details);
        die;
        
    }

    /*** Fenil ***/
    public function daily_sales_report_default()
    {
        $year  = date("Y");
        $month = date("m");
        $response = array();
        
        $list = array();
        
        for ($d = 1; $d <= 31; $d++) {
            $time = mktime(12, 0, 0, $month, $d, $year);
            if (date('m', $time) == $month)
                $list[] = date('Y-m-d', $time);
        }
        $j = 0;
        foreach ($list as $cal_date) 
        {
            //echo"<pre>";print_r($cal_date);
            $this->load->model('order_model');
            $result = $this->order_model->get_details_from_daily_sales_default($cal_date);

            //echo"<pre>";print_r($result); 
            if(empty($result))
            {
                $result['daily_sales_id'] = "0";
                $result['branch_id'] = "0";
                $result['created'] = $cal_date;
                $result['net_amount'] = "0.00";
                $result['tax_free'] = "0.00";
                $result['discount'] = "0.00";
                $result['bill_amount'] = "0.00";
                $result['round_off'] = "0.00";
                $result['total'] = "0";

                
                $result['SGST'] = "0.00";
                $result['CGST'] = "0.00";
               
                //echo"<pre>";print_r($result);
                
            }
            else
            {
                //$result['VAT'] = "0.00";
                //echo "Key exists!";
                
            }
            
           
         $details[$j] = $result;
         $j++;
        }
        
        $response['status'] = "1";
        $response['data']   = $details;
        echo json_encode($response);
        die;
        
    }

    public function daily_sales_report()
    {
        $branch_id = '';
        $fromdate  = '';
        
        $year  = date("Y");
        $month = date("m");
        
        
        if (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $branch_id = $_POST['branch_id'];
            $fromdate  = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));
            
            $from_date  = $_POST['fromdate'];
            $frdate_arr = explode('/', $from_date);
            
            if (!empty($frdate_arr)) {
                $year  = isset($frdate_arr[0]) ? $frdate_arr[0] : '';
                $month = isset($frdate_arr[1]) ? $frdate_arr[1] : '';
            }
            
            $year  = (int) $year;
            $month = (int) $month;
            
        }
        
        elseif (isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $from_date  = $_POST['fromdate'];
            $frdate_arr = explode('/', $from_date);
            
            if (!empty($frdate_arr)) {
                $year  = isset($frdate_arr[0]) ? $frdate_arr[0] : '';
                $month = isset($frdate_arr[1]) ? $frdate_arr[1] : '';
            }
            
            $year  = (int) $year;
            $month = (int) $month;
        }
        
        else if (isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
        }

        $this->load->model('tax_main_model');
        $tax_list = $this->tax_main_model->tax_list_all();
            
        
        $list = array();
        
        for ($d = 1; $d <= 31; $d++) {
            $time = mktime(12, 0, 0, $month, $d, $year);
            if (date('m', $time) == $month)
                $list[] = date('Y-m-d', $time);
        }
        $j = 0;
        $k = 0;

        foreach ($list as $cal_date) 
        {
            //echo"<pre>";print_r($cal_date);
            $this->load->model('order_model');
            $result = $this->order_model->get_details_from_daily_sales($cal_date,$_POST['branch_id']);

            if(empty($result))
            {
                $result['daily_sales_id'] = "0";
                $result['branch_id'] = "0";
                $result['created'] = $cal_date;
                $result['net_amount'] = "0.00";
                $result['tax_free'] = "0.00";
                $result['discount'] = "0.00";
                $result['bill_amount'] = "0.00";
                $result['round_off'] = "0.00";
                $result['total'] = "0";
                $result['SGST'] = "0.00";
                $result['CGST'] = "0.00";

            }
            else
            {
                //$result['VAT'] = "0.00";
                //echo "Key exists!";
                
            }

            //echo"<pre>"; print_r($result);

            $details[$k] = $result;
            
            $k++;
           $j++;
        }
        
        $response['status'] = "1";
        $response['data'] = $details;
       
        echo json_encode($response);
        die;
    }

    public function store_item_report()
    {
        $this->load->view('report/store_items');
    }

    public function store_items_data()
    {
        $response = array();

        $details = array();

        $branch_id = $_POST['branch_id'];

        if (isset($_POST['fromdate']) && $_POST['fromdate'] != '')
        {
            $fromdate = $_POST['fromdate'];
            $fromdate = explode('/', $fromdate);
            $year = $fromdate[0];
            $month = $fromdate[1];
        }
      
        // get categories
        $this->load->model('category_model');
        $category_list = $this->category_model->category_list();

        if(!empty($category_list))
        {
            $i = 0;
            foreach ($category_list as $cat_list) 
            {
                $category_id = $cat_list['category_id'];

                $this->load->model('storeinward_model');
                $store_item_list = $this->storeinward_model->store_items_report($category_id,$branch_id,$year,$month);
                $item_data = array();

                if($store_item_list)
                {
                    $details[$i] = $cat_list;
                    //$item_data = array();
                    $j = 0;

                    foreach ($store_item_list as $storeItems) 
                    {
                        $item_data[] = $storeItems;
                        $j++;
                    }
                }
                //print_r($item_data);die;
                $details[$i]['store_items'] = $item_data;               
                $i++;
            }

            
        }

        $sale_with_tax = $this->storeinward_model->sale_with_tax($month,$branch_id);
        $details['sale_with_tax'] = $sale_with_tax;

        $response['data'] = $details;
        echo json_encode($response);
        die;
    }

    public function daily_purchase()
    {
        $this->load->view('report/daily_purchase');

    }

    public function daily_purchase_data()
    {
        //print_r($_POST);die;
        if(isset($_POST['filterdate']) && $_POST['filterdate']!='')
        {
            $exp_date_slash = urldecode($_POST['filterdate']);
            $exp_date_dash = str_replace('/', '-', $exp_date_slash);
            $exp_date = date('Y-m-d', strtotime($exp_date_dash));
            $filterdate = date("Y-m-d",strtotime($exp_date));  
            //echo $filterdate;die;

            $this->load->model('storeinward_model');
            $result = $this->storeinward_model->daily_purchase_data($filterdate);
           // print_r($result);
            $response = array();
            $response['status'] = "1";
            $response['data'] = $result;

            echo json_encode($response);die;
        }       
    }
    
    public function product_recipe()
    {
      $this->load->view('report/product_recipe');
    }

    public function product_recipe_data()
    {
        $branch_id = $_POST['branch_id'];

        if(isset($_POST['filterdate']) && $_POST['filterdate']!='')
        {
            $exp_date_slash = urldecode($_POST['filterdate']);
            $exp_date_dash = str_replace('/', '-', $exp_date_slash);
            $exp_date = date('Y-m-d', strtotime($exp_date_dash));
            $filterdate = date("Y-m-d",strtotime($exp_date));  

            $this->load->model('order_model');

            $result = $this->order_model->total_product_by_date($filterdate,$branch_id);

            $storeProductArr = array();

            foreach ($result as $product) 
            {
                //print_r($product);
                 $data = array();
                $data['branch_id'] = $product['branch_id'];
                $data['product_id'] = $product['product_id'];
                $data['total_product_qty'] = $product['qty'];

                

                $store_products = $this->order_model->get_store_products_by_product_id($product['product_id']);
                // $storeData = array();
                
                // $storeData['store_products'] = $store_products;

                $storedata = array();
                $i = 0;

                

                foreach ($store_products as $store_product) 
                {
                    //make an array of today's used store products
                    $storeProductArr[] = $store_product['store_product_id'];
                   
                    $storedata[$i]['store_product_id'] = $store_product['store_product_id'];
                    $storedata[$i]['qty']= $store_product['qty'];
                    $i++;
                }
                
                $data['store_products'] = $storedata;

                //print_r($data);
            }

            //echo '<pre>';print_r($storeProductArr);
           // $total_qty = 0;

            $spArr = array();
            $k = 0;

            foreach ($storeProductArr as $sp) 
            {
                $na = array();
                $total_qty = 0;
                $na['store_product_id'] = $sp;
               foreach ($result as $product) 
                {
                    
                    $data['total_product_qty'] = $product['qty'];                    

                    $store_products = $this->order_model->get_store_products_by_product_id($product['product_id']);
                
                    $storedata = array();
                    $i = 0;
                    

                    foreach ($store_products as $store_product) 
                    {
                        //make an array of today's used store products
                        if($sp==$store_product['store_product_id'])
                        {
                           $total_qty += $product['qty'] * $store_product['qty'];

                           $na['store_product_quantity'] = $total_qty ;

                           // get kitchen inward details by store_product_id and date

                           
                           //$filterdate1 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $filterdate) ) ));
                           //echo $filterdate;die;
                           $result1 =  $this->order_model->get_second_largest_date_from_kitchen();
                           //echo $result['created'];die;

                           $opening_details = $this->order_model->get_kitchen_inward_details($sp,$result1['created']);

                           $closing_details = $this->order_model->get_kitchen_inward_details($sp,$filterdate);

                           $na['opening_stock'] = $opening_details['remaining_qty'];
                           $na['closing_stock'] = $closing_details['remaining_qty'];
                           $na['actual_weight'] = $na['opening_stock'] + $closing_details['today_inward_qty'] - $closing_details['remaining_qty'];

                           $na['ideal_weight'] = $na['store_product_quantity'];
                           $na['varience'] = $na['actual_weight'] - $na['ideal_weight'];



                           $na['store_product_name'] = $store_product['store_product_name'] ;
                            $na['unit'] = $store_product['unit'] ;
                            $na['price'] = $store_product['price'] ;
                        }

                        
                       
                        $storedata[$i]['store_product_id'] = $store_product['store_product_id'];
                        $storedata[$i]['qty']= $store_product['qty'];
                        $i++;
                    }

                    $spArr[$k] = $na;
                    
                    $data['store_products'] = $storedata;

                }
                $k++;
            }

            $spArr = $this->multi_unique($spArr);

            $response['status'] = '1';
            $response['data'] = $spArr;

            //echo '<pre>';print_r($spArr);die;


        }
        echo json_encode($response);die;
    }

    function multi_unique($src)
    {
        $output = array_map("unserialize",
        array_unique(array_map("serialize", $src)));
        return $output;
    }

    public function daily_sales_rpt_by_brand() {

        $branch_id = '';
        $brand_id = '';
        $fromdate = '';


        $year = date("Y");
        $month = date("m");

        $list = array();

        for ($d = 1; $d <= 31; $d++) {
            $time = mktime(12, 0, 0, $month, $d, $year);
            if (date('m', $time) == $month)
                $list[] = date('Y-m-d', $time);
        }


        $this->load->model('order_model');

        $j = 0;

        $dailyDetails = array();

        foreach ($list as $cal_date) {

            $result = $this->order_model->get_daily_sales_by_branch_and_brand($cal_date, $branch_id, $brand_id);

            //$response = $result;
            $response = array();

            /* $response['roundoff_value'] = isset($result['roundoff_value']) ? $result['roundoff_value'] : 0.00;
              $response['sub_total'] = isset($result['sub_total']) ? $result['sub_total'] : 0;
              $response['tax_free'] = isset($result['tax_free']) ? $result['tax_free'] : 0;
              $response['discount'] = isset($result['discount']) ? $result['discount'] : 0;
              $response['bill_amount'] = isset($result['bill_amount']) ? $result['bill_amount'] : 0;
              $response['roundoff'] = isset($result['roundoff']) ? $result['roundoff'] : 0;
              $response['created'] = $cal_date; */

            //$response['roundoff_value'] = isset($result['roundoff_value']) ? $result['roundoff_value'] : 0.00;
            $response['sub_total'] = isset($result['sub_total']) ? $result['sub_total'] : 0;
            $response['CGST'] = $result['sub_total']*0.025;
            $response['SGST'] = $result['sub_total']*0.025;
            $response['bill_amount'] = $response['sub_total'] + ($response['sub_total']*0.05);
            //$response['roundoff'] = $response['bill_amount'];
            //$response['roundoff'] = isset($result['roundoff']) ? $result['roundoff'] : 0;
            $response['created'] = $cal_date;

            // find tax by order
            // get all orders of the given date
            /*$this->load->model('tax_main_model');
            $tax_list = $this->tax_main_model->tax_list_all();

            $order_tax_list = array();

            foreach ($tax_list as $tax) {
                $tax_id = $tax['tax_id'];
                // get tax data by date and tax_id
                $tax_data = $this->order_model->get_tax_data_by_date_and_tax_id($cal_date, $tax_id);

                $order_tax_list[$tax_id] = $tax_data;
            }

            $response['order_tax'] = $order_tax_list;*/

            $details[$j] = $response;

            $j++;
        }


        $response['status'] = "1";
        $response['data'] = $details;
        echo json_encode($response);
        die;
    }


    public function get_daily_sales_by_branch_and_brand() {

        $branch_id = '';
        $fromdate = '';

        $year = date("Y");
        $month = date("m");

        $brand_id = '';

        if (isset($_POST['brand_id']) && $_POST['brand_id'] != '') {
            $brand_id = $_POST['brand_id'];
        } else {
            $brand_id = '';
        }

        if (isset($_POST['branch_id']) && $_POST['branch_id'] != '' && isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $branch_id = $_POST['branch_id'];
            $fromdate = date('Y-m-d H:i:s', strtotime($_POST['fromdate'] . '00:00:00'));

            $from_date = $_POST['fromdate'];
            $frdate_arr = explode('/', $from_date);

            if (!empty($frdate_arr)) {
                $year = isset($frdate_arr[0]) ? $frdate_arr[0] : '';
                $month = isset($frdate_arr[1]) ? $frdate_arr[1] : '';
            }

            $year = (int) $year;
            $month = (int) $month;
        } elseif (isset($_POST['fromdate']) && $_POST['fromdate'] != '') {
            $from_date = $_POST['fromdate'];
            $frdate_arr = explode('/', $from_date);

            if (!empty($frdate_arr)) {
                $year = isset($frdate_arr[0]) ? $frdate_arr[0] : '';
                $month = isset($frdate_arr[1]) ? $frdate_arr[1] : '';
            }

            $year = (int) $year;
            $month = (int) $month;
        } else if (isset($_POST['branch_id']) && $_POST['branch_id'] != '') {
            $branch_id = $_POST['branch_id'];
        }

        $list = array();

        for ($d = 1; $d <= 31; $d++) {
            $time = mktime(12, 0, 0, $month, $d, $year);
            if (date('m', $time) == $month)
                $list[] = date('Y-m-d', $time);
        }

        //echo '<pre>';print_r($list);die;


        $this->load->model('order_model');

        $j = 0;

        $dailyDetails = array();
        //echo "<pre>";
        //print_r($list);
        //exit;
        foreach ($list as $cal_date) {

            $result = $this->order_model->get_daily_sales_by_branch_and_brand($cal_date, $branch_id, $brand_id);
            //$result = $this->order_model->get_daily_sales_by_branch($cal_date,$branch_id);
            //$response = $result;
            $response = array();


            /* $response['roundoff_value'] = isset($result['roundoff_value']) ? $result['roundoff_value'] : 0.00;
              $response['sub_total'] = isset($result['sub_total']) ? $result['sub_total'] : 0;
              $response['tax_free'] = isset($result['tax_free']) ? $result['tax_free'] : 0;
              $response['discount'] = isset($result['discount']) ? $result['discount'] : 0;
              $response['bill_amount'] = $result['sub_total']-$response['discount'];
              $response['roundoff'] = $response['bill_amount'];
              $response['created'] = $cal_date;/

            $response['roundoff_value'] = isset($result['roundoff_value']) ? $result['roundoff_value'] : 0.00;
            $response['sub_total'] = isset($result['sub_total']) ? $result['sub_total'] : 0;
            $response['tax_free'] = isset($result['tax_free']) ? $result['tax_free'] : 0;
            $response['discount'] = isset($result['discount']) ? $result['discount'] : 0;
            $response['bill_amount'] = $result['sub_total'] - $response['discount'];
            //$response['roundoff'] = $response['bill_amount'];
            $response['roundoff'] = isset($result['roundoff']) ? $result['roundoff'] : 0;
            $response['created'] = $cal_date;*/
            $response['sub_total'] = isset($result['sub_total']) ? $result['sub_total'] : 0;
            $response['CGST'] = $result['sub_total']*0.025;
            $response['SGST'] = $result['sub_total']*0.025;
            $response['bill_amount'] = $response['sub_total'] + ($response['sub_total']*0.05);
            //$response['roundoff'] = $response['bill_amount'];
            //$response['roundoff'] = isset($result['roundoff']) ? $result['roundoff'] : 0;
            $response['created'] = $cal_date;

            // find tax by order
            // get all orders of the given date
            /*$this->load->model('tax_main_model');
            $tax_list = $this->tax_main_model->tax_list_all();

            $order_tax_list = array();

            foreach ($tax_list as $tax) {
                $tax_id = $tax['tax_id'];
                // get tax data by date and tax_id
                $tax_data = $this->order_model->get_tax_data_by_date_and_tax_id_daily_sales_by_brand($cal_date, $tax_id, $branch_id, $brand_id);

                $order_tax_list[$tax_id] = $tax_data;
            }

            $response['order_tax'] = $order_tax_list;*/

            $details[$j] = $response;

            $j++;
        }


        $response['status'] = "1";
        $response['data'] = $details;

        ///echo "<pre>";
        //print_r($response['data']);
        //exit;
        echo json_encode($response);
        die;
    }
}

?>