<?php

class Api extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
    }

    
    public function login()
	{
        $this->load->model('branch_model');
        
        $username = $_POST['username'];
        $password = $_POST['password'];

		$response = array();
        
		if( $username!='' && $password!='' )
		{
            $input_fields = array("username"=>$username, "password"=>$password);
            
            $result = $this->branch_model->login($input_fields);
            if($result['status'] == 1)
            {
                $response['status'] = "1";
                $response['data'] = $result;
                
            }
            else
            {
                $response['status'] = "0";
                $response['message'] = $result['message'];						
            }
		}
		else
		{
			$response['status'] = "0";
			$response['message'] = "Invalid username or password.";
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public function getLoggedInBranchDetails()
	{
        $this->load->model('branch_model');
		$response = array();

		$branch_id = $_POST['branch_id'];

		if($branch_id!='')
		{			
            $details = $this->branch_model->get_details_by_id($branch_id);
            $response['status'] = "1";
			$response['data'] = $details;	

            // get branch list
            if($details['branch_type']==1){
                $response['branch_list'] = $this->branch_model->present_branch_list();		
            }
			
        }
        
		header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public function getAllBranchList()
	{
        $this->load->model('branch_model');
		$response = array();

		$response['status'] = "1";
        $response['data'] = $this->branch_model->present_branch_list();
        
		header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public function getAllBranchData(){

        $this->load->model('branch_model');
        $this->load->model('brand_model');
        $this->load->model('order_model');
        $response = $branch_arr = array();
        $i=0;

		// $branch_id = $_POST['branch_id'];

        // $details = $this->branch_model->get_details_by_id($branch_id);
        $response['status'] = "1";
        	

        // get branch list
        $branch_list= $this->branch_model->present_branch_list();		
        foreach($branch_list as $branch){
            $branch_arr['branch_id'] = $branch['branch_id'];
            $branch_arr['branch_info'] = $branch;
            $branch_arr['brand_list'] = $this->brand_model->brand_list_by_branch_new($branch['branch_id']);
            $branch_arr['live_amount'] = $this->order_model->live_table_total_amount($branch['branch_id']);

            $daily_income_data = $this->order_model->getDailyincomebyBranch($branch['branch_id']);
            $monthly_income_data = $this->order_model->getMonthlyincome($branch['branch_id']);
            $total_income_data = $this->order_model->getTotalincome($branch['branch_id']);
			if($daily_income_data['daily_income']!=''){
				$branch_arr['daily_income'] = number_format($daily_income_data['daily_income'], 2);
			} else {
				$branch_arr['daily_income'] = "0.00";
            }
            if($monthly_income_data['monthly_income']!=''){
				$branch_arr['monthly_income'] = number_format($monthly_income_data['monthly_income'], 2);
			} else {
				$branch_arr['monthly_income'] = "0.00";
            }
            if($total_income_data['total_income']!=''){
				$branch_arr['total_income'] = number_format($total_income_data['total_income'], 2);
			} else {
				$branch_arr['total_income'] = "0.00";
            }
            $billandcover = $this->order_model->getMonthlyBillAndCover($branch['branch_id']);
            $branch_arr['monthly_bill_count'] = $billandcover['bill_count'];
            $branch_arr['monthly_cover_count'] =  ($billandcover['total_cover']!=null)?$billandcover['total_cover']:"0" ;

            $details[$i] = $branch_arr;
            $i++;
        }

        $response['data'] = $details;
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public function getAllBranchDataByBranchId(){
        
        $this->load->model('branch_model');
        $this->load->model('brand_model');
        $this->load->model('order_model');
        $response = $branch_arr = array();

        $branch_id = $_POST['branch_id'];
        
        if(!empty($branch_id)){
            // get branch list
             $branch_list = $this->branch_model->present_branch_list($branch_id);
             if(!empty($branch_list[0])){
                $branch_arr['branch_id'] = $branch_id;
                $branch_arr['branch_info'] = $branch_list[0];
                $branch_arr['brand_list'] = $this->brand_model->brand_list_by_branch_new($branch_id);
                $branch_arr['live_amount'] = $this->order_model->live_table_total_amount($branch_id);
        
                $daily_income_data = $this->order_model->getDailyincomebyBranch($branch_id);
                $monthly_income_data = $this->order_model->getMonthlyincome($branch_id);
                $total_income_data = $this->order_model->getTotalincome($branch_id);
                if($daily_income_data['daily_income']!=''){
                    $branch_arr['daily_income'] = number_format($daily_income_data['daily_income'], 2);
                } else {
                    $branch_arr['daily_income'] = "0.00";
                }
                if($monthly_income_data['monthly_income']!=''){
                    $branch_arr['monthly_income'] = number_format($monthly_income_data['monthly_income'], 2);
                } else {
                    $branch_arr['monthly_income'] = "0.00";
                }
                if($total_income_data['total_income']!=''){
                    $branch_arr['total_income'] = number_format($total_income_data['total_income'], 2);
                } else {
                    $branch_arr['total_income'] = "0.00";
                }
                $billandcover = $this->order_model->getMonthlyBillAndCover($branch_id);
                $branch_arr['monthly_bill_count'] = $billandcover['bill_count'];
                $branch_arr['monthly_cover_count'] =  ($billandcover['total_cover']!=null)?$billandcover['total_cover']:"0" ;
        
                $details = $branch_arr;
   
                $response['status'] = "1";
                $response['data'] = $details;
             } else {
                $response['status'] = "0";
                $response['data'] = array();
                $response['message'] = "Invalid branch id.";
             }
                 
             
        } else {
            $response['status'] = "0";
            $response['data'] = array();
            $response['message'] = "Invalid branch id.";

        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    public function getBrandByBranchId()
	{
        $this->load->model('branch_model');
        $this->load->model('brand_model');
        $branch_id = $_POST['branch_id'];
        $response = array();
        
		if ( $branch_id != '') {
            $response['status'] = "1";
            $response['brand_list_by_branch'] = $this->brand_model->brand_list_by_branch_new($branch_id);
           
        } else {
            $response['status'] = "0";
        }
        
		header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    public function daily_sales_report()
    {
        
        $branch_id = '';
        $fromdate  = '';
        
        $year  = date("Y");
        $month = date("m");
        
        
        if ($_POST['branch_id'] != '' &&  $_POST['fromdate'] != '') {
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
        
        elseif ($_POST['fromdate'] != '') {
            $from_date  = $_POST['fromdate'];
            $frdate_arr = explode('/', $from_date);
            
            if (!empty($frdate_arr)) {
                $year  = isset($frdate_arr[0]) ? $frdate_arr[0] : '';
                $month = isset($frdate_arr[1]) ? $frdate_arr[1] : '';
            }
            
            $year  = (int) $year;
            $month = (int) $month;
        }
        
        else if ( $_POST['branch_id'] != '') {
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
            

            $details[$k] = $result;
            
            $k++;
            $j++;
        }
        
        $response['status'] = "1";
        $response['data'] = $details;
       
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }




    public function CurrentOrderAmtByBranchId(){
        $this->load->model('order_model');
        $branch_id = $_POST['branch_id'];
		$response = array();
		$response['live_amount'] = $this->order_model->live_table_total_amount($branch_id);
		header('Content-Type: application/json');
        echo json_encode($response);
        exit;
		
    }
    
    public function CurrentOrderAmtForAdmin(){
        $this->load->model('branch_model');
        $this->load->model('order_model');
        $branch_list = $this->branch_model->present_branch_list();
        $response = $temp = array();
        $i=0;
        foreach($branch_list as $branch){
            if($branch['branch_id']!=1){
                $temp['branch_id'] = $branch['branch_id'];
                $temp['branch_name'] = $branch['name'];
    
                $live_amt = $this->order_model->live_table_total_amount($branch['branch_id']);
                $temp['live_amount'] = $live_amt;
                $response[$i] = $temp;
                $i++;
            }
            
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;

    }

    public function BranchIncome()
	{
		$response = array();

		
		$branch_id = $_POST['branch_id'];
		//$branch_id = 13;

		if($branch_id!='')
		{
			$this->load->model('order_model');
			
			$daily_income_data = $this->order_model->getDailyincomebyBranch($branch_id);
			
			$monthly_income_data = $this->order_model->getMonthlyincome($branch_id);

			$total_income_data = $this->order_model->getTotalincome($branch_id);
			//top 10 selling items of current month
			//$top_items = $this->order_model->top_selling_items_of_current_month($branch_id);

			$response['status'] = '1';

			//$response['top_items'] = $top_items;
			
			if($daily_income_data['daily_income']!=''){
				$response['daily_income'] = number_format($daily_income_data['daily_income'], 2);
			} else {
				$response['daily_income'] = "0.00";
			}

			if($monthly_income_data['monthly_income']!=''){
				$response['monthly_income'] = number_format($monthly_income_data['monthly_income'], 2);
			} else {
				$response['monthly_income'] = "0.00";
			}

			if($total_income_data['total_income']!=''){
				$response['total_income'] = number_format($total_income_data['total_income'], 2);
			} else {
				$response['total_income'] = "0.00";
			}
			

		}
		header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    

    public function report_sales()
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
        
        $tax_name = $this->tax_main_model->tax_list_all();
        
        $result = $this->order_model->sales_report($branch_id, $fromdate, $todate);
        
        $details = array();
        
        $i = 0;
        foreach ($result as $order_data) {
            
            //print_r($order_data);die;
            
            $order_tax_data = $this->order_model->order_tax_data($order_data['order_id'], $order_data['sub_total'], $order_data['discount']);
            
            
            $order_tax_list = array();
            
            $tax=0;
            
            foreach ($order_tax_data as $tax_data) {
                $order_tax_list[$tax] = $tax_data;
                $tax++;
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
            $order_data['bill_amount']    = (float) ($order_data['sub_total']) + $taxSum - ((float) ($order_data['discount']));
            $order_data['roundoff']       = round((float) ($order_data['bill_amount']));
            $order_data['roundoff_value'] = number_format(($order_data['roundoff'] - (float) ($order_data['bill_amount'])), 2);
            
            $details[$i] = $order_data;
            
            $details[$i]['order_tax'] = $order_tax_list;
            
            //$result['order_tax'] = $order_tax_list;
            $i++;
        }
        $response           = array();
        $response['status'] = "1";
        $response['data']   = $details;
        
       
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }


    public function getBillCountByBranchId(){

        

        if(!empty($_POST['branch_id']) && !empty($_POST['fromdate']) && !empty($_POST['todate'])){
            $branch_id = $_POST['branch_id'];
            $fromdate  = date('Y-m-d', strtotime($_POST['fromdate']));
            $todate    = date('Y-m-d', strtotime($_POST['todate']));

            $this->load->model('order_model');
            $details = $this->order_model->getBillCountByBranchId($branch_id, $fromdate, $todate);

            $response['status'] = 1;
            $response["branch_id"] = $branch_id;
            $response["bill_count"] = $details;
        } else {
            $response['status'] = 0;
            $response["message"] = "Invalid Parameter.";
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    





    
}

?>