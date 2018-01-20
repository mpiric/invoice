<?php

class Tax extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//$this->load->helper('form');
		$this->load->model('tax_model');
		
	}
	public function index()
	{
		//$data['tax'] = $this->tax_model->get_data();
		//$this->load->view('tax/index', $data);
		$this->load->view('tax/index');
	}

	public function tax_list()
	{
		$result = $this->tax_model->get_data();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;
	}

	public function create()
	{
		//$sess_arr=$this->session->userdata('logged_in');
		//echo '<pre>';print_r($sess_arr);die;
		$this->load->helper('form');
    	$this->load->library('form_validation');
    	$this->load->view('tax/create');

		// $is_validate = $this->tax_model->validateTax();

		// if($is_validate == TRUE)
		// {
		// 	//echo'<pre>'; print_r($_POST);die;
		// 	$result = $this->tax_model->insert_data();
		// 	if($result == TRUE)
		// 	{
		// 		echo"Created Successfully";
		// 		redirect(array("tax/index"));
		// 	}
		// 	else
		// 	{
		// 		//echo"ifelse";
		// 		$this->load->view('tax/create');
		// 	}
			
		// }
		// else
		// {
		// 	//echo"else";
		// 	$this->load->view('tax/create');
		// }
	}

	public function create_tax()
	{
		$this->load->helper('form');
    	$this->load->library('form_validation');

    	$is_create = true;

		$is_validate = $this->tax_model->validateTax($is_create);

		$response = array();

		if($is_validate == TRUE)
		{
			$result = $this->tax_model->insert_data();
			if($result == TRUE)
			{
				$response['status'] = "1";
				$response['data'] = "Tax created successfully";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error creating Tax.";
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

		$details = $this->tax_model->get_details_by_id($id);
		//echo '<pre>'; print_r($details);

		$is_validate = $this->tax_model->validateTax();
	
		if($is_validate==true)
		{			
			// update
			
			$result = $this->tax_model->update_data($id);
		
			if($result==true)
			{
				// redirect to list
				//$this->session->set_flashdata('success','City updated successfully');
				//$this->load->view('tax/index');
				redirect("tax/index");
			}
			
		}
		
		$this->load->view('tax/create',array('details'=>$details));

	}

	public function get_tax_details()
	{
		$response = array();

		if(isset($_POST['tax_master_id']) && $_POST['tax_master_id']!='')
		{			
			$details = $this->tax_model->get_details_by_id($_POST['tax_master_id']);
			$response['data'] = $details;			
		}
		echo json_encode($response);
	}

	public function get_tax_details_info()
	{
		$response = array();

		if(isset($_POST['tax_master_id']) && $_POST['tax_master_id']!='')
		{			
			$details = $this->tax_model->get_details_by_id_info($_POST['tax_master_id']);
			$response['data'] = $details;			
		}
		echo json_encode($response);
	}

	public function update_tax()
	{
		$response = array();
		if(isset($_POST['tax_master_id']) && $_POST['tax_master_id']!='')
		{			
			$this->load->library('form_validation');	

			$is_create = false;
			$is_validate = $this->tax_model->validateTax($is_create);
		
			if($is_validate==true)
			{	
				$result = $this->tax_model->update_data($_POST['tax_master_id']);

				if($result == TRUE)
				{
					$response['status'] = "1";
					$response['data'] = "tax updated successfully";
				}
				else
				{
					$response['status'] = "0";
					$response['data'] = "Error updating tax.";
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
			$this->tax_model->delete($id);
			echo "Tax deleted successfully";
			//$this->session->set_flashdata('success','City deleted successfully');
		}
		redirect('tax/index');  
	}

	public function tax_delete()
	{
		$response = array();

		if(isset($_POST['tax_master_id']) && $_POST['tax_master_id']!='')
		{
			$result = $this->tax_model->delete($_POST['tax_master_id']);

			if($result == TRUE)
			{
				$response['status'] = "1";
				$response['data'] = "tax deleted successfully";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error deleting tax.";
			}
		}
		echo json_encode($response);die;
	}

	public function infoTax()
	{
		$this->load->view('tax/info');
	}

	public function tax_by_branch()
	{
		$result = $this->tax_model->get_tax_by_branch();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;
	}

	public function parcel_tax_by_branch()
	{
		$result = $this->tax_model->parcel_get_tax_by_branch();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;
	}

	public function delivery_tax_by_branch()
	{
		$result = $this->tax_model->delivery_get_tax_by_branch();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;
	}

	public function branch_wise_tax()
	{

		if(isset($_POST['branch_id']) && $_POST['branch_id']!='')
		{
			//$result = $this->tax_model->branch_wise_tax($_POST['branch_id']);
			$result = $this->tax_model->branch_wise_tax_to_display($_POST['branch_id']);
			

			$response = array();
			$response['status'] = "1";
			$response['data'] = $result;

			echo json_encode($response);die;
		}

	}

	public function branch_specific_tax_list()
	{
		$result = $this->tax_model->branch_specific_tax_list();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;
		
	}

	public function parcel_branchSpecificTax_list()
	{
		$result = $this->tax_model->parcel_branchSpecificTax_list();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;
		
	}

	public function delivery_branchSpecificTax_list()
	{
		$result = $this->tax_model->delivery_branchSpecificTax_list();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;
		
	}

}

?>