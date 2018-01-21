<?php


class Brand extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//$this->load->helper('form');
		$this->load->model('brand_model');
		
	}
	public function index()
	{
		//$data['tax_main'] = $this->brand_model->get_data();
		//unset($data['deleted']);
		$this->load->view('brand/index');
	}

	public function create_brand()
	{
		$this->load->helper('form');
    	$this->load->library('form_validation');

    	$response = array();

		$is_validate = $this->brand_model->validatebrand();

		if($is_validate == TRUE)
		{
			//echo'<pre>'; print_r($_POST);die;
			$result = $this->brand_model->insert_data();
			if($result == TRUE)
			{
				$response['status'] = "1";
				$response['data'] = "Brand created successfully";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error creating main Brand.";
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
		$this->load->view('brand/create');
	}

	public function update($id='')
	{

		
		$this->load->library('form_validation');

		$details = $this->brand_model->get_details_by_id($id);
		//echo '<pre>'; print_r($details);

		$is_validate = $this->brand_model->validatebrand();
	
		if($is_validate==true)
		{			
			// update
			
			$result = $this->brand_model->update_data($id);
		
			if($result==true)
			{
				// redirect to list
				//$this->session->set_flashdata('success','City updated successfully');
				//$this->load->view('brand/index');
				redirect("brand/index");
			}
			
		}
		
		$this->load->view('brand/create',array('details'=>$details));

	}

	public function update_brand()
	{
		$response = array();
		//echo '<pre>';print_r($_POST);die;
		if(isset($_POST['brand_id']) && $_POST['brand_id']!='')
		{			
			$this->load->library('form_validation');	

			$is_create = false;
			$is_validate = $this->brand_model->validatebrand();
		
			if($is_validate==true)
			{	
				$result = $this->brand_model->update_data($_POST['brand_id']);

				if($result == TRUE)
				{
					$response['status'] = "1";
					$response['data'] = "Brand updated successfully";
				}
				else
				{
					$response['status'] = "0";
					$response['data'] = "Error updating Brand";
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
			$this->brand_model->delete($id);
			echo "Waiter deleted successfully";
			//$this->session->set_flashdata('success','City deleted successfully');
		}
		redirect('brand/index');  
	}

	public function getbrandList($value='')
	{   
		$response = array();
		$response['brand_list'] = $this->brand_model->brand_list();
		echo json_encode($response);
	}

	// public function getbrandListbybranch($value='')
	// {   
	// 	$response = array();
	// 	$response['tax_main_list_by_branch'] = $this->brand_model->tax_main_list_by_branch();
	// 	echo json_encode($response);
	// }

	public function brand_list()
	{
		$result = $this->brand_model->get_data();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;
	}

	public function brand_delete()
	{
		$response = array();

		if(isset($_POST['brand_id']) && $_POST['brand_id']!='')
		{
			$result = $this->brand_model->delete($_POST['brand_id']);

			if($result == TRUE)
			{
				$response['status'] = "1";
				$response['data'] = "Main Tax deleted successfully";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error deleting main Tax.";
			}
		}
		echo json_encode($response);die;
	}

	public function get_brand_details()
	{
		$response = array();

		if(isset($_POST['brand_id']) && $_POST['brand_id']!='')
		{			
			$details = $this->brand_model->get_details_by_id($_POST['brand_id']);
			$response['data'] = $details;			
		}
		echo json_encode($response);
	}

	public function infobrand()
	{
		$this->load->view('brand/info');
	}

}

?>