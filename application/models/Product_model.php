<?php 

class Product_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	public function validateProduct()
	{
		$this->load->library('form_validation');

        $this->form_validation->set_rules('product_category_id','product_category_id','required');
		$this->form_validation->set_rules('name','name','required');
        $this->form_validation->set_rules('unit','unit','required');
        $this->form_validation->set_rules('description','description','required');
		$this->form_validation->set_rules('price','price','required');
       


		if($this->form_validation->run() === FALSE)
        {
            return false;   
        }
        else
        {
            return true;
        }
		
	}

    function emptyElementExists($arr) {

        unset($arr['product_code']);
        return array_search("", $arr) !== false;
    }

	public function set_data()
    {

        $config['upload_path']      =    './uploads/';
        $config['allowed_types']        = 'gif|jpg|png';
        $config['max_width']        =     1024;
        $config['max_height']       =     768;

        $this->load->library('upload',$config);

    	$data = array(

    		
            'product_category_id'=> $this->input->post('product_category_id'),

            'name'=> $this->input->post('name'),

            'product_code'=> $this->input->post('product_code'),

            'unit'=> $this->input->post('unit'),            

            'description'=> $this->input->post('description'),

            'price'=> $this->input->post('price')

    		);

        if(! $this->upload->do_upload('image'))
        {
            $error=array('error' => $this->upload->display_errors());
        }
        else
        {
            $uploadData=array('upload_data' => $this->upload->data());
            $data['image'] = $uploadData['upload_data']['file_name'];
        }

    	return $data;

    }

    public function get_data()
    {
        $query = $this->db->query(' SELECT 
                    `p`.`product_category_id`,
                    `pro`.`product_id`,
                    `pro`.`name` AS product_name,
                    `p`.`name` AS product_cat_name,
                    `pc`.`name` AS parent_name,
                    `pro`.`unit`,
                    `pro`.`price`,
                    `pro`.`product_code`
                    FROM product pro
                    LEFT JOIN product_category p ON `p`.`product_category_id` = `pro`.`product_category_id`
                    LEFT JOIN product_category pc ON `pc`.`product_category_id` = `p`.`parent` 
                    WHERE `pro`.`deleted` IS NULL ');
        $result = $query->result_array();
        return $result;
    }

    public function get_details_by_id($id)
    {
        $query = $this->db->query(' SELECT 
`p`.`product_category_id`,
`pro`.`name` AS product_name,
`pro`.`product_id`,
`p`.`name` AS product_cat_name,
`pc`.`name` AS parent_name,
`pro`.`unit`,
`pro`.`price`,
`pro`.`product_code`,
`pro`.`image`
FROM product pro
LEFT JOIN product_category p ON `p`.`product_category_id` = `pro`.`product_category_id`
LEFT JOIN product_category pc ON `pc`.`product_category_id` = `p`.`parent` 
WHERE `pro`.`product_id`= "'.$id.'" AND `pro`.`deleted` IS NULL  ');
        $result = $query->row_array();
        return $result;
    }

    public function insert_data()
    {

        $data = $this->set_data();
        $result = $this->db->insert('product',$data);

        return $result;
    }

    public function update_data($id)
    {
        //echo "model";die;
        $data = $this->set_data();
        $data['updated']= date("Y-m-d H:i:s");
        $this->db->where('product_id', $id);
        $result = $this->db->update('product',$data);
        return $result;

    }

    public function delete($id)
    {
        $data['deleted']= date("Y-m-d H:i:s");
        $this->db->where('product_id', $id);
        $result = $this->db->update('product',$data);

        return $result;
    }

    public function custom_insert_data($post_data)
    {
        //echo '<pre>';print_r($post_data);die;
        $config['upload_path']      =    './uploads/products';
        $config['allowed_types']        = 'gif|jpg|png|bmp';
        $config['max_width']        =     1024;
        $config['max_height']       =     768;

        $this->load->library('upload',$config);

        $data = array(

            
            'product_category_id'=> $post_data['product_category_id'],

            'name'=> $post_data['name'],

            'product_code'=> isset($post_data['product_code']) ? $post_data['product_code'] : NULL,

            'unit'=> $post_data['unit'],

            'description'=> $post_data['description'],

            'price'=> $post_data['price']

            );

        $bool = true;
        $error = '';

        if(!empty($_FILES))
        {
            if(! $this->upload->do_upload('image'))
            {
                $bool = false;
               // $error=array('error' => $this->upload->display_errors());
                $error = $this->upload->display_errors();
            }
            else
            {
                $uploadData=array('upload_data' => $this->upload->data());
                $data['image'] = $uploadData['upload_data']['file_name'];
            }
        }

        if($bool==true)
        {
            $result = $this->db->insert('product',$data);

            $insert_id = $this->db->insert_id();
            $result_arr = array("status"=>"success","message"=>$insert_id);
             return  $result_arr;
        }
        else
        {
            $result_arr = array("status"=>"error","message"=>$error);
            return $result_arr;
        }
       
    }

    public function custom_update_data($post_data,$product_id)
    {
        //echo '<pre>';print_r($post_data);die;
        $config['upload_path']      =    './uploads/products';
        $config['allowed_types']        = 'gif|jpg|png|bmp';
        $config['max_width']        =     1024;
        $config['max_height']       =     768;

        $this->load->library('upload',$config);

        $data = array(

            
            'product_category_id'=> $post_data['product_category_id'],

            'name'=> $post_data['name'],

            'product_code'=> $post_data['product_code'],

            'unit'=> $post_data['unit'],

            'description'=> $post_data['description'],

            'price'=> $post_data['price']

            );

        $bool = true;
        $error = '';

        if(!empty($_FILES))
        {
            if(! $this->upload->do_upload('image'))
            {
                $bool = false;
                //$error=array('error' => $this->upload->display_errors());
                $error = $this->upload->display_errors();
            }
            else
            {
                $uploadData=array('upload_data' => $this->upload->data());
                $data['image'] = $uploadData['upload_data']['file_name'];
            }
        }

        if($bool==true)
        {
            $data['updated']= date("Y-m-d H:i:s");
            $this->db->where('product_id', $product_id);
            $result = $this->db->update('product',$data);
            //return $result;
            $result_arr = array("status"=>"success","message"=>true);
            return $result_arr;
        }
        else
        {
            $result_arr = array("status"=>"error","message"=>$error);
            return $result_arr;
        }
        
    }

    public function get_data_by_branch()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];

        // get tax details
        $this->load->model('tax_model');
        $tax_list = $this->tax_model->get_tax_by_branch();

        $select_tax_1 = '';
        $select_tax_2 = '';

        if(!empty($tax_list))
        {
            

            if(isset($tax_list[0]['tax_id']) && $tax_list[0]['tax_id']!='')
            {
                $tax_1 = $tax_list[0]['tax_id'];
                $select_tax_1 = ',( SELECT (`tax_master_id`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = '.$tax_1.' limit 1) AS service_tax_id
            ,( SELECT (`tax_percent`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = '.$tax_1.' limit 1 ) AS service_tax_percent  ';

            }
            if(isset($tax_list[1]['tax_id']) && $tax_list[1]['tax_id']!='')
            {
                $tax_2 = $tax_list[1]['tax_id'];
            
                $select_tax_2 = ',( SELECT (`tax_master_id`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = '.$tax_2.' limit 1) AS other_tax_id
            ,( SELECT (`tax_percent`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = '.$tax_2.' limit 1) AS other_tax_percent  ';    
            }
            

        }

        // find brand_id associated with branch
        $this->load->model('branch_model');
        $branch_details = $this->branch_model->branch_details_by_id($branch_id);

        $brand_id = $branch_details['brand_id'];

        $this->db->select('p.*,bp.product_price as price '.$select_tax_1.$select_tax_2.' ');        
            $this->db->join('branch_products bp', 'bp.`product_id` = p.`product_id`','left'); 
            $this->db->join('product_category pc', 'p.`product_category_id` = pc.`product_category_id`','left');  
            $this->db->join('tax_master tm', 'pc.`product_category_id` = tm.`product_category_id`','left'); 
            $this->db->join('branch b','FIND_IN_SET(pc.brand_id, b.brand_id)','left');   
            $this->db->where('p.deleted',null);  
            $this->db->where('bp.branch_id',$branch_id);  
            $this->db->where('bp.is_available','Y'); 
            // if($brand_id!=''&&$brand_id!=0)
            // {
            //     $this->db->where("FIND_IN_SET('$brand_id',pc.brand_id) !=", 0);
            // } 
            
            $this->db->group_by('p.product_id');
            $this->db->order_by('ABS(p.product_code)','ASC');  
            $query = $this->db->get('product p');

             // $str = $this->db->last_query();
             // echo '<pre>';print_r($str);
                       
           $result = $query->result_array();
           return $result;
            
       //return false;

    }

    public  function product_list()
    {
        $this->db->select('product_id,price,name,product_code');
        $this->db->where('deleted',null);
        $query = $this->db->get('product');
        $result = $query->result_array();

        return $result;
    }

    public function parcel_product_list_by_branch()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];

        // get tax details
        $this->load->model('tax_model');
        $tax_list = $this->tax_model->parcel_get_tax_by_branch();

        $select_tax_1 = '';
        $select_tax_2 = '';

        if(!empty($tax_list))
        {
            

            if(isset($tax_list[0]['tax_id']) && $tax_list[0]['tax_id']!='')
            {
                $tax_1 = $tax_list[0]['tax_id'];
                $select_tax_1 = ',( SELECT (`tax_master_id`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = '.$tax_1.' AND tax_master.deleted IS NULL limit 1) AS service_tax_id
            ,( SELECT (`tax_percent`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = '.$tax_1.' AND tax_master.deleted IS NULL limit 1 ) AS service_tax_percent  ';

            }
            if(isset($tax_list[1]['tax_id']) && $tax_list[1]['tax_id']!='')
            {
                $tax_2 = $tax_list[1]['tax_id'];
            
                $select_tax_2 = ',( SELECT (`tax_master_id`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = '.$tax_2.' AND tax_master.deleted IS NULL limit 1) AS other_tax_id
            ,( SELECT (`tax_percent`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = '.$tax_2.' AND tax_master.deleted IS NULL limit 1) AS other_tax_percent  ';    
            }
            

        }

        // find brand_id associated with branch
        $this->load->model('branch_model');
        $branch_details = $this->branch_model->branch_details_by_id($branch_id);

        $brand_id = $branch_details['brand_id'];
      
        $this->db->select('p.*,bp.product_price as price '.$select_tax_1.$select_tax_2.' ');        
            $this->db->join('branch_products bp', 'bp.`product_id` = p.`product_id`','left'); 
            $this->db->join('product_category pc', 'p.`product_category_id` = pc.`product_category_id`','left');  
            $this->db->join('tax_master tm', 'pc.`product_category_id` = tm.`product_category_id`','left');  
            $this->db->join('branch b','FIND_IN_SET(pc.brand_id, b.brand_id)','left');  
            $this->db->where('p.deleted',null);  
            $this->db->where('bp.branch_id',$branch_id); 
            $this->db->where('bp.is_available','Y'); 
            // if($brand_id!=''&&$brand_id!=0)
            // {
            //     $this->db->where("FIND_IN_SET('$brand_id',pc.brand_id) !=", 0); 
            // }
            $this->db->group_by('p.product_id');  
            $this->db->order_by('ABS(p.product_code)','ASC');
            $query = $this->db->get('product p');

             // $str = $this->db->last_query();
             // echo '<pre>';print_r($str);
                       
           $result = $query->result_array();
           return $result;
            
       //return false;

    }

    public function delivery_product_list_by_branch()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];

        // get tax details
        $this->load->model('tax_model');
        $tax_list = $this->tax_model->delivery_get_tax_by_branch();

        $select_tax_1 = '';
        $select_tax_2 = '';

        if(!empty($tax_list))
        {
            

            if(isset($tax_list[0]['tax_id']) && $tax_list[0]['tax_id']!='')
            {
                $tax_1 = $tax_list[0]['tax_id'];
                $select_tax_1 = ',( SELECT (`tax_master_id`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = '.$tax_1.' AND tax_master.deleted IS NULL limit 1) AS service_tax_id
            ,( SELECT (`tax_percent`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = '.$tax_1.' AND tax_master.deleted IS NULL limit 1 ) AS service_tax_percent  ';

            }
            if(isset($tax_list[1]['tax_id']) && $tax_list[1]['tax_id']!='')
            {
                $tax_2 = $tax_list[1]['tax_id'];
            
                $select_tax_2 = ',( SELECT (`tax_master_id`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = '.$tax_2.' AND tax_master.deleted IS NULL limit 1) AS other_tax_id
            ,( SELECT (`tax_percent`) FROM `tax_master` WHERE tax_master.`product_category_id`= p.`product_category_id` AND tax_master.`tax_id` = '.$tax_2.' AND tax_master.deleted IS NULL limit 1) AS other_tax_percent  ';    
            }
            

        }

        // find brand_id associated with branch
        $this->load->model('branch_model');
        $branch_details = $this->branch_model->branch_details_by_id($branch_id);

        $brand_id = $branch_details['brand_id'];

        $this->db->select('p.*,bp.product_price as price '.$select_tax_1.$select_tax_2.' ');        
            $this->db->join('branch_products bp', 'bp.`product_id` = p.`product_id`','left'); 
            $this->db->join('product_category pc', 'p.`product_category_id` = pc.`product_category_id`','left');  
            $this->db->join('tax_master tm', 'pc.`product_category_id` = tm.`product_category_id`','left');  
            $this->db->join('branch b','FIND_IN_SET(pc.brand_id, b.brand_id)','left');  
            $this->db->where('p.deleted',null);  
            $this->db->where('bp.branch_id',$branch_id); 
            $this->db->where('bp.is_available','Y'); 
            // if($brand_id!=''&&$brand_id!=0)
            // {
            //     $this->db->where("FIND_IN_SET('$brand_id',pc.brand_id) !=", 0); 
            // }
            $this->db->group_by('p.product_id');  
            $this->db->order_by('ABS(p.product_code)','ASC');  
            $query = $this->db->get('product p');

             // $str = $this->db->last_query();
             // echo '<pre>';print_r($str);
                       
           $result = $query->result_array();
           return $result;
            
       //return false;

    }

    function get_products_by_brand($brand_id)
    {
        $sql = 'select p.* from product p
                join product_category pc on (p.product_category_id=pc.product_category_id)
                where FIND_IN_SET("'.$brand_id.'", pc.brand_id) > 0 ';

        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result;

    }

    function get_products_by_product_category_id($inserted_product_category_id)
    {
        $sql = 'SELECT * FROM `product` WHERE `product_category_id`="'.$inserted_product_category_id.'" ';

        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result;
    }

    public function check_name($name)
    {
        $this->db->select('*');
        $this->db->like('name',$name);
        $query=$this->db->get('product');

        $result = $query->result_array();
        return $result;
    }

    public function check_code($code)
    {
        $this->db->select('*');
        $this->db->like('product_code',$code);
        $query=$this->db->get('product');

        $result = $query->result_array();
        return $result;
    }


} 

?>