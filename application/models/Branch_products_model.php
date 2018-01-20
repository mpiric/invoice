<?php 

class Branch_products_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	public function validatebranchProducts()
	{
		$this->load->library('form_validation');

        $this->form_validation->set_rules('product_id','product_id','required');
        $this->form_validation->set_rules('branch_id','branch_id','required');
		$this->form_validation->set_rules('product_price','product_price','required');
       
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

            'product_id' => $this->input->post('product_id'),

            'branch_id' => $this->input->post('branch_id'),

    		'product_price'=> $this->input->post('product_price'),

            'is_available'=> $this->input->post('is_available'),

            'waiter_commission_branch'=> $this->input->post('waiter_commission_branch')

    		);
    	return $data;

    }
    public function get_data()
    {
        $query = $this->db->query("SELECT branch_products.*, product.price AS default_price, product.name AS product_name, product.product_code AS productcode, branch.name AS branch_name FROM branch_products JOIN product ON product.product_id = branch_products.product_id JOIN branch ON branch.branch_id = branch_products.branch_id WHERE branch_products.deleted IS NULL");
        //  $query = $this->db->get_where('branch_products', array('deleted' => null));
        $result = $query->result_array();
        return $result;
    }

    public function get_details_by_id($id)
    {
    $query = $this->db->query(" SELECT branch_products.*, product.price AS default_price, product.name AS product_name, product.product_code AS productcode, branch.name AS branch_name FROM branch_products JOIN product ON product.product_id = branch_products.product_id JOIN branch ON branch.branch_id = branch_products.branch_id WHERE branch_products.deleted IS NULL AND branch_products.branch_products_id = $id ");     
        //$query = $this->db->get_where('branch_products', array('branch_products_id' => $id));
        $result = $query->row_array();
        return $result;
    }

    public function insert_data()
    {

        $data = $this->set_data();
        $result = $this->db->insert('branch_products',$data);
        
        return $result;
    }

    public function update_data($id)
    {
        //echo "model";die;
        $data = $this->set_data();
        $data['updated']= date("Y-m-d H:i:s");
        $this->db->where('branch_products_id', $id);
        $result = $this->db->update('branch_products',$data);
        return $result;

    }
    public function delete($id)
    {
        $data['deleted']= date("Y-m-d H:i:s");
        $this->db->where('branch_products_id', $id);
        $result = $this->db->update('branch_products',$data);
        return $result;
    }

    public function get_product_data()
    {
        $query = $this->db->query(" SELECT product_id, product.name AS product_name,product.price AS default_price,product.product_code AS productcode FROM product ");    
        //$query = $this->db->get_where('branch_products', array('branch_products_id' => $id));
        $result = $query->result_array();
        return $result; 
    }

    public function insert_branch_products($data)
    {
        $data['created'] = date("Y-m-d H:i:s");

        $data = array(

            'product_id' => $data['product_id'],

            'branch_id' => $data['branch_id'],

            'product_price'=> $data['product_price'],

            'is_available'=> $data['is_available'],

            'waiter_commission_branch'=> isset($data['waiter_commission_branch']) ? $data['waiter_commission_branch'] : null,

            'created'=> $data['created']

            );
        
            $var = $this->get_data_by_branch_and_product($data['branch_id'],$data['product_id']);
            //print_r($var['branch_products_id']);
            //print_r( $var);
            //die;

            if( !empty($var) )
            {

                $var['updated'] = date('Y-m-d H:i:s');
                $var['is_available']= $data['is_available'];
                $var['product_price']= $data['product_price'];
                $var['waiter_commission_branch'] = $data['waiter_commission_branch'];
                
                $this->db->where('branch_products_id',$var['branch_products_id']);
                $result = $this->db->update('branch_products',$var);
                return $result;

            }
            else
            {

                $result = $this->db->insert('branch_products',$data);

                $insert_id = $this->db->insert_id();
                
                return $insert_id;
            }

           // echo "<pre>"; print_r($var);die;
        
        // find if the branch_id has the product_id

    }

    public function get_data_by_branch_and_product($branch_id,$product_id)
    {
        $query = $this->db->query(" SELECT * FROM branch_products WHERE branch_id=$branch_id AND product_id=$product_id ");    
        
        $result = $query->row_array();
        return $result; 
    }

    public function get_branch_products($branch_id)
    {
        // $query = $this->db->query(" SELECT p.product_id, p.name AS product_name,p.price AS default_price,p.product_code AS productcode,bp.* FROM product p
        //                             LEFT JOIN branch_products bp ON (p.product_id = bp.product_id)
        //                             WHERE branch_id=$branch_id
        //                             GROUP BY p.product_id ");    

        $query = $this->db->query(" SELECT p.product_id, p.name AS product_name,p.price AS default_price,p.product_code AS productcode,bp.*,`pc`.`name` AS catName FROM product p LEFT JOIN branch_products bp ON (p.product_id = bp.product_id) LEFT JOIN branch b ON b.branch_id = bp.branch_id
        LEFT JOIN product_category pc ON pc.product_category_id = p.product_category_id WHERE bp.branch_id=$branch_id AND b.brand_id=(SELECT brand_id FROM branch b WHERE branch_id=$branch_id) GROUP BY p.product_id  ");

        $result = $query->result_array();

        //$query = $this->db->get_where('branch_products', array('branch_products_id' => $id));
        // $rows = $query->num_rows();
        // echo 'ROWS:'.$rows;die;
        //$result = $query->result_array();
        // $str = $this->db->last_query();
        // echo $str;

        // $totalRows = $query->num_rows();

        //  if( $totalRows > 0 )
        //  {
        //      $result = $query->result_array();
        //  }
        //  else
        //  {
        //     $query = $this->db->query(" SELECT p.product_id, p.name AS product_name,p.price AS default_price,p.product_code AS productcode,bp.*,NULL AS is_available FROM product p
        //                             LEFT JOIN branch_products bp ON (p.product_id = bp.product_id)
        //                             GROUP BY p.product_id "); 
        //     $result = $query->result_array();   
        // }

        return $result; 
    }

    public function get_products_by_branch($branch_id)
    {
        
        $sql = "SELECT bp.product_id, bp.product_price, p.name AS 'product_name', p.product_code, pc.name AS 'product_category_name', pc.product_category_id
                FROM branch_products bp
                LEFT JOIN product p ON p.product_id = bp.product_id
                LEFT JOIN product_category pc ON pc.product_category_id = p.product_category_id
                WHERE bp.branch_id = '".$branch_id."' AND bp.is_available = 'Y' ";

        /*$query = $this->db->query(" SELECT p.product_id, p.name AS product_name,p.price AS default_price,p.product_code AS productcode,bp.*,`pc`.`name` AS catName 
        FROM product p 
        LEFT JOIN branch_products bp ON (p.product_id = bp.product_id) 
        LEFT JOIN branch b ON b.branch_id = bp.branch_id
        LEFT JOIN product_category pc ON pc.product_category_id = p.product_category_id 
        WHERE bp.branch_id=$branch_id AND b.brand_id=(SELECT brand_id FROM branch b WHERE branch_id=$branch_id) GROUP BY p.product_id  ");*/

        $query = $this->db->query($sql);

        $result = $query->result_array();

        

        return $result; 
    }

} 

?>