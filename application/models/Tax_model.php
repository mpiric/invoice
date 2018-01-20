<?php 

class Tax_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	public function validateTax()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('branch_id','branch_id','required');
        //$this->form_validation->set_rules('product_category_id','product_category_id','required');
       // $this->form_validation->set_rules('tax_id','tax_id','required');
        $this->form_validation->set_rules('tax_percent','tax_percent','required');


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

    		'branch_id'=> $this->input->post('branch_id'),

            'product_category_id'=> $this->input->post('product_category_id'),

            'order_type'=> $this->input->post('order_type'),

            'tax_id'=> $this->input->post('tax_id'),

            'branch_tax_id'=> $this->input->post('branch_tax_id'),

            'tax_percent'=> $this->input->post('tax_percent')
            );

    	return $data;

    }

    public function get_data()
    {
        $query = $this->db->query("SELECT 
  CASE
    WHEN order_type = 1 
    THEN 'Table Order' 
    WHEN order_type = 2 
    THEN 'Home Delivery' 
    WHEN order_type = 3 
    THEN 'Parcel' 
  END AS `order_type`,
  `product_category`.`name` AS `product_category_name`,
  `branch`.`name` AS `branch_name`,
  `tax_main`.`tax_name` AS `taxname`,
  `tax_master`.`tax_master_id`,
  `tax_master`.`tax_id`,
  `tax_master`.`branch_id`,
  `tax_master`.`product_category_id`,
  `tax_master`.`tax_percent`,
  `tax_master`.`created`,
  `tax_master`.`updated` 
FROM
  `tax_master` 
  LEFT JOIN `branch` 
    ON `tax_master`.`branch_id` = `branch`.`branch_id` 
  LEFT JOIN `product_category` 
    ON `tax_master`.`product_category_id` = `product_category`.`product_category_id` 
  LEFT JOIN `tax_main`
    ON `tax_main`.`tax_id` = `tax_master`.`tax_id`
WHERE `tax_master`.`deleted` IS NULL ");

        $result = $query->result_array();
        return $result;
        //return $query->result_array();
    }

    public function get_details_by_id($id)
    {
       
        $query = $this->db->query("
SELECT 

  `product_category`.`name` AS `product_category_name`,
  `branch`.`name` AS `branch_name`,
  `tax_main`.`tax_name` AS `taxname`,
  `tax_master`.`tax_master_id`,
  `tax_master`.`tax_id`,
  `tax_master`.`order_type`,
  `tax_master`.`branch_id`,
  `tax_master`.`product_category_id`,
  `tax_master`.`tax_percent`,
  `tax_master`.`created`,
  `tax_master`.`updated` 
FROM
  `tax_master` 
  LEFT JOIN `branch` 
    ON `tax_master`.`branch_id` = `branch`.`branch_id` 
  LEFT JOIN `product_category` 
    ON `tax_master`.`product_category_id` = `product_category`.`product_category_id` 
  LEFT JOIN `tax_main`
    ON `tax_main`.`tax_id` = `tax_master`.`tax_id`
WHERE `tax_master`.`deleted` IS NULL AND `tax_master`.`tax_master_id` = $id");
        $result = $query->row_array();
        return $result;
    }

     public function get_details_by_id_info($id)
    {
       
        $query = $this->db->query("SELECT 
  CASE
    WHEN order_type = 1 
    THEN 'Table Order' 
    WHEN order_type = 2 
    THEN 'Home Delivery' 
    WHEN order_type = 3 
    THEN 'Parcel' 
  END AS `order_type`,
  `product_category`.`name` AS `product_category_name`,
  `branch`.`name` AS `branch_name`,
  `tax_main`.`tax_name` AS `taxname`,
  `tax_master`.`tax_master_id`,
  `tax_master`.`tax_id`,
  `tax_master`.`branch_id`,
  `tax_master`.`product_category_id`,
  `tax_master`.`tax_percent`,
  `tax_master`.`created`,
  `tax_master`.`updated` 
FROM
  `tax_master` 
  LEFT JOIN `branch` 
    ON `tax_master`.`branch_id` = `branch`.`branch_id` 
  LEFT JOIN `product_category` 
    ON `tax_master`.`product_category_id` = `product_category`.`product_category_id` 
  LEFT JOIN `tax_main`
    ON `tax_main`.`tax_id` = `tax_master`.`tax_id`
WHERE `tax_master`.`deleted` IS NULL  AND `tax_master`.`tax_master_id` = $id");
        $result = $query->row_array();
        return $result;
    }

    public function insert_data()
    {

        $data = $this->set_data();
        $result = $this->db->insert('tax_master',$data);

        return $result;
    }

    public function update_data($id)
    {
        //echo "model";die;
        $data = $this->set_data();
        $data['updated']= date("Y-m-d H:i:s");
        $this->db->where('tax_master_id', $id);
        $result = $this->db->update('tax_master',$data);
        return $result;

    }

    public function delete($id)
    {
        //$data = $this->set_data();
        $data['deleted']= date("Y-m-d H:i:s");
        $this->db->where('tax_master_id', $id);
        $result = $this->db->update('tax_master',$data);
        return $result;
    }

    public function tax_list()
    {
        $this->db->select('tax_master_id,name');
        $this->db->where('deleted',null);
        $query = $this->db->get('tax_master');        
        $result = $query->result_array();
        return $result;
    }

    // for table order
    public function get_tax_by_branch()
    {

        $session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];

        $query = $this->db->query("SELECT t.`tax_id`,t.`tax_name` FROM `tax_master` tm
                                    LEFT JOIN `product_category` pc ON (tm.`product_category_id` = pc.`product_category_id`)
                                    LEFT JOIN `tax_main` t ON (t.`tax_id` = tm.`tax_id`)
                                    WHERE tm.`branch_id` = $branch_id  AND t.`tax_id` IS NOT NULL AND t.`tax_id`!=0 AND branch_tax_id=0 AND tm.`order_type`=1 AND tm.`deleted` IS NULL
                                    GROUP BY tm.`tax_id`");
        $result = $query->result_array();
        return $result;
    }

    // for parcel order
    public function parcel_get_tax_by_branch()
    {

        $session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];

        $query = $this->db->query("SELECT t.`tax_id`,t.`tax_name` FROM `tax_master` tm
                                    LEFT JOIN `product_category` pc ON (tm.`product_category_id` = pc.`product_category_id`)
                                    LEFT JOIN `tax_main` t ON (t.`tax_id` = tm.`tax_id`)
                                    WHERE tm.`branch_id` = $branch_id  AND t.`tax_id` IS NOT NULL AND t.`tax_id`!=0 AND branch_tax_id=0 AND tm.order_type=3 AND tm.`deleted` IS NULL
                                    GROUP BY tm.`tax_id`");
        $result = $query->result_array();
        return $result;
    }

    // for delivery order
    public function delivery_get_tax_by_branch()
    {

        $session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];

        $query = $this->db->query("SELECT t.`tax_id`,t.`tax_name` FROM `tax_master` tm
                                    LEFT JOIN `product_category` pc ON (tm.`product_category_id` = pc.`product_category_id`)
                                    LEFT JOIN `tax_main` t ON (t.`tax_id` = tm.`tax_id`)
                                    WHERE tm.`branch_id` = $branch_id  AND t.`tax_id` IS NOT NULL AND t.`tax_id`!=0 AND branch_tax_id=0 AND tm.order_type=2 AND tm.`deleted` IS NULL
                                    GROUP BY tm.`tax_id`");
        $result = $query->result_array();
        return $result;
    }


    public function branch_wise_tax($id)
    {
      $query = $this->db->query(" SELECT 
CASE WHEN tm.tax_id=0 THEN tb.tax_id
ELSE tp.tax_id END AS tax_id,
CASE WHEN tm.tax_id=0 THEN tb.tax_name
ELSE tp.tax_name END AS tax_name , 
CASE WHEN tm.tax_id=0 THEN ( CASE WHEN tb.tax_type=1 THEN 'Product Specific' ELSE 'Branch Specific' END  )
ELSE ( CASE WHEN tp.tax_type=1 THEN 'Product Specific' ELSE 'Branch Specific' END  ) END AS tax_type ,
tm.tax_percent
 FROM `tax_master` tm
LEFT JOIN `tax_main` tp ON (tp.`tax_id` = tm.`tax_id`)
LEFT JOIN `tax_main` tb ON (tb.`tax_id` = tm.`branch_tax_id`)
                                  WHERE tm.`branch_id` = $id AND tm.order_type=1 AND tm.deleted IS NULL
                                  GROUP BY tm.`tax_id` ");
      $result = $query->result_array();
      return $result;

    }

    public function branch_wise_tax_to_display($id)
    {
      $query = $this->db->query(" SELECT 
CASE WHEN tm.tax_id=0 THEN tb.tax_id
ELSE tp.tax_id END AS tax_id,
CASE WHEN tm.tax_id=0 THEN tb.tax_name
ELSE tp.tax_name END AS tax_name , 
CASE WHEN tm.tax_id=0 THEN ( CASE WHEN tb.tax_type=1 THEN 'Product Specific' ELSE 'Branch Specific' END  )
ELSE ( CASE WHEN tp.tax_type=1 THEN 'Product Specific' ELSE 'Branch Specific' END  ) END AS tax_type ,
tm.tax_percent
 FROM `tax_master` tm
LEFT JOIN `tax_main` tp ON (tp.`tax_id` = tm.`tax_id`)
LEFT JOIN `tax_main` tb ON (tb.`tax_id` = tm.`branch_tax_id`)
                                  WHERE tm.`branch_id` = $id AND tm.deleted IS NULL
                                  GROUP BY tm.`tax_id` ");
      $result = $query->result_array();
      return $result;

    }

    public function branch_specific_tax_list()
    {
      $session_data = $this->session->userdata('logged_in');
      $branch_id = $session_data['branch_id'];

      $query = $this->db->query("SELECT t.tax_master_id,tax_percent,tm.`tax_name`,tm.tax_id FROM `tax_master` t
JOIN `tax_main` tm ON (t.`branch_tax_id` = tm.`tax_id`) AND tm.`tax_type`= 2
WHERE branch_id=$branch_id AND t.`tax_id` = 0 AND t.order_type=1 AND t.deleted IS NULL GROUP BY t.`branch_tax_id` ");

      $result = $query->result_array();
      return $result;
    }

    public function branch_specific_tax_list_by_order_type($order_type)
    {
      $session_data = $this->session->userdata('logged_in');
      $branch_id = $session_data['branch_id'];
      $branch_type = $session_data['branch_type'];

      $cond='';

      if($branch_type != 1)
      {
        $cond = "AND branch_id='".$branch_id."'";
      }

      $query = $this->db->query("SELECT t.tax_master_id,tax_percent,tm.`tax_name`,tm.tax_id FROM `tax_master` t
                                  JOIN `tax_main` tm ON (t.`branch_tax_id` = tm.`tax_id`) AND tm.`tax_type`= 2
                                  WHERE  t.`tax_id` = 0 AND t.order_type='".$order_type."' AND t.deleted IS NULL ".$cond." GROUP BY t.`branch_tax_id` ");

      $result = $query->result_array();

      // $str = $this->db->last_query();
      // echo $str;die;
      
      return $result;
    }

     public function parcel_branchSpecificTax_list()
    {
      $session_data = $this->session->userdata('logged_in');
      $branch_id = $session_data['branch_id'];

      $query = $this->db->query("SELECT t.tax_master_id,tax_percent,tm.`tax_name`,tm.tax_id FROM `tax_master` t
JOIN `tax_main` tm ON (t.`branch_tax_id` = tm.`tax_id`) AND tm.`tax_type`= 2
WHERE branch_id=$branch_id AND t.`tax_id` = 0 AND t.order_type=3 AND t.deleted IS NULL GROUP BY t.`branch_tax_id` ");

      $result = $query->result_array();
      return $result;
    }

     public function delivery_branchSpecificTax_list()
    {
      $session_data = $this->session->userdata('logged_in');
      $branch_id = $session_data['branch_id'];

      $query = $this->db->query("SELECT t.tax_master_id,tax_percent,tm.`tax_name`,tm.tax_id FROM `tax_master` t
JOIN `tax_main` tm ON (t.`branch_tax_id` = tm.`tax_id`) AND tm.`tax_type`= 2
WHERE branch_id=$branch_id AND t.`tax_id` = 0 AND t.order_type=2 AND t.deleted IS NULL GROUP BY t.`branch_tax_id` ");

      $result = $query->result_array();
      return $result;
    }




} 

?>