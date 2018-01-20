<?php

class Kitcheninward extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('kitcheninward_model');
		
	}

	public function create()
	{
		$this->load->view('kitcheninward/index');
	}

	public function get_kitchen_product_list()
	{
		$result = $this->kitcheninward_model->get_kitchen_product_list();

		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;
	}

	public function get_kitchen_product_list_by_date()
	{
		if(isset($_POST['filterdate']) && $_POST['filterdate']!='')
		{

			$filterdate = date("Y-m-d",strtotime($_POST['filterdate'])); 
			//echo $filterdate;die;
			$result = $this->kitcheninward_model->get_kitchen_product_list_by_date($filterdate);

			$response = array();
			$response['status'] = "1";
			$response['data'] = $result;

			echo json_encode($response);die;
		}		
	}

	public function dokitcheninward()
	{
		$response = array();	
		$this->load->model('storeinward_model');
		if(isset($_POST["product_id_arr"]) && $_POST["product_id_arr"]!='')
		{
			$product_id_arr = explode(',',$_POST["product_id_arr"]);

			$inserted_product_id_arr = array();

			foreach ($product_id_arr as $store_product_id) {
			
				$data = array();
				$data['store_product_id'] = $store_product_id;	

				if(isset($_POST['inward_date']) && $_POST['inward_date']!="")
				{
					$_POST['inward_date'] = str_replace("/","-",$_POST['inward_date']);
					$data['created'] = date("Y-m-d H:i:s",strtotime($_POST['inward_date'].date('H:i:s')));
				}
				else
				{
					$data['created'] = date("Y-m-d H:i:s");
				}
				//echo $data['inward_date'];die; 						

				if(isset($_POST['inward_qty_'.$store_product_id]) && $_POST['inward_qty_'.$store_product_id]!="")
				{
					$data['inward_qty'] = $_POST['inward_qty_'.$store_product_id];	
				}
				if(isset($_POST['prepared_qty_'.$store_product_id]) && $_POST['prepared_qty_'.$store_product_id]!="")
				{
					$data['prepared_qty'] = $_POST['prepared_qty_'.$store_product_id];	
				}
				if(isset($_POST['remaining_qty_'.$store_product_id]) && $_POST['remaining_qty_'.$store_product_id]!="")
				{
					$data['remaining_qty'] = $_POST['remaining_qty_'.$store_product_id];	

					// insert the remaining qty in kitchen instock tbl
					$instock_data = array();
					$instock_data['store_product_id'] = $data['store_product_id'];
					$instock_data['instock'] = $data['remaining_qty'];
					$instock_data['stock_date'] = date('Y-m-d H:i:s');

					$this->kitcheninward_model->insert_into_kitchen_inward($instock_data);

				}
				if(isset($_POST['waste_qty_'.$store_product_id]) && $_POST['waste_qty_'.$store_product_id]!="")
				{
					$data['waste_qty'] = $_POST['waste_qty_'.$store_product_id];	
				}

				$insert_id = $this->kitcheninward_model->do_kitchen_inward($data);

				if($insert_id>0)
				{
					//echo"hello";
					// if(isset($_POST['filterdate']) && $_POST['filterdate']!='')
					// {
					$filterdate = date("Y-m-d",strtotime($data['created'])); 
						//echo"date-->";print_r($_POST['inward_date']);die;
						

						$this->storeinward_model->update_store_instock($data,$filterdate);
					//}
					
					$inserted_product_id_arr[] = $insert_id;
				}
			}

			if(array_filter($inserted_product_id_arr))
			{
				$response['status'] = "1";
				$response['data'] = "Successfully inwarded into kitchen";
			}
			else
			{
				$response['status'] = "0";
				$response['data'] = "Error in kitchen inward";
			}	

		}

		echo json_encode($response);die;
	}

	public function check_for_editable_field()
	{	
		if(isset($_POST['filterdate']) && $_POST['filterdate']!='')
		{
			$filterdate = date("Y-m-d",strtotime($_POST['filterdate'])); 

			$result = $this->kitcheninward_model->check_for_editable_field($filterdate);

			$response = array();
			$response['status'] = "1";
			$response['total_rows'] = $result;

			echo json_encode($response);die;
		}
	}
	public function check_max_date()
	{
		$result = $this->kitcheninward_model->get_max_date();
		
		$response = array();
		$response['status'] = "1";
		$response['data'] = $result;

		echo json_encode($response);die;

	}
	
}

?>