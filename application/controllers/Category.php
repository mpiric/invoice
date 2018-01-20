<?php

class category extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//$this->load->helper('form');
		$this->load->model('category_model');
		
	}
	public function index()
	{
		//$data['category'] = $this->category_model->get_data();
		//unset($data['deleted']);
		$this->load->view('category/index');
	}

	public function create_category()
	{
		$this->load->helper('form');
    	$this->load->library('form_validation');

    	$response = array();

		$is_validate = $this->category_model->validateCategory();

		if($is_validate == TRUE)
		{
			//echo'<pre>'; print_r($_POST);die;
			$result = $this->category_model->insert_data();
			if($result == TRUE)
			{
				$response['status'] = "1";
				$response['data'] = "Category created successfully";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error creating Category.";
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
		$this->load->view('category/create');
	}

	public function update($id='')
	{

		
		$this->load->library('form_validation');

		$details = $this->category_model->get_details_by_id($id);
		//echo '<pre>'; print_r($details);

		$is_validate = $this->category_model->validateCategory();
	
		if($is_validate==true)
		{			
			// update
			
			$result = $this->category_model->update_data($id);
		
			if($result==true)
			{
				// redirect to list
				//$this->session->set_flashdata('success','City updated successfully');
				//$this->load->view('category/index');
				redirect("category/index");
			}
			
		}
		
		$this->load->view('category/create',array('details'=>$details));

	}

	public function update_category()
	{
		$response = array();
		//echo '<pre>';print_r($_POST);die;
		if(isset($_POST['category_id']) && $_POST['category_id']!='')
		{			
			$this->load->library('form_validation');	

			$is_create = false;
			$is_validate = $this->category_model->validateCategory();
		
			if($is_validate==true)
			{	
				$result = $this->category_model->update_data($_POST['category_id']);

				if($result == TRUE)
				{
					$response['status'] = "1";
					$response['data'] = "Category updated successfully";
				}
				else
				{
					$response['status'] = "0";
					$response['data'] = "Error updating Category";
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
			$this->category_model->delete($id);
			echo "Waiter deleted successfully";
			//$this->session->set_flashdata('success','City deleted successfully');
		}
		redirect('category/index');  
	}

	public function getCategoryList($value='')
	{   
		$response = array();
		$response['category_list'] = $this->category_model->category_list();
		echo json_encode($response);
	}

	public function getCategoryListbybranch($value='')
	{   
		$response = array();
		$response['category_list_by_branch'] = $this->category_model->category_list_by_branch();
		echo json_encode($response);
	}

	public function category_list()
	{
		$result = $this->category_model->get_data();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;
	}

	public function category_delete()
	{
		$response = array();

		if(isset($_POST['category_id']) && $_POST['category_id']!='')
		{
			$result = $this->category_model->delete($_POST['category_id']);

			if($result == TRUE)
			{
				$response['status'] = "1";
				$response['data'] = "Category deleted successfully";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error deleting Category.";
			}
		}
		echo json_encode($response);die;
	}

	public function get_category_details()
	{
		$response = array();

		if(isset($_POST['category_id']) && $_POST['category_id']!='')
		{			
			$details = $this->category_model->get_details_by_id($_POST['category_id']);
			$response['data'] = $details;			
		}
		echo json_encode($response);
	}

	public function infocategory()
	{
		$this->load->view('category/info');
	}

}

?>