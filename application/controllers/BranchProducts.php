<?php

class BranchProducts extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//$this->load->helper('form');
		$this->load->model('branch_products_model');
		
	}
	public function index()
	{
		//$data['branchProducts'] = $this->branch_products_model->get_data();
		$this->load->view('branchProducts/index');	
	}

	public function create()
	{

		$this->load->helper('form');
    	$this->load->library('form_validation');
		$this->load->view('branchProducts/create');

	}

	public function create_branchproducts()
	{
		$response = array();	
		if(isset($_POST["product_id_arr"]) && $_POST["product_id_arr"]!='')
		{
			$product_id_arr = explode(',',$_POST["product_id_arr"]);

			// echo '<pre>';
			// print_r($_POST);

			$inserted_product_id_arr = array();

			foreach ($product_id_arr as $product_id) {
				
				//product_id;
				$data = array();
				$data['product_id'] = $product_id;	

				if(isset($_POST['branch_id']) && $_POST['branch_id']!="")
				{
					$data['branch_id'] = $_POST['branch_id'];
				}			

				if(isset($_POST['is_available_'.$product_id]) && $_POST['is_available_'.$product_id]=="on")
				{
					$data['is_available'] = "Y";		
				}
				else
				{
					$data['is_available'] = "N";	
				}

				if(isset($_POST['product_price_'.$product_id]) && $_POST['product_price_'.$product_id]!="")
				{
					$data['product_price'] = $_POST['product_price_'.$product_id];	
				}

				if(isset($_POST['waiter_commission_branch_'.$product_id]) && $_POST['waiter_commission_branch_'.$product_id]!="")
				{
					$data['waiter_commission_branch'] = $_POST['waiter_commission_branch_'.$product_id];	
				}

				$insert_id = $this->branch_products_model->insert_branch_products($data);

				if($insert_id>0)
				{
					$inserted_product_id_arr[] = $insert_id;
				}
			}

			if(array_filter($inserted_product_id_arr))
			{
				$response['status'] = "1";
				$response['data'] = "Branch Products updated";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error creating Branch Products";
			}	

		}

		echo json_encode($response);die;
	}

	public function update($id='')
	{
		
		$this->load->library('form_validation');

		$details = $this->branch_products_model->get_details_by_id($id);

		$is_validate = $this->branch_products_model->validatebranchProducts();
	
		if($is_validate==true)
		{
			// update
			
			$result = $this->branch_products_model->update_data($id);
		
			if($result==true)
			{
				redirect('branchProducts/index');
			}
		}
		
		$this->load->view('branchProducts/create',array('details'=>$details));

	}

	public function update_branchproducts()
	{
		$response = array();
		if(isset($_POST['branch_products_id']) && $_POST['branch_products_id']!='')
		{			
			$this->load->library('form_validation');	

			$is_create = false;
			$is_validate = $this->branch_products_model->validatewaiterCommission($is_create);
		
			if($is_validate==true)
			{	
				$result = $this->branch_products_model->update_data($_POST['branch_products_id']);

				if($result == TRUE)
				{
					$response['status'] = "1";
					$response['data'] = "Branch Product updated successfully";
				}
				else
				{
					$response['status'] = "0";
					$response['data'] = "Error updating Branch Product";
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
			$this->branch_products_model->delete($id);
			echo "Branch Products deleted successfully";
			//$this->session->set_flashdata('success','City deleted successfully');
		}
		redirect('branchProducts/index');  
	}

	public function branchproducts_delete()
	{
		$response = array();

		if(isset($_POST['branch_products_id']) && $_POST['branch_products_id']!='')
		{
			$result = $this->branch_products_model->delete($_POST['branch_products_id']);

			if($result == TRUE)
			{
				$response['status'] = "1";
				$response['data'] = "Branch Product deleted successfully";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error deleting Branch Product";
			}
		}
		echo json_encode($response);die;
	}

	public function branchproducts_list()
	{
		$result = $this->branch_products_model->get_data();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;
	}

	public function infoBranchproducts()
	{
		$this->load->view('branchProducts/info');
	}

	public function get_branchproducts_details()
	{
		$response = array();

		if(isset($_POST['branch_products_id']) && $_POST['branch_products_id']!='')
		{			
			$details = $this->branch_products_model->get_details_by_id($_POST['branch_products_id']);
			$response['data'] = $details;			
		}
		echo json_encode($response);
	}
	public function get_product_data()
	{
		$result = $this->branch_products_model->get_product_data();
		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;

	}

	public function get_branch_products()
	{
		$result = $this->branch_products_model->get_branch_products($_POST['branch_id']);
		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;

	}

	/*** API for application ***/
	public function get_products_by_branch()
	{
		if(isset($_POST['branch_id']) && $_POST['branch_id']!='' ){
			$result = $this->branch_products_model->get_products_by_branch($_POST['branch_id']);
			$response = array();
			$response['status'] = "1";
			$response['data'] = $result;

		} else {
			$response['status'] = "0";
			$response['message'] = "Invalid Parameter.";
		}
		header("Content-type:application/json");
		echo json_encode($response);exit;
		

	}

}

?>