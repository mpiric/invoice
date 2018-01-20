<?php

    include_once('constantsFile.php');

    $base_url = $_SERVER['HTTP_HOST'].'/'._PROJECT_FOLDER_NAME_.'/photo_selector_api/api.php?';

?>


<style type="text/css">

.noteCls

{

    color: green;

}

.leftDiv

{

	width:75%; float:left

}

.leftDiv p

{

    text-indent: 50px;

}

.rightDiv

{

	width:25%; float:right

}

a{

	text-decoration: none;

}

</style>



<div>



	<div class="leftDiv" >



		<h2 style="text-align:center"> <?php echo 'PHOTO SELECTOR API'; ?> </h2>



		<div>

		    <h3> 1) category_list</h3>

		    <p>URL : <a href="api.php?action=category_list" target="_blank"><?php echo $base_url; ?>action=category_list</a></p>

		</div>

		<div>

		    <h3> 2) product_category_list</h3>

		    <p>URL : <a href="api.php?action=product_category_list" target="_blank"><?php echo $base_url; ?>action=product_category_list</a></p>

		</div>
		
		<div>

		    <h3> 3) product_list_by_branch_and_category_id</h3>

		    <p>URL : <a href="api.php?action=product_list_by_branch_and_category_id&branch_id=5&product_category_id=1" target="_blank"><?php echo $base_url; ?>action=product_list_by_branch_and_category_id&branch_id=5&product_category_id=1</a></p>

		</div>
		

	</div>



	<div class="rightDiv">



		<h3 style="text-align:center">Status List</h3>



		<div>

			<p>1) 200 : success</p>

			<p>2) 201 : Required parameters are missing</p>

			<p>3) 202 : parameter can not be blank</p>

			<p>4) 203 : error in database operation</p>

			<p>5) 204 : record already exists in database(insertion error)</p>

			<p>6) 205 : invalid parameter</p>

			<p>7) 206 : data not found</p>
			
			<p>8) 207 : invalid number</p>

			<p>9) 208 : invalid user</p>

		</div>



	</div>



</div>