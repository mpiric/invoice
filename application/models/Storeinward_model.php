<?php 

class Storeinward_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	public function validatestoreinward()
	{
		$this->load->library('form_validation');

        $this->form_validation->set_rules('store_product_id','store_product_id','required');
        //$this->form_validation->set_rules('purchase_qty','purchase_qty','required');
		
       
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

            'store_product_id' => $this->input->post('store_product_id'),

            'purchase_qty' => $this->input->post('purchase_qty'),

    		);
    	return $data;

    }


    public function find_inward_info_by_product_and_date($store_product_id,$date)
    {
        $query = $this->db->query("SELECT * FROM store_inward WHERE  store_product_inward_id='".$store_product_id."' AND DATE(created) = '".$date."'");

         $result = $query->row_array();

         return $result;

    }
    public function get_data()
    {
        // get logged in branch_id
        $session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];

        $query = $this->db->query(" SELECT CASE WHEN sp.unit=0 THEN 'none' WHEN sp.unit=1 THEN 'kg' WHEN sp.unit=2 THEN 'gm' WHEN sp.unit=3 THEN 'l' WHEN sp.unit=4 THEN 'ml' END AS unit,sp.store_product_id,sp.name,sp.product_code,sp.price,sp.created,sp.updated,NULL AS store_inward_id,NULL AS purchase_qty ,sins.instock 
                FROM `store_product` sp  
                LEFT JOIN `store_instock` sins ON (sins.store_product_id = sp.store_product_id)
            WHERE sp.`branch_id`='".$branch_id."' "); //AND DATE(si.created) = '".date("Y-m-d")."' 
        
        $result = $query->result_array();

        $response = array();

        if(!empty($result))
        {
            $i=0;

            foreach ($result as $store_product) {

                $store_product_details = $store_product;

               // find inward info by store product id and date
                $store_product_id = $store_product['store_product_id'];

                $date = date("Y-m-d");

                $inward_data = $this->find_inward_info_by_product_and_date($store_product_id,$date);

                if(!empty($inward_data))
                {
                    $store_product_details['store_product_inward_id'] = $inward_data['store_product_inward_id'];
                    $store_product_details['purchase_qty'] = $inward_data['purchase_qty'];
                }

                $response[$i] = $store_product_details;

                $i++;
            }
        }
       return $response;

    }

    public function get_details_by_id($id)
    {

        $session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];

        $query = $this->db->query("  SELECT sp.store_product_id,sp.name,sp.product_code,sp.price,sp.created,sp.updated, si.store_inward_id,SUM(si.purchase_qty) as purchase_qty,sins.instock, b.branch_id FROM store_product sp LEFT JOIN store_inward si ON si.store_product_id = sp.store_product_id LEFT JOIN store_instock sins ON sins.store_product_id = si.store_product_id LEFT JOIN branch b ON b.branch_id = sp.branch_id WHERE b.branch_id=$branch_id ");     
        
        $result = $query->row_array();
        return $result;
    }

    public function insert_data()
    {

        $data = $this->set_data();
        $result = $this->db->insert('store_inward',$data);
        
        return $result;
    }

    public function get_product_data()
    {
        $query = $this->db->query(" SELECT product_id, product.name AS product_name,product.price AS default_price,product.product_code AS productcode FROM product ");    
        //$query = $this->db->get_where('branch_products', array('branch_products_id' => $id));
        $result = $query->result_array();
        return $result; 
    }

    public function do_store_inward($data)
    {
        $purchase_qty = '';
        //print_r($data);die;
        if(isset($data['purchase_qty']) && $data['purchase_qty'] !='') 
            {
                $purchase_qty = $data['purchase_qty'];
            }

        $data = array(

            'store_product_inward_id' => $data['store_product_inward_id'],

            'purchase_qty' =>  $purchase_qty,

            'created'=> $data['created']

            );
            
            $var = $this->get_data_by_branch_and_product($data['store_product_inward_id'],$data['created']);
            //print_r($var['branch_products_id']);print_r( $var);die;

            if( !empty($var) )
            {

                $var1['updated'] = date('Y-m-d H:i:s');
                //$var['store_product_inward_id']= $data['store_product_inward_id'];
                $var1['purchase_qty']= $data['purchase_qty'];
                $var1['today_purchase_qty']= $var['today_purchase_qty']+$data['purchase_qty'];
                $this->db->where('store_product_inward_id',$var['store_product_inward_id']);
                $this->db->where('created',$var['created']);
                $result = $this->db->update('store_inward',$var1);
                $updated_status = $this->db->affected_rows();

                if($updated_status){
                    return $var['store_product_inward_id'];
                }

                // $str = $this->db->last_query();
                // //echo $str;die;
             
            }

            else
            {
                $data['today_purchase_qty']= $data['purchase_qty'];
                $result = $this->db->insert('store_inward',$data);

                $insert_id = $this->db->insert_id();

                $str = $this->db->last_query();
                //echo $str;die;
                
                return $insert_id;
            }

        // find if the branch_id has the product_id

    }

    public function get_data_by_branch_and_product($store_product_id,$dateParam)
    {
        $date = date('Y-m-d',strtotime($dateParam));
        // $query = $this->db->query(" SELECT si.* FROM store_inward si
        //                             LEFT JOIN store_product sp ON sp.store_product_id = si.store_product_id
        //                             WHERE si.store_product_id=$store_product_id AND DATE(si.created) = '".$date."'  ");    
        $query = $this->db->query(" SELECT si.* FROM store_inward si LEFT JOIN store_product_inward sp ON sp.store_product_inward_id = si.store_product_inward_id WHERE si.store_product_inward_id= '".$store_product_id."' AND DATE(si.created) = '".$date."' ");  
        
        $result = $query->row_array();
        return $result;
    }

    public function check_stock_by_product_and_date($store_product_id,$stock_dateParam)
    {
        $stock_date = date("Y-m-d",strtotime($stock_dateParam));
        $query = $this->db->query(" SELECT * FROM store_instock WHERE store_product_inward_id='".$store_product_id."' AND DATE(stock_date) = '".$stock_date."'  ");   
        
        $result = $query->row_array();
        return $result;
    }

    public function get_second_largest_date_from_store($store_product_inward_id)
    {
        //$query = $this->db->query(" SELECT MAX( DATE(stock_date) ) AS stock_date,instock FROM store_instock WHERE DATE(stock_date) < ( SELECT MAX( DATE(stock_date) ) FROM store_instock ) AND `store_product_inward_id` = '".$store_product_inward_id."' ");

        $query = $this->db->query(" SELECT MAX( DATE(stock_date) ) AS stock_date,instock FROM store_instock WHERE `store_product_inward_id` = '".$store_product_inward_id."' ");

        $query_result = $query->row_array();

        return $query_result;
    }

    public function insert_into_store_instock($data)
    {
        // check for store_product_id by date, if stock is available for that product

        $stock_details = $this->check_stock_by_product_and_date($data['store_product_inward_id'],$data['stock_date']);

        $instock_data = $this->get_second_largest_date_from_store($data['store_product_inward_id']);


        if(!empty($stock_details))
        {
            //update
            $data['instock'] = $stock_details['instock']+$data['instock'];
            $this->db->where('store_product_inward_id',$stock_details['store_product_inward_id']);
            $this->db->where('stock_date',$stock_details['stock_date']);
            $result = $this->db->update('store_instock',$data);
            $insert_id = $this->db->insert_id();            
            return $insert_id;            
        }
        else
        {
            // insert
            $data['instock'] = $data['instock']+$instock_data['instock'];
            $result = $this->db->insert('store_instock',$data);
            $insert_id = $this->db->insert_id();            
            return $insert_id;
        }
    }

    public function get_inwarded_store_product_ids()
    {
        $query = $this->db->query(" SELECT DISTINCT(store_product_id) FROM store_inward ");   
        
        $result = $query->result_array();

        // $str = $this->db->last_query();
        // echo $str; die;
        return $result;
    }

    public function get_inwarded_store_product_ids_by_date($date)
    {
        $date = date("Y-m-d",strtotime($date));

        $query = $this->db->query(" SELECT DISTINCT(store_product_inward_id) FROM store_inward where date(created) = '".$date."' ");   
        
        $result = $query->result_array();

        // $str = $this->db->last_query();
        // echo $str; die;
        return $result;
    }

    public function delete_from_inward_by_product_and_date($store_product_id,$date)
    {
        $created = date('Y-m-d',strtotime($date));
       
        $this->db->where('store_product_id', $store_product_id);
        $this->db->where('DATE(created)', $created);
        $this->db->delete('store_inward');
    }

    public function delete_from_instock_by_product_and_date($store_product_id,$date)
    {
        $created = date('Y-m-d',strtotime($date));
       
        $this->db->where('store_product_id', $store_product_id);
        $this->db->where('DATE(stock_date)', $created);
        $this->db->delete('store_instock');
    }

    public function get_store_product_list_by_date($date)
    {
        $today = date('Y-m-d');

        // get logged in branch_id

        $session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];

        // replaced sins.instock with subqry as instock

        // /$sql = "SELECT CASE WHEN sp.unit=0 THEN 'none' WHEN sp.unit=1 THEN 'kg' WHEN sp.unit=2 THEN 'gm' WHEN sp.unit=3 THEN 'l' WHEN sp.unit=4 THEN 'ml' END AS unit,sp.store_product_id,sp.name,sp.product_code,sp.price,sp.created,sp.updated,NULL AS store_inward_id,NULL AS purchase_qty ,   (SELECT sub_sin.today_purchase_qty FROM store_inward sub_sin WHERE sub_sin.store_product_id = sp.store_product_id AND DATE(sub_sin.created) = '".$date."') AS today_qty, (SELECT sub_si.instock FROM store_instock sub_si WHERE sub_si.store_product_id=sp.store_product_id AND DATE(sub_si.stock_date)='".$date."' ) AS instock FROM `store_product` sp LEFT JOIN `store_instock` sins ON (sins.store_product_id = sp.store_product_id) WHERE sp.`branch_id`='".$branch_id."' group by sp.store_product_id ";


        $sql = " SELECT CASE WHEN sp.unit = 0 THEN 'none' WHEN sp.unit = 1 THEN 'kg' WHEN sp.unit = 2 THEN 'gm' WHEN sp.unit = 3 THEN 'lt' WHEN sp.unit = 4 THEN 'ml' END AS unit, sp.name,NULL AS store_inward_id,NULL AS purchase_qty , (SELECT sub_sin.today_purchase_qty FROM store_inward sub_sin WHERE sub_sin.store_product_inward_id = spi.`store_product_inward_id` AND DATE(sub_sin.created) = '".$date."') AS today_qty, (SELECT sub_si.instock FROM store_instock sub_si WHERE sub_si.store_product_inward_id = spi.`store_product_inward_id` AND DATE(sub_si.stock_date) = '".$date."' ) AS instock, spi.* FROM `store_product_inward` spi 
            LEFT JOIN `store_product` sp ON sp.`store_product_id` = spi.`store_product_id` 
            LEFT JOIN `store_instock` sins ON sins.`store_product_inward_id` = spi.`store_product_inward_id`
            LEFT JOIN store_inward si ON si.`store_product_inward_id` = spi.`store_product_inward_id`
            WHERE spi.`branch_id` = '".$branch_id."' 
            GROUP BY spi.store_product_inward_id ORDER BY si.updated ";

            //echo $sql;die; 

        $query = $this->db->query($sql); 
        
        $result = $query->result_array();


        $response = array();

        if(!empty($result))
        {
            $i=0;

            foreach ($result as $store_product) {

                $store_product_details = $store_product;

               // find inward info by store product id and date
                $store_product_inward_id = $store_product['store_product_inward_id'];
                if($store_product['instock'] == '')
                {
                    if(strtotime($today) > strtotime($date))
                    {
                        
                        $previousInstock_query = $this->db->query (" SELECT instock FROM `store_instock` WHERE `store_product_inward_id` = '".$store_product_inward_id."' AND DATE(stock_date) = '".$date."' ");

                        $previousInstock = $previousInstock_query->row_array();

                        if($previousInstock['instock'] == '')
                        {

                            $previous_data1 = $this->db->query(" SELECT DATE(stock_date) AS stock_date FROM store_instock WHERE `store_product_inward_id` = '".$store_product_inward_id."' AND DATE(stock_date) < '".$date."' ORDER BY DATE(stock_date) DESC ");

                            $previous_data12 = $previous_data1->row_array();

                            $previous_date1 = $previous_data12['stock_date'];


                             $prevInstock1_query = $this->db->query (" SELECT instock FROM `store_instock` WHERE `store_product_inward_id` = '".$store_product_inward_id."' AND DATE(stock_date) = '".$previous_date1."' ");

                             $prevInstock1 = $prevInstock1_query->row_array();

                             $store_product_details['instock'] = $prevInstock1['instock'];
                        }
                        
                    }
                    else
                    {
                        //echo 'else';die;
                        $previous_data = $this->get_second_largest_date_from_store($store_product_inward_id);
                        $previous_date = $previous_data['stock_date'];


                        $prevInstock_query = $this->db->query (" SELECT instock FROM `store_instock` WHERE `store_product_inward_id` = '".$store_product_inward_id."' AND DATE(stock_date) = '".$previous_date."' ");

                        $prevInstock = $prevInstock_query->row_array();

                        $store_product_details['instock'] = $prevInstock['instock'];
                    }
                    
                }

                

                $inward_data = $this->find_inward_info_by_product_and_date($store_product_inward_id,$date);

                if(!empty($inward_data))
                {
                    $store_product_details['store_product_inward_id'] = $inward_data['store_product_inward_id'];
                    $store_product_details['purchase_qty'] = $inward_data['purchase_qty'];

                }

                $response[$i] = $store_product_details;

                $i++;
            }
        }

        // $str = $this->db->last_query();
        // echo $str;die;
       return $response;   

    }

    public function get_max_inward_date()
    {
        // inward_date is created date
        $query = $this->db->query(" SELECT MAX(created) as max_inward_date FROM store_inward ");
        $result = $query->row_array();

        if(!empty($result))
        {
            return $result['max_inward_date'];
        }
        else
        {
            return false;
        }        
    }


    public function check_for_editable_field($filterdate)
    {

        $session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];
        // get max created date

        $max_inward_date = $this->get_max_date();
        $date = $max_inward_date['created'];
        //echo  $max_inward_date['created'];die;
        $cur_date = date("Y-m-d");

        if($filterdate==$cur_date)
        {
            $query = $this->db->query(" SELECT * FROM store_inward si LEFT JOIN `store_product_inward` spi ON spi.`store_product_inward_id` = si.`store_product_inward_id` WHERE DATE(si.created) = '".$filterdate."' AND branch_id = '".$branch_id."' " );
            $result = $query->num_rows();

            if($result>0)
            {
                return $result;
            }
            else
            {
                return 1;
            }

        }
        elseif($filterdate>$cur_date)
        {
            return 0;
        }
        else
        {
            //echo 'else';die;
            $query = $this->db->query("SELECT * FROM store_inward si LEFT JOIN `store_product_inward` spi ON spi.`store_product_inward_id` = si.`store_product_inward_id` WHERE DATE(si.created) BETWEEN '".$date."' AND '".$filterdate."' AND branch_id = '".$branch_id."' ");

            // $str = $this->db->last_query();
            // echo $str; die;

            $result = $query->num_rows();

            return $result;
        }

    
    }
    function get_store_instock($store_product_id)
    {
        $query = $this->db->query("SELECT instock FROM store_instock WHERE store_product_id = '".$store_product_id."' ");

        $result = $query->row_array();

        $instock = 0;

        if(!empty($result))
        {
            if(isset($result['instock']))
            {
                $instock = $result['instock'];
            }
        }

        return $instock;
    }

    public function update_store_instock_kitchen($data,$filterdate)
    {
        // print_r($data);
        // echo $filterdate;

        $store_product_id = $data['store_product_inward_id'];
        $instockstr = isset($data['inward_qty']) ? $data['inward_qty'] : 0 ;
        $instock = floatval($instockstr);
        //echo "Kchnin".$instock."<br>";

        $old_instockArr = $this->check_stock_by_product_and_date($store_product_id,$filterdate);

        if(!empty($old_instockArr))
        {
            $old_instockstr = $old_instockArr['instock'];
            $old_instock = floatval($old_instockstr);

            //echo "Stoin".$old_instock."<br>";

            $remain = $old_instock-$instock;
            
            $data = array( 'instock' => $remain );

            $this->db->where('date(stock_date)', $filterdate);
            $this->db->where('store_product_inward_id',$store_product_id);
            $result = $this->db->update('store_instock',$data);
        }
        else
        {
            $previous_instock = $this->db->query(" SELECT DATE(stock_date) AS stock_date FROM store_instock WHERE `store_product_inward_id` = '".$store_product_id."' ORDER BY DATE(stock_date) DESC LIMIT 1 ");

            $previous_instock_res = $previous_instock->row_array();

            $previous_instock_date = $previous_instock_res['stock_date'];

            $prevIns = $this->db->query(" SELECT * FROM store_instock WHERE store_product_inward_id='".$store_product_id."' AND DATE(stock_date) = '".$previous_instock_date."'  ");   
        
            $prevInsresult = $prevIns->row_array();
            $instockPrev = $prevInsresult['instock'];

            $remain = $instockPrev-$instock;

            $instockArr = array('store_product_inward_id' => $store_product_id,
                                'instock' => $remain,
                                'stock_date' => $filterdate);

            $result = $this->db->insert('store_instock',$instockArr);
         
        }
       
        // $str = $this->db->last_query();
        // echo $str;

        return $result;

    }

    public function update_store_instock($data,$filterdate)
    {

        $store_product_id = $data['store_product_id'];
        $instockstr = isset($data['inward_qty']) ? $data['inward_qty'] : 0 ;
        $instock = floatval($instockstr);
        //echo "Kchnin".$instock."<br>";

        $old_instockstr = $this->get_store_instock($store_product_id);
        $old_instock = floatval($old_instockstr);

        //echo "Stoin".$old_instock."<br>";

        $remain = $old_instock+$instock;
        
        $data = array( 'instock' => $remain );

        $this->db->where('date(stock_date)', $filterdate);
        $this->db->where('store_product_id',$store_product_id);
        $result = $this->db->update('store_instock',$data);

        // $str = $this->db->last_query();
        // echo $str;

        return $result;

    }

    public function get_max_date()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];

        $query = $this->db->query(" SELECT MAX(DATE(si.created)) AS created FROM store_inward si LEFT JOIN `store_product_inward` spi ON spi.`store_product_inward_id` = si.`store_product_inward_id` WHERE spi.`branch_id` = '".$branch_id."' ");
        $result = $query->row_array();
        return $result;

    }

    //store items report function 
    public function store_items_report($category_id,$branch_id,$year,$month)
    {
        $query = $this->db->query(" SELECT CASE WHEN sp.`unit`=0 THEN 'none' WHEN sp.`unit`=1 THEN 'kg' WHEN sp.`unit`=2 THEN 'gm' WHEN sp.`unit`=3 THEN 'l' WHEN sp.`unit`=4 THEN 'ml' END AS unit, sp.`name`, NULL AS open_stock, c.`cat_name`, spi.`price`, (SELECT SUM(ssi.`today_purchase_qty`) FROM store_inward ssi WHERE ssi.`store_product_inward_id` = spi.`store_product_inward_id`) AS purchase, (SELECT SUM(sub_sin.`instock`) FROM store_instock sub_sin WHERE sub_sin.`store_product_inward_id` = spi.`store_product_inward_id`) AS instock, NULL AS cons_amt, NULL AS close_amt
            FROM store_product_inward spi 
            LEFT JOIN `store_inward` si ON si.`store_product_inward_id` = spi.`store_product_inward_id` 
            LEFT JOIN `store_product` sp ON sp.`store_product_id` = spi.`store_product_id` 
            LEFT JOIN `category` c ON c.`category_id` = sp.`category_id` 
            LEFT JOIN `store_instock` s_in ON s_in.`store_product_inward_id` = spi.`store_product_inward_id` 
            WHERE MONTH(si.`created`) = '".$month."' AND YEAR(si.`created`) = '".$year."' AND c.category_id='".$category_id."' AND spi.branch_id='".$branch_id."'
            GROUP BY sp.`store_product_id` ");

        // $str = $this->db->last_query();
        // echo $str;die;


        $result = $query->result_array();
        return $result;

    }

    public function sale_with_tax($month,$branch_id)
    {
        $query = $this->db->query(" SELECT SUM(`total`) as sale_with_tax FROM daily_sales WHERE MONTH(`created`) = '".$month."' AND branch_id = '".$branch_id."' ");

        //  $str = $this->db->last_query();
        // echo $str;die;

        $result = $query->row_array();
        return $result;
    }

    public function daily_purchase_data($date)
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];

        $sql = "SELECT CASE WHEN sp.`unit`=0 THEN 'none' WHEN sp.`unit`=1 THEN 'kg' WHEN sp.`unit`=2 THEN 'gm' WHEN sp.`unit`=3 THEN 'l' WHEN sp.`unit`=4 THEN 'ml' END AS unit, sp.name,sp.price,(SELECT SUM(sub_si.instock) FROM store_instock sub_si WHERE sub_si.store_product_inward_id = spi.`store_product_inward_id`) AS instock, (SELECT sub_sin.today_purchase_qty FROM store_inward sub_sin WHERE sub_sin.store_product_inward_id = spi.`store_product_inward_id` AND DATE(sub_sin.created) = '".$date."') AS today_qty FROM `store_inward` si LEFT JOIN `store_product_inward` spi ON spi.`store_product_inward_id` = si.`store_product_inward_id` LEFT JOIN `store_product` sp ON sp.`store_product_id` = spi.`store_product_id` WHERE spi.`branch_id` = '".$branch_id."' AND DATE(si.created) = '".$date."' GROUP BY si.`store_product_inward_id` ";

        $query = $this->db->query($sql); //AND DATE(si.created) = '".date("Y-m-d")."' 
        
        $result = $query->result_array();

        //  $str = $this->db->last_query();
        // echo $str;die;

        return $result;
    }

 
} 

?>