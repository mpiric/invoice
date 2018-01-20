<?php

class WaiterCommission extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//$this->load->helper('form');
		$this->load->model('waiter_commission_model');
		
	}
	public function index()
	{
		$this->load->view('waiterCommission/index');
	}

	public function create()
	{
		
		$this->load->helper('form');
    	$this->load->library('form_validation');
		$this->load->view('waiterCommission/create');

	}

	public function create_waitercommission()
	{
		$this->load->helper('form');
    	$this->load->library('form_validation');

    	$is_create = true;

		$is_validate = $this->waiter_commission_model->validatewaiterCommission($is_create);

		$response = array();

		if($is_validate == TRUE)
		{
			$result = $this->waiter_commission_model->insert_data();
			if($result == TRUE)
			{
				$response['status'] = "1";
				$response['data'] = "Waiter Commission created successfully";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error creating Waiter Commission.";
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

		$details = $this->waiter_commission_model->get_details_by_id($id);
		//echo '<pre>'; print_r($details);

		$is_validate = $this->waiter_commission_model->validatewaiterCommission();
	
		if($is_validate==true)
		{			
			// update
			
			$result = $this->waiter_commission_model->update_data($id);
		
			if($result==true)
			{
				redirect("waiterCommission/index");
			}
			
		}
		
		$this->load->view('waiterCommission/create',array('details'=>$details));

	}

	public function update_waitercommission()
	{
		$response = array();
		if(isset($_POST['waiter_commission_id']) && $_POST['waiter_commission_id']!='')
		{			
			$this->load->library('form_validation');	

			$is_create = false;
			$is_validate = $this->waiter_commission_model->validatewaiterCommission($is_create);
		
			if($is_validate==true)
			{	
				$result = $this->waiter_commission_model->update_data($_POST['waiter_commission_id']);

				if($result == TRUE)
				{
					$response['status'] = "1";
					$response['data'] = "Waiter Commission updated successfully";
				}
				else
				{
					$response['status'] = "0";
					$response['data'] = "Error updating Waiter Commission";
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
			$this->waiter_commission_model->delete($id);
			echo "Waiter Commission deleted successfully";
			//$this->session->set_flashdata('success','City deleted successfully');
		}
		redirect('waiterCommission/index');  
	}

	public function waitercommission_delete()
	{
		$response = array();

		if(isset($_POST['waiter_commission_id']) && $_POST['waiter_commission_id']!='')
		{
			$result = $this->waiter_commission_model->delete($_POST['waiter_commission_id']);

			if($result == TRUE)
			{
				$response['status'] = "1";
				$response['data'] = "Waiter Commission deleted successfully";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error deleting Waiter Commission";
			}
		}
		echo json_encode($response);die;
	}

	public function infoWaitercommission()
	{
		$this->load->view('waiterCommission/info');
	}

	public function waitercommission_list()
	{
		$result = $this->waiter_commission_model->get_data();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;
	}

	public function get_waitercommission_details()
	{
		$response = array();

		if(isset($_POST['waiter_commission_id']) && $_POST['waiter_commission_id']!='')
		{			
			$details = $this->waiter_commission_model->get_details_by_id($_POST['waiter_commission_id']);
			$response['data'] = $details;			
		}
		echo json_encode($response);
	}

}

?>