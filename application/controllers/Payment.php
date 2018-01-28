<?php

class Payment extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//$this->load->helper('form');
		$this->load->model('payment_model');
		
	}
	public function index()
	{
		//$data['tax_main'] = $this->payment_model->get_data();
		//unset($data['deleted']);
		$this->load->view('payment/index');
	}

	public function create_payment()
	{
		$this->load->helper('form');
    	$this->load->library('form_validation');

    	$response = array();

		$is_validate = $this->payment_model->validatepayment();

		if($is_validate == TRUE)
		{
			//echo'<pre>'; print_r($_POST);die;
			$result = $this->payment_model->insert_data();
			if($result == TRUE)
			{
				$response['status'] = "1";
				$response['data'] = "Payment type created successfully";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error creating Payment type.";
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
		$this->load->view('payment/create');
	}

	public function update($id='')
	{

		
		$this->load->library('form_validation');

		$details = $this->payment_model->get_details_by_id($id);
		//echo '<pre>'; print_r($details);

		$is_validate = $this->payment_model->validatepayment();
	
		if($is_validate==true)
		{			
			// update
			
			$result = $this->payment_model->update_data($id);
		
			if($result==true)
			{
				// redirect to list
				//$this->session->set_flashdata('success','City updated successfully');
				//$this->load->view('taxmain/index');
				redirect("payment/index");
			}
			
		}
		
		$this->load->view('payment/create',array('details'=>$details));

	}

	public function update_payment()
	{
		$response = array();
		//echo '<pre>';print_r($_POST);die;
		if(isset($_POST['payment_id']) && $_POST['payment_id']!='')
		{			
			$this->load->library('form_validation');	

			$is_create = false;
			$is_validate = $this->payment_model->validatepayment();
		
			if($is_validate==true)
			{	
				$result = $this->payment_model->update_data($_POST['payment_id']);

				if($result == TRUE)
				{
					$response['status'] = "1";
					$response['data'] = "Payment type updated successfully";
				}
				else
				{
					$response['status'] = "0";
					$response['data'] = "Error updating payment type";
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
			$this->payment_model->delete($id);
			echo "Payment type deleted successfully";
			//$this->session->set_flashdata('success','City deleted successfully');
		}
		redirect('payment/index');  
	}

	public function getPaymentTypeForOrder()
	{   
		$response = array();
		$response['payment_list'] = $this->payment_model->getPaymentTypeForOrder();
		echo json_encode($response);
	}

	// public function gettaxmainListbybranch($value='')
	// {   
	// 	$response = array();
	// 	$response['payment_main_list_by_branch'] = $this->payment_model->tax_main_list_by_branch();
	// 	echo json_encode($response);
	// }

	// public function getpaymentList($value='')
	// {
	// 	$response = array();
	// 	$response['payment_list_all'] = $this->payment_model->payment_list_all();
	// 	echo json_encode($response);
	// }

	public function payment_list()
	{
		$result = $this->payment_model->get_data();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;
	}

	// public function payment_main_delete()
	// {
	// 	$response = array();

	// 	if(isset($_POST['payment_id']) && $_POST['payment_id']!='')
	// 	{
	// 		$result = $this->payment_model->delete($_POST['payment_id']);

	// 		if($result == TRUE)
	// 		{
	// 			$response['status'] = "1";
	// 			$response['data'] = "Payment type deleted successfully";
	// 		}
	// 		else
	// 		{
	// 			$response['status'] = "0";
	// 			$response['data'] = "Error deleting payment type.";
	// 		}
	// 	}
	// 	echo json_encode($response);die;
	// }

	public function get_payment_details()
	{
		$response = array();

		if(isset($_POST['payment_id']) && $_POST['payment_id']!='')
		{			
			$details = $this->payment_model->get_details_by_id($_POST['payment_id']);
			$response['data'] = $details;			
		}
		echo json_encode($response);
	}

	

}

?>