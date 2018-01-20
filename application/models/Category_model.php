<?php 

class Category_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	public function validateCategory()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('cat_name','cat_name','required');
		//$this->form_validation->set_rules('parent','parent','required');
       
		
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
    		'cat_name'=> $this->input->post('cat_name')
    		);
    	return $data;

    }

    public function get_data()
    {
        $query = $this->db->query("SELECT * FROM  `category` WHERE `category`.`deleted` IS NULL ");
        
        $result = $query->result_array();

        return $result;
    }

    public function get_details_by_id($id)
    {

        $query = $this->db->query("SELECT * FROM  `category` WHERE `category`.`deleted` IS NULL AND category_id= $id ");
        $result = $query->row_array();
        return $result;
    }

    public function insert_data()
    {

        $data = $this->set_data();
        $result = $this->db->insert('category',$data);
        return $result;
    }

    public function update_data($id)
    {
        //echo "model";die;
        $data = $this->set_data();
        $data['updated']= date("Y-m-d H:i:s");
        $this->db->where('category_id', $id);
        $result = $this->db->update('category',$data);
        return $result;

    }
    public function delete($id)
    {
        //$data = $this->set_data();
        $data['deleted']= date("Y-m-d H:i:s");
        $this->db->where('category_id', $id);
        $result = $this->db->update('category',$data);
        return $result;
    }

    public function category_list()
    {
        $this->db->select('category_id,cat_name');
        $this->db->where('deleted', null);
        $query = $this->db->get('category');        
        $result = $query->result_array();
        return $result;
    }

} 

?>