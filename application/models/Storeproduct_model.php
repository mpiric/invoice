<?php 

class Storeproduct_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	public function validatestoreproduct()
	{
		$this->load->library('form_validation');
        $this->form_validation->set_rules('category_id','category_id','required');
		$this->form_validation->set_rules('name','name','required');
		//$this->form_validation->set_rules('poduct_code','poduct_code','required');
        $this->form_validation->set_rules('unit','unit','required');
        $this->form_validation->set_rules('price','price','required');
       
		
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
        $session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];

    	$data = array(
            
            'branch_id'=> $branch_id,

            'category_id' => $this->input->post('category_id'), 

    		'name'=> $this->input->post('name'),

            'product_code'=> $this->input->post('product_code'),

            'unit'=> $this->input->post('unit'),

            'price'=> $this->input->post('price'),

            
    		);
    	return $data;

    }

    public function get_data()
    {
        $query = $this->db->get_where('store_product');
        return $query->result_array();
    }

    public function get_data_by_branch($branch_id)
    {

        $query = $this->db->query( " SELECT spi.*,sp.name, sp.`product_code` FROM `store_product_inward` spi LEFT JOIN `store_product` sp ON sp.store_product_id = spi.store_product_id WHERE spi.`branch_id` = '".$branch_id."' ");

        //$query = $this->db->get_where('store_product_inward');
        return $query->result_array();
    }

    public function get_details_by_id($id)
    {
        $query = $this->db->get_where('store_product', array('store_product_id' => $id));
        $result = $query->row_array();
        return $result;
    }

    public function insert_data()
    {

        $data = $this->set_data();
        $query = $this->db->insert('store_product',$data);
        $result = $this->db->insert_id();
        return $result;
        // $q = $this->db->get_where('store_product', array('store_product_id' => $id));
        // return $q->row_array();
    }

    public function insert_data_store_product_inward($data)
    {
        $query = $this->db->insert('store_product_inward',$data);
        $result = $this->db->insert_id();
        return $result;
    }

    public function update_data($id)
    {
        //echo "model";die;
        $data = $this->set_data();
        $data['updated']= date("Y-m-d H:i:s");
        $this->db->where('store_product_id', $id);
        $result = $this->db->update('store_product',$data);
        return $result;
    }
    public function update_price($data)
    {
        $data['updated']= date("Y-m-d H:i:s");
        $this->db->where('store_product_id', $data['store_product_id']);
        $result = $this->db->update('store_product',$data);
        return $result;
    }

    public function update_price_store_product_inward($data)
    {
        $data['updated']= date("Y-m-d H:i:s");
        $this->db->where('store_product_inward_id', $data['store_product_inward_id']);
        $result = $this->db->update('store_product_inward',$data);
        return $result;
    }

    public function update_store_product_inward($data)
    {
        $data['updated']= date("Y-m-d H:i:s");

        $this->db->where('store_product_id',$data['store_product_id']);
        $this->db->where('branch_id',$data['branch_id']);
        $result = $this->db->update('store_product_inward',$data);

        return $result;
    }

    public function delete($id)
    {
        $this->db->where('store_product_id', $id);
        $this->db->delete('store_product');
       
    }

    public function delete_by_branch($id)
    {
        $this->db->where('branch_id',$id);
        $this->db->delete('store_product');
    }

    public function check_name($name)
    {
        $this->db->select('*');
        $this->db->like('name',$name);
        $query=$this->db->get('store_product');

        $result = $query->result_array();
        return $result;
    }

    public function check_code($code)
    {
        $this->db->select('*');
        $this->db->like('product_code',$code);
        $query=$this->db->get('store_product');

        $result = $query->result_array();
        return $result;
    }

    public function get_store_product_data($product_id)
    {
        $query = $this->db->query( " SELECT sp.store_product_id, sp.name, sp.product_code, (SELECT qty FROM `product_recipe` pr WHERE pr.`store_product_id` = sp.`store_product_id` AND pr.product_id='".$product_id."') AS qty FROM  store_product sp " );
        //LEFT JOIN `product_recipe` pr1  ON  pr1.`store_product_id` = sp.`store_product_id`  GROUP BY pr1.`store_product_id

        return $query->result_array();
    }



} 

?>