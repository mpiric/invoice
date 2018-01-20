<?php 

class Waiter_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	public function validateWaiter()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('firstname','firstname','required');
		$this->form_validation->set_rules('lastname','lastname','required');
        $this->form_validation->set_rules('contact','contact','required');
        $this->form_validation->set_rules('email','email','required');
        $this->form_validation->set_rules('password','password','required');
        $this->form_validation->set_rules('waiter_code','waiter_code','required');
        $this->form_validation->set_rules('branch_id','branch_id','required');
		$this->form_validation->set_rules('address','address','required');
		$this->form_validation->set_rules('city','city','required');
		$this->form_validation->set_rules('state','state','required');
		$this->form_validation->set_rules('pincode','pincode','required');
		

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

    		'firstname'=> $this->input->post('firstname'),

            'lastname'=> $this->input->post('lastname'),

            'contact'=> $this->input->post('contact'),

            'email'=> $this->input->post('email'),

            'password'=> $this->input->post('password'),

            'waiter_code'=> $this->input->post('waiter_code'),

            'address'=> $this->input->post('address'),

            'branch_id'=> $this->input->post('branch_id'),

            'location_id'=> $this->input->post('city'),

            'pincode'=> $this->input->post('pincode'),

    		);
    	return $data;

    }

    // public function get_data()
    // {
    //     $query = $this->db->get('waiter');
    //     return $query->result_array();
    // }

    // public function get_details_by_id($id)
    // {
    //     $query = $this->db->get_where('waiter', array('waiter_id' => $id));
    //     $result = $query->row_array();
    //     return $result;
    // }

    public function get_data()
    {
        // $query = $this->db->get('branch');
        // return $query->result_array();

        $this->db->select('w.*,city.`name` AS city,city.`location_id` AS city_id,state.`name` AS state,state.`location_id` AS state_id, country.`name` AS country, country.`location_id` AS country_id, b.name as branch_name');
        $this->db->join('location city', 'w.`location_id` = city.`location_id` AND city.`location_type`=2','left');
        $this->db->join('location state', 'city.`parent_id` = state.`location_id` AND state.`location_type`=1','left');
        $this->db->join('location country', 'state.`parent_id` = country.`location_id` AND country.`location_type`=0','left');   
        $this->db->join('branch b', 'b.`branch_id` = w.`branch_id`','left');   
        $this->db->where('w.deleted',null);  
        $query = $this->db->get('waiter w');
                   
       $result = $query->result_array();
       return $result;
    }

    public function get_details_by_id($id)
    {
        $this->db->select('w.*,city.`name` AS city,city.`location_id` AS city_id,state.`name` AS state,state.`location_id` AS state_id, country.`name` AS country, country.`location_id` AS country_id');
        $this->db->join('location city', 'w.`location_id` = city.`location_id` AND city.`location_type`=2','left');
        $this->db->join('location state', 'city.`parent_id` = state.`location_id` AND state.`location_type`=1','left');
        $this->db->join('location country', 'state.`parent_id` = country.`location_id` AND country.`location_type`=0','left');             
        $this->db->where('w.waiter_id',$id);

        $query = $this->db->get('waiter w');
        
        $result = $query->row_array();
        return $result;
    }

    public function insert_data()
    {

        $data = $this->set_data();
        $result = $this->db->insert('waiter',$data);

        return $result;
    }

    public function update_data($id)
    {
        //echo "model";die;
        $data = $this->set_data();
        $data['updated']= date("Y-m-d H:i:s");
        $this->db->where('waiter_id', $id);
        $result = $this->db->update('waiter',$data);
        return $result;

    }
    
    public function delete($id)
    {
        $data['deleted']= date("Y-m-d H:i:s");
        $this->db->where('waiter_id', $id);
        $result = $this->db->update('waiter',$data);

        return $result;
    }

    public function get_data_by_branch()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];

        $this->db->select('w.waiter_id,w.firstname,w.lastname,w.branch_id,w.waiter_code');
        $this->db->join('branch b', 'b.`branch_id` = w.`branch_id`','left');   
        $this->db->where('w.deleted',null);  
        $this->db->where('w.branch_id',$branch_id);  
        $query = $this->db->get('waiter w');
                   
       $result = $query->result_array();
       return $result;
    }

    public function branch_wise_waiter()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];

        $this->db->select('w.waiter_id,w.waiter_code,w.firstname,w.contact,w.waiter_code,w.location_id');
        $this->db->join('branch b', 'b.`branch_id` = w.`branch_id`','left');   
        $this->db->where('w.deleted',null);  
        $this->db->where('w.branch_id',$branch_id);  
        $query = $this->db->get('waiter w');
                   
       $result = $query->result_array();
       return $result;
    }

    public function waiter_list()
    {
        $this->db->select('waiter_id,waiter_code');
        $query = $this->db->get('waiter');        
        $result = $query->result_array();
        return $result;
    }
    public function waiter_list_by_branch($id)
    {
        $query= $this->db->query('SELECT waiter_id,waiter_code,branch_id from waiter
                                    where branch_id = "'.$id.'" AND deleted is null');

        $result = $query->result_array();

        return $result;
    }

    public function waiter_login(){
        $contact = $this->input->post('contact_number');
        $password = $this->input->post('password');

        $this->db->select('*');
        $this->db->where('contact',$contact);
        $this->db->where('password',$password);
        $this->db->where('deleted is null');

        
        $query = $this->db->get('waiter');        
        $result = $query->row_array();
        $count = $query->num_rows();
        //return $result;
        if($count == 0){
            return 0;
        } else {
            //update waite login status and return details
            $sql = "UPDATE waiter SET is_login = 1 WHERE contact = '".$contact."' AND password = '".$password."' ";
            $query = $this->db->query( $sql );

            return $result;
        }

    }

} 

?>