<?php
//defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends CI_Controller {

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
		$this->load->model('customer_model');
		
	}
	public function index()
	{
		//$data['customer'] = $this->customer_model->get_data();
		$this->load->view('customer/index');
		//$this->load->view('customer/index');
	}

	public function create()
	{
			
		$this->load->helper('form');
    	$this->load->library('form_validation');
		$this->load->view('customer/create');		
	}

	public function create_customer()
	{
		$this->load->helper('form');
    	$this->load->library('form_validation');

    	$is_create = true;

		$is_validate = $this->customer_model->validateCustomer($is_create);

		$response = array();

		if($is_validate == TRUE)
		{
			$result = $this->customer_model->insert_data();
			if($result == TRUE)
			{
				$response['status'] = "1";
				$response['data'] = "Customer created successfully";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error creating Customer.";
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

		$details = $this->customer_model->get_details_by_id($id);
		//echo '<pre>'; print_r($details);

		$is_validate = $this->customer_model->validateCustomer();
	
		if($is_validate==true)
		{			
			// update
			
			$result = $this->customer_model->update_data($id);
		
			if($result==true)
			{
				redirect("customer/index");
			}
			
		}
		
		$this->load->view('customer/create',array('details'=>$details));

	}

	public function update_customer()
	{
		$response = array();
		if(isset($_POST['customer_id']) && $_POST['customer_id']!='')
		{			
			$this->load->library('form_validation');	

			$is_create = false;
			$is_validate = $this->customer_model->validateCustomer($is_create);
		
			if($is_validate==true)
			{	
				$result = $this->customer_model->update_data($_POST['customer_id']);

				if($result == TRUE)
				{
					$response['status'] = "1";
					$response['data'] = "Customer updated successfully";
				}
				else
				{
					$response['status'] = "0";
					$response['data'] = "Error updating Customer.";
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
			$this->customer_model->delete($id);
			echo "Customer deleted successfully";
			//$this->session->set_flashdata('success','City deleted successfully');
		}
		redirect('customer/index');  
	}

	public function customer_delete()
	{
		$response = array();

		if(isset($_POST['customer_id']) && $_POST['customer_id']!='')
		{
			$result = $this->customer_model->delete($_POST['customer_id']);

			if($result == TRUE)
			{
				$response['status'] = "1";
				$response['data'] = "Customer deleted successfully";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error deleting Customer.";
			}
		}
		echo json_encode($response);die;
	}

	public function get_customer_details()
	{
		$response = array();

		if(isset($_POST['customer_id']) && $_POST['customer_id']!='')
		{			
			$details = $this->customer_model->get_details_by_id($_POST['customer_id']);
			$response['data'] = $details;			
		}
		echo json_encode($response);
	}

	public function customer_list()
	{
		$result = $this->customer_model->get_data();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;
	}

	public function infoCustomer()
	{
		$this->load->view('customer/info');
	}

}
