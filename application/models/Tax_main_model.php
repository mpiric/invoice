<?php 

class Tax_main_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	public function validatetaxmain()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('tax_name','tax_name','required');
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
            

    		'tax_name'=> $this->input->post('tax_name'),

            'tax_type' => $this->input->post('tax_type')

            
    		);
    	return $data;

    }

    // public function get_data()
    // {
    //      $query = $this->db->query("SELECT * FROM tax_main WHERE deleted IS NULL");
    //     //$query = $this->db->get_where('tax_main', array('deleted' => null));

    //     $result = $query->result_array();

    //     return $result;
    // }

    // public function get_details_by_id($id)
    // {

    //     $query = $this->db->query("SELECT * FROM tax_main WHERE deleted IS NULL AND tax_id= $id ");
    //     $result = $query->row_array();
    //     return $result;
    // }

    public function get_data()
    {
         $query = $this->db->query("SELECT 
  CASE
    WHEN tax_type = 1 
    THEN 'Product Specific' 
    WHEN tax_type = 2 
    THEN 'Branch Specific' 
  END AS `taxtype`,
  tax_main.* 
FROM
  `tax_main` 
WHERE `tax_main`.`deleted` IS NULL ");
        //$query = $this->db->get_where('tax_main', array('deleted' => null));

        $result = $query->result_array();

        return $result;
    }

    public function get_details_by_id($id)
    {

        $query = $this->db->query("SELECT 
  CASE
    WHEN tax_type = 1 
    THEN 'Product Specific' 
    WHEN tax_type = 2 
    THEN 'Branch Specific' 
  END AS `taxtype`,
  tax_main.* 
FROM
  `tax_main` 
WHERE `tax_main`.`deleted` IS NULL AND tax_id= $id ");
        $result = $query->row_array();
        return $result;
    }

    public function insert_data()
    {

        $data = $this->set_data();
        $result = $this->db->insert('tax_main',$data);
        return $result;
    }

    public function update_data($id)
    {
        //echo "model";die;
        $data = $this->set_data();
        $data['updated']= date("Y-m-d H:i:s");
        $this->db->where('tax_id', $id);
        $result = $this->db->update('tax_main',$data);
        return $result;

    }
    public function delete($id)
    {
        //$data = $this->set_data();
        $data['deleted']= date("Y-m-d H:i:s");
        $this->db->where('tax_id', $id);
        $result = $this->db->update('tax_main',$data);
        return $result;
    }

    public function tax_main_list()
    {
        $this->db->select('tax_id,tax_name,tax_type');
        $this->db->where('tax_type = 1');
        $this->db->where('deleted', null);
        $query = $this->db->get('tax_main');        
        $result = $query->result_array();
        return $result;
    }

    public function tax_main_list_by_branch()
    {
        $this->db->select('tax_id,tax_name,tax_type');
        $this->db->where('tax_type = 2');
        $this->db->where('deleted', null);
        $query = $this->db->get('tax_main');        
        $result = $query->result_array();
        return $result;
    }
    public function tax_list_all()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];

        $query = $this->db->query(" SELECT case when ( tmas.tax_percent is null ) then tm.tax_percent else      tmas.tax_percent end as tax_percent,
                    t.tax_name,t.tax_id FROM tax_main t
                    LEFT JOIN  tax_master tm on tm.tax_id = t.tax_id
                    LEFT JOIN tax_master tmas on tmas.branch_tax_id = t.tax_id
                    WHERE t.deleted IS NULL GROUP BY t.tax_id  ");
        
        $result = $query->result_array();
        return $result;
    }

} 

?>