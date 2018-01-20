<?php

class ProductCategory extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//$this->load->helper('form');
		$this->load->model('product_category_model');
		
	}
	public function index()
	{
		//$data['product_category'] = $this->product_category_model->get_data();
		//unset($data['deleted']);
		$this->load->view('productCategory/index');
	}

	public function create_productcategory()
	{
		$this->load->helper('form');
    	$this->load->library('form_validation');
    	$this->load->model('branch_model');
    	$this->load->model('branch_products_model');
    	$this->load->model('product_model');

    	$response = array();

		$is_validate = $this->product_category_model->validateproductCategory();

		if($is_validate == TRUE)
		{
			//echo'<pre>'; print_r($_POST);die;
			$result = $this->product_category_model->insert_data();

			if($result > 0)
			{
				$inserted_product_category_id = $result;

				// get details by id
				// $prod_cat_details = $this->product_category_model->get_details_by_id($inserted_product_category_id);

				// if(!empty($prod_cat_details))
				// {

				// 	// get brand
				// 	$brand_id_csv = $prod_cat_details['brand_id'];
				// 	$brand_id_arr = explode(',', $brand_id_csv);

				// 	foreach ($brand_id_arr as $brand_id) {
						
				// 		if($brand_id!="" && $brand_id!=0 && $brand_id!=null)
				// 		{

				// 			// get associated branches by brand id
				// 			$branch_list = $this->branch_model->get_branch_details_by_brand_id($brand_id);

				// 			if(!empty($branch_list))
				// 			{

				// 				// get branch id
				// 				foreach ($branch_list as $branch) 
				// 				{
				// 					$branch_products_array = array();
				// 					$branch_products_array['branch_id'] = $branch['branch_id'];

				// 					// get all products by product category
				// 					$product_list = $this->product_model->get_products_by_product_category_id($inserted_product_category_id);

				// 					//echo 'inserted_product_category_id='.$inserted_product_category_id;

				// 					//echo '<pre>'.print_r($product_list);

				// 					if(!empty($product_list))
				// 					{
				// 						foreach ($product_list as $product) 
				// 						{
											
				// 							$branch_products_array['product_id'] = $product['product_id'];
				// 							$branch_products_array['is_available'] = "Y";	
				// 							$branch_products_array['product_price'] = $product['price'];	

				// 							$insert_id = $this->branch_products_model->insert_branch_products($branch_products_array);

				// 							if($insert_id>0)
				// 							{
				// 								$inserted_product_id_arr[] = $insert_id;
				// 							}
				// 						}
				// 					}

				// 				}
				// 			}
				// 		}
				// 	}
				// }



				$response['status'] = "1";
				$response['data'] = "Product Category created successfully";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error creating Product Category.";
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
		$this->load->view('productCategory/create');
	}

	public function update($id='')
	{

		
		$this->load->library('form_validation');

		$details = $this->product_category_model->get_details_by_id($id);
		//echo '<pre>'; print_r($details);

		$is_validate = $this->product_category_model->validateproductCategory();
	
		if($is_validate==true)
		{			
			// update
			
			$result = $this->product_category_model->update_data($id);
		
			if($result==true)
			{
				// redirect to list
				//$this->session->set_flashdata('success','City updated successfully');
				//$this->load->view('productCategory/index');
				redirect("productCategory/index");
			}
			
		}
		
		$this->load->view('productCategory/create',array('details'=>$details));

	}

	public function update_productcategory()
	{
		$this->load->model('branch_model');
    	$this->load->model('branch_products_model');
    	$this->load->model('product_model');
    	
		$response = array();
		//echo '<pre>';print_r($_POST);die;
		if(isset($_POST['product_category_id']) && $_POST['product_category_id']!='')
		{			
			$this->load->library('form_validation');	

			$is_create = false;
			$is_validate = $this->product_category_model->validateproductCategory();
		
			if($is_validate==true)
			{	
				$result = $this->product_category_model->update_data($_POST['product_category_id']);

				if($result == TRUE)
				{
					
					// get details by id
					$prod_cat_details = $this->product_category_model->get_details_by_id($_POST['product_category_id']);

					if(!empty($prod_cat_details))
					{

						// get brand
						$brand_id_csv = $prod_cat_details['brand_id'];
						$brand_id_arr = explode(',', $brand_id_csv);

						foreach ($brand_id_arr as $brand_id) {
							
							if($brand_id!="" && $brand_id!=0 && $brand_id!=null)
							{

								// get associated branches by brand id
								$branch_list = $this->branch_model->get_branch_details_by_brand_id($brand_id);

								if(!empty($branch_list))
								{

									// get branch id
									foreach ($branch_list as $branch) 
									{
										$branch_products_array = array();
										$branch_products_array['branch_id'] = $branch['branch_id'];

										// get all products by product category
										$product_list = $this->product_model->get_products_by_product_category_id($_POST['product_category_id']);

										//echo 'inserted_product_category_id='.$inserted_product_category_id;

										//echo '<pre>'.print_r($product_list);

										if(!empty($product_list))
										{
											foreach ($product_list as $product) 
											{
												
												$branch_products_array['product_id'] = $product['product_id'];
												$branch_products_array['is_available'] = "Y";	
												$branch_products_array['product_price'] = $product['price'];	

												$insert_id = $this->branch_products_model->insert_branch_products($branch_products_array);

												if($insert_id>0)
												{
													$inserted_product_id_arr[] = $insert_id;
												}
											}
										}

									}
								}
							}
						}
					}

					$response['status'] = "1";
					$response['data'] = "Product Category updated successfully";
				}
				else
				{
					$response['status'] = "0";
					$response['data'] = "Error updating Product Category";
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
			$this->product_category_model->delete($id);
			echo "Waiter deleted successfully";
			//$this->session->set_flashdata('success','City deleted successfully');
		}
		redirect('productCategory/index');  
	}

	public function getProductCatList($value='')
	{   
		$response = array();
		$response['product_cat_list'] = $this->product_category_model->product_category_list();
		echo json_encode($response);
	}

	public function product_category_list()
	{
		$result = $this->product_category_model->get_data();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;
	}

	public function product_category_delete()
	{
		$response = array();

		if(isset($_POST['product_category_id']) && $_POST['product_category_id']!='')
		{
			$result = $this->product_category_model->delete($_POST['product_category_id']);

			if($result == TRUE)
			{
				$response['status'] = "1";
				$response['data'] = "Product Category deleted successfully";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error deleting Product Category.";
			}
		}
		echo json_encode($response);die;
	}

	public function get_product_category_details()
	{
		$response = array();

		if(isset($_POST['product_category_id']) && $_POST['product_category_id']!='')
		{			
			$details = $this->product_category_model->get_details_by_id($_POST['product_category_id']);
			$response['data'] = $details;			
		}
		echo json_encode($response);
	}

	public function infoProductcategory()
	{
		$this->load->view('productCategory/info');
	}

}

?>