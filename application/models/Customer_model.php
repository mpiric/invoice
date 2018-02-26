<?php 

class Customer_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	public function validateCustomer()
	{
		$this->load->library('form_validation');

        $this->form_validation->set_rules('order_id','order_id','required');
        //$this->form_validation->set_rules('order_type','order_type','required');
		$this->form_validation->set_rules('firstname','firstname','required');
        $this->form_validation->set_rules('lastname','lastname','required');
        $this->form_validation->set_rules('contact','contact','required');
        $this->form_validation->set_rules('email','email','required');
       
		if($this->form_validation->run() === FALSE)
        {
            return false;   
        }
        else
        {
            return true;
        }
		
	}

	public function set_data()
    {
    	$data = array(

            'order_id' => $this->input->post('order_id'),

            'order_type' => $this->input->post('order_type'),

    		'firstname'=> $this->input->post('firstname'),

            'lastname'=> $this->input->post('lastname'),

            'contact'=> $this->input->post('contact'),

            'email'=> $this->input->post('email'),

    		);
    	return $data;

    }
    public function get_data()
    {
        $query = $this->db->query(" SELECT CASE WHEN order_type = 1 THEN 'Table Order' WHEN order_type = 2 THEN 'Home Delivery' WHEN order_type = 3 THEN 'Parcel' END AS `order_type`, order_detail.order_code, customer.order_id, customer.customer_id, customer.firstname, customer.lastname, customer.contact, customer.email, customer.`created`, customer.`updated`, customer.`deleted` FROM `customer` LEFT JOIN `order_detail` ON `order_detail`.`order_id` = `customer`.`order_id` WHERE `customer`.`deleted` IS NULL ");

        $result = $query->result_array();
        return $result;
    }

    public function get_details_by_id($id)
    {

        $query = $this->db->query(" SELECT order_detail.order_code,customer.order_id,customer.order_type, customer.customer_id, customer.firstname, customer.lastname,customer.address, customer.contact, customer.email, customer.`created`, customer.`updated`, customer.`deleted` FROM `customer` LEFT JOIN `order_detail` ON `order_detail`.`order_id` = `customer`.`order_id` WHERE `customer`.`deleted` IS NULL AND customer.`customer_id`= $id ");

        $result = $query->row_array();
        return $result;
    }

    public function insert_data()
    {

        $data = $this->set_data();
        $result = $this->db->insert('customer',$data);
        
        return $result;
    }

    public function update_data($id)
    {
        //echo "model";die;
        $data = $this->set_data();
        $data['updated']= date("Y-m-d H:i:s");
        $this->db->where('customer_id', $id);
        $result = $this->db->update('customer',$data);
        return $result;

    }
    public function delete($id)
    {
        $data['deleted']= date("Y-m-d H:i:s");
        $this->db->where('customer_id', $id);
        $result = $this->db->update('customer',$data);
        return $result;
    }

    public function customer_list()
    {
        $this->db->select('customer_id,firstname,lastname,order_type,contact,email');
        $query = $this->db->get('customer');        
        $result = $query->result_array();
        return $result;
    }

    public function insert_into_customer($data)
    {
         $this->db->insert('customer',$data);
         $result = $this->db->insert_id();
        
        return $result;
    }

} 

?>