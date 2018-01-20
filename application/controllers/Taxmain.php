<?php

class Taxmain extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//$this->load->helper('form');
		$this->load->model('tax_main_model');
		
	}
	public function index()
	{
		//$data['tax_main'] = $this->tax_main_model->get_data();
		//unset($data['deleted']);
		$this->load->view('taxmain/index');
	}

	public function create_taxmain()
	{
		$this->load->helper('form');
    	$this->load->library('form_validation');

    	$response = array();

		$is_validate = $this->tax_main_model->validatetaxmain();

		if($is_validate == TRUE)
		{
			//echo'<pre>'; print_r($_POST);die;
			$result = $this->tax_main_model->insert_data();
			if($result == TRUE)
			{
				$response['status'] = "1";
				$response['data'] = "Main Tax created successfully";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error creating main Tax.";
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
		$this->load->view('taxmain/create');
	}

	public function update($id='')
	{

		
		$this->load->library('form_validation');

		$details = $this->tax_main_model->get_details_by_id($id);
		//echo '<pre>'; print_r($details);

		$is_validate = $this->tax_main_model->validatetaxmain();
	
		if($is_validate==true)
		{			
			// update
			
			$result = $this->tax_main_model->update_data($id);
		
			if($result==true)
			{
				// redirect to list
				//$this->session->set_flashdata('success','City updated successfully');
				//$this->load->view('taxmain/index');
				redirect("taxmain/index");
			}
			
		}
		
		$this->load->view('taxmain/create',array('details'=>$details));

	}

	public function update_taxmain()
	{
		$response = array();
		//echo '<pre>';print_r($_POST);die;
		if(isset($_POST['tax_id']) && $_POST['tax_id']!='')
		{			
			$this->load->library('form_validation');	

			$is_create = false;
			$is_validate = $this->tax_main_model->validatetaxmain();
		
			if($is_validate==true)
			{	
				$result = $this->tax_main_model->update_data($_POST['tax_id']);

				if($result == TRUE)
				{
					$response['status'] = "1";
					$response['data'] = "Tax updated successfully";
				}
				else
				{
					$response['status'] = "0";
					$response['data'] = "Error updating Tax";
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
			$this->tax_main_model->delete($id);
			echo "Waiter deleted successfully";
			//$this->session->set_flashdata('success','City deleted successfully');
		}
		redirect('taxmain/index');  
	}

	public function gettaxmainList($value='')
	{   
		$response = array();
		$response['tax_main_list'] = $this->tax_main_model->tax_main_list();
		echo json_encode($response);
	}

	public function gettaxmainListbybranch($value='')
	{   
		$response = array();
		$response['tax_main_list_by_branch'] = $this->tax_main_model->tax_main_list_by_branch();
		echo json_encode($response);
	}

	public function gettaxList($value='')
	{
		$response = array();
		$response['tax_list_all'] = $this->tax_main_model->tax_list_all();
		echo json_encode($response);
	}

	public function tax_main_list()
	{
		$result = $this->tax_main_model->get_data();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;
	}

	public function tax_main_delete()
	{
		$response = array();

		if(isset($_POST['tax_id']) && $_POST['tax_id']!='')
		{
			$result = $this->tax_main_model->delete($_POST['tax_id']);

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

	public function get_tax_main_details()
	{
		$response = array();

		if(isset($_POST['tax_id']) && $_POST['tax_id']!='')
		{			
			$details = $this->tax_main_model->get_details_by_id($_POST['tax_id']);
			$response['data'] = $details;			
		}
		echo json_encode($response);
	}

	public function infotaxmain()
	{
		$this->load->view('taxmain/info');
	}

}

?>