<?php
	//daily_sales_cron.php

	//connection of database
// Report all PHP errors
	//error_reporting(-1);
	date_default_timezone_set("Asia/Kolkata");

	(@include_once('dbconnection.php')) or die('connectionClass file does not found.');

	$ConnectionObj = new Connection();

	$conn = $ConnectionObj->conn;

	//current date

	// $query = $conn->prepare(" SELECT CURDATE() as today");

	// $query->execute();

	// $result = $query->setFetchMode(PDO::FETCH_ASSOC); 

	// $date = $query->fetch();

	// //$current_date = $date['today'];


	
	$current_date = date('Y-m-d', strtotime('-2 days', strtotime(date("Y-m-d"))));
	//$current_date = '2017-07-01';

	$selected_branch = "5,6,8"; //insert branch id here

	$array=array_map('intval', explode(',', $selected_branch));
	$branch_array = implode("','",$array);

	//branch list

	$query = $conn->prepare(" SELECT * FROM branch WHERE branch_id IN ('".$branch_array."')");

	$query->execute();

	$result = $query->setFetchMode(PDO::FETCH_ASSOC); 

	$branch_list = $query->fetchAll();

	
	

	foreach ($branch_list as $branch) 
	{
		$final = array();
		$j = 0;
		$branch_type = $branch['branch_type'];
		$branch_id = $branch['branch_id'];

        if($branch_type!=1)
        {
           
			$daily_result = dailySalesList($conn,$branch_id,$current_date);
			//print_r($daily_result); 

			//tax list
			$taxSum = 0;
			$round_off_value_total = 0;
			$order_tax_list = array();
			$tax_list = tax_list($conn); 

			//get orders by branch 

			$orderList = getOrdersbyBranch($conn,$current_date,$branch_id);
			//echo "<pre>";print_r($orderList);die;

			foreach ($orderList as $order_data) 
			{

				$order_id = $order_data['order_id'];

				//get order items by order_id
				$orderItemDetails = getOrderItemsByOrderId($conn,$order_id);
				//echo "<pre>";print_r($orderItemDetails);

				$sub_total = $orderItemDetails['sub_total'];
				$discount = $orderItemDetails['discount'];

				//get tax on order items by order_id
				$amnt = $sub_total-$discount;
				$order_tax_data = order_tax_data($conn,$order_id,$amnt);
				//echo "<pre>";print_r($order_tax_data);

				$order_tax_list2 = array();
                
                foreach ($order_tax_data as $tax_data) 
                {
                	
                    $order_tax_list2[$tax_data['tax_id']] = $tax_data;
                }

                //
                $taxSum2   = 0;
                $order_tax = $order_tax_data;

                foreach ($tax_list as $column) 
                {
                	
                    $col_tax_id = $column['tax_id'];
                    
                    if (!empty($order_tax)) 
                    {
                        if (!empty($order_tax_list2[$col_tax_id])) 
                        {
                            $taxSum2 += $order_tax_list2[$col_tax_id]['tax_amount'];
                            
                        }
                    }
                    
                }

                $billAmount    = (float) ($sub_total) + $taxSum2 - ((float) ($discount));
                $roundOff      = round((float) ($billAmount));
                $roundoffValue = number_format(($roundOff - (float) ($billAmount)), 2);
                
                $round_off_value_total += $roundoffValue;
                //echo "<pre>";print_r($order_tax_list2);
			}
			
			$field_cond = '';
			$val_cond = '';
			foreach ($tax_list as $tax) 
			{
				
				// echo '<pre>';print_r($tax);
                $tax_id   = $tax['tax_id'];
                // get tax data by date and tax_id
                $tax_data = get_tax_data_by_date_and_tax_id_daily_sales($conn,$current_date, $tax_id, $branch_id);


                //echo '<pre>';print_r($tax_data);
                
                
                if (array_filter($tax_data)) 
                {

                	//echo '<pre>';print_r($tax_data);
                	$order_tax_list[$tax_id] = $tax_data;
          
                    if (!empty($order_tax_list[$tax_id])) 
                    {
	  					$tax_name = $order_tax_list[$tax_id]['tax_name'];

	  					$tax_name = str_replace(' ', '', $tax_name); // Replaces all spaces with hyphens.
						$tax_name = preg_replace('/[^A-Za-z0-9\-]/', '', $tax_name); // Removes special chars.
						$tax_name =  preg_replace('/-+/', '-', $tax_name); // Replaces multiple hyphens with single one.

						//check if column exists or not
						$column_query = $conn->prepare(" SHOW COLUMNS FROM `daily_sales` WHERE `field` = '".$tax_name."' ");

						$column_query->execute();

						$column_query_result = $column_query->setFetchMode(PDO::FETCH_ASSOC); 

						$column_query_row = $column_query->rowCount();

	                	//echo "<pre>";print_r($column_query_row);
	                	//echo "branch_id_tax =".$branch_id_tax = $order_tax_list[$tax_id]['branch_id'];

						if($column_query_row == '0' || $column_query_row == 0)
						{
							//  column does not exists add to table
		                		if($tax_name != '')
		                		{
		                			try
		                			{
		                				$sql = "ALTER TABLE `daily_sales` ADD COLUMN `".$tax_name."` DOUBLE(10,2) NOT NULL" ;

				                		//echo $sql; die;
				                		$conn->query($sql);
		                			}
		                			catch (PDOException $e) 
				                	{
									    print $e->getMessage ();
									}
		                			
			                		
		                		}
						}
						//now get details from column

						if(isset($daily_result['branch_id']))
						{
							$final[$tax_name] = $order_tax_list[$tax_id]['tax_amount'];

							//echo $tax_name;

							$field_cond .= ','.$tax_name;
							$val_cond .= ','.$order_tax_list[$tax_id]['tax_amount'];
						}


                        $taxSum += $order_tax_list[$tax_id]['tax_amount'];
                    }
                }

               
            }

            $daily_result['bill_amount']    = (float) ($daily_result['sub_total']) + (float) ($taxSum) - ((float) ($daily_result['discount']));
            $daily_result['roundoff']       = round($daily_result['bill_amount'] + $round_off_value_total);
            $daily_result['roundoff_value'] = $round_off_value_total;







            //values to insert in the table

            if(isset($daily_result['branch_id']))
            {
            	$final['roundoff_value'] = isset($daily_result['roundoff_value']) ? $daily_result['roundoff_value'] : 0.00;
	            $final['sub_total']  = isset($daily_result['sub_total']) ? $daily_result['sub_total'] : 0;
	            $final['tax_free']    = isset($daily_result['tax_free']) ? $daily_result['tax_free'] : 0;
	            $final['discount']   = isset($daily_result['discount']) ? $daily_result['discount'] : 0;
	            $final['bill_amount'] = isset($daily_result['bill_amount']) ? $daily_result['bill_amount'] : 0;
	            $final['roundoff']  = isset($daily_result['roundoff']) ? $daily_result['roundoff'] : 0;
	            $final['created']  =  date("Y-m-d");
	            //$final['created']  =  '2017-07-01';

            	$final['branch_id'] = isset($daily_result['branch_id']) ? $daily_result['branch_id'] : 0; 

            	//$order_tax_list_cnt = count($order_tax_list);

	           // if($order_tax_list_cnt>0)
	           // {
	           // 	$final['order_tax'] = $order_tax_list;
	           // }

	           //$details = $final;
            	

            	//echo $field_cond;
            
            	//echo $val_cond;
            	
				// get values from daily sales table

				$qry = $conn->prepare(" SELECT * FROM daily_sales WHERE branch_id='".$branch_id."' AND created = '".$current_date."'  ");

				$qry->execute();

				$result = $qry->setFetchMode(PDO::FETCH_ASSOC); 

				$qry_result = $qry->rowCount();

				if($qry_result=='0')
				{
					//echo "Insert";
					$ins = "INSERT INTO `daily_sales` (branch_id,created,net_amount,tax_free,discount,bill_amount,round_off,total".$field_cond.") VALUES (".$final['branch_id'].",'".$final['created']."',".$final['sub_total'].",".$final['tax_free'].",".$final['discount'].",".$final['bill_amount'].",".$final['roundoff_value'].",".$final['roundoff']."".$val_cond.");";

	            	//echo $ins;

					$ins_qry = $conn->prepare($ins);

			        $ins_qry->execute();

			        echo "<pre>";print_r($final);
	            
		            $j++;

				}
				else
				{
					//echo "Update";
					$field_cond =  str_replace(',', ' ', $field_cond);
	            	$field_cond = explode(' ', $field_cond);
	            	array_splice($field_cond, 0,1);

	            	$val_cond = str_replace(',', ' ', $val_cond);
	            	$val_cond = explode(' ', $val_cond);
	            	array_splice($val_cond, 0,1);

	            	$combine  = array_combine($field_cond, $val_cond);
	            	//print_r($combine);

	            	$str="";

	            	foreach ($combine as $key => $value) 
	            	{
	            		$str .= ",".$key."="."'".$value."'";
	            	}

					$update = "UPDATE `daily_sales` SET branch_id=".$final['branch_id'].", created ='".$final['created']."', net_amount=".$final['sub_total'].", tax_free=".$final['tax_free'].", discount=".$final['discount'].", bill_amount=".$final['bill_amount'].", round_off=".$final['roundoff_value'].", total=".$final['roundoff']."".$str." WHERE branch_id='".$branch_id."' AND created = '".$current_date."' ";

	            	// echo $update;
	            	// die;

					$update_qry = $conn->prepare($update);

			        $update_qry->execute();

			        echo "<pre>";print_r($final);
	            
		            $j++;

				}

	            
            }
            

           //echo '<pre>';print_r($order_tax_list);

         
        }

	}

	
	function dailySalesList($conn,$branch_id,$current_date)
	{

        $daily_sales_query = $conn->prepare('SELECT CAST( SUM(  CASE
							                  WHEN (ROUND(o.total_amount)) < o.total_amount
							                          THEN ROUND(o.total_amount) - o.total_amount
							                   WHEN (ROUND(o.total_amount)) > o.total_amount
							                          THEN CONCAT("+", ROUND(o.total_amount) - o.total_amount)
							                   WHEN (ROUND(o.total_amount)) = o.total_amount
							                          THEN  ROUND(o.total_amount) - o.total_amount
							                      END ) AS DECIMAL(10,2) )  AS roundoff_value,        
							                SUM((SELECT sum(quantity*price) from order_items where order_id=o.order_id)) as sub_total,
							                ( SUM((SELECT SUM(quantity*price) FROM order_items WHERE order_id=o.order_id AND
							                ( o.order_type=2 ) )) ) AS tax_free,
							                SUM(((SELECT sum(quantity*price) from order_items where order_id=o.order_id) * o.discount_amount/100)) as discount,      
							                       SUM(o.total_amount) AS bill_amount,  
							                       SUM(ROUND(o.total_amount)) AS roundoff,o.branch_id AS branch_id
							                FROM order_detail o                
							                LEFT JOIN branch b ON b.branch_id = o.branch_id              
							                WHERE DATE( o.order_date_time ) = "'.$current_date.'" AND o.branch_id="'.$branch_id.'" AND o.is_print=1 AND o.table_detail_id != 0
							                GROUP BY DATE(o.order_date_time)' );


        $daily_sales_query->execute();

		$daily_sales_result = $daily_sales_query->setFetchMode(PDO::FETCH_ASSOC); 

		 $daily_sales_query_result = $daily_sales_query->fetch();

		 return $daily_sales_query_result;
        
	}

	function tax_list($conn)
	{
		$taxquery = $conn->prepare(" SELECT CASE WHEN ( tmas.tax_percent IS NULL ) THEN tm.tax_percent 
									ELSE tmas.tax_percent END AS tax_percent,t.tax_name,t.tax_id 
									FROM tax_main t
				                    LEFT JOIN  tax_master tm ON tm.tax_id = t.tax_id
				                    LEFT JOIN tax_master tmas ON tmas.branch_tax_id = t.tax_id
				                    WHERE t.deleted IS NULL GROUP BY t.tax_id  ");

		$taxquery->execute();

		$result = $taxquery->setFetchMode(PDO::FETCH_ASSOC); 

		return $tax_list = $taxquery->fetchAll();
	}

	function getOrdersbyBranch($conn,$date,$branch_id)
	{
		$orderquery = $conn->prepare( 'SELECT * FROM order_detail o 
									LEFT JOIN branch b ON b.branch_id = o.branch_id 
									WHERE DATE( o.order_date_time ) = "'.$date.'" 
									AND o.branch_id = "'.$branch_id.'" AND o.is_print=1 ');

		$orderquery->execute();

		$result = $orderquery->setFetchMode(PDO::FETCH_ASSOC); 

		return $orderList = $orderquery->fetchAll();
	}
	
	function getOrderItemsByOrderId($conn,$order_id)
	{
		$orderItemsquery = $conn->prepare( 'SELECT SUM(oi.quantity*oi.price) AS sub_total , 
										( (SUM(oi.quantity*oi.price)) * o.discount_amount/100) AS discount
										FROM order_items oi
										JOIN order_detail o ON (oi.order_id = o.order_id)
										WHERE o.order_id = "'.$order_id.'"  ');

		$orderItemsquery->execute();

		$result = $orderItemsquery->setFetchMode(PDO::FETCH_ASSOC); 

		return $orderItemList = $orderItemsquery->fetch();
	}

	function order_tax_data($conn,$order_id, $amnt)
	{
		//$amnt = $sub_total - $discount;

		$orderItemsTaxquery = $conn->prepare( ' SELECT  ot.*,tm.tax_name,ot.order_id, 
												CAST((ot.tax_percent*"'.$amnt.'")/100 AS DECIMAL(6,2)) AS tax_amount FROM order_tax ot  
												LEFT JOIN tax_main tm ON tm.tax_id = ot.tax_id 
												LEFT JOIN order_detail od ON od.order_id = ot.order_id
												WHERE  ot.order_id="'.$order_id.'"
												GROUP BY ot.tax_id ');

		$orderItemsTaxquery->execute();

		$result = $orderItemsTaxquery->setFetchMode(PDO::FETCH_ASSOC); 

		return $orderItemsTax = $orderItemsTaxquery->fetchAll();


	}

	function get_tax_data_by_date_and_tax_id_daily_sales($conn,$date, $tax_id, $branch_id)
	{
		$taxquery = $conn->prepare(" SELECT ot.*,(SELECT SUM(quantity*price) FROM order_items 
									WHERE order_id=o.order_id) AS subTotal, SUM( ( ((SELECT SUM(quantity*price) FROM order_items WHERE order_id=o.order_id)-((SELECT SUM(quantity*price) FROM order_items WHERE order_id=o.order_id) * o.discount_amount/100)) *ot.tax_percent)/100 ) AS tax_amount, tm.tax_name, o.branch_id AS branch_id 
									FROM order_detail o 
									LEFT JOIN order_tax ot ON (o.order_id = ot.order_id) 
									LEFT JOIN tax_main tm ON tm.tax_id = ot.tax_id 
									WHERE DATE(o.order_date_time)='".$date."' AND o.branch_id='".$branch_id."'  AND ot.tax_id='".$tax_id."' ");

		$taxquery->execute();

		$result = $taxquery->setFetchMode(PDO::FETCH_ASSOC); 

		return $tax_data = $taxquery->fetch();
	}

	function get_data_from_daily_sales($conn,$date,$branch_id)
	{
		$qry = $conn->prepare(" SELECT * FROM daily_sales WHERE branch_id='".$branch_id."' AND created = '".$date."'  ");

		$qry->execute();

		$result = $qry->setFetchMode(PDO::FETCH_ASSOC); 
		$email_qry_result = $email_qry->rowCount();

		return $dailySalesData = $qry->fetch();


	} 



?>