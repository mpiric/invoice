<?php

class Product extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//$this->load->helper('form');
		$this->load->model('product_model');
		
	}
	public function index()
	{
		//$data['product'] = $this->product_model->get_data();
		//$this->load->view('product/index', $data);
		$this->load->view('product/index');
	}

	public function create_product()
	{
		$this->load->helper('form');
    	$this->load->library('form_validation');
    	$this->load->model('branch_model');
    	$this->load->model('branch_products_model');
    	$this->load->model('product_category_model');

    	$response = array();


    	if(isset($_POST['params']))
    	{
    		$keyVal = explode('&',$_POST['params']);

			$data = array();

			foreach ($keyVal as $value) {
				$postParsmsArr = explode("=",$value);
				$postParsmskey = $postParsmsArr[0];
				
				$postParsmsval = str_replace('+', ' ', $postParsmsArr[1]);
				$data[$postParsmskey] = $postParsmsval;
			}

			$is_empty_elem = $this->product_model->emptyElementExists($data);

			$this->load->model('branch_model');
			$branch_id_arr = $this->branch_model->getBranchIdList();

			//echo '<pre>';print_r($branch_id_arr);die;

			//$branch_id_arr = array();


			// if(isset($_POST['branch_id_csv']) && $_POST['branch_id_csv']!='')
			// {
			// 	$branch_id_arr = explode(",", $_POST['branch_id_csv']);
			// }

			if($is_empty_elem==true || empty($branch_id_arr))
			{
				$response['status'] = "-1";
				$response['data'] = "Please fill out the mandatory fields.";
			}
			else
			{			
				$result = $this->product_model->custom_insert_data($data);

				
				//echo"<pre>";print_r($branch_id_arr);die;
				

				if(!empty($result))
				{	

					if($result['status']=="success")
					{
						$product_id =$result['message'];
						$product_arr = $this->product_model->get_details_by_id($product_id);
						$product_price = $product_arr['price'];


							// get details by id
							$prod_cat_details = $this->product_category_model->get_details_by_id($product_arr['product_category_id']);

							if(!empty($prod_cat_details))
							{

								// get brand
								$brand_id = $prod_cat_details['brand_id'];



									
									if($brand_id!="" && $brand_id!=0 && $brand_id!=null)
									{
										//$branch_list = $this->branch_model->get_branch_details_by_brand_id($brand_id);

										// get associated branches by brand id
										$branch_list = $this->branch_model->get_associated_branches_by_brand_id($brand_id);

										if(!empty($branch_list))
										{

											// get branch id
											foreach ($branch_list as $branch) 
											{
												$branch_products_array = array();
												$branch_products_array['branch_id'] = $branch['branch_id'];
														
												$branch_products_array['product_id'] = $product_arr['product_id'];
												$branch_products_array['is_available'] = "N";	
												$branch_products_array['product_price'] = $product_arr['price'];	

												$insert_id = $this->branch_products_model->insert_branch_products($branch_products_array);

												if($insert_id>0)
												{
													$inserted_product_id_arr[] = $insert_id;
												}
													
											}
										}
									}
								
							}

						

							// if(!empty($branch_id_arr))
							// {
							// 	foreach ($branch_id_arr as $branch_id) {
							// 		// insert into branch products tbl
							// 		$branch_product_data = array();
							// 		$branch_product_data['branch_id'] = $branch_id['branch_id'];
							// 		$branch_product_data['product_id'] = $product_id;
							// 		$branch_product_data['product_price'] = $product_price;
							// 		$branch_product_data['is_available'] = 'Y';
							// 		$branch_product_data['waiter_commission_branch'] = '0';

							// 		$this->branch_products_model->insert_branch_products($branch_product_data);						
							// 	}
							// }
						
						$response['status'] = "1";
						$response['data'] = "product created successfully";
					}
					else
					{
						$response['status'] = "-1";
						$response['data'] = $result['message'];
					}

				}

			}	
    	}    			
		
		echo json_encode($response);die;
	}

	public function create()
	{
		$this->load->helper('form');
    	$this->load->library('form_validation');
		$this->load->view('product/create');
	}

	public function update_product()
	{
		$this->load->helper('form');
    	$this->load->library('form_validation');
    	$this->load->model('branch_products_model');

    	$response = array();

    	if(isset($_POST['product_id']) && $_POST['product_id']!='')
    	{    	

	    	if(isset($_POST['params']) && $_POST['params']!=='')
	    	{
	    		$keyVal = explode('&',$_POST['params']);

				$data = array();

				foreach ($keyVal as $value) {
					$postParsmsArr = explode("=",$value);
					$postParsmskey = $postParsmsArr[0];
					$postParsmsval = str_replace('+', ' ', $postParsmsArr[1]);

					$data[$postParsmskey] = $postParsmsval;
				}

				$is_empty_elem = $this->product_model->emptyElementExists($data);

				
				if($is_empty_elem==true)
				{
					$response['status'] = "-1";
					$response['data'] = "Please fill out the mandatory fields.";
				}
				else
				{			
					$result = $this->product_model->custom_update_data($data,$_POST['product_id']);

					//echo '<pre>';print_r($result);die;

					if(!empty($result))
					{
						if($result['status']=="success")
						{
							$response['status'] = "1";
							$response['data'] = "product updated successfully";	
						}
						else
						{
							$response['status'] = "-1";
							$response['data'] = $result['message'];
						}						
					}
				}	
	    	}    

    	}			
		
		echo json_encode($response);die;
	}

	public function update($id='')
	{
		
		$this->load->library('form_validation');

		$details = $this->product_model->get_details_by_id($id);

		$is_validate = $this->product_model->validateProduct();
	
		if($is_validate==true)
		{
			// update
			
			$result = $this->product_model->update_data($id);
		
			if($result==true)
			{
				// redirect to list
				//$this->session->set_flashdata('success','City updated successfully');
				redirect('product/index');
			}
			
		}
		
		$this->load->view('product/create',array('details'=>$details));

	}

	public function update_product_old()
	{
		$response = array();
		if(isset($_POST['product_id']) && $_POST['product_id']!='')
		{			
			$this->load->library('form_validation');	

			$is_create = false;
			$is_validate = $this->product_model->validateProduct();
		
			if($is_validate==true)
			{	
				$result = $this->product_model->update_data($_POST['product_id']);

				if($result == TRUE)
				{
					$response['status'] = "1";
					$response['data'] = "product updated successfully";
				}
				else
				{
					$response['status'] = "0";
					$response['data'] = "Error updating product.";
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
			$this->product_model->delete($id);
			echo "Product deleted successfully";
			//$this->session->set_flashdata('success','City deleted successfully');
		}
		redirect('product/index');  
	}

	public function product_list()
	{
		$result = $this->product_model->get_data();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;
	}

	public function product_delete()
	{
		$response = array();

		if(isset($_POST['product_id']) && $_POST['product_id']!='')
		{
			$result = $this->product_model->delete($_POST['product_id']);

			if($result == TRUE)
			{
				$response['status'] = "1";
				$response['data'] = "product deleted successfully";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error deleting product.";
			}
		}
		echo json_encode($response);die;
	}

	public function get_product_details()
	{
		$response = array();

		if(isset($_POST['product_id']) && $_POST['product_id']!='')
		{			
			$details = $this->product_model->get_details_by_id($_POST['product_id']);
			$response['data'] = $details;			
		}
		echo json_encode($response);
	}


	public function infoProduct()
	{
		$this->load->view('product/info');
	}

	public function uploadFile()
	{
		$keyVal = explode('&',$_POST['params']);

		$data = array();

		foreach ($keyVal as $value) {
			$postParsmsArr = explode("=",$value);
			$postParsmskey = $postParsmsArr[0];
			$postParsmsval = $postParsmsArr[1];
			$data[$postParsmskey] = $postParsmsval;
		}

		echo '<pre>';print_r($data);
		echo '<pre>';print_r($_FILES);
		die;
	}

	public function product_list_by_branch()
	{
		$result = $this->product_model->get_data_by_branch();

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

	// get product list for parcel order
	public function parcel_product_list_by_branch()
	{
		$result = $this->product_model->parcel_product_list_by_branch();

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

	public function delivery_product_list_by_branch()
	{
		$result = $this->product_model->delivery_product_list_by_branch();

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

	// script to add branch products
	// public function addp()
	// {
	// 	// get all products
	// 	$product_arr = $this->product_model->product_list();

	// 	$this->load->model('branch_products_model');

	// 	//echo '<pre>';print_r($product_arr);die;

	// 	foreach ($product_arr as $product) {
	// 								// insert into branch products tbl
	// 								$branch_product_data = array();
	// 								$branch_product_data['branch_id'] = 5;
	// 								$branch_product_data['product_id'] = $product['product_id'];
	// 								$branch_product_data['product_price'] = $product['price'];
	// 								$branch_product_data['is_available'] = 'Y';
	// 								$branch_product_data['waiter_commission_branch'] = '0';

	// 								$this->branch_products_model->insert_branch_products($branch_product_data);						
	// 							}
	// }

	public function check_name()
	{
		$response = array();

		if(isset($_POST['name']) && $_POST['name']!='')
		{			
			$details = $this->product_model->check_name($_POST['name']);

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
			$details = $this->product_model->check_code($_POST['code']);

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

	public function product_details()
	{
		$result = $this->product_model->product_list();

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