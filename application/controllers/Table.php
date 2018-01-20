<?php

class Table extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//$this->load->helper('form');
		$this->load->model('table_model');
		
	}
	public function index()
	{
		$data['table'] = $this->table_model->get_data();
		$this->load->view('table/index', $data);
		//$this->load->view('waiter/index');
	}

	public function create()
	{
		$sess_arr=$this->session->userdata('logged_in');
		//echo '<pre>';print_r($sess_arr);die;
		$this->load->helper('form');
    	$this->load->library('form_validation');

		$is_validate = $this->table_model->validateTable();

		if($is_validate == TRUE)
		{
			//echo'<pre>'; print_r($_POST);die;
			$result = $this->table_model->insert_data();
			if($result == TRUE)
			{
				echo"Created Successfully";
				redirect(array("table/index"));
			}
			else
			{
				//echo"ifelse";
				$this->load->view('table/create');
			}
			
		}
		else
		{
			//echo"else";
			$this->load->view('table/create');
		}
	}

	public function update($id='')
	{
		
		$this->load->library('form_validation');

		$details = $this->table_model->get_details_by_id($id);

		$is_validate = $this->table_model->validateTable();
	
		if($is_validate==true)
		{
			// update
			
			$result = $this->table_model->update_data($id);
		
			if($result==true)
			{
				// redirect to list
				//$this->session->set_flashdata('success','City updated successfully');
				redirect('table/index');
			}
			
		}
		
		$this->load->view('table/create',array('details'=>$details));

	}

	public function delete($id='')
	{
		if($id!='')
		{
			$this->table_model->delete($id);
			echo "Waiter deleted successfully";
			//$this->session->set_flashdata('success','City deleted successfully');
		}
		redirect('table/index');  
	}

	public function table_list()
	{
		$result = $this->table_model->get_data();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;	

		echo json_encode($response);die;
	}

	public function table_list_by_branch()
	{
		$result = $this->table_model->get_data_by_branch();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;	

		echo json_encode($response);die;
	}

	/*** API for application ***/

	public function get_table_details_by_branch(){
		$response = array();
		if( isset($_POST['branch_id']) && $_POST['branch_id']!='' ){
			$result = $this->table_model->get_tables_by_branch_id($_POST['branch_id']);
			$response = array();
			$response['status'] = "1";
			$response['data'] = $result;
		} else {
			$response['status'] = 0;
			$response['message'] = "Invalid Parameter.";
		}
		header("Content-type:application/json");
		echo json_encode($response);
		exit;
		
	}

}

?>