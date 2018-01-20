<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>


<html>
<table border="2">
<th> Table No</th>
<th> Max Capacity</th>
<th> Notes</th>


<th colspan="2">Action</th>

<?php foreach($table as $table_details): ?>

	<tr>
			<td><?php echo $table_details['table_number']; ?></td>

			<td><?php echo $table_details['max_capacity']; ?></td>

			<td>
                                <?php 
                                
                                    if(($table_details['notes']) != '')
                                    {
                                        echo $table_details['notes']; 
                                    }
                                    else
                                    {
                                        echo '--';
                                    }

                                ?>      </td>

			<?php $segments = array('table', 'update', $table_details['table_detail_id']); ?>
			<td><a href="<?php echo site_url($segments);?>">Edit</a></td> 

			<?php $segments = array('table', 'delete', $table_details['table_detail_id']); ?>
			<td><a href="<?php echo site_url($segments);?>">Delete</a></td> 
	</tr>

<?php endforeach;?>
				
</table>
</html>

