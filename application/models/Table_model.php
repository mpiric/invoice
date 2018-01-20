<?php 

class Table_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	public function validateTable()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('table_number','table_number','required');
		$this->form_validation->set_rules('max_capacity','max_capacity','required');
        //$this->form_validation->set_rules('max_capacity','max_capacity','required');
       
		
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
            

    		'table_number'=> $this->input->post('table_number'),

            'max_capacity'=> $this->input->post('max_capacity'),

            'notes'=> $this->input->post('notes'),

            
    		);
    	return $data;

    }

    public function insert_data_by_branch($data)
    {
        //$data = $this->set_data_by_branch();
        $result = $this->db->insert('table_detail',$data);
        return $result;
    }

    public function get_data()
    {
        $query = $this->db->get_where('table_detail', array('deleted' => null));
        return $query->result_array();
    }

    public function get_details_by_id($id)
    {
        $query = $this->db->get_where('table_detail', array('table_detail_id' => $id));
        $result = $query->row_array();
        return $result;
    }

    public function insert_data()
    {

        $data = $this->set_data();
        $result = $this->db->insert('table_detail',$data);
        return $result;
    }

    public function update_data($id)
    {
        //echo "model";die;
        $data = $this->set_data();
        $data['updated']= date("Y-m-d H:i:s");
        $this->db->where('table_detail_id', $id);
        $result = $this->db->update('table_detail',$data);
        return $result;
    }
    public function delete($id)
    {
       
        $data['deleted']= date("Y-m-d H:i:s");
        $this->db->where('table_detail_id', $id);
        $this->db->update('table_detail',$data);
       
    }

    public function delete_by_branch($id)
    {
        $this->db->where('branch_id',$id);
        $this->db->delete('table_detail');
    }

    public function get_data_by_branch()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];

        $this->db->select('td.*,odl.order_id,od.order_code,od.total_amount');
        $this->db->join('order_detail_live odl', 'td.`table_detail_id` = odl.`table_detail_id`','left'); 
        $this->db->join('order_detail od', 'od.`order_id` = odl.`order_id`','left'); 

        $this->db->where('td.branch_id',$branch_id);  
        $this->db->where('td.deleted',null);  
        $this->db->group_by('td.table_detail_id');  
        $query = $this->db->get('table_detail td');

         // $str = $this->db->last_query();
         // echo '<pre>';print_r($str);

       // $query = $this->db->get_where('table_detail', array('deleted' => null,'branch_id'=>$branch_id));
        return $query->result_array();
    }
    
    public function get_table_id_by_table_number($table_id, $branch_id){
	    $sql = "SELECT table_detail_id FROM table_detail WHERE branch_id = '".$branch_id."' AND table_number = '".$table_id."'";
	    
	    $query = $this->db->query( $sql );
		$result = $query->result_array();
        
		return $result[0]['table_detail_id'];
    }

    /***** API for application *****/

    public function get_tables_by_branch_id($branch_id)
    {
        $this->db->select('table_detail_id,table_number');
        $this->db->where('branch_id',$branch_id);
        $query = $this->db->get('table_detail');        
        
        $k=0;
        foreach ($query->result() as $row)
        {
                $temp['table_detail_id'] = $row->table_detail_id;
                $temp['table_number'] = $row->table_number;
                
                $sql = "SELECT od.order_id, od.waiter_id, w.waiter_code FROM order_detail od 
                        LEFT JOIN waiter w ON w.waiter_id = od.waiter_id
                        WHERE od.branch_id = '".$branch_id."' AND od.table_detail_id = '".$row->table_detail_id."' AND od.is_print = '0' ORDER BY od.order_id DESC LIMIT 0,1 ";
                       
	    
                $q = $this->db->query( $sql );
                $res = $q->row_array();
                $count = $q->num_rows();
                

                if($count==1){
                    $temp['is_live'] = '1';
                    $temp['waiter_id'] = $res['waiter_id'];
                    $temp['waiter_code'] = $res['waiter_code'];

                } else {
                    $temp['is_live'] = '0';
                    $temp['waiter_id'] = '0';
                    $temp['waiter_code'] = '0';
                }

                $result[$k] = $temp;
                $k++;
        }
        
        return $result;
    }

    

} 

?>