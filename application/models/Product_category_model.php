<?php 

class Product_category_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	public function validateproductCategory()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('name','name','required');
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
            

    		'name'=> $this->input->post('name'),

            'parent'=> $this->input->post('parent'),

            'brand_id' => $this->input->post('brand_id')

            
    		);
    	return $data;

    }

    public function get_data()
    {
         $query = $this->db->query("  SELECT 
                                            `p`.`product_category_id`,
                                            `p`.`name`,
                                            `p`.`created`,
                                            `p`.`updated`,
                                            `p`.`parent`,
                                            `pc`.`name` AS parent_name,
                                            `p`.`brand_id` ,
                                            b.brand_name 
                                          FROM
                                            product_category p 
                                            LEFT JOIN product_category pc 
                                              ON `pc`.`product_category_id` = `p`.`parent` 
                                            LEFT JOIN brand b 
                                              ON (p.brand_id = b.brand_id)
                                          WHERE `p`.`deleted` IS NULL  ");
        //$query = $this->db->get_where('product_category', array('deleted' => null));

        $result = $query->result_array();
        return $result;
    }

    public function get_details_by_id($id)
    {

        $query = $this->db->query("  SELECT 
                                          `p`.`product_category_id`,
                                          `p`.`name`,
                                          `p`.`created`,
                                          `p`.`updated`,
                                          `p`.`parent`,
                                          `pc`.`name` AS parent_name,
                                          `p`.`brand_id` 
                                        FROM
                                          product_category p 
                                          LEFT JOIN product_category pc 
                                            ON `pc`.`product_category_id` = `p`.`parent` 
                                        WHERE `p`.`deleted` IS NULL 
                                          AND `p`.`product_category_id` = $id ");
        $result = $query->row_array();
        return $result;

        //$query = $this->db->get_where('product_category', array('product_category_id' => $id));
        //$result = $query->row_array();
        //return $result;
    }

    public function insert_data()
    {

        $data = $this->set_data();
        $result = $this->db->insert('product_category',$data);

        $insert_id = $this->db->insert_id();
                
        return $insert_id;
       // return $result;
    }

    public function update_data($id)
    {
        //echo "model";die;
        $data = $this->set_data();
        $data['updated']= date("Y-m-d H:i:s");
        $this->db->where('product_category_id', $id);
        $result = $this->db->update('product_category',$data);
        return $result;

    }
    public function delete($id)
    {
        //$data = $this->set_data();
        $data['deleted']= date("Y-m-d H:i:s");
        $this->db->where('product_category_id', $id);
        $result = $this->db->update('product_category',$data);
        return $result;
    }

    public function product_category_list()
    {
        $this->db->select('product_category_id,name,brand_id');
        $query = $this->db->get('product_category');        
        $result = $query->result_array();
        return $result;
    }

    public function product_category_list_by_brand($brand_id)
    {
        $this->db->select('product_category_id,name,brand_id');

        if($brand_id!="")
        {
            $this->db->where('brand_id', $brand_id);
        }

        $query = $this->db->get('product_category');        
        $result = $query->result_array();
        return $result;
    }

    public function get_all_items()
    {
        $query = $this->db->query("select pc.product_category_id,p.product_code,p.name as product_name, items.quantity, p.price, (items.quantity*p.price) as total
        from order_detail o
        join order_items items on (o.order_id = items.order_id)
        join product p on (items.product_id = p.product_id)
        left join product_category pc on (p.product_category_id = pc.product_category_id) 
        where date(o.order_date_time) = date(curdate()) AND o.is_print=1");

        $result = $query->result_array();
        return $result;


    }

    public function get_daily_sold_items_by_product_category($product_category_id,$fromdate,$todate,$branch_id,$brand_id)
    {

        // $query = $this->db->query(" select pc.product_category_id,p.product_code,p.name as product_name, items.quantity, p.price, (items.quantity*p.price) as total
        // from order_detail o
        // join order_items items on (o.order_id = items.order_id)
        // join product p on (items.product_id = p.product_id)
        // left join product_category pc on (p.product_category_id = pc.product_category_id)
        // where pc.product_category_id='".$product_category_id."' ");
                
        // $result = $query->result_array();
        // return $result;



        /*
		$select = 'SELECT pc.product_category_id,p.product_code,p.name as product_name, sum(items.quantity) as quantity, p.price as price, (sum(items.quantity)*p.price) as total 
        FROM order_detail o
        JOIN order_items items ON (o.order_id = items.order_id)
        JOIN product p ON (items.product_id = p.product_id)
        LEFT JOIN product_category pc ON (p.product_category_id = pc.product_category_id)
		WHERE pc.product_category_id="'.$product_category_id.'" AND o.is_print=1 '; 
		

        if($fromdate!='' && $todate!='')
        {
            $select .= ' AND (o.order_date_time BETWEEN "'.$fromdate.'" AND "'.$todate.'") ';
        }
        else if($fromdate!="" && $todate=="")
        {
            $select .= ' AND (o.order_date_time >="'.$fromdate.'" ) ';
        }
        else if($todate!="" && $fromdate=="")
        {
            $select .= ' AND (o.order_date_time <="'.$todate.'" ) ';
        }
    

       if($branch_id != ''){
			$select .= ' AND o.branch_id="'.$branch_id.'" '; 
		}  

		if($brand_id != ''){
			$select .= ' AND pc.brand_id="'.$brand_id.'" '; 
		}              
     
		$select .='group by pc.product_category_id,p.product_code,p.name';
        $query = $this->db->query( $select );

        // $str = $this->db->last_query();
        // echo $str;die;
        $result = $query->result_array();
        return $result;*/
		
		$select = "SELECT pc.product_category_id, p.product_code, p.name as product_name, SUM(i.quantity) as quantity, i.price as price, SUM(i.quantity*i.price) as total
				FROM order_detail o
				LEFT JOIN order_items i ON i.order_id = o.order_id 
				LEFT JOIN product p ON p.product_id = i.product_id
				LEFT JOIN product_category pc ON pc.product_category_id = p.product_category_id
				WHERE pc.product_category_id = '".$product_category_id."' AND o.is_print = 1";
				
		if ($fromdate != '') {
               $select .= ' AND (o.order_date_time >="' . $fromdate . '" ) ';
		}
          if ($todate != "" ) {
               $select .= ' AND (o.order_date_time <="' . $todate . '" ) ';
          }
          
          
          if ($branch_id != '') {
               $select .= ' AND o.branch_id="' . $branch_id . '"';
          }
          
          if ($brand_id != '') {
               $select .= ' AND pc.brand_id="' . $brand_id . '" ';
          }
		
		$select .= 'group by pc.product_category_id,p.product_code,p.name order by p.name';
          
		
          
          $query = $this->db->query($select);
          
          // $str = $this->db->last_query();
          // echo $str;die;
          $result = $query->result_array();
          return $result;


    }

} 

?>