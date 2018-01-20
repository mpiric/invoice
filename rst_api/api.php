<?php

date_default_timezone_set("Asia/Kolkata");

(@include_once('connectionClass.php')) or die('connectionClass file does not found.');

// if($_SERVER['SERVER_NAME']=='localhost')
// {
//     define('AWS_PS_PATH', 'https://media.photoselector.com');
// }
// else
// {
//     define('AWS_PS_PATH', '//media.photoselector.com');
// }


$ConnectionObj = new Connection();

$conn = $ConnectionObj->conn;

$arrReturn = array();

if(isset($_REQUEST['action']))
{
	switch ($_REQUEST['action']) 
    {

        case 'category_list':

            $category = $conn->prepare(" SELECT `category_id`, `cat_name`, `created` FROM `category` ");

            $category->execute();

            $category_list = $category->setFetchMode(PDO::FETCH_ASSOC); 

            $cat_list = $category->fetchAll();

            if(!empty($cat_list))
            {
                $arrReturn['status'] = '200';

                $arrReturn['data'] = $cat_list;
            }
            else
            {
                $arrReturn['status'] = '206';

                $arrReturn['message'] = 'Data not found';
            }

            

        break;

        case 'product_category_list':

            $pro_category = $conn->prepare(" SELECT `p`.`product_category_id`,`p`.`name`, `p`.`created`, `p`.`parent`, `pc`.`name` AS parent_name, `p`.`brand_id` , `b`.`brand_name` FROM product_category p LEFT JOIN product_category pc ON `pc`.`product_category_id` = `p`.`parent` LEFT JOIN brand b ON (p.brand_id = b.brand_id) WHERE `p`.`deleted` IS NULL ");

            $pro_category->execute();

            $pro_category_list = $pro_category->setFetchMode(PDO::FETCH_ASSOC); 

            $product_category_list = $pro_category->fetchAll();

            if(!empty($product_category_list))
            {
                $arrReturn['status'] = '200';

                $arrReturn['data'] = $product_category_list;
            }
            else
            {
                $arrReturn['status'] = '206';

                $arrReturn['message'] = 'Data not found';
            }


        break;

        case 'product_list_by_branch_and_category_id':

            if(!isset($_REQUEST['branch_id']) || !isset($_REQUEST['product_category_id']))
            {
                $arrReturn['status'] = '201';
                $arrReturn['message'] = 'Required parameters are missing.';
            }
            else
            {
                if($_REQUEST['branch_id'] == '' && $_REQUEST['product_category_id'] == '')
                {
                    $arrReturn['status'] = '202';
                    $arrReturn['message'] = 'Parameters cannot be blank';
                }

                else
                {
                    $branch_id = $_REQUEST['branch_id'];
                    $product_category_id = $_REQUEST['product_category_id'];

                    $query = $conn->prepare(" SELECT p.`product_id`, p.`name`, p.`price` FROM `branch_products` bp LEFT JOIN branch b ON b.`branch_id` = bp.`branch_id` LEFT JOIN product p ON p.`product_id` = bp.`product_id` LEFT JOIN product_category pc ON pc.`product_category_id` = p.`product_category_id` WHERE b.`branch_id` = '".$branch_id."' AND p.`product_category_id` = '".$product_category_id."' ");

                    $query->execute();

                    $query->setFetchMode(PDO::FETCH_ASSOC);

                    $result = $query->fetchAll();

                    if(!empty($result))
                    {
                        $arrReturn['status'] = '200';

                        $arrReturn['data'] = $result;
                    }
                    else
                    {
                        $arrReturn['status'] = '206';

                        $arrReturn['message'] = 'Data not found';
                    }


                }
            }

        break;

        case 'branch_tax':

            if(!isset($_REQUEST['branch_id']))
            {
                $arrReturn['status'] = '201';
                $arrReturn['message'] = 'Required parameters are missing.';
            }
            else
            {
                if($_REQUEST['branch_id'] == '')
                {
                    $arrReturn['status'] = '202';
                    $arrReturn['message'] = 'Parameters cannot be blank';
                }

                else
                {
                    $branch_id = $_REQUEST['branch_id'];

                    $query = $conn->query(" SELECT * FROM `tax_master` WHERE `branch_id` = '".$branch_id."' ");

                    $query->execute();

                    $query->setFetchMode(PDO::FETCH_ASSOC);

                    $result = $query->fetchAll();

                    if(!empty($result))
                    {
                        $arrReturn['status'] = '200';

                        $arrReturn['data'] = $result;
                    }
                    else
                    {
                        $arrReturn['status'] = '206';

                        $arrReturn['message'] = 'Data not found';
                    }

                }
            }

        break;

        case 'create_bill':

            if(!isset($_REQUEST['branch_id']) || !isset($_REQUEST['brand_id']) ||!isset($_REQUEST['sub_brand_id']) ||!isset($_REQUEST['order_type']) ||!isset($_REQUEST['order_code']) ||!isset($_REQUEST['payment_type']) ||!isset($_REQUEST['sub_total']) ||!isset($_REQUEST['total_amount']) ||!isset($_REQUEST['round_off_total_amount']) ||!isset($_REQUEST['given_amount']) ||!isset($_REQUEST['return_amount']) ||!isset($_REQUEST['discount_type']) ||!isset($_REQUEST['discount_amount']) ||!isset($_REQUEST['is_print']) ||!isset($_REQUEST['table_detail_id']) )
            {
                $arrReturn['status'] = '201';
                $arrReturn['message'] = 'Required parameters are missing.';
            }
            else
            {

                if($_REQUEST['branch_id']=='' && $_REQUEST['brand_id']=='' && $_REQUEST['sub_brand_id']=='' && $_REQUEST['order_type']=='' && $_REQUEST['order_code']=='' && $_REQUEST['payment_type']=='' && $_REQUEST['sub_total']=='' && $_REQUEST['total_amount']=='' && $_REQUEST['round_off_total_amount']=='' && $_REQUEST['given_amount']=='' && $_REQUEST['return_amount']=='' && $_REQUEST['discount_type']=='' && $_REQUEST['discount_amount']=='' && $_REQUEST['is_print']=='' && $_REQUEST['table_detail_id']=='' )
                {
                    $arrReturn['status'] = '202';
                    $arrReturn['message'] = 'Parameters cannot be blank'; 
                }
                else
                {
                    $branch_id = $_REQUEST['branch_id'];
                    $brand_id = $_REQUEST['brand_id']; 
                    $sub_brand_id = $_REQUEST['sub_brand_id']; 
                    $order_type = $_REQUEST['order_type']; 
                    $order_code = $_REQUEST['order_code']; 
                    $payment_type = $_REQUEST['payment_type'];
                    $table_detail_id = $_REQUEST['table_detail_id'];  
                    $sub_total = $_REQUEST['sub_total']; 
                    $total_amount = $_REQUEST['total_amount'];
                    $round_off_total_amount = $_REQUEST['round_off_total_amount']; 
                    $given_amount = $_REQUEST['given_amount']; 
                    $return_amount = $_REQUEST['return_amount']; 
                    $discount_type = $_REQUEST['discount_type']; 
                    $discount_amount = $_REQUEST['discount_amount']; 
                    $is_print = $_REQUEST['is_print']; 
                    

                    $ins = $conn->prepare(" INSERT INTO `order_detail`( `branch_id`, `brand_id`, `sub_brand_id`, `order_type`, `order_code`, `payment_type`, `table_detail_id`, `sub_total`, `total_amount`, `round_off_total_amount`, `given_amount`, `return_amount`, `discount_type`, `discount_amount`, `is_print`) VALUES (:branch_id, :brand_id, :sub_brand_id, :order_type, :order_code, :payment_type, :table_detail_id, :sub_total, :total_amount, :round_off_total_amount, :given_amount, :return_amount, :discount_type, :discount_amount, :is_print ) ");

                    $ins->bindParam(':branch_id',$branch_id);
                    $ins->bindParam(':brand_id',$brand_id);
                    $ins->bindParam(':sub_brand_id',$sub_brand_id);
                    $ins->bindParam(':order_type',$order_type);
                    $ins->bindParam(':order_code',$order_code);
                    $ins->bindParam(':payment_type',$payment_type);
                    $ins->bindParam(':table_detail_id',$table_detail_id);
                    $ins->bindParam(':sub_total',$sub_total);
                    $ins->bindParam(':total_amount',$total_amount);
                    $ins->bindParam(':round_off_total_amount',$round_off_total_amount);
                    $ins->bindParam(':given_amount',$given_amount);
                    $ins->bindParam(':return_amount',$return_amount);
                    $ins->bindParam(':discount_type',$discount_type);
                    $ins->bindParam(':discount_amount',$discount_amount);
                    $ins->bindParam(':is_print',$is_print);

                    if($ins->execute())
                    {
                        $arrReturn['status'] = '200';
                        $arrReturn['message'] = 'Order inserted Successfully';
                    }
                    else
                    {
                        $arrReturn['status'] = '206';
                        $arrReturn['message'] = 'Error in insert';
                    }


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

if(is_resource($conn))

{

    $conn->close();

}

?>
                        



                        

                        






                        

                                


                                
                        










        

