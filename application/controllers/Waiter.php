<?php

class Waiter extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//$this->load->helper('form');
		$this->load->model('waiter_model');
		
	}
	public function index()
	{
		$data['waiter'] = $this->waiter_model->get_data();
		$this->load->view('waiter/index', $data);
		//$this->load->view('waiter/index');
	}

	public function create_waiter()
	{
		$this->load->helper('form');
    	$this->load->library('form_validation');

    	$response = array();

		$is_validate = $this->waiter_model->validateWaiter();

		if($is_validate == TRUE)
		{
			//echo'<pre>'; print_r($_POST);die;
			$result = $this->waiter_model->insert_data();
			if($result == TRUE)
			{
				$response['status'] = "1";
				$response['data'] = "waiter created successfully";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error creating waiter.";
			}			
		}
		else
		{
			$response['status'] = "-1";
			$response['data'] = validation_errors();
		}
		echo json_encode($response);die;
	}

	public function create()
	{
		$this->load->helper('form');
    	$this->load->library('form_validation');
		$this->load->view('waiter/create');
	}

	public function update($id='')
	{
		
		$this->load->library('form_validation');

		$details = $this->waiter_model->get_details_by_id($id);

		$is_validate = $this->waiter_model->validateWaiter();
	
		if($is_validate==true)
		{
			// update
			
			$result = $this->waiter_model->update_data($id);
		
			if($result==true)
			{
				// redirect to list
				//$this->session->set_flashdata('success','City updated successfully');
				redirect('waiter/index');
			}
			
		}
		
		$this->load->view('waiter/create',array('details'=>$details));

	}

	public function update_waiter()
	{
		$response = array();
		if(isset($_POST['waiter_id']) && $_POST['waiter_id']!='')
		{			
			$this->load->library('form_validation');	

			$is_create = false;
			$is_validate = $this->waiter_model->validateWaiter();
		
			if($is_validate==true)
			{	
				$result = $this->waiter_model->update_data($_POST['waiter_id']);

				if($result == TRUE)
				{
					$response['status'] = "1";
					$response['data'] = "waiter updated successfully";
				}
				else
				{
					$response['status'] = "0";
					$response['data'] = "Error updating waiter.";
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
			$this->waiter_model->delete($id);
			echo "Waiter deleted successfully";
			//$this->session->set_flashdata('success','City deleted successfully');
		}
		redirect('waiter/index');  
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
		if(isset($_POST['waiter_id']) && $_POST['waiter_id']!='')
		{
			$waiter_id = $_POST['waiter_id'];
			// get state and city id by waiter
			$details = $this->waiter_model->get_details_by_id($waiter_id);

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

	public function waiter_list()
	{
		$result = $this->waiter_model->get_data();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;
		
		echo json_encode($response);die;
	}

	public function waiter_delete()
	{
		$response = array();

		if(isset($_POST['waiter_id']) && $_POST['waiter_id']!='')
		{
			$result = $this->waiter_model->delete($_POST['waiter_id']);

			if($result == TRUE)
			{
				$response['status'] = "1";
				$response['data'] = "waiter deleted successfully";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error deleting waiter.";
			}
		}
		echo json_encode($response);die;
	}

	public function get_waiter_details()
	{
		$response = array();

		if(isset($_POST['waiter_id']) && $_POST['waiter_id']!='')
		{			
			$details = $this->waiter_model->get_details_by_id($_POST['waiter_id']);
			$response['data'] = $details;			
		}
		echo json_encode($response);
	}

	public function infoWaiter()
	{
		$this->load->view('waiter/info');
	}

	public function waiter_list_by_branch()
	{
		$result = $this->waiter_model->branch_wise_waiter();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;
	}

	public function getWaiterList($value='')
	{   
		$response = array();
		$response['waiter_list'] = $this->waiter_model->waiter_list();
		echo json_encode($response);
	}

	public function testNature()
	{
		$this->load->view('waiter/testNature');
	}

	/*********** API for app **************/

	public function waiter_login(){
		$response = array();
		
		if(isset($_POST['contact_number']) && $_POST['contact_number']!='' && isset($_POST['password']) && $_POST['password']!='' ){
			
			$waiter = $this->waiter_model->waiter_login();
			
			
			if($waiter != 0){
				$response['status'] = "1";
				$response['waiter_data'] = $waiter;
				
				$this->load->model('branch_model');
				$branch_details = $this->branch_model->branch_details_by_id($waiter['branch_id']);

				$response['branch_data'] = $branch_details;

				/*$this->load->model('table_model');
				$table_details = $this->table_model->get_tables_by_branch_id($waiter['branch_id']);

				$response['table_data'] = $table_details;*/
			} else {
				$response['status'] = "2";
				$response['message'] = "Invalid Contact number or password.";
			}

			
		} else {
			$response['status'] = 0;
			$response['message'] = "Invalid Parameter.";

		}
		
		header("Content-type:application/json");
		echo json_encode($response);
		exit;
	}

}

?>