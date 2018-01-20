<?php
//defined('BASEPATH') OR exit('No direct script access allowed');

class Branch extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('branch_model');
		
	}
	public function index()
	{
		//$data['branch'] = $this->branch_model->get_data();
		//echo '<pre>';print_r($this->session->userdata('logged_in'));die;
		//$this->load->view('branch/index', $data);	
		$this->load->view('branch/index');	
	}

	public function branch_list()
	{
		$result = $this->branch_model->get_data();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;
	}

	public function check_login()
	{
		$response = array();
		$logged_in = $this->session->userdata('logged_in');

		if(isset($logged_in['branch_id']) && $logged_in['branch_id']!='')
		{	
			$response['status'] = "1";
			$response['data'] = $logged_in;
		}
		else
		{
			$response['status'] = "0";
		}
		echo json_encode($response);die;
	}

	public function login()
	{
		$data = json_decode(file_get_contents("php://input"));

		$response = array();

		if(isset($data->username) && $data->username!='' && isset($data->password) && $data->password!='' )
		{
			 //print_r($data);
			 //echo $data->password;

				$input_fields = array("username"=>$data->username, "password"=>$data->password);

				$result = $this->branch_model->login($input_fields);

					if($result['status'] == 1)
					{
						//if(isset($result['is_active']) && $result['is_active']!='' && $result['is_active'] == 1)
						//{
							$sess_array = array( 

							'username'			=> 	$result['message']['username'],
							'branch_id'			=> 	$result['message']['branch_id'],
							'name'				=> 	$result['message']['name'],
							'branch_type'		=> 	$result['message']['branch_type'], // 1- super admin, 2-franchise
							'is_active' 		=>	$result['message']['is_active']
							);

							// Add user data in session
							$this->session->set_userdata('logged_in', $sess_array);
							
							$response['status'] = "1";
							$response['data'] = $result;
						// } 
						// else
						// {
						// 	$response['status'] = "0";
						// 	$response['message'] = "Unauthorized User";
						// }
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
			$response['message'] = $result['message'];
		}
		echo json_encode($response);
	}

	public function get_state_and_city()
	{
		$location_id = $_POST["location_id"]; //Country ID
		$locationType = $_POST["location_type"];

		$types = array('country', 'State', 'City');

		$this->load->model('location_model');
		$result = $this->location_model->get_details_by_location_type_and_id($locationType,$location_id);
		//print_r($result);

		$selectedLocArr = array();
		if(isset($_POST['branch_id']) && $_POST['branch_id']!='')
		{
			$branch_id = $_POST['branch_id'];
			// get state and city id by branch
			$details = $this->branch_model->get_details_by_id($branch_id);

			if(!empty($details))
			{
				array_push($selectedLocArr,$details['state_id'],$details['city_id']);
			}
		}

		// Now display all location in dorp down list...
		$str = '';
		$str .= '<option value="">Select '.$types[$locationType].'</option>';
		foreach ($result as $value) {
			$selected = '';
			if(in_array($value->location_id, $selectedLocArr))
			{
				$selected = "selected = selected";
			}
		        $str .=  "<option value='" . $value->location_id . "' ".$selected.">" . $value->name . "</option>";
		}
		echo $str;die;

	}

	// Logout from admin page
	public function logout() 
	{

		// Removing session data
		try
		{
			$sess_array = $this->session->userdata('logged_in');
			$this->session->unset_userdata('logged_in', $sess_array);
			$this->session->sess_destroy();
			echo 1;
		}
		catch(Exception $e)
		{
			echo 0;
		}
	
	}

	public function create()
	{
		$this->load->helper('form');
    	$this->load->library('form_validation');
    	$this->load->view('branch/create');

	}

	public function create_branch()
	{
		$this->load->helper('form');
    	$this->load->library('form_validation');

    	$is_create = true;

		$is_validate = $this->branch_model->validateBranch($is_create);

		$response = array();

		if($is_validate == TRUE)
		{
			$result = $this->branch_model->insert_data();


			if($result > 0)
			{
				// insert into tbl
				$noOftables = $this->input->post('no_of_tables');
				$this->load->model('table_model');

				for($i=1;$i<=$noOftables;$i++)
				{

					$data = array( 'table_number' => $i,
									'branch_id' => $result,
									//'max_capacity' => 4
									);

					$this->table_model->insert_data_by_branch($data);
				}

				// insert into branch order code
				$branch_code_data = array();
				$branch_code_data['branch_id'] = $result;
				$branch_code_data['last_order_id'] = 1;

				$this->branch_model->insert_into_branch_order_code($branch_code_data);

				// assign products to created branch according to selected brand
				// get branch details by id
				$branchDetails = $this->branch_model->branch_details_by_id($result);


				if(!empty($branchDetails))
				{
					//get brand_id
					$this->load->model('product_model');
					$this->load->model('branch_products_model');
					
					$brand_id = $branchDetails['brand_id'];
					
					$str = explode(',',$brand_id);
					foreach($str as $bid){
						
						// get brand products
					
						$brand_products = $this->product_model->get_products_by_brand($bid);
	
						//echo '<pre>';print_r($brand_products);
						if(!empty($brand_products))
						{
							foreach ($brand_products as $product) 
							{
								$data = array();
								$data['product_id'] = $product['product_id'];								
								$data['branch_id'] = $result;	
								$data['is_available'] = "N";	
								$data['product_price'] = $product['price'];											
	
								$insert_id = $this->branch_products_model->insert_branch_products($data);
	
								if($insert_id>0)
								{
									$inserted_product_id_arr[] = $insert_id;
								}
							}
						}
					}
					

						

				}

				$response['status'] = "1";
				$response['data'] = "branch created successfully";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error creating branch.";
			}
		}
		else
		{
			//echo 'else';die;
			$response['status'] = "-1";
			$response['data'] = validation_errors();
		}
		echo json_encode($response);die;
	}

	public function update($id='')
	{

		
		$this->load->library('form_validation');

		$details = $this->branch_model->get_details_by_id($id);
		//echo '<pre>'; print_r($details);

		$is_validate = $this->branch_model->validateBranch();
	
		if($is_validate==true)
		{			
			// update
			
			$result = $this->branch_model->update_data($id);
		
			if($result==true)
			{
				// redirect to list
				//$this->session->set_flashdata('success','City updated successfully');
				//$this->load->view('branch/index');
				redirect("branch/index");
			}
			
		}
		
		$this->load->view('branch/create',array('details'=>$details));

	}

	public function get_branch_details()
	{
		$response = array();

		if(isset($_POST['branch_id']) && $_POST['branch_id']!='')
		{			
			$details = $this->branch_model->get_details_by_id($_POST['branch_id']);
			$response['data'] = $details;			
		}
		echo json_encode($response);
	}

	public function update_branch()
	{
		$response = array();
		if(isset($_POST['branch_id']) && $_POST['branch_id']!='')
		{			
			$this->load->library('form_validation');	

			$is_create = false;
			$is_validate = $this->branch_model->validateBranch($is_create);
		
			if($is_validate==true)
			{	
				$result = $this->branch_model->update_data($_POST['branch_id']);

				if($result == TRUE)
				{
					$response['status'] = "1";
					$response['data'] = "branch updated successfully";
					$response['message'] = $result;
				}
				else
				{
					$response['status'] = "0";
					$response['data'] = "Error updating branch.";
				}
			}
			else
			{
				//echo 'else';die;
				$response['status'] = "-1";
				$response['data'] = validation_errors();
			}			
		}
		echo json_encode($response);die;
	}

	public function delete($id='')
	{
		if($id!='')
		{
			$this->branch_model->delete($id);
			echo "Branch deleted successfully";
			//$this->session->set_flashdata('success','City deleted successfully');
		}
		redirect('branch/index');  
	}

	public function branch_delete()
	{
		$response = array();

		if(isset($_POST['branch_id']) && $_POST['branch_id']!='')
		{
			$result = $this->branch_model->delete($_POST['branch_id']);

			if($result == TRUE)
			{
				$response['status'] = "1";
				$response['data'] = "branch deleted successfully";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error deleting branch.";
			}
		}
		echo json_encode($response);die;
	}

	public function infoBranch()
	{
		$this->load->view('branch/info');
	}

	public function update_branch_password()
	{
		$response = array();

		if(isset($_POST['branch_id']) && $_POST['branch_id']!='')
		{
			if(isset($_POST['password']) && $_POST['password']!='')
			{
				$data['password'] = md5($_POST['password']);
				$data['updated']= date("Y-m-d H:i:s");
        		$this->db->where('branch_id', $_POST['branch_id']);
        		$result = $this->db->update('branch',$data);

        		if($result == TRUE)
				{
					$response['status'] = "1";
					$response['data'] = "Password is successfully changed";
				}
				else
				{
					$response['status'] = "0";
					$response['data'] = "Error updating branch";
				}
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Password can not be blank";
			}			
		}
		else
		{
			$response['status'] = "0";
			$response['data'] = "Branch is not available";
		}

		echo json_encode($response);die;		
	}

	public function getLoggedInBranchDetails()
	{
		$response = array();

		$logged_in = $this->session->userdata('logged_in');

		if(isset($logged_in['branch_id']) && $logged_in['branch_id']!='')
		{			
			$details = $this->branch_model->get_details_by_id($logged_in['branch_id']);
			$response['data'] = $details;	

			// get branch list
			$response['branch_list'] = $this->branch_model->present_branch_list();		
		}
		echo json_encode($response);
	}

	public function dashboardData()
	{
		$response = array();

		$logged_in = $this->session->userdata('logged_in');

		if(isset($logged_in['branch_id']) && $logged_in['branch_id']!='')
		{		
			$this->load->model('order_model');
			$details = $this->branch_model->get_data();
			//$response['data'] = $details;	
			//print_r($details);die;
	
			$branch_arr = array();

			$i=0;
			foreach ($details as $branch_data) 
			{

				$branch_id = $branch_data['branch_id'];
				 
				$daily_income = $this->order_model->getDailyincome($branch_id);
				
				$branch_data['daily_income'] = $daily_income;
				$branch_arr[$i] = $branch_data;

				$i++;
			}

			//echo '<pre>';print_r($branch_arr);die;
			$response['status'] = '1';
			// get branch list
			$response['data'] = $branch_arr;		
		}
		echo json_encode($response);
	}
	public function dashboardDatabyBranch()
	{
		$response = array();

		$logged_in = $this->session->userdata('logged_in');
		$branch_id = $logged_in['branch_id'];
		//$branch_id = 13;

		if(isset($branch_id) && $branch_id!='')
		{
			$this->load->model('order_model');
			
			$daily_income_data = $this->order_model->getDailyincomebyBranch($branch_id);
			
			$monthly_income_data = $this->order_model->getMonthlyincome($branch_id);

			$total_income_data = $this->order_model->getTotalincome($branch_id);
			//top 10 selling items of current month
			$top_items = $this->order_model->top_selling_items_of_current_month($branch_id);

			$response['status'] = '1';

			$response['top_items'] = $top_items;
			
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
		echo json_encode($response);
	}

	public function last_week_sale(){
		$response = $temp = array();
		$logged_in = $this->session->userdata('logged_in');
		$branch_id = $logged_in['branch_id'];
		
		$k= 0;
		if(isset($logged_in['branch_id']) && $logged_in['branch_id']!='')
		{
			$this->load->model('order_model');
			
			for($i=7; $i>=0; $i--){
				$date = date('Y-m-d',strtotime("-$i day"));
				$weekDay = date('l',strtotime("-$i day"));
				
				$income = $this->order_model->last_week_sale($branch_id, $date);
				
				if($income['income']!=''){
					$income_final = $income['income'];
				} else {
					$income_final = 0;
				}
				$temp['data_day'] = $weekDay;
				$temp['data_income'] = $income_final;
				$response[$k]=$temp;
				$k++;

			}
		}
		
		echo json_encode($response);

	}

	public function last_week_sale_for_admin(){
		$response = $temp = array();
		
		$branch_id_1 = $_POST['branch_id_1'];
		$branch_id_2 = $_POST['branch_id_1'];
		
		$k= 0;
		if($branch_id_1!='' && $branch_id_2!='')
		{
			$this->load->model('order_model');
			
			for($i=7; $i>=0; $i--){
				
				$date = date('Y-m-d',strtotime("-$i day"));
				$weekDay = date('l',strtotime("-$i day"));
				
				$income_1 = $this->order_model->last_week_sale($branch_id_1, $date);
				$income_2 = $this->order_model->last_week_sale($branch_id_2, $date);
				
				if($income_1['income']!=''){
					$income_final_1 = $income_1['income'];
				} else {
					$income_final_1 = 0;
				}
				if($income_2['income']!=''){
					$income_final_2 = $income_2['income'];
				} else {
					$income_final_2 = 0;
				}
				$temp['data_day'] = $weekDay;
				$temp['data_income_1'] = $income_final_1;
				$temp['data_income_2'] = $income_final_2;
				$response[$k]=$temp;
				$k++;

			}
		}
		
		echo json_encode($response);exit;

	}

	public function monthly_sales(){
		$response = array();
		$logged_in = $this->session->userdata('logged_in');
		$branch_id = $logged_in['branch_id'];
		
		$k= 0;
		
		if(isset($logged_in['branch_id']) && $logged_in['branch_id']!='')
		{
			$this->load->model('order_model');
			for($i=11; $i>=0; $i--){
				$temp['month'] = date("M-Y", strtotime(date('Y-m-01')." -$i months"));
				$start_month = date('Y-m-01', strtotime($temp['month']));
				$end_month = date('Y-m-t',strtotime($start_month));

				$income = $this->order_model->monthly_sales($branch_id, $start_month, $end_month);
				if($income['monthly_sales']!=''){
					$income_final = $income['monthly_sales'];
				} else {
					$income_final = 0;
				}
				$temp['data_income'] = $income_final;

				$response[$k]=$temp;
				$k++;

			}
			
		}
		
		echo json_encode($response);

	}


	public function timezone_php()
	{
		echo date('Y-m-d H:i:s');
	}

	public function get_brand_list_by_branch()
	{
		$this->load->model('brand_model');

		$response = array();

		$logged_in = $this->session->userdata('logged_in');

		if(isset($logged_in['branch_id']) && $logged_in['branch_id']!='')
		{			
			$details = $this->branch_model->get_details_by_id($logged_in['branch_id']);


			if(!empty($details))
			{
				// get brand_id 
				$brand_id_csv = $details['brand_id'];
				$brand_id_arr = explode(',',$brand_id_csv);

				$brand_data = array();

				$i = 0;
				foreach ($brand_id_arr as $brand_id) 
				{
					//echo $brand_id;
					// get brand details by id
					$brand_details = $this->brand_model->get_details_by_id($brand_id);

					if(!empty($brand_details))
					{
						$brand_name = $brand_details['brand_name'];
					}

					$brand_array = array('brand_id'=>$brand_id, 'brand_name'=>$brand_name );

					$brand_data[$i] = $brand_array;
					
					$i++;
				}
			}
			$response['data'] = $brand_data;	

		}
		echo json_encode($response);die;
	}

}
