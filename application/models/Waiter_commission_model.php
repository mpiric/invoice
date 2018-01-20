<?php 

class Waiter_commission_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	public function validatewaiterCommission()
	{
		$this->load->library('form_validation');

        $this->form_validation->set_rules('waiter_id','waiter_id','required');
		$this->form_validation->set_rules('product_category_id','product_category_id','required');
        $this->form_validation->set_rules('order_id','order_id','required');
        $this->form_validation->set_rules('commission_date','commission_date','required');
        $this->form_validation->set_rules('commission_amount','commission_amount','required');
		$this->form_validation->set_rules('product_qty','product_qty','required');
       


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

    		'waiter_id'=> $this->input->post('waiter_id'),

            'product_category_id'=> $this->input->post('product_category_id'),

            'order_id'=> $this->input->post('order_id'),

            'commission_date'=> $this->input->post('commission_date'),

            'commission_amount'=> $this->input->post('commission_amount'),

            'product_qty'=> $this->input->post('product_qty'),

    		);

    	return $data;

    }

    public function get_data()
    {
        $query = $this->db->query("SELECT waiter_commission.*, waiter.waiter_code AS waitercode, product_category.name AS product_category_name, order_detail.order_code AS ordercode FROM waiter_commission LEFT JOIN waiter ON waiter.waiter_id = waiter_commission.waiter_id LEFT JOIN product_category ON product_category.product_category_id = waiter_commission.product_category_id LEFT JOIN order_detail ON order_detail.order_id = waiter_commission.order_id WHERE waiter_commission.deleted IS NULL AND order_detail.is_print=1");

        //$query = $this->db->get_where('waiter_commission', array('deleted' => null));
        
        $result = $query->result_array();
        return $result;
    }

    public function get_details_by_id($id)
    {

        $query = $this->db->query("SELECT waiter_commission.*, waiter.waiter_code AS waitercode, product_category.name AS product_category_name, order_detail.order_code AS ordercode FROM waiter_commission LEFT JOIN waiter ON waiter.waiter_id = waiter_commission.waiter_id LEFT JOIN product_category ON product_category.product_category_id = waiter_commission.product_category_id LEFT JOIN order_detail ON order_detail.order_id = waiter_commission.order_id WHERE waiter_commission.deleted IS NULL AND waiter_commission.waiter_commission_id=$id");
        $result = $query->row_array();
        return $result;
    }

    public function insert_data()
    {

        $data = $this->set_data();
        $result = $this->db->insert('waiter_commission',$data);

        return $result;
    }

    public function update_data($id)
    {
        
        $data = $this->set_data();
        $data['updated']= date("Y-m-d H:i:s");
        $this->db->where('waiter_commission_id', $id);
        $result = $this->db->update('waiter_commission',$data);
        return $result;

    }
    public function delete($id)
    {
        $data['deleted']= date("Y-m-d H:i:s");
        $this->db->where('waiter_commission_id', $id);
        $result = $this->db->update('waiter_commission',$data);
        return $result;
    }

    // public function waitercommission_list()
    // {
    //     $this->db->select('waiter_commission_id,name');
    //     $query = $this->db->get('waiter_commission');        
    //     $result = $query->result_array();
    //     return $result;
    // }

} 

?>