<?php

class Storeproduct extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//$this->load->helper('form');
		$this->load->model('storeproduct_model');
		
	}
	public function index()
	{
		$this->load->view('storeproduct/index');
	}

	public function create_storeproduct()
	{
		$this->load->helper('form');
    	$this->load->library('form_validation');

    	$response = array();

		$is_validate = $this->storeproduct_model->validatestoreproduct();

		if($is_validate == TRUE)
		{
			$storeData = array();
			//echo'<pre>'; print_r($_POST);die;
			$resultId = $this->storeproduct_model->insert_data();

			$result = $this->storeproduct_model->get_details_by_id($resultId);
  
			$this->load->model('branch_model');
			$branch_list = $this->branch_model->present_branch_list();

			foreach ($branch_list as $branch) {

				$storeData['branch_id'] = $branch['branch_id'];
				$storeData['store_product_id'] = $result['store_product_id'];
			    $storeData['price'] = $result['price'];

			    $inserId = $this->storeproduct_model->insert_data_store_product_inward($storeData);

			    if(!empty($inserId))
				{
					$response['status'] = "1";
					$response['data'] = "Store Product created successfully";
				}
				else
				{
					$response['status'] = "0";
					$response['data'] = "Error creating Store Product.";
				}			
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
		$this->load->view('storeproduct/create');
	}

	public function update($id='')
	{
		
		$this->load->library('form_validation');

		$details = $this->storeproduct_model->get_details_by_id($id);

		$is_validate = $this->storeproduct_model->validatestoreproduct();
	
		if($is_validate==true)
		{
			// update
			
			$result = $this->storeproduct_model->update_data($id);
		
			if($result==true)
			{
				// redirect to list
				//$this->session->set_flashdata('success','City updated successfully');
				redirect('storeproduct/index');
			}
			
		}
		
		$this->load->view('storeproduct/create',array('details'=>$details));

	}

	public function update_storeproduct()
	{
		$response = array();
		//echo '<pre>';print_r($_POST);die;

		if(isset($_POST['store_product_id']) && $_POST['store_product_id']!='')
		{			
			$this->load->library('form_validation');	

			$is_create = false;
			$is_validate = $this->storeproduct_model->validatestoreproduct();
		
			if($is_validate==true)
			{	
				if($_POST['branch_id'] == '1')
				{
					$result = $this->storeproduct_model->update_data($_POST['store_product_id']);
				}
				else
				{
					$updateData = array();
					$updateData['store_product_id'] = $_POST['store_product_id'];
					$updateData['branch_id'] = $_POST['branch_id'];
					$updateData['price'] = $_POST['price'];
					
					$result = $this->storeproduct_model->update_store_product_inward($updateData);
				}
				

				if($result == TRUE)
				{
					$response['status'] = "1";
					$response['data'] = "Store Product updated successfully";
				}
				else
				{
					$response['status'] = "0";
					$response['data'] = "Error updating Store Product";
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
			$this->storeproduct_model->delete($id);
			echo "Store Product deleted successfully";
			
		}
		redirect('storeproduct/index');  
	}
	public function storeproduct_delete()
	{
		$response = array();

		if(isset($_POST['store_product_id']) && $_POST['store_product_id']!='')
		{
			$this->storeproduct_model->delete($_POST['store_product_id']);
			$response['status'] = "1";
			$response['data'] = "Store Product deleted successfully";
			
		}
		echo json_encode($response);die;
	}

	public function storeproduct_list()
	{
		$logged_in = $this->session->userdata('logged_in');

		if( $logged_in['branch_id'] == '1')
		{
			$result = $this->storeproduct_model->get_data();
		}
		else
		{
			$result = $this->storeproduct_model->get_data_by_branch($logged_in['branch_id']);
		}

		

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;	

		echo json_encode($response);die;
	}
	public function get_storeproduct_details()
	{
		$response = array();

		if(isset($_POST['store_product_id']) && $_POST['store_product_id']!='')
		{			
			$details = $this->storeproduct_model->get_details_by_id($_POST['store_product_id']);
			$response['data'] = $details;			
		}
		echo json_encode($response);
	}

	public function storeproduct_list_by_branch()
	{
		$result = $this->storeproduct_model->get_data_by_branch();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;	

		echo json_encode($response);die;
	}

	public function check_name()
	{
		$response = array();

		if(isset($_POST['name']) && $_POST['name']!='')
		{			
			$details = $this->storeproduct_model->check_name($_POST['name']);

			if(!empty($details))
			{
				$response['status'] = false;
			}
			else
			{
				$response['status'] = true;
			}
				
		}
		echo json_encode($response);
	}

	public function check_code()
	{
		$response = array();

		if(isset($_POST['code']) && $_POST['code']!='')
		{			
			$details = $this->storeproduct_model->check_code($_POST['code']);

			if(!empty($details))
			{
				$response['status'] = false;
			}
			else
			{
				$response['status'] = true;
			}
				
		}
		echo json_encode($response);
	}

	public function store_product_details()
	{

		//print_r($_POST);die;
		$result = $this->storeproduct_model->get_store_product_data($_POST['product_id']);

		$response = array();

		if($result!='')
		{
			$response['status'] = "1";
			$response['data'] = $result;
		}
		else
		{
			$response['status'] = "0";
			$response['data'] = array();
		}

		
		
		echo json_encode($response);die;
	}

}

?>