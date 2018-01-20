<?php
// error_reporting(E_ALL);
// die;
(@include_once('dbconnection.php')) or die('connection file does not found.');

$ConnectionObj = new Connection();

$conn = $ConnectionObj->conn;

$arrReturn = array();

if(isset($_REQUEST['action']))
{
  switch ($_REQUEST['action']) {

    
      case 'order_items_by_users':

          if( !isset($_REQUEST['user_id']))

          {

              $arrReturn['status'] = '201';

              $arrReturn['message'] = 'Required parameters are missing.';

          }

          else

          {                   

              if( $_REQUEST['user_id']=='')

              {

                  $arrReturn['status'] = '202';

                  $arrReturn['message'] = 'Parameters can not be blank.';

              }

              else
              {
                  $user_id = $_REQUEST['user_id'];
                  
                  $sql = " SELECT post_id FROM `wp_postmeta` WHERE `meta_key` = '_customer_user' AND `meta_value` = '".$user_id."' ";

                  $select_qry = $conn->prepare($sql);

                  $select_qry->execute();

                  $res = $select_qry->setFetchMode(PDO::FETCH_ASSOC); 

                  $orders = $select_qry->fetchAll();
                  $str= '';
                  $i = 0;
                  foreach ($orders as $key => $value) 
                  {
                    
                    if($i != 0)
                    {
                      $str .= ',';
                    }
                    $str .= $value['post_id'];
                    
                    $i++;
                  }
                  
                  // $sql = "SELECT * FROM `wp_woocommerce_order_items` WHERE `order_id` IN (".$str.")";

                  // echo $sql; die;
                  
                  $order_items = $conn->prepare(" SELECT order_item_name, COUNT(*) AS ordered_count FROM `wp_woocommerce_order_items` WHERE `order_id` IN (".$str.") GROUP BY order_item_name ORDER BY `wp_woocommerce_order_items`.`order_item_name` ");

                 // $order_items = $conn->prepare(" SELECT order_item_name , count(*) as items_ordered FROM `wp_woocommerce_order_items` group by order_id  HAVING `order_id` IN (".$str.") ");
                  $order_items->execute();

                  $result = $order_items->setFetchMode(PDO::FETCH_ASSOC); 

                  $order_items_data = $order_items->fetchAll();

                  //echo '<pre>';print_r($order_items_data);die;

                  //$response = $order_items_data;

                  
                  $i=0;

                  $newArr = array();

                  foreach ($order_items_data as $ordersItems) 
                  {
                    // echo '<pre>'; print_r($ordersItems);die;
                    //$dtl = array();
                     $response = array();
                     $response = $ordersItems;

                     $qrry = " SELECT id FROM `wp_posts` WHERE `post_title` = '".$ordersItems['order_item_name']."' AND post_status = 'publish' ";
                     //query 
                     $select_posts = $conn->prepare($qrry);

                      $select_posts->execute();

                      $select_posts->setFetchMode(PDO::FETCH_ASSOC); 

                      $res1 = $select_posts->fetch();  

                      

                      //echo '<br>'.$res1['post_title'];          

                      array_push($response , $res1['id']);

                      //echo '<pre>'; print_r($response);// $res[$i] = $response;

                      //$dtl['ar'] = $res1['post_title'];

                      //$newArr[$i] = $dtl;

                      
                      //

                      //$newArr[$i] = $response;
                      $i++; 
                      echo '<pre>'; print_r($newArr);

                  }
                  echo '<pre>'; print_r($newArr);die;

                  $arrReturn['status'] = '200';
                  $arrReturn['message'] = $newArr;
                 
              }

          }

      break;

      case 'testCase':

          if( !isset($_REQUEST['user_id']))

          {

              $arrReturn['status'] = '201';

              $arrReturn['message'] = 'Required parameters are missing.';

          }

          else

          {                   

              if( $_REQUEST['user_id']=='')

              {

                  $arrReturn['status'] = '202';

                  $arrReturn['message'] = 'Parameters can not be blank.';

              }

              else
              {
                  $user_id = $_REQUEST['user_id'];
                  
                  $sql = " SELECT post_id FROM `wp_postmeta` WHERE `meta_key` = '_customer_user' AND `meta_value` = '".$user_id."' ";

                  $select_qry = $conn->prepare($sql);

                  $select_qry->execute();

                  $res = $select_qry->setFetchMode(PDO::FETCH_ASSOC); 

                  $orders = $select_qry->fetchAll();
                  $str= '';
                  $i = 0;
                  foreach ($orders as $key => $value) 
                  {
                    
                    if($i != 0)
                    {
                      $str .= ',';
                    }
                    $str .= $value['post_id'];
                    
                    $i++;
                  }
                  
                  // $sql = "SELECT * FROM `wp_woocommerce_order_items` WHERE `order_id` IN (".$str.")";

                  // echo $sql; die;
                  
                  $order_items = $conn->prepare(" SELECT order_item_name, COUNT(*) AS ordered_count FROM `wp_woocommerce_order_items` WHERE `order_id` IN (".$str.") GROUP BY order_item_name ORDER BY `wp_woocommerce_order_items`.`order_item_name` ");

                 // $order_items = $conn->prepare(" SELECT order_item_name , count(*) as items_ordered FROM `wp_woocommerce_order_items` group by order_id  HAVING `order_id` IN (".$str.") ");
                  $order_items->execute();

                  $result = $order_items->setFetchMode(PDO::FETCH_ASSOC); 

                  $order_items_data = $order_items->fetchAll();

                  //echo '<pre>';print_r($order_items_data);die;

                  //$response = $order_items_data;
                  
                  $i=0;

                  $resp_arr = array();

                  foreach ($order_items_data as $item) 
                  {
                    //echo '<pre>';print_r($item);
                    $item_name = $item['order_item_name'];

                    $detail_arr = $item;


                    // $order_items2 = $conn->prepare(" SELECT id FROM `wp_posts` WHERE `post_title` = '".$item_name."' AND post_status = 'publish' ");

                    // $order_items2->execute();

                    // $order_items2->setFetchMode(PDO::FETCH_ASSOC); 

                    // $order_items2_data = $order_items2->fetch();

                    //echo '<pre>';print_r($order_items2_data);

                   // detail_arr['id'] = $order_items2_data['id'];

                     $detail_arr['id'] = $order_items2_data['id'];

                     $resp_arr[$i] = $detail_arr;

                     $i++;
                  }
                  //echo '<pre>';print_r($resp_arr);die;


                  $arrReturn['status'] = '200';
                  $arrReturn['message'] = $resp_arr;
                 
              }

          }

      break;

    
  }
}
else
{
    $arrReturn['status'] = '201';

    $arrReturn['message'] = 'Action is missing.';
}

echo json_encode($arrReturn);
//echo utf8_encode($row);



if(is_resource($conn))

{

    $conn->close();

}



?>