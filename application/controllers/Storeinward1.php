<?php

class Storeinward extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//$this->load->helper('form');
		$this->load->model('storeinward_model');
		
	}
	public function index()
	{
		//$data['storeinward'] = $this->storeinward_model->get_data();
		//$this->load->view('storeinward/index');	
	}

	public function create()
	{

		$this->load->helper('form');
    	$this->load->library('form_validation');
		$this->load->view('storeinward/create');

	}
	public function createstoreinward()
	{
		$response = array();

		$is_validate = $this->storeinward_model->validatestoreinward();

		if($is_validate == TRUE)
		{
			//echo'<pre>'; print_r($_POST);die;
			$result = $this->storeinward_model->insert_data();
			if($result == TRUE)
			{
				$response['status'] = "1";
				$response['data'] = "Store Inward created successfully";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error creating main Store Inward.";
			}			
		}
		else
		{
			$response['status'] = "-1";
			$response['data'] = validation_errors();
		}
		echo json_encode($response);die;
	}

	public function create_storeinward()
	{
		$response = array();	
		

		if(isset($_POST["store_product_id_arr"]) && $_POST["store_product_id_arr"]!='')
		{
			$store_product_id_arr = explode(',',$_POST["store_product_id_arr"]);

			// echo '<pre>';
			// print_r($store_product_id_arr);die;

			$inserted_store_product_id_arr = array();

			foreach ($store_product_id_arr as $store_product_id) {
				
				//store_product_id;
				$data = array();
				$data['store_product_id'] = $store_product_id;	

				//print_r($_POST['purchase_qty']);die;

				//$data['created'] = date("Y-m-d H:i:s");	

				if(isset($_POST['inward_date']) && $_POST['inward_date']!="")
				{
					$_POST['inward_date'] = str_replace("/","-",$_POST['inward_date']);
					$data['created'] = date("Y-m-d H:i:s",strtotime($_POST['inward_date'].date('H:i:s')));
				}
				else
				{
					$data['created'] = date("Y-m-d H:i:s");
				}		

				if(isset($_POST['is_available_'.$store_product_id]) && $_POST['is_available_'.$store_product_id]=="on")
				{
					$data['is_available'] = "Y";

					if(isset($_POST['purchase_qty_'.$store_product_id]) && $_POST['purchase_qty_'.$store_product_id]!="")
					{
						$data['purchase_qty'] = $_POST['purchase_qty_'.$store_product_id];	
					}	
					else
					{
						$data['purchase_qty'] = 0;
					}
					
						$insert_id = $this->storeinward_model->do_store_inward($data);

						//echo $insert_id;

						if($insert_id>0)
						{
							$inserted_store_product_id_arr[] = $insert_id;

							// insert into store instock
							// the purchased qty would be instock for store

							$store_instock = array();
							$store_instock['store_product_id'] = $data['store_product_id'];
							$store_instock['instock'] = $data['purchase_qty'];
							$store_instock['stock_date'] = date('Y-m-d H:i:s');

							 $this->storeinward_model->insert_into_store_instock($store_instock);

						}	
					
				}
				else
				{
					$data['is_available'] = "N";	

					// check before insert, if the store_product_id is purchased then and only the insert it

					$present_store_products = $this->storeinward_model->get_inwarded_store_product_ids();

					$present_store_product_id_arr = array();

					foreach ($present_store_products as $store_product_idArr) 
					{						
						array_push($present_store_product_id_arr, $store_product_idArr['store_product_id']);
					}

					// compare whether store_product_id is present in inward tbl
					// if present in $present_store_product_id_arr and it is not in $store_product_id_arr, then remove it from inward tbl

					if(in_array($store_product_id,$present_store_product_id_arr))
					{
						// remove 
						$this->storeinward_model->delete_from_inward_by_product_and_date($data['store_product_id'],$data['created']);
						$this->storeinward_model->delete_from_instock_by_product_and_date($data['store_product_id'],$data['created']);
					}
				}

				// update into product table

				$this->load->model('storeproduct_model');

				if(isset($_POST['price_'.$store_product_id]) && $_POST['price_'.$store_product_id]!="")
				{

					$data['price'] = $_POST['price_'.$store_product_id];

					$price = array();
					$price['store_product_id']=$data['store_product_id'];
					$price['price']=$data['price'];


					$insert_id = $this->storeproduct_model->update_price($price);

					//print_r($insert_id);die;

				}
					
				
			}

			if(array_filter($inserted_store_product_id_arr))
			{
				$response['status'] = "1";
				$response['data'] = "Successfully inwarded into store";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Please purchase any product";//Error updating Store Inward
			}	

		}

		echo json_encode($response);die;
	}

	public function update($id='')
	{
		
		$this->load->library('form_validation');

		$details = $this->storeinward_model->get_details_by_id($id);

		$is_validate = $this->storeinward_model->validatestoreinward();
	
		if($is_validate==true)
		{
			// update
			
			$result = $this->storeinward_model->update_data($id);
		
			if($result==true)
			{
				redirect('storeinward/index');
			}
		}
		
		$this->load->view('storeinward/create',array('details'=>$details));

	}

	public function update_storeinward()
	{
		$response = array();
		if(isset($_POST['store_product_id']) && $_POST['store_product_id']!='')
		{			
			$this->load->library('form_validation');	

			$is_create = false;
			$is_validate = $this->storeinward_model->validatewaiterCommission($is_create);
		
			if($is_validate==true)
			{	
				$result = $this->storeinward_model->update_data($_POST['store_product_id']);

				if($result == TRUE)
				{
					$response['status'] = "1";
					$response['data'] = "Store Inward updated successfully";
				}
				else
				{
					$response['status'] = "0";
					$response['data'] = "Error updating Store Inward";
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

	// public function delete($id='')
	// {
	// 	if($id!='')
	// 	{
	// 		$this->storeinward_model->delete($id);
	// 		echo "Branch Products deleted successfully";
	// 		//$this->session->set_flashdata('success','City deleted successfully');
	// 	}
	// 	redirect('storeinward/index');  
	// }

	// public function storeinward_delete()
	// {
	// 	$response = array();

	// 	if(isset($_POST['branch_products_id']) && $_POST['branch_products_id']!='')
	// 	{
	// 		$result = $this->storeinward_model->delete($_POST['branch_products_id']);

	// 		if($result == TRUE)
	// 		{
	// 			$response['status'] = "1";
	// 			$response['data'] = "Branch Product deleted successfully";
	// 		}
	// 		else
	// 		{
	// 			$response['status'] = "0";
	// 			$response['data'] = "Error deleting Branch Product";
	// 		}
	// 	}
	// 	echo json_encode($response);die;
	// }

	public function storeinward_list()
	{
		$result = $this->storeinward_model->get_data();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;
	}

	public function get_storeinward_details()
	{
		$response = array();

		if(isset($_POST['branch_products_id']) && $_POST['branch_products_id']!='')
		{			
			$details = $this->storeinward_model->get_details_by_id($_POST['branch_products_id']);
			$response['data'] = $details;			
		}
		echo json_encode($response);
	}

	public function get_store_product_list_by_date()
	{
		if(isset($_POST['filterdate']) && $_POST['filterdate']!='')
		{

			$filterdate = date("Y-m-d",strtotime($_POST['filterdate'])); 
			//echo $filterdate;die;
			$result = $this->storeinward_model->get_store_product_list_by_date($filterdate);

			$response = array();
			$response['status'] = "1";
			$response['data'] = $result;

			echo json_encode($response);die;
		}		
	}

	public function check_for_editable_field()
	{	
		if(isset($_POST['filterdate']) && $_POST['filterdate']!='')
		{
			$filterdate = date("Y-m-d",strtotime($_POST['filterdate'])); 

			$result = $this->storeinward_model->check_for_editable_field($filterdate);

			$response = array();
			$response['status'] = "1";
			$response['total_rows'] = $result;

			echo json_encode($response);die;
		}
	}
		public function check_max_date()
	{
		$result = $this->storeinward_model->get_max_date();
		
		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;

	}

}

?>