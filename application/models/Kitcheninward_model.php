<?php 

class Kitcheninward_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function get_kitchen_product_list()
	{
		// get logged in branch_id
		$session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];


        $query = $this->db->query(" SELECT CASE WHEN sp.unit=0 THEN 'none' WHEN sp.unit=1 THEN 'kg' WHEN sp.unit=2 THEN 'gm' WHEN sp.unit=3 THEN 'l' WHEN sp.unit=4 THEN 'ml' END AS unit,sp.store_product_id,sp.name,sp.product_code,sp.price,sp.created,sp.updated,NULL AS inward_qty,NULL AS prepared_qty,NULL AS remaining_qty,NULL AS waste_qty ,kins.instock ,si.instock AS storeInstock
                FROM `store_product` sp  
                LEFT JOIN `kitchen_instock` kins ON (kins.store_product_id = sp.store_product_id)
                LEFT JOIN store_instock si ON si.store_product_id = sp.store_product_id
            WHERE sp.`branch_id`='".$branch_id."' ");

      $result = $query->result_array();

            $response = array();

            if(!empty($result))
            {
                $i=0;

                foreach ($result as $store_product) {

                    $store_product_details = $store_product;

                   // find inward info by store product id and date
                    $store_product_id = $store_product['store_product_inward_id'];

                    $date = date("Y-m-d");

                    $inward_data = $this->find_inward_info_by_product_and_date($store_product_id,$date);

                    if(!empty($inward_data))
                    {
                        $store_product_details['inward_qty'] = $inward_data['inward_qty'];
                        $store_product_details['prepared_qty'] = $inward_data['prepared_qty'];
                        $store_product_details['remaining_qty'] = $inward_data['remaining_qty'];
                        $store_product_details['waste_qty'] = $inward_data['waste_qty'];
                    }

                    $response[$i] = $store_product_details;

                    $i++;
                }
            }

           return $response;       
	}

    public function find_inward_info_by_product_and_date($store_product_id,$date)
    {
        $date = date('Y-m-d',strtotime($date));
        $query = $this->db->query("SELECT * FROM kitchen_inward kin LEFT JOIN kitchen_instock ki ON ki.`store_product_inward_id` = kin.`store_product_inward_id` WHERE  kin.store_product_inward_id='".$store_product_id."' AND DATE(kin.created) = '".$date."' ");

         $result = $query->row_array();

         return $result;

    }

	public function get_kitchen_product_list_by_date($date)
	{
		// get logged in branch_id
        $today = date('Y-m-d');
		$session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];

        // $query = $this->db->query(" SELECT CASE WHEN sp.unit=0 THEN 'none' WHEN sp.unit=1 THEN 'kg' WHEN sp.unit=2 THEN 'gm' WHEN sp.unit=3 THEN 'l' WHEN sp.unit=4 THEN 'ml' END AS unit,sp.store_product_id,sp.name,sp.product_code,sp.price,sp.created,sp.updated,NULL AS inward_qty,NULL AS prepared_qty,NULL AS remaining_qty,NULL AS waste_qty ,kins.instock ,(SELECT sub_si.instock FROM store_instock sub_si WHERE sub_si.store_product_id=sp.store_product_id AND DATE(sub_si.stock_date)='".$date."' ) as storeInstock
        //         FROM `store_product` sp  
        //         LEFT JOIN `kitchen_instock` kins ON (kins.store_product_id = sp.store_product_id)
        //         LEFT JOIN store_instock si ON si.store_product_id = sp.store_product_id
        //     WHERE sp.`branch_id`='".$branch_id."' group by sp.store_product_id ");

        $query = $this->db->query(" SELECT CASE WHEN sp.unit=0 THEN 'none' WHEN sp.unit=1 THEN 'kg' WHEN sp.unit=2 THEN 'gm' WHEN sp.unit=3 THEN 'l' WHEN sp.unit=4 THEN 'ml' END AS unit, NULL AS inward_qty, NULL AS prepared_qty, NULL AS remaining_qty, NULL AS waste_qty, kins.instock, sp.name, (SELECT sub_kin.today_inward_qty FROM kitchen_inward sub_kin WHERE sub_kin.store_product_inward_id = spi.`store_product_inward_id` AND DATE(sub_kin.created) = '".$date."') AS today_inward_qty, (SELECT sub_si.`instock` FROM store_instock sub_si WHERE sub_si.`store_product_inward_id` = spi.`store_product_inward_id` AND DATE(sub_si.`stock_date`) = '".$date."') AS storeInstock,spi.* FROM `store_product_inward` spi LEFT JOIN `store_product` sp ON sp.`store_product_id` = spi.`store_product_id` LEFT JOIN `store_instock` sins ON sins.`store_product_inward_id` = spi.`store_product_inward_id` LEFT JOIN `kitchen_instock` kins ON kins.`store_product_inward_id` = spi.`store_product_inward_id` LEFT JOIN `kitchen_inward` ki ON ki.`store_product_inward_id` = spi.`store_product_inward_id`WHERE spi.`branch_id` = '".$branch_id."' GROUP BY spi.store_product_inward_id  ");

        $result = $query->result_array();

            $response = array();

            if(!empty($result))
            {
                $i=0;

                foreach ($result as $store_product) {

                    $store_product_details = $store_product;

                   // find inward info by store product id and date
                    $store_product_inward_id = $store_product['store_product_inward_id'];

                    if(strtotime($today) > strtotime($date))
                    {

                        if($store_product['storeInstock'] == '' && $store_product['instock'] == '')
                        {

                            //echo 'if';die;
                        
                            //----------------------------Store Instock------------------------------------//

                            $previousInstock_query = $this->db->query (" SELECT instock FROM `store_instock` WHERE `store_product_inward_id` = '".$store_product_inward_id."' AND DATE(stock_date) = '".$date."' ");

                            $previousInstock = $previousInstock_query->row_array();

                            if($previousInstock['instock']=='')
                            {
                                
                                //get last inwarded date
                                $previous_data1 = $this->db->query(" SELECT DATE(stock_date) AS stock_date FROM store_instock WHERE `store_product_inward_id` = '".$store_product_inward_id."' AND DATE(stock_date) < '".$date."' ORDER BY DATE(stock_date) DESC ");

                                $previous_data12 = $previous_data1->row_array();

                                $previous_date1 = $previous_data12['stock_date'];

                                $prevInstock1_query = $this->db->query (" SELECT instock FROM `store_instock` WHERE `store_product_inward_id` = '".$store_product_inward_id."' AND DATE(stock_date) = '".$previous_date1."' ");

                                $prevInstock1 = $prevInstock1_query->row_array();


                                $store_product_details['storeInstock'] = $prevInstock1['instock'];

                                
                            }

                            //-------------------------------Kitchen Instock----------------------------------//

                            $previouskiInstock_query = $this->db->query (" SELECT instock FROM `kitchen_instock` WHERE `store_product_inward_id` = '".$store_product_inward_id."' AND DATE(stock_date) = '".$date."' ");

                            $previouskiInstock = $previouskiInstock_query->row_array();

                            if($previouskiInstock['instock']=='')
                            {
                                //get last inwarded date
                                $previous_dataki1 = $this->db->query(" SELECT DATE(stock_date) AS stock_date FROM store_instock WHERE `store_product_inward_id` = '".$store_product_inward_id."' AND DATE(stock_date) < '".$date."' ORDER BY DATE(stock_date) DESC ");

                                $previous_dataki12 = $previous_dataki1->row_array();

                                $previous_dateki1 = $previous_dataki12['stock_date'];

                                $prevInstockki1_query = $this->db->query (" SELECT instock FROM `store_instock` WHERE `store_product_inward_id` = '".$store_product_inward_id."' AND DATE(stock_date) = '".$previous_dateki1."' ");

                                $prevInstockki1 = $prevInstockki1_query->row_array();

                                $store_product_details['instock'] = $prevInstockki1['instock'];
                                
                            }

                        }
                        elseif($store_product['storeInstock'] == '')
                        {
                           // echo 'elseif1';die;
                            $previousInstock_query = $this->db->query (" SELECT instock FROM `store_instock` WHERE `store_product_inward_id` = '".$store_product_inward_id."' AND DATE(stock_date) = '".$date."' ");

                            $previousInstock = $previousInstock_query->row_array();

                            if($previousInstock['instock']=='')
                            {
                                
                                //get last inwarded date
                                $previous_data1 = $this->db->query(" SELECT DATE(stock_date) AS stock_date FROM store_instock WHERE `store_product_inward_id` = '".$store_product_inward_id."' AND DATE(stock_date) < '".$date."' ORDER BY DATE(stock_date) DESC ");

                                $previous_data12 = $previous_data1->row_array();

                                $previous_date1 = $previous_data12['stock_date'];

                                $prevInstock1_query = $this->db->query (" SELECT instock FROM `store_instock` WHERE `store_product_inward_id` = '".$store_product_inward_id."' AND DATE(stock_date) = '".$previous_date1."' ");

                                $prevInstock1 = $prevInstock1_query->row_array();


                                $store_product_details['storeInstock'] = $prevInstock1['instock'];

                                
                            }

                        }
                        elseif($store_product['instock'] == '')
                        {
                            //echo 'elseif2';die;
                            $previouskiInstock_query = $this->db->query (" SELECT instock FROM `kitchen_instock` WHERE `store_product_inward_id` = '".$store_product_inward_id."' AND DATE(stock_date) = '".$date."' ");

                            $previouskiInstock = $previouskiInstock_query->row_array();

                            if($previouskiInstock['instock']=='')
                            {
                                //get last inwarded date
                                $previous_dataki1 = $this->db->query(" SELECT DATE(stock_date) AS stock_date FROM store_instock WHERE `store_product_inward_id` = '".$store_product_inward_id."' AND DATE(stock_date) < '".$date."' ORDER BY DATE(stock_date) DESC ");

                                $previous_dataki12 = $previous_dataki1->row_array();

                                $previous_dateki1 = $previous_dataki12['stock_date'];

                                $prevInstockki1_query = $this->db->query (" SELECT instock FROM `store_instock` WHERE `store_product_inward_id` = '".$store_product_inward_id."' AND DATE(stock_date) = '".$previous_dateki1."' ");

                                $prevInstockki1 = $prevInstockki1_query->row_array();

                                $store_product_details['instock'] = $prevInstockki1['instock'];
                                
                            }
                        }
                        else
                        {
                            //sssecho 'else';die;
                            //store
                            $previous_data = $this->get_second_largest_date_from_store($store_product_inward_id);
                            $previous_date = $previous_data['stock_date'];


                            $prevInstock_query = $this->db->query (" SELECT instock FROM `store_instock` WHERE `store_product_inward_id` = '".$store_product_inward_id."' AND DATE(stock_date) = '".$previous_date."' ");

                            $prevInstock = $prevInstock_query->row_array();

                            $store_product_details['storeInstock'] = $prevInstock['instock'];

                            //kitchen
                            $previouski_data = $this->get_second_largest_date_from_kitchen($store_product_inward_id);
                            $previouski_date = $previouski_data['stock_date'];


                            $prevInstockki_query = $this->db->query (" SELECT instock FROM `kitchen_instock` WHERE `store_product_inward_id` = '".$store_product_inward_id."' AND DATE(stock_date) = '".$previous_date."' ");

                            $prevInstockki = $prevInstockki_query->row_array();

                            $store_product_details['instock'] = $prevInstockki['instock'];

                        }

                    }
                    else
                    {
                        //store
                        $previous_data = $this->get_second_largest_date_from_store($store_product_inward_id);
                        $previous_date = $previous_data['stock_date'];


                        $prevInstock_query = $this->db->query (" SELECT instock FROM `store_instock` WHERE `store_product_inward_id` = '".$store_product_inward_id."' AND DATE(stock_date) = '".$previous_date."' ");

                        $prevInstock = $prevInstock_query->row_array();

                        $store_product_details['storeInstock'] = $prevInstock['instock'];

                        //kitchen
                        $previous_kitchenData=$this->get_second_largest_date_from_kitchen($store_product_inward_id);
                        $previous_date_kitchen = $previous_kitchenData['stock_date'];


                        $prevInstock_query_kitchen = $this->db->query (" SELECT instock FROM `kitchen_instock` WHERE `store_product_inward_id` = '".$store_product_inward_id."' AND DATE(stock_date) = '".$previous_date_kitchen."' ");

                        $prevInstockKitchen = $prevInstock_query_kitchen->row_array();

                        $store_product_details['instock'] = $prevInstockKitchen['instock'];
                    }

                    $inward_data = $this->find_inward_info_by_product_and_date($store_product_inward_id,$date);


                    if(!empty($inward_data))
                    {
                        $store_product_details['inward_qty'] = $inward_data['inward_qty'];
                        $store_product_details['prepared_qty'] = $inward_data['prepared_qty'];
                        $store_product_details['remaining_qty'] = $inward_data['remaining_qty'];
                        $store_product_details['waste_qty'] = $inward_data['waste_qty'];
                        $store_product_details['instock'] = $store_product_details['instock']+$inward_data['today_inward_qty'];
                    }

                    $response[$i] = $store_product_details;

                    $i++;
                }
            }

            //echo '<pre>';print_r($response);die;

           return $response;  

	}

    public function get_second_largest_date_from_store($store_product_inward_id)
    {
        //$query = $this->db->query(" SELECT MAX( DATE(stock_date) ) AS stock_date,instock FROM store_instock WHERE DATE(stock_date) < ( SELECT MAX( DATE(stock_date) ) FROM store_instock ) AND `store_product_inward_id` = '".$store_product_inward_id."' ");

        $query = $this->db->query(" SELECT MAX( DATE(stock_date) ) AS stock_date,instock FROM store_instock WHERE `store_product_inward_id` = '".$store_product_inward_id."' ");

        $query_result = $query->row_array();

        return $query_result;
    }

	public function get_data_product_and_date($store_product_inward_id,$dateParam)
	{
		//$cur_date = date("Y-m-d");
        $inward_date = date('Y-m-d',strtotime($dateParam));
		$query = $this->db->query("SELECT * FROM kitchen_inward WHERE DATE(created) = '".$inward_date."' AND store_product_inward_id='".$store_product_inward_id."' ");
		$result = $query->row_array();
        return $result;
	}

    public function get_max_inward_date()
    {
        // inward_date is created date
        $query = $this->db->query(" SELECT MAX(created) as max_inward_date FROM kitchen_inward ");
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
        // get max created date
        $session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];

        $max_inward_date = $this->get_max_date();

        //echo $max_inward_date['created'];die;
        $date = $max_inward_date['created'];
        $cur_date = date("Y-m-d");

        if($filterdate==$cur_date)
        {
            $query = $this->db->query(" SELECT * FROM kitchen_inward ki LEFT JOIN `store_product_inward` spi ON spi.`store_product_inward_id` = ki.`store_product_inward_id` WHERE DATE(ki.created) = '".$filterdate."' AND branch_id = '".$branch_id."' ");

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
            $query = $this->db->query(" SELECT * FROM kitchen_inward ki LEFT JOIN `store_product_inward` spi ON spi.`store_product_inward_id` = ki.`store_product_inward_id` WHERE DATE(ki.created) BETWEEN '".$date."' AND '".$filterdate."' AND branch_id = '".$branch_id."' ");

            $result = $query->num_rows();

            return $result;
        }

        
    }

    public function get_second_largest_date_from_kitchen($store_product_inward_id)
    {
        //$query = $this->db->query(" SELECT MAX( DATE(stock_date) ) AS stock_date,instock FROM store_instock WHERE DATE(stock_date) < ( SELECT MAX( DATE(stock_date) ) FROM store_instock ) AND `store_product_inward_id` = '".$store_product_inward_id."' ");

        $query = $this->db->query(" SELECT MAX( DATE(stock_date) ) AS stock_date,instock FROM kitchen_instock WHERE `store_product_inward_id` = '".$store_product_inward_id."' ");

        $query_result = $query->row_array();

        return $query_result;
    }

	public function do_kitchen_inward($data)
    {
      
        $var = $this->get_data_product_and_date($data['store_product_inward_id'],$data['created']); 
       //print_r($var);   

        if( !empty($var) )
        {
            //echo 'if';
            unset($data['created']);
            $data['updated'] = date('Y-m-d H:i:s');
            $data['today_inward_qty'] = $data['inward_qty']+$var['today_inward_qty'];

            //print_r($data);
            $this->db->where('store_product_inward_id',$var['store_product_inward_id']);
            $this->db->where('created',$var['created']);

            $result = $this->db->update('kitchen_inward',$data);

            // $str = $this->db->last_query();
            // echo $str;die;

            $updated_status = $this->db->affected_rows();

            if($updated_status)
            {
                return $var['store_product_inward_id'];
            }


            //return $result;

        }
        else
        {
            //echo 'else';
            $data['today_inward_qty'] = $data['inward_qty'];

            //print_r($data);die;
            $result = $this->db->insert('kitchen_inward',$data);

          

            $insert_id = $this->db->insert_id();
            
            return $insert_id;
        }

    }

    function get_stock_by_store_product_id($store_product_id,$stock_dateParam)
    {
        $stock_date = date("Y-m-d",strtotime($stock_dateParam));
        $query = $this->db->query("SELECT * FROM kitchen_instock WHERE store_product_inward_id = '".$store_product_id."' AND DATE(stock_date) = '".$stock_date."' ");

        $result = $query->row_array();

        return $result;
    }

    public function insert_into_kitchen_inward($data)
    {


        $nextDate_query = $this->db->query(" SELECT CURDATE() + INTERVAL 1 DAY AS stock_date");
        $nextDate = $nextDate_query->row_array();

        // echo $nextDate['stock_date'];
        // print_r($nextDate);
        // die;
        $data = array(

            'store_product_inward_id' => $data['store_product_inward_id'],

            'instock'=> $data['next_day_instock'],

            'stock_date'=>$data['stock_date'],


            );
        
            $var = $this->get_stock_by_store_product_id($data['store_product_inward_id'],$data['stock_date']);

            //$data['instock'] = $var['instock'] + $data['instock'];           

            if( !empty($var) )
            {       

                $this->db->where('stock_date',$var['stock_date']);
                $this->db->where('store_product_inward_id',$var['store_product_inward_id']);
                $result = $this->db->update('kitchen_instock',$data);



                $updated_status = $this->db->affected_rows();

                if($updated_status)
                {
                    $kitchenIn = array();
                    $kitchenIn['instock'] = $data['instock'];
                    $kitchenIn['store_product_inward_id'] = $data['store_product_inward_id'];
                    $kitchenIn['stock_date'] = $nextDate['stock_date'];

                    $this->db->insert('kitchen_instock',$kitchenIn);

                    return $var['store_product_inward_id'];
                }

            }
            else
            {

                $result = $this->db->insert('kitchen_instock',$data);

                $insert_id = $this->db->insert_id();
                
                return $insert_id;
            }
    }
    public function get_max_date()
    {
        $session_data = $this->session->userdata('logged_in');
        $branch_id = $session_data['branch_id'];

        $query = $this->db->query(" SELECT MAX(DATE(ki.created)) AS created FROM kitchen_inward ki LEFT JOIN `store_product_inward` spi ON spi.`store_product_inward_id` = ki.`store_product_inward_id` WHERE spi.`branch_id` = '".$branch_id."' ");

        $result = $query->row_array();
        return $result;

    }
	

} 

?>