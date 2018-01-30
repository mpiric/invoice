<?php

class Order extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //$this->load->helper('form');
        $this->load->model('order_model');
        
    }
    public function index()
    {
        //$data['order'] = $this->order_model->get_data();
        $this->load->view('order/index');
        
    }
    
    public function create()
    {
        $sess_arr = $this->session->userdata('logged_in');
        //echo '<pre>';print_r($sess_arr);die;
        $this->load->helper('form');
        $this->load->library('form_validation');
        
        $is_validate = $this->order_model->validateOrder();
        
        if ($is_validate == TRUE) {
            //echo'<pre>'; print_r($_POST);die;
            $result = $this->order_model->insert_data();
            if ($result == TRUE) {
                echo "Created Successfully";
                redirect(array(
                    "order/index"
                ));
            } else {
                //echo"ifelse";
                $this->load->view('order/create');
            }
            
        } else {
            //echo"else";
            $this->load->view('order/create');
        }
    }
    
    public function update($id = '')
    {
        
        $this->load->library('form_validation');
        
        $details = $this->order_model->get_details_by_id($id);
        
        $is_validate = $this->order_model->validateOrder();
        
        if ($is_validate == true) {
            // update
            
            $result = $this->order_model->update_data($id);
            
            if ($result == true) {
                // redirect to list
                //$this->session->set_flashdata('success','City updated successfully');
                redirect('order/index');
            }
        }
        
        $this->load->view('order/create', array(
            'details' => $details
        ));
        
    }
    
    public function delete($id = '')
    {
        if ($id != '') {
            $this->order_model->delete($id);
            echo "order deleted successfully";
            //$this->session->set_flashdata('success','City deleted successfully');
        }
        redirect('order/index');
    }
    
    public function get_next_bill_id()
    {
        $response = array();
        
        /// get next bill id
        //$response['next_bill_id'] = $this->order_model->getMaxOrderId();	
        $response['next_bill_id'] = $this->order_model->get_next_bill_code();
        echo json_encode($response);
        die;
    }
    
    public function order_list_for_dd()
    {
        $result = $this->order_model->order_list_for_dd();
        
        $response           = array();
        $response['status'] = "1";
        $response['data']   = $result;
        
        echo json_encode($response);
        die;
    }
    
    // add live items to order
    public function add_order_live()
    {
        ///echo print_r($_POST);
        
        $response = array();
        
        // insert into order_live table
        if (!empty($_POST)) {
            $result = $this->order_model->insert_into_order_live($_POST);
            
            if ($result == TRUE) {
                $response['status'] = "1";
                $response['data']   = "Order created successfully";
            } else {
                $response['status'] = "0";
                $response['data']   = "Error creating Order.";
            }
        }
        echo json_encode($response);
        die;
    }
    
    public function add_order()
    {
        //echo print_r($_POST);
        
        $response = array();
        
        // insert into order_live table
        if (!empty($_POST)) {
            $result = $this->order_model->insert_into_order($_POST);
            
            // if(isset($_POST['order_type']) && ( $_POST['order_type']==1 || $_POST['order_type']=='1' ) )
            // {
            // 	$_POST['order_id'] = $result;
            // 	//echo $_POST['order_id'];
            // 	$result_live = $this->order_model->insert_into_order_live($_POST);
            // }
            // else
            // {
            // 	$result_live = TRUE;
            // }
            
            $_POST['order_id'] = $result;
            $result_live       = $this->order_model->insert_into_order_live($_POST);
            
            
            if ($result > 0 && $result_live == TRUE) {
                $response['status']   = "1";
                $response['data']     = "Order created successfully";
                $response['order_id'] = $result;
            } else {
                $response['status'] = "0";
                $response['data']   = "Error creating Order.";
            }
        }
        echo json_encode($response);
        die;
    }
    
    public function add_order_items_live()
    {
        $response = array();
        
        if (isset($_POST['order_id']) && $_POST['order_id'] != '') {
            
            $order_id = $_POST['order_id'];
            
            if (isset($_POST['products']) && $_POST['products'] != '') {
                $product_arr = json_decode($_POST['products'], true);
                // echo '<pre>';print_r($product_arr);die;
                
                $result = $this->order_model->add_items_order_live($order_id, $product_arr);
                
                if ($result == TRUE) {
                    $response['status'] = "1";
                    $response['data']   = "OrderItem added successfully";
                } else {
                    $response['status'] = "0";
                    $response['data']   = "Error creating OrderItem.";
                }
            }
        }
        // echo print_r($_POST);
        // echo print_r(json_decode($_POST['products']));
        echo json_encode($response);
        die;
    }
    
    public function add_order_items()
    {
        $response = array();
        //print_r($_POST);die;
        
        if (isset($_POST['order_id']) && $_POST['order_id'] != '') {
            
            $order_id = $_POST['order_id'];
            
            if (isset($_POST['products']) && $_POST['products'] != '') {
                $product_arr = json_decode($_POST['products'], true);
                // echo '<pre>';print_r($product_arr);die;
                
                $result = $this->order_model->add_items_order($order_id, $product_arr);
                
                if ($result == TRUE) {
                    $response['status'] = "1";
                    $response['data']   = "OrderItem added successfully";
                } else {
                    $response['status'] = "0";
                    $response['data']   = "Error creating OrderItem.";
                }
            }
        }
        // echo print_r($_POST);
        // echo print_r(json_decode($_POST['products']));
        echo json_encode($response);
        die;
    }
    
    
    public function update_order_items_live()
    {
        $response   = array();
        //echo '<pre>';print_r($_POST);die;
        $order_data = array();
        
        if (isset($_POST['tax']) && $_POST['tax'] != "") {
            $order_data['tax'] = $_POST['tax'];
        }
        if (isset($_POST['total_amount']) && $_POST['total_amount'] != "") {
            $order_data['total_amount'] = $_POST['total_amount'];
        }
        if (isset($_POST['return_amount']) && $_POST['return_amount'] != "") {
            $order_data['return_amount'] = $_POST['return_amount'];
        }
        if (isset($_POST['order_id']) && $_POST['order_id'] != "") {
            $order_data['order_id'] = $_POST['order_id'];
        }
        
        $order_items_data = array();
        
        if (isset($_POST['product_id']) && $_POST['product_id'] != "") {
            $order_items_data['product_id'] = $_POST['product_id'];
        }
        if (isset($_POST['quantity']) && $_POST['quantity'] != "") {
            $order_items_data['quantity'] = $_POST['quantity'];
        }
        if (isset($_POST['order_id']) && $_POST['order_id'] != "") {
            $order_items_data['order_id'] = $_POST['order_id'];
        }
        
        
        
        // update in order table
        $order_result = $this->order_model->update_order_live($order_data);
        
        // update in order items table
        $order_items_result = $this->order_model->update_order_items_live($order_items_data);
        
        if ($order_result == TRUE && $order_items_result == TRUE) {
            $response['status'] = "1";
            $response['data']   = "Update successful";
        } else {
            $response['status'] = "0";
            $response['data']   = "Error";
        }
        
        echo json_encode($response);
        die;
    }
    
    public function update_order_items()
    {
        $response   = array();
        //echo '<pre>';print_r($_POST);die;
        $order_data = array();
        
        if (isset($_POST['tax']) && $_POST['tax'] != "") {
            $order_data['tax'] = $_POST['tax'];
        }
        if (isset($_POST['total_amount']) && $_POST['total_amount'] != "") {
            $order_data['total_amount'] = $_POST['total_amount'];
        }
        if (isset($_POST['return_amount']) && $_POST['return_amount'] != "") {
            $order_data['return_amount'] = $_POST['return_amount'];
        }
        if (isset($_POST['order_id']) && $_POST['order_id'] != "") {
            $order_data['order_id'] = $_POST['order_id'];
        }
        
        $order_items_data = array();
        
        if (isset($_POST['product_id']) && $_POST['product_id'] != "") {
            $order_items_data['product_id'] = $_POST['product_id'];
        }
        if (isset($_POST['quantity']) && $_POST['quantity'] != "") {
            $order_items_data['quantity'] = $_POST['quantity'];
        }
        if (isset($_POST['order_id']) && $_POST['order_id'] != "") {
            $order_items_data['order_id'] = $_POST['order_id'];
        }
        if (isset($_POST['order_item_id']) && $_POST['order_item_id'] != "") {
            $order_items_data['order_item_id'] = $_POST['order_item_id'];
        }
        
        
        // update in order table
        $order_result = $this->order_model->update_order($order_data);
        
        // update in order items table
        $order_items_result = $this->order_model->update_order_items($order_items_data);
        
        if ($order_result == TRUE && $order_items_result == TRUE) {
            $response['status'] = "1";
            $response['data']   = "Update successful";
        } else {
            $response['status'] = "0";
            $response['data']   = "Error";
        }
        
        echo json_encode($response);
        die;
    }
    
    public function delete_order_items_live()
    {
        $response     = array();
        //echo '<pre>';print_r($_POST);die;
        $order_data   = array();
        $tax          = '';
        $given_amount = '';
        
        if (isset($_POST['tax']) && $_POST['tax'] != "") {
            $tax = $_POST['tax'];
        }
        if (isset($_POST['given_amount']) && $_POST['given_amount'] != "") {
            $given_amount = $_POST['given_amount'];
        }
        
        $order_items_data = array();
        
        if (isset($_POST['product_id']) && $_POST['product_id'] != "") {
            $order_items_data['product_id'] = $_POST['product_id'];
        }
        if (isset($_POST['order_id']) && $_POST['order_id'] != "") {
            $order_items_data['order_id'] = $_POST['order_id'];
        }
        
        // update in order items table
        $order_items_result = $this->order_model->delete_order_items_live($order_items_data, $tax, $given_amount);
        
        if ($order_items_result == TRUE) {
            $response['status'] = "1";
            $response['data']   = "Delete successful";
        } else {
            $response['status'] = "0";
            $response['data']   = "Error";
        }
        
        echo json_encode($response);
        die;
    }
    
    public function delete_order_items()
    {
        $response     = array();
        // /echo '<pre>';print_r($_POST);die;
        $order_data   = array();
        $tax          = '';
        $given_amount = '';
        
        if (isset($_POST['tax']) && $_POST['tax'] != "") {
            $tax = $_POST['tax'];
        }
        if (isset($_POST['given_amount']) && $_POST['given_amount'] != "") {
            $given_amount = $_POST['given_amount'];
        }
        
        $order_items_data = array();
        
        if (isset($_POST['product_id']) && $_POST['product_id'] != "") {
            $order_items_data['product_id'] = $_POST['product_id'];
        }
        if (isset($_POST['order_id']) && $_POST['order_id'] != "") {
            $order_items_data['order_id'] = $_POST['order_id'];
            
            // if(isset($_POST['tax_to_be_deducted']) && $_POST['tax_to_be_deducted']!="")
            // {
            // 	$order_tax_data = array();
            // 	$order_tax_data['order_id'] = $_POST['order_id'];
            // 	$order_tax_data['tax_percent'] = $_POST['tax_to_be_deducted'];
            // }
            
        }
        if (isset($_POST['order_item_id']) && $_POST['order_item_id'] != "") {
            //$order_items_data['order_item_id'] = $_POST['order_item_id'];
            $order_items_data['order_item_id'] = $_POST['product_id'];
        }
        
        // update in order items table
        $order_items_result = $this->order_model->delete_order_items($order_items_data, $tax, $given_amount);
        
        if ($order_items_result == TRUE) {
            $response['status'] = "1";
            $response['data']   = "Delete successful";
        } else {
            $response['status'] = "0";
            $response['data']   = "Error";
        }
        
        echo json_encode($response);
        die;
    }
    
    public function save_order_to_main_table()
    {
        $response = array();
        
        //echo '<pre>';print_r($_POST);die;
        
        if (isset($_POST['order_id']) && $_POST['order_id'] != '') {
            $order_id = $_POST['order_id'];
            
            // get order data from live table and insert into main table
            $order_live_details = $this->order_model->get_order_details_by_id_live($order_id);
            
            
            
            // insert order
            $order_result = $this->order_model->insert_order($order_live_details);
            
            
            //echo '<pre>';print_r($order_result);die;
            
            //get order items data from live table and insert into main table
            $order_items_live_details = $this->order_model->get_order_items_by_id_live($order_id);
            
            $inserted_order_items = array();
            
            if (!empty($order_items_live_details)) {
                foreach ($order_items_live_details as $items) {
                    
                    // insert order
                    $inserted_order_items = $this->order_model->insert_order_items($items);
                    
                }
            }
            
            if ($order_result == true && !empty($order_result)) {
                $response['status'] = "1";
                $response['data']   = "order placed successful";
            } else {
                $response['status'] = "0";
                $response['data']   = "Error";
            }
            
        }
        
        echo json_encode($response);
        die;
        
    }
    public function order_list()
    {
        $result = $this->order_model->get_data_list();
        
        $response           = array();
        $response['status'] = "1";
        $response['data']   = $result;
        
        echo json_encode($response);
        die;
    }
    
    public function get_order_details_by_id_live()
    {
        $response = array();
        
        if (isset($_POST['order_id']) && $_POST['order_id'] != '') {
            $result = $this->order_model->get_order_details_by_id_live($_POST['order_id']);
            
            //get order items data from live table and insert into main table
            $order_items_live_details = $this->order_model->get_all_data_order_items_live($_POST['order_id']);
            
            $response['status']      = "1";
            $response['order']       = $result;
            $response['order_items'] = $order_items_live_details;
        }
        
        echo json_encode($response);
        die;
    }
    
    
    public function get_order_details_by_id()
    {
        $response = array();
        
        if (isset($_POST['order_id']) && $_POST['order_id'] != '') {
            $result = $this->order_model->get_details_by_id($_POST['order_id']);
            
            //get order items data from live table and insert into main table
            $order_items_live_details = $this->order_model->get_all_data_order_items($_POST['order_id']);
            
            $response['status']      = "1";
            $response['order']       = $result;
            $response['order_items'] = $order_items_live_details;
        }
        
        echo json_encode($response);
        die;
    }
    
    public function change_given_amount()
    {
        $response = array();
        
        if (isset($_POST['order_id']) && $_POST['order_id'] != '') {
            $order_id = $_POST['order_id'];
            
            $data                  = array();
            $data['return_amount'] = $_POST['return_amount'];
            $data['given_amount']  = $_POST['given_amount'];
            
            // update order table
            $result = $this->order_model->update_order_on_change_of_given_amount($order_id, $data);
            
            if ($result == true) {
                $response['status'] = "1";
                $response['data']   = "success";
            } else {
                $response['status'] = "0";
                $response['data']   = "Error";
            }
        }
        
        echo json_encode($response);
        die;
    }
    
    public function print_order()
    {
        // remove order from live table
        $response = array();
        
        if (isset($_POST['order_id']) && $_POST['order_id'] != '') {
            $order_id = $_POST['order_id'];
            
            // delete order table
            $result = $this->order_model->delete_order_from_live_tbl($order_id);
            
            $updateData                    = array();
            $updateData['order_date_time'] = date("Y-m-d H:i:s");
            $updateData['is_print']        = 1;
            $result2                       = $this->order_model->update_order_data_by_id($updateData, $_POST['order_id']);
            
            if ($result == true && $result2 == true) {
                $response['status'] = "1";
                $response['data']   = "success";
            } else {
                $response['status'] = "0";
                $response['data']   = "Error";
            }
        }
        
        echo json_encode($response);
        die;
    }
    
    public function check_for_previous_order()
    {
        $response = array();
        
        if (isset($_POST['order_code']) && $_POST['order_code'] != '' && !empty($_POST['brand_id'])) {
            $order_code = $_POST['order_code'];
            $brand_id   = $_POST['brand_id'];
            
            $result = $this->order_model->check_for_previous_order($order_code, $brand_id);
            
            if ($result == true) {
                $response['status'] = "1";
                $response['data']   = "success";
            } else {
                $response['status'] = "0";
                $response['data']   = "Error";
            }
        }
        
        echo json_encode($response);
        die;
    }
    
    public function reset_order()
    {
        // remove order from table
        $response = array();
        
        if (isset($_POST['order_id']) && $_POST['order_id'] != '') {
            $order_id = $_POST['order_id'];
            
            // delete order table
            $result       = $this->order_model->reset_order($order_id);
            $result_items = $this->order_model->reset_order_items($order_id);
            
            if ($result == true && $result_items == true) {
                $response['status'] = "1";
                $response['data']   = "success";
            } else {
                $response['status'] = "0";
                $response['data']   = "Error";
            }
        }
        
        echo json_encode($response);
        die;
    }
    
    public function recent_orders()
    {
        
        $response = array();
        
        $this->load->model('tax_main_model');
        $tax_name = $this->tax_main_model->tax_list_all();
        
        $result = $this->order_model->recent_orders();
        
        //echo"<pre>";print_r($result);die;
        
        $details = array();
        
        $i = 0;
        foreach ($result as $order_data) {
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
            
            $order_data['bill_amount'] = (float) ($order_data['sub_total']) + $taxSum - ((float) ($order_data['discount']));
            $order_data['roundoff']    = round((float) ($order_data['bill_amount']));
            $details[$i]               = $order_data;
            $i++;
            
        }
        
        
        //echo"<pre>";print_r($details);die;
        
        
        
        $response['status'] = "1";
        $response['data']   = $details;
        
        
        echo json_encode($response);
        die;
    }
    
    public function update_order_discount()
    {
        $response = array();
        
        if (isset($_POST['order_id']) && $_POST['order_id'] != '') {
            $order_id = $_POST['order_id'];
            
            $data                  = array();
            $data['discount_type'] = $_POST['discount_type'];
            if ($_POST['discount_type'] == 1) {
                // 100% discount for complementary
                $data['discount_amount'] = 100;
            } else {
                $data['discount_amount'] = $_POST['discount_amount'];
            }
            $data['total_amount']           = $_POST['total_amount'];
            $data['round_off_total_amount'] = round($_POST['total_amount']);
            $data['return_amount']          = $_POST['return_amount'];
            $data['notes']                  = $_POST['note'];
            
            // if($_POST['discount_type']=="1" || $_POST['discount_type']==1)
            //       {
            //           // get complementary order code
            //           $data['order_code'] = $this->order_model->get_next_complementary_bill_code();
            //           $data['order_code'] = "COMP".$data['order_code'];
            //       }	
            //       else
            //       {
            //       	$data['order_code'] = $this->order_model->get_next_bill_code();
            //       }	
            
            
            // update order table
            $result = $this->order_model->update_order_discount($order_id, $data);
            
            if ($result == true) {
                $response['status'] = "1";
                $response['data']   = "success";
            } else {
                $response['status'] = "0";
                $response['data']   = "Error";
            }
        }
        
        echo json_encode($response);
        die;
    }
    
    public function add_order_tax()
    {
        $response = array();
        
        $tax_id_arr = array();
        
        if (!empty($_POST)) {
            //echo '<pre>';print_r($_POST);die;
            $tax_arr = json_decode($_POST['tax_arr'], true);
            
            if (!empty($tax_arr)) {
                //echo '<pre>';print_r($tax_arr);die;
                foreach ($tax_arr as $value) {
                    $data             = array();
                    $data['order_id'] = $_POST['order_id'];
                    if (isset($value['tax_id']) && $value['tax_id'] != '') {
                        $data['tax_id']      = $value['tax_id'];
                        $data['tax_percent'] = $value['tax_percent'];
                        
                        $tax_id_arr[] = $this->order_model->add_order_tax($data);
                    }
                }
                
                if (array_filter($tax_id_arr)) {
                    $response['status'] = "1";
                    $response['data']   = "Order tax created successfully";
                } else {
                    $response['status'] = "0";
                    $response['data']   = "Error creating Order tax.";
                }
            } else {
                $response['status'] = "0";
                $response['data']   = "Order tax not available";
            }
            
            
        }
        echo json_encode($response);
        die;
    }
    
    function get_details_to_print_invoive()
    {
        $response = array();
        
        
        if (isset($_POST['order_id']) && $_POST['order_id'] != '') {
            
            $order_id      = $_POST['order_id'];

            // get next bill
            //$next_bill_id = $this->order_model->get_next_bill_code();

            // compare the order_code with the existing latest printed bill code
            $latest_printed_bill_code        = $this->order_model->get_latest_printed_bill_code($_POST['order_id']);

            //$latest_printed_bill_code = $latest_printed_bill_code_arr['order_code'];
            $response['latest_printed_bill'] = $latest_printed_bill_code;
            
            // if($next_bill_id==$latest_printed_bill_code)
            // {
            $updateData                    = array();
            $updateData['order_code']      = $latest_printed_bill_code;
            
            $this->order_model->update_order_data_by_id($updateData, $order_id);
            //}
            
            $order_details = $this->order_model->get_details_by_id($order_id);
            
            // then update branch code
            $this->order_model->update_branch_code($latest_printed_bill_code);
            
            $response['status'] = "1";
            $response['data']   = $order_details;
            
            //$order_items_live_details = $this->order_model->get_all_data_order_items($_POST['order_id']);
            $order_items = $this->order_model->get_items_by_order_id($order_id);
            
            $grand_total   = 0;
            $invoice_total = 0;
            
            if (!empty($order_items)) {
                $str = '';
                
                $total_items = 0;
                foreach ($order_items as $item) {
                    $qty = (int) $item["quantity"];
                    $total_items += $qty;
                    $invoice_total += $item["price"] * $item["quantity"];
                    
                    $str .= '<tr >';
                    $str .= '<td >' . $item["product_name"] . '</td>';
                    $str .= '<td align="center">' . $qty . '</td>';
                    $str .= '<td align="right">' . $item["price"] . '</td>';
                    $str .= '<td align="right" style="padding-left:4px;">' . ($item["price"] * $item["quantity"]) . '</td>';
                    $str .= '</tr>';
                    
                }
                
                $grand_total += $invoice_total;
                
                $response['invoice_items'] = $str;
                $response['invoice_total'] = $invoice_total;
                
                
                // get total order items
                //$total_order_items = $this->order_model->get_total_order_items_by_order_id($order_id);
                $total_order_items = $total_items;

                $response['total_order_items'] = $total_order_items;
            }
            
            $this->load->model('tax_model');
            $branch_specific_taxes = $this->tax_model->branch_specific_tax_list_by_order_type($order_details['order_type']);
            
            //echo'<pre>';print_r($branch_specific_taxes);die;
            
            $bs_tax_str = '';
            if (!empty($branch_specific_taxes)) {
                
                foreach ($branch_specific_taxes as $bs_tax) {
                    
                    $tax_value = number_format((float) ($invoice_total * $bs_tax["tax_percent"]) / 100, 2, '.', '');
                    
                    $grand_total += $tax_value;
                    
                    if ($order_details['discount_type'] == 1) {
                        $bs_tax_str .= '<tr><td colspan="4">' . $bs_tax["tax_name"] . ' @ ' . $bs_tax["tax_percent"] . '%</td><td align="right">0</td></tr>';
                    } else {
                        $bs_tax_str .= '<tr><td colspan="4">' . $bs_tax["tax_name"] . ' @ ' . $bs_tax["tax_percent"] . '%</td><td align="right">' . $tax_value . '</td></tr>';
                    }

                    $this->order_model->check_order_tax($bs_tax, $order_id);
                }
            }
            // reduce discount from total amount
            
            $response['branch_specific_taxes'] = $bs_tax_str;
            
            if (intVal($order_details['discount_amount']) != 0 && $order_details['discount_amount'] != '') {
                $grand_total = $grand_total - (($grand_total * $order_details['discount_amount']) / 100);
            }
            
            if ($order_details['discount_type'] == 1) {
                //complementary
                $response['grand_total'] = $grand_total - $grand_total;
            } else {
                $response['grand_total'] = $grand_total;
            }

            $finalUpdate = array();
            $finalUpdate['sub_total'] = $invoice_total;
            $finalUpdate['total_amount'] = $response['grand_total'];
            $finalUpdate['round_off_total_amount'] = round($response['grand_total']);
            $finalUpdate['updated'] = date("Y-m-d H:i:s");
            //$updateData['is_print'] = 1;
            $this->order_model->update_order_data_by_id($finalUpdate, $order_id);
            
            
            // get branch details 
            $this->load->model('branch_model');
            $branch_details = $this->branch_model->get_branch_details_by_login_branch();
            
            if (!empty($branch_details)) {
                $response['branch_details'] = $branch_details;
            }
            
            
            
            //$response['invoice_items'] = '<tr><td colspan="2">Capicum Rava</td><td>1</td><td>185</td><td>185</td></tr><tr><td colspan="2">Capicum Rava</td><td>1</td><td>185</td><td>185</td></tr>';
            
        }
        
        echo json_encode($response);
        die;
    }
    
    function get_details_to_print_invoive_parcel()
    {
        $response = array();
        if (isset($_POST['order_id']) && $_POST['order_id'] != '') {
            
            // get next bill
            $next_bill_id = $this->order_model->get_next_bill_code();
            
            
            // compare the order_code with the existing latest printed bill code
            $latest_printed_bill_code        = $this->order_model->get_latest_printed_bill_code($_POST['order_id']);
            //$latest_printed_bill_code = $latest_printed_bill_code_arr['order_code'];
            $response['latest_printed_bill'] = $latest_printed_bill_code;
            
            // if($next_bill_id==$latest_printed_bill_code)
            // {
            $updateData                    = array();
            $updateData['order_code']      = $latest_printed_bill_code;
            $updateData['order_date_time'] = date("Y-m-d H:i:s");
            //$updateData['is_print'] = 1;
            $this->order_model->update_order_data_by_id($updateData, $_POST['order_id']);
            //}
            
            
            $order_id      = $_POST['order_id'];
            $order_details = $this->order_model->get_details_by_id($_POST['order_id']);
            
            // if the order is complementary - discount_type 
            
            // then update branch code
            $this->order_model->update_branch_code();
            
            $response['status'] = "1";
            $response['data']   = $order_details;
            
            //$order_items_live_details = $this->order_model->get_all_data_order_items($_POST['order_id']);
            $order_items = $this->order_model->get_items_by_order_id($_POST['order_id']);
            
            $grand_total   = 0;
            $invoice_total = 0;
            
            if (!empty($order_items)) {
                $str = '';
                
                
                foreach ($order_items as $item) {
                    
                    $invoice_total += $item["price"] * $item["quantity"];
                    
                    $str .= '<tr >';
                    $str .= '<td >' . $item["product_name"] . '</td>';
                    $str .= '<td align="center">' . (int) $item["quantity"] . '</td>';
                    $str .= '<td align="right">' . $item["price"] . '</td>';
                    $str .= '<td align="right" style="padding-left:4px;">' . ($item["price"] * $item["quantity"]) . '</td>';
                    $str .= '</tr>';
                    
                }
                
                $grand_total += $invoice_total;
                
                $response['invoice_items'] = $str;
                $response['invoice_total'] = $invoice_total;
                
                // get total order items
                $total_order_items = $this->order_model->get_total_order_items_by_order_id($order_id);
                
                $response['total_order_items'] = $total_order_items;
            }
            
            $this->load->model('tax_model');
            $branch_specific_taxes = $this->tax_model->branch_specific_tax_list_by_order_type($order_details['order_type']);
            
            //echo'<pre>';print_r($branch_specific_taxes);die;
            
            $bs_tax_str = '';
            if (!empty($branch_specific_taxes)) {
                
                foreach ($branch_specific_taxes as $bs_tax) {
                    
                    $tax_value = number_format((float) ($invoice_total * $bs_tax["tax_percent"]) / 100, 2, '.', '');
                    
                    $grand_total += $tax_value;
                    
                    if ($order_details['discount_type'] == 1) {
                        $bs_tax_str .= '<tr><td colspan="4">' . $bs_tax["tax_name"] . ' @ ' . $bs_tax["tax_percent"] . '%</td><td align="right">0</td></tr>';
                    } else {
                        $bs_tax_str .= '<tr><td colspan="4">' . $bs_tax["tax_name"] . ' @ ' . $bs_tax["tax_percent"] . '%</td><td align="right">' . $tax_value . '</td></tr>';
                    }
                }
            }
            // reduce discount from total amount
            
            $response['branch_specific_taxes'] = $bs_tax_str;
            
            if (intVal($order_details['discount_amount']) != 0 && $order_details['discount_amount'] != '') {
                $grand_total = $grand_total - (($grand_total * $order_details['discount_amount']) / 100);
            }
            
            if ($order_details['discount_type'] == 1) {
                //complementary
                $response['grand_total'] = $grand_total - $grand_total;
            } else {
                $response['grand_total'] = $grand_total;
            }
            
            
            // get branch details 
            $this->load->model('branch_model');
            $branch_details = $this->branch_model->get_branch_details_by_login_branch();
            
            if (!empty($branch_details)) {
                $response['branch_details'] = $branch_details;
            }
            
            
            //$response['invoice_items'] = '<tr><td colspan="2">Capicum Rava</td><td>1</td><td>185</td><td>185</td></tr><tr><td colspan="2">Capicum Rava</td><td>1</td><td>185</td><td>185</td></tr>';
            
        }
        
        echo json_encode($response);
        die;
    }
    
    function get_details_to_print_invoive_delivery()
    {
        $response = array();
        if (isset($_POST['order_id']) && $_POST['order_id'] != '') {
            
            // get next bill
            $next_bill_id = $this->order_model->get_next_bill_code();
            
            
            // compare the order_code with the existing latest printed bill code
            $latest_printed_bill_code        = $this->order_model->get_latest_printed_bill_code($_POST['order_id']);
            //$latest_printed_bill_code = $latest_printed_bill_code_arr['order_code'];
            $response['latest_printed_bill'] = $latest_printed_bill_code;
            
            // if($next_bill_id==$latest_printed_bill_code)
            // {
            $updateData                    = array();
            $updateData['order_code']      = $latest_printed_bill_code;
            $updateData['order_date_time'] = date("Y-m-d H:i:s");
            //$updateData['is_print'] = 1;
            $this->order_model->update_order_data_by_id($updateData, $_POST['order_id']);
            //}
            
            
            $order_id      = $_POST['order_id'];
            $order_details = $this->order_model->get_details_by_id($_POST['order_id']);
            
            // if the order is complementary - discount_type 
            
            // then update branch code
            $this->order_model->update_branch_code();
            
            $response['status'] = "1";
            $response['data']   = $order_details;
            
            //$order_items_live_details = $this->order_model->get_all_data_order_items($_POST['order_id']);
            $order_items = $this->order_model->get_items_by_order_id($_POST['order_id']);
            
            $grand_total   = 0;
            $invoice_total = 0;
            
            if (!empty($order_items)) {
                $str = '';
                
                
                foreach ($order_items as $item) {
                    
                    $invoice_total += $item["price"] * $item["quantity"];
                    
                    $str .= '<tr style="font-size: 13px;">';
                    $str .= '<td colspan="2">' . $item["product_name"] . '</td>';
                    $str .= '<td>' . $item["quantity"] . '</td>';
                    $str .= '<td>' . $item["price"] . '</td>';
                    $str .= '<td>' . ($item["price"] * $item["quantity"]) . '</td>';
                    $str .= '</tr>';
                    
                }
                
                $grand_total += $invoice_total;
                
                $response['invoice_items'] = $str;
                $response['invoice_total'] = $invoice_total;
                
                // get total order items
                $total_order_items = $this->order_model->get_total_order_items_by_order_id($order_id);
                
                $response['total_order_items'] = $total_order_items;
            }
            
            $this->load->model('tax_model');
            $branch_specific_taxes = $this->tax_model->branch_specific_tax_list_by_order_type($order_details['order_type']);
            
            //echo'<pre>';print_r($branch_specific_taxes);die;
            
            $bs_tax_str = '';
            if (!empty($branch_specific_taxes)) {
                
                foreach ($branch_specific_taxes as $bs_tax) {
                    
                    $tax_value = ($invoice_total * $bs_tax["tax_percent"]) / 100;
                    
                    $grand_total += $tax_value;
                    
                    if ($order_details['discount_type'] == 1) {
                        $bs_tax_str .= '<tr style="font-size: 13px;"><td>&nbsp;</td><td>&nbsp;</td><td colspan="2">' . $bs_tax["tax_name"] . '(' . $bs_tax["tax_percent"] . '%)</td><td>0</td></tr>';
                    } else {
                        $bs_tax_str .= '<tr style="font-size: 13px;"><td>&nbsp;</td><td>&nbsp;</td><td colspan="2">' . $bs_tax["tax_name"] . '(' . $bs_tax["tax_percent"] . '%)</td><td>' . $tax_value . '</td></tr>';
                    }
                }
            }
            // reduce discount from total amount
            
            $response['branch_specific_taxes'] = $bs_tax_str;
            
            if (intVal($order_details['discount_amount']) != 0 && $order_details['discount_amount'] != '') {
                $grand_total = $grand_total - (($grand_total * $order_details['discount_amount']) / 100);
            }
            
            if ($order_details['discount_type'] == 1) {
                //complementary
                $response['grand_total'] = $grand_total - $grand_total;
            } else {
                $response['grand_total'] = $grand_total;
            }
            
            
            // get branch details 
            $this->load->model('branch_model');
            $branch_details = $this->branch_model->get_branch_details_by_login_branch();
            
            if (!empty($branch_details)) {
                $response['branch_details'] = $branch_details;
            }
            
            
            //$response['invoice_items'] = '<tr><td colspan="2">Capicum Rava</td><td>1</td><td>185</td><td>185</td></tr><tr><td colspan="2">Capicum Rava</td><td>1</td><td>185</td><td>185</td></tr>';
            
        }
        
        echo json_encode($response);
        die;
    }
    
    public function get_daily_income_of_branch()
    {
        $response = array();
        
        $session_data = $this->session->userdata('logged_in');
        $branch_id    = $session_data['branch_id'];
        
        $dailyIncome_data = $this->order_model->getDailyincomebyBranch($branch_id);
        
        if (!empty($dailyIncome_data)) {
            $response['dailyIncome'] = $dailyIncome_data['daily_income'];
        }
        
        echo json_encode($response);
        die;
    }
    
    function update_order_field()
    {
        $response = array();
        
        if (isset($_POST['order_id']) && $_POST['order_id'] != '') {
            $order_data = array();
            
            if (isset($_POST['waiter_id']) && $_POST['waiter_id'] != '') {
                $order_data['waiter_id'] = $_POST['waiter_id'];
            }
            if (isset($_POST['brand_id']) && $_POST['brand_id'] != '') {
                $order_data['sub_brand_id'] = $_POST['brand_id'];
            }
            if (isset($_POST['number_of_person']) && $_POST['number_of_person'] != '') {
                $order_data['number_of_person'] = $_POST['number_of_person'];
            }
            if (isset($_POST['payment_type']) && $_POST['payment_type'] != '') {
                $order_data['payment_type'] = $_POST['payment_type'];
            }
            if (isset($_POST['payment_card_number']) && $_POST['payment_card_number'] != '' && $_POST['payment_card_number'] != 'undefined') {
                $order_data['payment_card_number'] = $_POST['payment_card_number'];
            }
            
            $order_result = $this->order_model->update_order_data_by_id($order_data, $_POST['order_id']);
            
            if ($order_result == TRUE) {
                $response['status'] = "1";
                $response['data']   = "Update successful";
            } else {
                $response['status'] = "0";
                $response['data']   = "Error";
            }
        }
        
        echo json_encode($response);
        die;
    }
    public function orderUpdate()
    {
        $this->load->view('order/update');
    }
    public function orderUpdatefromList()
    {
        
        $response = array();
        
        //print_r($_POST);die;
        
        if (isset($_POST['order_id']) && $_POST['order_id'] != '') {
            $result = $this->order_model->updateOrder($_POST['order_id']);
            
            //print_r($result);die;
            $order_detail = $this->order_model->getRowByOrderId($_POST['order_id']);
            
            $response['status']       = "1";
            $response['order_items']  = $result;
            $response['order_detail'] = $order_detail;
        }
        echo json_encode($response);
        die;
    }
    
    public function get_live_parcel_order()
    {
        $response = array();
        
        $order_details = $this->order_model->get_live_parcel_order();
        
        if (!empty($order_details)) {
            $order_id = $order_details['order_id'];
            
            //get order items data from live table and insert into main table
            $order_items_live_details = $this->order_model->get_all_data_order_items($order_id);
            
            $response['status']      = "1";
            $response['order']       = $order_details;
            $response['order_items'] = $order_items_live_details;
        }
        
        echo json_encode($response);
        die;
    }
    
    
    
    public function get_live_delivery_order()
    {
        $response = array();
        
        $order_details = $this->order_model->get_live_delivery_order();
        
        if (!empty($order_details)) {
            $order_id = $order_details['order_id'];
            
            //get order items data from live table and insert into main table
            $order_items_live_details = $this->order_model->get_all_data_order_items($order_id);
            
            $response['status']      = "1";
            $response['order']       = $order_details;
            $response['order_items'] = $order_items_live_details;
        }
        
        echo json_encode($response);
        die;
    }
    
    function get_details_to_print_invoive_admin()
    {
        $response = array();
        if (isset($_POST['order_id']) && $_POST['order_id'] != '') {
            
            $order_id      = $_POST['order_id'];
            $order_details = $this->order_model->get_details_by_id($_POST['order_id']);
            
            // if the order is complementary - discount_type 
            
            $updateData = array();
            
            $updateData['updated'] = date("Y-m-d H:i:s");
            
            $this->order_model->update_order_data_by_id($updateData, $_POST['order_id']);
            
            // then update branch code
            
            
            $response['status'] = "1";
            $response['data']   = $order_details;
            
            //$order_items_live_details = $this->order_model->get_all_data_order_items($_POST['order_id']);
            $order_items = $this->order_model->get_items_by_order_id($_POST['order_id']);
            
            $grand_total   = 0;
            $invoice_total = 0;
            
            if (!empty($order_items)) {
                $str = '';
                
                
                foreach ($order_items as $item) {
                    
                    $invoice_total += $item["price"] * $item["quantity"];
                    
                    $str .= '<tr style="font-size: 13px;">';
                    $str .= '<td colspan="2">' . $item["product_name"] . '</td>';
                    $str .= '<td>' . $item["quantity"] . '</td>';
                    $str .= '<td>' . $item["price"] . '</td>';
                    $str .= '<td>' . ($item["price"] * $item["quantity"]) . '</td>';
                    $str .= '</tr>';
                    
                }
                
                $grand_total += $invoice_total;
                
                $response['invoice_items'] = $str;
                $response['invoice_total'] = $invoice_total;
                
                // get total order items
                $total_order_items = $this->order_model->get_total_order_items_by_order_id($order_id);
                
                $response['total_order_items'] = $total_order_items;
            }
            
            $this->load->model('tax_model');
            $branch_specific_taxes = $this->tax_model->branch_specific_tax_list_by_order_type($order_details['order_type']);
            
            //echo'<pre>';print_r($branch_specific_taxes);die;
            
            $bs_tax_str = '';
            if (!empty($branch_specific_taxes)) {
                
                foreach ($branch_specific_taxes as $bs_tax) {
                    
                    $tax_value = ($invoice_total * $bs_tax["tax_percent"]) / 100;
                    
                    $grand_total += $tax_value;
                    
                    if ($order_details['discount_type'] == 1) {
                        $bs_tax_str .= '<tr style="font-size: 13px;"><td>&nbsp;</td><td>&nbsp;</td><td colspan="2">' . $bs_tax["tax_name"] . '(' . $bs_tax["tax_percent"] . '%)</td><td>0</td></tr>';
                    } else {
                        $bs_tax_str .= '<tr style="font-size: 13px;"><td>&nbsp;</td><td>&nbsp;</td><td colspan="2">' . $bs_tax["tax_name"] . '(' . $bs_tax["tax_percent"] . '%)</td><td>' . $tax_value . '</td></tr>';
                    }
                    
                    //$bs_tax_str .= '<tr style="font-size: 13px;"><td>&nbsp;</td><td>&nbsp;</td><td colspan="2">'.$bs_tax["tax_name"].'('.$bs_tax["tax_percent"].'%)</td><td>'.$tax_value.'</td></tr>';
                    
                }
            }
            // reduce discount from total amount
            
            $response['branch_specific_taxes'] = $bs_tax_str;
            
            if (intVal($order_details['discount_amount']) != 0 && $order_details['discount_amount'] != '') {
                $grand_total = $grand_total - (($grand_total * $order_details['discount_amount']) / 100);
            }
            
            if ($order_details['discount_type'] == 1) {
                //complementary
                $response['grand_total'] = $grand_total - $grand_total;
            } else {
                $response['grand_total'] = $grand_total;
            }
            
            
            
            // get branch details 
            $this->load->model('branch_model');
            $branch_details = $this->branch_model->get_branch_details_by_login_branch();
            
            if (!empty($branch_details)) {
                $response['branch_details'] = $branch_details;
            }
            
            
            //$response['invoice_items'] = '<tr><td colspan="2">Capicum Rava</td><td>1</td><td>185</td><td>185</td></tr><tr><td colspan="2">Capicum Rava</td><td>1</td><td>185</td><td>185</td></tr>';
            
        }
        
        echo json_encode($response);
        die;
    }
    
    function get_details_to_print_invoive_brand()
    {
        $this->load->model('brand_model');
        
        $response = array();
        if (isset($_POST['order_id']) && $_POST['order_id'] != '' && isset($_POST['brand_id']) && $_POST['brand_id'] != '') {
            $brand_id = $_POST['brand_id'];
            
            $brand_details = $this->brand_model->get_details_by_id($brand_id);
            
            $brand_name = '';
            
            if (!empty($brand_details)) {
                $brand_name = $brand_details['brand_name'];
            }
            
            $order_id      = $_POST['order_id'];
            $order_details = $this->order_model->get_details_by_id($_POST['order_id']);
            
            $response['status'] = "1";
            $response['data']   = $order_details;
            
            //$order_items_live_details = $this->order_model->get_all_data_order_items($_POST['order_id']);
            $order_items = $this->order_model->get_items_by_order_id_brand_wise($_POST['order_id'], $brand_id);
            
            $grand_total   = 0;
            $invoice_total = 0;
            
            if (!empty($order_items)) {
                $str = '';
                
                
                foreach ($order_items as $item) {
                    
                    $invoice_total += $item["price"] * $item["quantity"];
                    
                    $str .= '<tr style="font-size: 13px;">';
                    $str .= '<td colspan="2">' . $item["product_name"] . '</td>';
                    $str .= '<td>' . $item["quantity"] . '</td>';
                    $str .= '<td>' . $item["price"] . '</td>';
                    $str .= '<td>' . ($item["price"] * $item["quantity"]) . '</td>';
                    $str .= '</tr>';
                    
                }
                
                $grand_total += $invoice_total;
                
                $response['invoice_items'] = $str;
                $response['invoice_total'] = $invoice_total;
                
                // get total order items
                $total_order_items = $this->order_model->get_total_order_items_by_order_id($order_id);
                
                $response['total_order_items'] = $total_order_items;
            }
            
            $this->load->model('tax_model');
            $branch_specific_taxes = $this->tax_model->branch_specific_tax_list_by_order_type($order_details['order_type']);
            
            //echo'<pre>';print_r($branch_specific_taxes);die;
            
            $bs_tax_str = '';
            if (!empty($branch_specific_taxes)) {
                
                foreach ($branch_specific_taxes as $bs_tax) {
                    
                    $tax_value = ($invoice_total * $bs_tax["tax_percent"]) / 100;
                    
                    $grand_total += $tax_value;
                    
                    if ($order_details['discount_type'] == 1) {
                        $bs_tax_str .= '<tr style="font-size: 13px;"><td>&nbsp;</td><td>&nbsp;</td><td colspan="2">' . $bs_tax["tax_name"] . '(' . $bs_tax["tax_percent"] . '%)</td><td>0</td></tr>';
                    } else {
                        $bs_tax_str .= '<tr style="font-size: 13px;"><td>&nbsp;</td><td>&nbsp;</td><td colspan="2">' . $bs_tax["tax_name"] . '(' . $bs_tax["tax_percent"] . '%)</td><td>' . $tax_value . '</td></tr>';
                    }
                    
                    //$bs_tax_str .= '<tr style="font-size: 13px;"><td>&nbsp;</td><td>&nbsp;</td><td colspan="2">'.$bs_tax["tax_name"].'('.$bs_tax["tax_percent"].'%)</td><td>'.$tax_value.'</td></tr>';
                    
                }
            }
            // reduce discount from total amount
            
            $response['branch_specific_taxes'] = $bs_tax_str;
            
            if (intVal($order_details['discount_amount']) != 0 && $order_details['discount_amount'] != '') {
                $grand_total = $grand_total - (($grand_total * $order_details['discount_amount']) / 100);
            }
            
            if ($order_details['discount_type'] == 1) {
                //complementary
                $response['grand_total'] = $grand_total - $grand_total;
            } else {
                $response['grand_total'] = $grand_total;
            }
            
            
            
            // get branch details 
            $this->load->model('branch_model');
            $branch_details = $this->branch_model->get_branch_details_by_login_branch();
            
            if (!empty($branch_details)) {
                $response['branch_details'] = $branch_details;
            }
            
            $response['brand_name'] = $brand_name;
            
            
            //$response['invoice_items'] = '<tr><td colspan="2">Capicum Rava</td><td>1</td><td>185</td><td>185</td></tr><tr><td colspan="2">Capicum Rava</td><td>1</td><td>185</td><td>185</td></tr>';
            
        }
        
        echo json_encode($response);
        die;
    }
    function get_details_to_print_invoive_brand_new()
    {
        $this->load->model('brand_model');
        
        $response = array();
        if (isset($_POST['order_id']) && $_POST['order_id'] != '' && isset($_POST['brand_id']) && $_POST['brand_id'] != '') {
            $brand_id = $_POST['brand_id'];
            
            $brand_details = $this->brand_model->get_details_by_id($brand_id);
            
            
            
            $brand_name = '';
            
            if (!empty($brand_details)) {
                $brand_name = $brand_details['brand_name'];
            }
            
            $order_id      = $_POST['order_id'];
            $order_details = $this->order_model->get_details_by_id($_POST['order_id']);
            
            print_r($order_details);
            
            $response['status'] = "1";
            $response['data']   = $order_details;
            
            //$order_items_live_details = $this->order_model->get_all_data_order_items($_POST['order_id']);
            $order_items = $this->order_model->get_items_by_order_id_brand_wise($_POST['order_id'], $brand_id);
            
            $grand_total   = 0;
            $invoice_total = 0;
            
            if (!empty($order_items)) {
                $str = '';
                
                foreach ($order_items as $item) {
                    
                    $invoice_total += $item["price"] * $item["quantity"];
                    
                    $str .= '<tr style="font-size: 13px;">';
                    $str .= '<td colspan="2">' . $item["product_name"] . '</td>';
                    $str .= '<td>' . $item["quantity"] . '</td>';
                    $str .= '<td>' . $item["price"] . '</td>';
                    $str .= '<td>' . ($item["price"] * $item["quantity"]) . '</td>';
                    $str .= '</tr>';
                    
                }
                
                $grand_total += $invoice_total;
                
                $response['invoice_items'] = $str;
                $response['invoice_total'] = $invoice_total;
                
                // get total order items
                $total_order_items = $this->order_model->get_total_order_items_by_order_id($order_id);
                
                $response['total_order_items'] = $total_order_items;
            }
            
            $this->load->model('tax_model');
            $branch_specific_taxes = $this->tax_model->branch_specific_tax_list_by_order_type($order_details['order_type']);
            
            //echo'<pre>';print_r($branch_specific_taxes);die;
            
            $bs_tax_str = '';
            if (!empty($branch_specific_taxes)) {
                
                foreach ($branch_specific_taxes as $bs_tax) {
                    
                    $tax_value = ($invoice_total * $bs_tax["tax_percent"]) / 100;
                    
                    $grand_total += $tax_value;
                    
                    if ($order_details['discount_type'] == 1) {
                        $bs_tax_str .= '<tr style="font-size: 13px;"><td>&nbsp;</td><td>&nbsp;</td><td colspan="2">' . $bs_tax["tax_name"] . '(' . $bs_tax["tax_percent"] . '%)</td><td>0</td></tr>';
                    } else {
                        $bs_tax_str .= '<tr style="font-size: 13px;"><td>&nbsp;</td><td>&nbsp;</td><td colspan="2">' . $bs_tax["tax_name"] . '(' . $bs_tax["tax_percent"] . '%)</td><td>' . $tax_value . '</td></tr>';
                    }
                    
                    //$bs_tax_str .= '<tr style="font-size: 13px;"><td>&nbsp;</td><td>&nbsp;</td><td colspan="2">'.$bs_tax["tax_name"].'('.$bs_tax["tax_percent"].'%)</td><td>'.$tax_value.'</td></tr>';
                    
                }
            }
            // reduce discount from total amount
            
            $response['branch_specific_taxes'] = $bs_tax_str;
            
            if (intVal($order_details['discount_amount']) != 0 && $order_details['discount_amount'] != '') {
                $grand_total = $grand_total - (($grand_total * $order_details['discount_amount']) / 100);
            }
            
            if ($order_details['discount_type'] == 1) {
                //complementary
                $response['grand_total'] = $grand_total - $grand_total;
            } else {
                $response['grand_total'] = $grand_total;
            }
            
            
            
            // get branch details 
            $this->load->model('branch_model');
            $branch_details = $this->branch_model->get_branch_details_by_login_branch();
            
            if (!empty($branch_details)) {
                $response['branch_details'] = $branch_details;
            }
            
            $response['brand_name'] = $brand_name;
            
            
            //$response['invoice_items'] = '<tr><td colspan="2">Capicum Rava</td><td>1</td><td>185</td><td>185</td></tr><tr><td colspan="2">Capicum Rava</td><td>1</td><td>185</td><td>185</td></tr>';
            
        }
        
        echo json_encode($response);
        die;
    }
    
    function get_details_to_print_invoive_all_brand()
    {
        $this->load->model('brand_model');
        $this->load->model('branch_model');
        
        $response = array();
        if (isset($_POST['order_id']) && $_POST['order_id'] != '') {
            
            $logged_in = $this->session->userdata('logged_in');
            
            if (isset($logged_in['branch_id']) && $logged_in['branch_id'] != '') {
                $details = $this->branch_model->get_details_by_id($logged_in['branch_id']);
                
                
                if (!empty($details)) {
                    // get brand_id 
                    $brand_id_csv = $details['brand_id'];
                    $brand_id_arr = explode(',', $brand_id_csv);
                    
                    $brand_data = array();
                    
                    $i = 0;
                    foreach ($brand_id_arr as $brand_id) {
                        //echo $brand_id;
                        // get brand details by id
                        $brand_details = $this->brand_model->get_details_by_id($brand_id);
                        
                        if (!empty($brand_details)) {
                            $brand_name = $brand_details['brand_name'];
                        }
                        
                        $brand_array = array(
                            'brand_id' => $brand_id,
                            'brand_name' => $brand_name
                        );
                        
                        $brand_data[$i] = $brand_array;
                        
                        $i++;
                    }
                }
                $response['data'] = $brand_data;
                
            }
            
            //$brand_list = $this->brand_model->get_data();
            
            //echo '<pre>';print_r($brand_data);die;
            
            
            $order_id      = $_POST['order_id'];
            $order_details = $this->order_model->get_details_by_id($_POST['order_id']);
            
            $response['status']          = "1";
            $response['data']            = $order_details;
            $response['order_date_time'] = date("m/d/Y h:i a");
            
            $str        = '';
            $bs_tax_str = '';
            
            if (!empty($brand_details)) {
                foreach ($brand_data as $brand) {
                    $brand_name = $brand['brand_name'];
                    
                    $brand_id = $brand['brand_id'];
                    
                    
                    //$str .= '<tr><td colspan="5" > <hr></td></tr>';
                    
                    
                    $str .= '<tr> <td colspan="5" align="center"> Brand : ' . $brand_name . '</td></tr>';
                    $str .= '<tr> <td colspan="5" align="center" class="dotted"> KOT </td></tr>';
                    $str .= '<tr> <td colspan="5" align="center"> ' . $response['order_date_time'] . ' </td></tr>';
                    if ($response['data']['order_type'] == 1) {
                        //order table
                        $str .= '<tr> <td colspan="3" class="dotted"> CAPTAIN  : ' . $response['data']['waiter_name'] . '</td>
								<td colspan="2" class="dotted" align="right" > TABLE  : ' . $response['data']['table_number'] . '</td></tr>';
                    }
                    if ($response['data']['order_type'] == 3) {
                        //parcel order
                        $str .= '<tr> <td colspan="5" class="dotted" align="center" > CAPTAIN  : ' . $response['data']['waiter_name'] . '</td></tr>';
                    }
                    
                    //$str .= '<tr><td colspan="2" class="dotted">Table No: &nbsp; '.$response['data']['table_number'].'</td><td class="dotted" colspan="3" align="right">Date: &nbsp;'.$response['order_date_time'].'</td></tr>';
                    
                    //$order_items_live_details = $this->order_model->get_all_data_order_items($_POST['order_id']);
                    $order_items = $this->order_model->get_items_by_order_id_brand_wise($_POST['order_id'], $brand_id);
                    
                    $grand_total   = 0;
                    $invoice_total = 0;
                    $qty           = 0;
                    
                    if (count($order_items) != 0) {
                        
                        $str .= '<tr> <td colspan="2" class="dotted" ><b>ITEM NAME</b></td>
										<td class="dotted" align="right"><b>QTY</b></td>
										<td class="dotted" align="right"><b>RATE</b></td>
										<td class="dotted" align="right"><b>AMT</b></td>
								</tr>';
                        
                        foreach ($order_items as $item) {
                            
                            $invoice_total += $item["price"] * $item["quantity"];
                            $qty += $item["quantity"];
                            
                            $str .= '<tr >';
                            $str .= '<td colspan="2">' . $item["product_name"] . '</td>';
                            $str .= '<td align="center">' . (int) $item["quantity"] . '</td>';
                            $str .= '<td align="right">' . $item["price"] . '</td>';
                            $str .= '<td align="right">' . ($item["price"] * $item["quantity"]) . '</td>';
                            $str .= '</tr>';
                            
                        }
                        
                        $str .= '<tr>
									<td colspan="3" class="dotted-top-only"><b>TOTAL QTY</b></td>
									<td colspan="2" class="dotted-top-only" align="right"><b>' . $qty . '</b></td>
								</tr>
								<tr>
									<td colspan="3" ><b>SUB TOTAL</b></td>
									<td colspan="2" align="right"><b>' . number_format($invoice_total, 2) . '</b></td>
								</tr>
								';
                        
                        $grand_total += $invoice_total;
                        
                        $response['invoice_items'] = $str;
                        $response['invoice_total'] = $invoice_total;
                        
                        // get total order items
                        $total_order_items = $this->order_model->get_total_order_items_by_order_id($order_id);
                        
                        //$str .= '<tr> <td colspan="2"></td><td class="dotted-top"><b>'.$total_order_items.'</b></td><td></td><td></td></tr>';
                        
                        //$str .= '<tr> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr> <td>&nbsp;</td><td>&nbsp;</td></tr>';
                        
                        $response['total_order_items'] = $total_order_items;
                    } else {
                        $response['invoice_items'] = "No new items";
                        $response['invoice_total'] = 0;
                    }
                    $this->load->model('tax_model');
                    $branch_specific_taxes = $this->tax_model->branch_specific_tax_list_by_order_type($order_details['order_type']);
                    
                    //echo'<pre>';print_r($branch_specific_taxes);die;
                    
                    
                    if (!empty($branch_specific_taxes)) {
                        
                        foreach ($branch_specific_taxes as $bs_tax) {
                            
                            $tax_value = number_format((float) ($invoice_total * $bs_tax["tax_percent"]) / 100, 2, '.', '');
                            
                            $grand_total += $tax_value;
                            
                            if ($order_details['discount_type'] == 1) {
                                $bs_tax_str .= '<tr><td colspan="4">' . $bs_tax["tax_name"] . ' @ ' . $bs_tax["tax_percent"] . '%</td><td align="right">0</td></tr>';
                            } else {
                                $bs_tax_str .= '<tr><td colspan="4">' . $bs_tax["tax_name"] . ' @ ' . $bs_tax["tax_percent"] . '%</td><td align="right">' . $tax_value . '</td></tr>';
                            }
                        }
                    }
                    // reduce discount from total amount
                    
                    $response['branch_specific_taxes'] = $bs_tax_str;
                    
                    if (intVal($order_details['discount_amount']) != 0 && $order_details['discount_amount'] != '') {
                        $grand_total = $grand_total - (($grand_total * $order_details['discount_amount']) / 100);
                    }
                    
                    if ($order_details['discount_type'] == 1) {
                        //complementary
                        $response['grand_total'] = $grand_total - $grand_total;
                    } else {
                        $response['grand_total'] = $grand_total;
                    }
                    
                }
            }
            
            
            
            // get branch details 
            $this->load->model('branch_model');
            $branch_details = $this->branch_model->get_branch_details_by_login_branch();
            
            if (!empty($branch_details)) {
                $response['branch_details'] = $branch_details;
            }
            
            $response['brand_name'] = $brand_name;
            
            
            //$response['invoice_items'] = '<tr><td colspan="2">Capicum Rava</td><td>1</td><td>185</td><td>185</td></tr><tr><td colspan="2">Capicum Rava</td><td>1</td><td>185</td><td>185</td></tr>';
            
        }
        
        echo json_encode($response);
        die;
    }
    
    public function live_table_total_amount()
    {
        $response                = array();
        $response['live_amount'] = $this->order_model->live_table_total_amount();
        echo json_encode($response);
        die;
        
    }
    
    public function change_table()
    {
        
        $response = array();
        
        if ($_POST['new_table_id'] != '' && $_POST['new_table_id'] != 'undefined') {
            $session_data = $this->session->userdata('logged_in');
            $branch_id    = $session_data['branch_id'];
            $branch_type  = $session_data['branch_type'];
            
            $this->load->model('table_model');
            $new_table_id = $this->table_model->get_table_id_by_table_number($_POST['new_table_id'], $branch_id);
            
            $order_id = $_POST['order_id'];
            
            $result = $this->order_model->change_table($order_id, $new_table_id);
            
            $response['result'] = $result;
            
        } else {
            $response['result'] = 2;
        }
        
        
        echo json_encode($response);
        die;
    }
    
    /*** API For App ***/
    
    public function add_order_from_app()
    {
        ///echo print_r($_POST);
        
        $response = array();
        
        $data = (array) json_decode($_POST['data']);
        
        /*echo "<pre>";
        print_r($data['products']);
        
        foreach($data['products'] as $product_arr ){
        echo "<pre>";
        print_r($product_arr);
        $arr = (array) $product_arr;
        }
        exit;
        echo "<pre>";
        print_r($data);
        */
        
        
        // insert into order_live table
        if ($data['branch_id'] == '') {
            
            
            $response['status'] = "0";
            $response['data']   = "Invalid Branch ID.";
            header("Content-type:application/json");
            echo json_encode($response);
            exit;
        }
        
        if ($data['waiter_id'] == '') {
            $response['status'] = "0";
            $response['data']   = "Invalid Waiter ID.";
            header("Content-type:application/json");
            echo json_encode($response);
            exit;
        }
        if ($data['table_detail_id'] == '') {
            $response['status'] = "0";
            $response['data']   = "Invalid Table ID.";
            header("Content-type:application/json");
            echo json_encode($response);
            exit;
        }
        if ($data['number_of_person'] == '') {
            $response['status'] = "0";
            $response['data']   = "Invalid Number Of Person.";
            header("Content-type:application/json");
            echo json_encode($response);
            exit;
        }
        if (!empty($data)) {
            
            if ($data['order_id'] > 0) {
                $new_post = FALSE;
            } else {
                $new_post = TRUE;
            }
            
            $result = $this->order_model->add_order_from_app($data); //return order_id
            
            $data['order_id'] = $result;
            $data['table_id'] = $data['table_detail_id'];
            $result_live      = $this->order_model->insert_into_order_live($data);
            
            
            if ($result > 0 && $result_live == TRUE) {
                
                
                $order_id = $data['order_id'];
                
                if (!empty($data['products'])) {
                    //$product_arr = $data['products'];
                    // echo '<pre>';print_r($product_arr);die;
                    foreach ($data['products'] as $product_arr) {
                        $arr    = (array) $product_arr;
                        $result = $this->order_model->add_items_order($order_id, $arr);
                    }
                    
                }
                if ($new_post) {
                    $this->order_model->add_order_tax_from_app($order_id, $data['branch_id']);
                }
                
                
                $response['status']   = "1";
                $response['data']     = "Order created successfully";
                $response['order_id'] = $order_id;
            } else {
                $response['status'] = "0";
                $response['data']   = "Database error. Please contact admin.";
            }
        }
        header("Content-type:application/json");
        echo json_encode($response);
        exit;
    }
    
    public function add_order_from_app_24x7()
    {
        
        //print_r($_POST); die;
        $jsonStr  = json_encode($_POST);
        //print_r($_POST['data']); die;
        //echo  $jsonStr;die;
        $response = array();
        
        $data = (array) json_decode($jsonStr);
        
        $dataN = (array) $data;
        
        print_r($dataN);
        die;
        
        /*echo "<pre>";
        print_r($data['products']);
        
        foreach($data['products'] as $product_arr ){
        echo "<pre>";
        print_r($product_arr);
        $arr = (array) $product_arr;
        }
        exit;
        echo "<pre>";
        print_r($data);
        */
        
        
        // insert into order_live table
        if ($data['branch_id'] == '') {
            
            
            $response['status'] = "0";
            $response['data']   = "Invalid Branch ID.";
            header("Content-type:application/json");
            echo json_encode($response);
            exit;
        }
        
        if ($data['waiter_id'] == '') {
            $response['status'] = "0";
            $response['data']   = "Invalid Waiter ID.";
            header("Content-type:application/json");
            echo json_encode($response);
            exit;
        }
        if ($data['table_detail_id'] == '') {
            $response['status'] = "0";
            $response['data']   = "Invalid Table ID.";
            header("Content-type:application/json");
            echo json_encode($response);
            exit;
        }
        if ($data['number_of_person'] == '') {
            $response['status'] = "0";
            $response['data']   = "Invalid Number Of Person.";
            header("Content-type:application/json");
            echo json_encode($response);
            exit;
        }
        if (!empty($data)) {
            
            if ($data['order_id'] > 0) {
                $new_post = FALSE;
            } else {
                $new_post = TRUE;
            }
            
            $result = $this->order_model->add_order_from_app($data); //return order_id
            
            $data['order_id'] = $result;
            $data['table_id'] = $data['table_detail_id'];
            $result_live      = $this->order_model->insert_into_order_live($data);
            
            
            if ($result > 0 && $result_live == TRUE) {
                
                
                $order_id = $data['order_id'];
                
                if (!empty($data['products'])) {
                    //$product_arr = $data['products'];
                    // echo '<pre>';print_r($product_arr);die;
                    foreach ($data['products'] as $product_arr) {
                        $arr    = (array) $product_arr;
                        $result = $this->order_model->add_items_order($order_id, $arr);
                    }
                    
                }
                if ($new_post) {
                    $this->order_model->add_order_tax_from_app($order_id, $data['branch_id']);
                }
                
                
                $response['status']   = "1";
                $response['data']     = "Order created successfully";
                $response['order_id'] = $order_id;
            } else {
                $response['status'] = "0";
                $response['data']   = "Database error. Please contact admin.";
            }
        }
        header("Content-type:application/json");
        echo json_encode($response);
        exit;
    }
    
    
}

?>