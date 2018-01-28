<?php 

class Payment_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	public function validatepayment()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('payment_type','payment_type','required');
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
            'payment_type'=> $this->input->post('payment_type')
        );
    	return $data;

    }

    

    public function get_data()
    {
         $query = $this->db->query("SELECT * FROM `payment_type` WHERE `deleted` IS NULL ");
        //$query = $this->db->get_where('payment_type', array('deleted' => null));

        $result = $query->result_array();

        return $result;
    }

    public function get_details_by_id($id)
    {

        $query = $this->db->query("SELECT * FROM `payment_type` WHERE `deleted` IS NULL AND payment_id= $id ");
        $result = $query->row_array();
        return $result;
    }

    public function insert_data()
    {

        $data = $this->set_data();
        $result = $this->db->insert('payment_type',$data);
        return $result;
    }

    public function update_data($id)
    {
        //echo "model";die;
        $data = $this->set_data();
        $data['updated']= date("Y-m-d H:i:s");
        $this->db->where('payment_id', $id);
        $result = $this->db->update('payment_type',$data);
        return $result;

    }
    public function delete($id)
    {
        //$data = $this->set_data();
        $data['deleted']= date("Y-m-d H:i:s");
        $this->db->where('payment_id', $id);
        $result = $this->db->update('payment_type',$data);
        return $result;
    }

    public function payment_type_list()
    {
        $this->db->select('payment_id,payment_type');
        
        $this->db->where('deleted', null);
        $query = $this->db->get('payment_type');        
        $result = $query->result_array();
        return $result;
    }

    public function payment_type_list_by_branch()
    {
        $this->db->select('payment_id,payment_type');
        
        $this->db->where('deleted', null);
        $query = $this->db->get('payment_type');        
        $result = $query->result_array();
        return $result;
    }

    public function getPaymentTypeForOrder(){

        $this->db->select('*');
        $this->db->where('deleted', null);
        $query = $this->db->get('payment_type');        
        $result = $query->result_array();
        
        $data = $temp = array();
        $i=0;

        foreach ($result as $row) {
            $temp['key'] = $row['payment_id'];
            $temp['value'] = $row['payment_type'];

            $data[$i] = $temp;
            $i++;
        }

        return $data;
    }


    

} 

?>