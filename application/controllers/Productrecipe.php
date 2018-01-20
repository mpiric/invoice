<?php

class Productrecipe extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//$this->load->helper('form');
		$this->load->model('productrecipe_model');
		
	}

	public function create()
	{

		$this->load->helper('form');
    	$this->load->library('form_validation');
		$this->load->view('productrecipe/create');

	}

	public function create_productrecipe()
	{
		$response = array();	
		//print_r($_POST);die;

		if(isset($_POST["store_product_id_arr"]) && $_POST["store_product_id_arr"]!='')
		{
			$store_product_id_arr = explode(',',$_POST["store_product_id_arr"]);

			$inserted_store_product_id_arr = array();

			foreach ($store_product_id_arr as $store_product_id) {
				
				$data = array();
				
				$data['store_product_id'] = $store_product_id;	
				$data['product_id'] = $_POST['product_id'];	

				
				if(isset($_POST['qty_'.$store_product_id]) && $_POST['qty_'.$store_product_id]!="" && $_POST['qty_'.$store_product_id]!= '0')
				{
					$data['qty'] = $_POST['qty_'.$store_product_id];	
				}
				//print_r($_POST['qty_'.$store_product_id]); die;

				$insert_id = $this->productrecipe_model->insert_branch_products($data);

				if($insert_id>0)
				{
					$inserted_store_product_id_arr[] = $insert_id;
				}
			}

			if(array_filter($inserted_store_product_id_arr))
			{
				$response['status'] = "1";
				$response['data'] = "Products updated";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error creating Products";
			}	

		}

		echo json_encode($response);die;
	}

	public function productrecipe_list()
	{
		$result = $this->productrecipe_model->get_data();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;
	}


	public function get_productrecipe_details()
	{
		$response = array();

		if(isset($_POST['branch_products_id']) && $_POST['branch_products_id']!='')
		{			
			$details = $this->productrecipe_model->get_details_by_id($_POST['branch_products_id']);
			$response['data'] = $details;			
		}
		echo json_encode($response);
	}
	public function get_product_data()
	{
		$result = $this->productrecipe_model->get_product_data();
		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;

	}

	public function get_added_products()
	{
		$result = $this->productrecipe_model->get_added_products($_POST['product_id']);
		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;

	}

	public function delete_added_products()
	{
		$response = array();
		
		$product_id = $_POST['product_id'];

		if(isset($_POST["store_product_id"]) && $_POST["store_product_id"]!='')
		{
			$store_product_id_arr = explode(',',$_POST["store_product_id"]);

			foreach ($store_product_id_arr as $store_product_id) 
			{
				$result = $this->productrecipe_model->delete_added_products($store_product_id,$product_id);

				if($result==true)
				{
					$response['status'] = "1";
					$response['message'] = "Recipe Updated";
				}
				else
				{
					$response['status'] = "-1";
					$response['message'] = "Error";
				}
			}
		}

		

		echo json_encode($response);die;
	}

}

?>