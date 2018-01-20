<?php 

class Brand_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	public function validatebrand()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('brand_name','brand_name','required');
		
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
            

    		'brand_name'=> $this->input->post('brand_name'),

            
    		);
    	return $data;

    }

    public function get_data()
    {
        $query = $this->db->query("SELECT * FROM brand WHERE deleted IS NULL");

        $result = $query->result_array();

        return $result;
    }

    public function get_details_by_id($id)
    {

        $query = $this->db->query("SELECT * FROM brand WHERE deleted IS NULL AND brand_id= $id ");
        $result = $query->row_array();
        return $result;
    }

    public function insert_data()
    {

        $data = $this->set_data();
        $result = $this->db->insert('brand',$data);
        return $result;
    }

    public function update_data($id)
    {
        //echo "model";die;
        $data = $this->set_data();
        $data['updated']= date("Y-m-d H:i:s");
        $this->db->where('brand_id', $id);
        $result = $this->db->update('brand',$data);
        return $result;

    }
    public function delete($id)
    {
        //$data = $this->set_data();
        $data['deleted']= date("Y-m-d H:i:s");
        $this->db->where('brand_id', $id);
        $result = $this->db->update('brand',$data);
        return $result;
    }

    public function brand_list()
    {
        $this->db->select('brand_id,brand_name');
       
        $query = $this->db->get('brand');        
        $result = $query->result_array();
        return $result;
    }
    public function brand_list_by_branch($id)
    {
        $query = $this->db->query("SELECT br.branch_id,b.brand_id,b.brand_name from brand b
                                    left join branch br on br.brand_id = b.brand_id
                                    where br.branch_id= $id and b.deleted is null");
        $result = $query->result_array();
        return $result;

    }

    public function brand_list_by_branch_new($id)
    {
        // get details by branch

        $query = $this->db->query(" SELECT brand_id FROM branch WHERE branch_id=$id ");
        $branch_result = $query->row_array();

        //get brand_id_csv
        $brand_csv = $branch_result['brand_id'];
        $brand_arr = explode(',',$brand_csv);

        $brand_list = array();
        $i=0;

        foreach ($brand_arr as $brand_id) {

            if($brand_id!="")
            {
                //get brand details
                $qry = $this->db->query(" SELECT brand_id,brand_name FROM brand WHERE brand_id=$brand_id ");
                $brand_result = $qry->row_array();

              $brand_list[$i] = $brand_result;

            }

        $i++;    
            
        }


        return $brand_list;

    }

    public function brand_list_by_branch_new_item_wise_sales()
    {
       
        $session_data = $this->session->userdata('logged_in');

        $brand_list = array();

        if($session_data['branch_type']!=1)
        {
            $branch_id = $session_data['branch_id'];
            $branch_cond = ' AND o.branch_id="'.$branch_id.'" ';
                    
       
            // get details by branch

            $query = $this->db->query(" SELECT brand_id FROM branch WHERE branch_id=$branch_id ");
            $branch_result = $query->row_array();

            //get brand_id_csv
            $brand_csv = $branch_result['brand_id'];
            $brand_arr = explode(',',$brand_csv);

            
            $i=0;

            foreach ($brand_arr as $brand_id) 
            {

                if($brand_id!="")
                {
                    //get brand details
                    $qry = $this->db->query(" SELECT brand_id,brand_name FROM brand WHERE brand_id=$brand_id ");
                    $brand_result = $qry->row_array();

                  $brand_list[$i] = $brand_result;

                }

                $i++;
            }
        }
        else
        {
            $qry = $this->db->query(" SELECT brand_id,brand_name FROM brand WHERE deleted IS NULL ");
            $brand_list = $qry->result_array();
        }


        return $brand_list;

    }

    // public function tax_main_list_by_branch()
    // {
    //     $this->db->select('tax_id,tax_name,tax_type');
    //     $this->db->where('tax_type = 2');
    //     $query = $this->db->get('brand');        
    //     $result = $query->result_array();
    //     return $result;
    // }

    public function get_data_by_branch()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];

        $query = $this->db->query("SELECT * FROM brand WHERE deleted IS NULL ");

        $result = $query->result_array();

        return $result;
    }


} 

?>