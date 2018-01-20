<?php 

class Branch_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	public function validateBranch($is_create)
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('name','name','required');
		$this->form_validation->set_rules('username','username','required');

        if($is_create==true)
        {
            $this->form_validation->set_rules('password','password','required');
        }
		
        $this->form_validation->set_rules('address','address','required');
		$this->form_validation->set_rules('city','location_id','required');
		$this->form_validation->set_rules('contact','contact','required');
		$this->form_validation->set_rules('email','email','required');
		$this->form_validation->set_rules('pincode','pincode','required');
        $this->form_validation->set_rules('no_of_tables','no_of_tables','required');
		$this->form_validation->set_rules('contact_person_name','contact_person_name','required');
		$this->form_validation->set_rules('contact_person_phone','contact_person_phone','required');

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

    		'name'=> $this->input->post('name'),

            'username'=> $this->input->post('username'),

            'password'=> md5($this->input->post('password')),

            'address' => $this->input->post('address'),

            'location_id'=> $this->input->post('city'),

            'contact'=> $this->input->post('contact'),

            'email'=> $this->input->post('email'),

            'pincode'=> $this->input->post('pincode'),

            'brand_id'=> $this->input->post('brand_id'),

            'service_tax_number'=> $this->input->post('service_tax_number'),

            'other_number'=> $this->input->post('other_number'),

            'no_of_tables'=> $this->input->post('no_of_tables'),

            'contact_person_name'=> $this->input->post('contact_person_name'),

            'contact_person_phone'=> $this->input->post('contact_person_phone'),

            'is_active'=> $this->input->post('is_active')

    		);
    	return $data;

    }
    public function get_data()
    {
        // $query = $this->db->get('branch');
        // return $query->result_array();

  //       $this->db->select('CASE
  //   WHEN b.is_active = 0 
  //   THEN Inactive 
  //   WHEN b.is_active= 1 
  //   THEN Active 
  // END AS `branch_status`,b.*,city.`name` AS city,state.`name` AS state, country.`name` AS country');
  //       $this->db->join('location city', 'b.`location_id` = city.`location_id` AND city.`location_type`=2','left');
  //       $this->db->join('location state', 'city.`parent_id` = state.`location_id` AND state.`location_type`=1','left');
  //       $this->db->join('location country', 'state.`parent_id` = country.`location_id` AND country.`location_type`=0','left');  
         
  //       $this->db->where('b.deleted',null);  
  //       $query = $this->db->get('branch b');

        $query=$this->db->query(" SELECT CASE
    WHEN `b`.`is_active` = 0 
    THEN 'Inactive' 
    WHEN `b`.is_active= 1 
    THEN 'Active' 
  END AS `branch_status`,b.*,city.`name` AS city,state.`name` AS state, country.`name` AS country
FROM branch b
LEFT JOIN location city ON b.`location_id` = city.`location_id` AND city.`location_type`=2
LEFT JOIN location state ON city.`parent_id` = state.`location_id` AND state.`location_type`=1
LEFT JOIN location country ON state.`parent_id` = country.`location_id` AND country.`location_type`=0 ");
                   
       $result = $query->result_array();
       return $result;
    }

    public function get_details_by_id($id)
    {
  //       $this->db->select('CASE
  //   WHEN b.is_active = 0 
  //   THEN Inactive 
  //   WHEN b.is_active= 1 
  //   THEN Active 
  // END AS `branch_status`,b.*,city.`name` AS city,city.`location_id` AS city_id,state.`name` AS state,state.`location_id` AS state_id, country.`name` AS country, country.`location_id` AS country_id');
  //       $this->db->join('location city', 'b.`location_id` = city.`location_id` AND city.`location_type`=2','left');
  //       $this->db->join('location state', 'city.`parent_id` = state.`location_id` AND state.`location_type`=1','left');
  //       $this->db->join('location country', 'state.`parent_id` = country.`location_id` AND country.`location_type`=0','left');             
  //       $this->db->where('b.branch_id',$id);

  //       $query = $this->db->get('branch b');

        $query = $this->db->query(" SELECT CASE
    WHEN `b`.`is_active` = 0 
    THEN 'Inactive' 
    WHEN `b`.is_active= 1 
    THEN 'Active' 
  END AS `branch_status`,b.*,city.`name` AS city,city.`location_id` AS city_id,state.`name` AS state,state.`location_id` AS state_id, country.`name` AS country, country.`location_id` AS country_id
FROM branch b
LEFT JOIN location city ON b.`location_id` = city.`location_id` AND city.`location_type`=2
LEFT JOIN location state ON city.`parent_id` = state.`location_id` AND state.`location_type`=1
LEFT JOIN location country ON state.`parent_id` = country.`location_id` AND country.`location_type`=0
WHERE `b`.`branch_id` = $id");
        
        $result = $query->row_array();
        return $result;
    }

    public function insert_data()
    {

        $data = $this->set_data();
        $this->db->insert('branch',$data);

        $result = $this->db->insert_id();
        return $result;
    }

    public function insert_into_branch_order_code($data)
    {
         $this->db->insert('branch_order_code',$data);

        $result = $this->db->insert_id();
        return $result;   
    }

    public function update_data($id)
    {
        $data = $this->set_data();
        unset($data['password']);
        $data['updated']= date("Y-m-d H:i:s");
        $this->db->where('branch_id', $id);
        $result = $this->db->update('branch',$data);
         

        $this->load->model('table_model');
        $this->table_model->delete_by_branch($id);

        $noOftables = $data['no_of_tables'];

        for($i=1;$i<=$noOftables;$i++)
                {

                    $tableData = array( 'table_number' => $i,
                                    'branch_id' => $id,
                                    'max_capacity' => 4
                                    );

                    $this->table_model->insert_data_by_branch($tableData);
                }


        return $result;

    }
    public function delete($id)
    {
        $data['deleted']= date("Y-m-d H:i:s");
        $this->db->where('branch_id', $id);
        $result = $this->db->update('branch',$data);

        return $result;
    }

    // Read data using username and password
       public function login($data)
       {


            $condition = "username =" . "'" . $data['username'] . "'";

            $this->db->select('password,is_active');
            $this->db->from('branch');
            $this->db->where($condition);
            $query=$this->db->get();

            $return=$query->row_array();

            if($return['is_active'] == 1)
            {
            //echo'<pre>'; print_r($return); 

                if( $query->num_rows() > 0) 
                {

                    // echo $return['password'];

                    // echo '<br>'.md5($data['password']);

                    if( $return['password'] == md5($data['password']))
                    {

                        $condition = "username =" . "'" . $data['username'] . "' AND " . "password =" . "'" . $return['password'] . "'";
                        $this->db->select('*');
                        $this->db->from('branch');
                        $this->db->where($condition);
                        $this->db->limit(1);
                        $query = $this->db->get();

                        if ($query->num_rows() == 1)
                        {
                            $result=$query->row_array();                            
                            return array('status' => '1','message' => $result);
                        } 
                        else 
                        {
                            return array('status' => '0','message' => 'Invalid user');
                        }
                    }
                    else
                    {
                        return array('status' => '0','message' => 'Invalid password');
                    }
                }
            }
            else
            {
                return array('status' => '0','message' => 'Unauthorized user');
            }
       }

    public function branch_list()
    {
        $this->db->select('branch_id,name,address');
        $query = $this->db->get('branch');        
        $result = $query->result_array();
        return $result;
    }

    public function present_branch_list()
    {
        $this->db->select('branch_id,name,address');
        $this->db->where('deleted',null);
        $query = $this->db->get('branch');        
        $result = $query->result_array();
        return $result;
    }

    public function getBranchIdList()
    {
        $this->db->select('branch_id');
        $this->db->where('deleted',null); 
        $query = $this->db->get('branch');        
        $result = $query->result_array();
        return $result;
    }

    public function getDetailsByBranchId($brand_id)
    {
        $this->db->select('*');
        $this->db->where('brand_id',$brand_id);
        $query = $this->db->get('branch');        
        $result = $query->row_array();
        return $result;
    }

    public function get_branch_details_by_login_branch()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];

        $sql = "SELECT b.*, l.name as city_name FROM branch b
                LEFT JOIN location l ON b.location_id = l.location_id
                WHERE branch_id = '".$branch_id."'";
        $query = $this->db->query($sql);        
        $result = $query->row_array();
        return $result;
    }

    public function get_branch_details_by_brand_id($brand_id)
    {
        $this->db->select('*');
        $this->db->where('brand_id',$brand_id);
        $query = $this->db->get('branch');        
        $result = $query->result_array();
        return $result;
    }

    public function get_associated_branches_by_brand_id($brand_id)
    {
        $query = $this->db->query(" SELECT * FROM branch WHERE FIND_IN_SET('".$brand_id."',brand_id) > 0 ");
        
        $result = $query->result_array();

        return $result;
    }

    /***** API for application *****/

    public function branch_details_by_id($branch_id)
    {
        $this->db->select('*');
        $this->db->where('branch_id',$branch_id);
        $query = $this->db->get('branch');        
        $result = $query->row_array();
        
        return $result;
    }


} 

?>