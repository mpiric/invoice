<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
$this->load-> helper('form');
$this->load->library('session');
?>
<!DOCTYPE html>
<html lang="en">

<body>

	<?php echo validation_errors(); ?>

	<?php //echo '<pre>'; print_r($details); die;?>
	<?php $controller_function = $this->router->fetch_method(); ?>

	<?php if($controller_function=='create') { ?>
	    <?php echo form_open_multipart('table/create'); ?>
	<?php } ?>

	<?php if($controller_function=='update') { ?>
	    <?php echo form_open_multipart('table/update/'.$details['table_detail_id']); ?>
	<?php } 

	?>

<div id="container">
	<b>Table Details</b>
</div>
<br>

	<?php $sess_arr=$this->session->userdata('logged_in');//echo '<pre>'; print_r($sess_arr);  ?>

		
		<label for="table_number">Table No:</label>
		<input type="text" name="table_number" <?php if(isset($details['table_number']) && $details['table_number']!='') { ?> value="<?php echo $details['table_number']; ?>" <?php } ?> required><br />

		<br>
		<label for="max_capacity">Max. Capacity:</label>
		<input type="text" name="max_capacity" <?php if(isset($details['max_capacity']) && $details['max_capacity']!='') { ?> value="<?php echo $details['max_capacity']; ?>" <?php } ?> required><br />
		<br>

		<label for="notes">Notes:</label>
		<input type="text" name="notes" <?php if(isset($details['notes']) && $details['notes']!='') { ?> value="<?php echo $details['notes']; ?>" <?php } ?> ><br />
		<br>
		
 <input type="submit" name="submit" value="Submit Details">

</form>

</body>
</html>	