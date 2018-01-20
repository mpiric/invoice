<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
$this->load-> helper('form');
?>

<div class="row" ng-controller="waiterCreateCtrl">

<section id="page-title">
	<div class="row">
		<div class="col-sm-8">
			<h1 class="mainTitle">{{heading}} Waiter</h1>
			<!-- <span class="mainDescription"></span> -->
		</div>
		<!-- <div ncy-breadcrumb></div> -->
	</div>
</section>
	
<div style="margin:10px" id="validation_err">
	
</div>

	

<br>

	
	<form action="javascript:void(0)" name="create_waiter" novalidate="novalidate" ng-submit="createWaiter(create_waiter)" method="post" >

	<div class="col-md-6">
		<fieldset>
			<legend>
				Waiter Details
			</legend>

			<!-- <div ng-show="!show_branch_dd">
				<input type="hidden" name="branch_id" ng-model="branch_id">
			</div> -->

			<div ng-show="show_branch_dd" class="form-group">
				<label for="branch_id"  class="control-label">Branch: <span class="symbol required"></span></label>
				 <select class="form-control" name="branch_id" ng-options="item as item.name for item in branch_list track by item.branch_id" ng-model="branch_id">

    			</select>
			</div>

			

			<div class="form-group">
			<label for="firstname"  class="control-label">Waiter Firstname: <span class="symbol required"></span></label>
				<input type="text" class="form-control" ng-model="firstname" id="firstname" placeholder="Waiter Firstname" name="firstname" required >

			    <div class="has-error" ng-show="create_waiter.$submitted || create_waiter.firstname.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_waiter.firstname.$error.required">Waiter First Name is required.</span>
			    </div>
			</div>

			<div class="form-group">
			<label for="lastname"  class="control-label">Waiter Lastname: <span class="symbol required"></span></label>
				<input type="text" class="form-control" ng-model="lastname" id="lastname" placeholder="Waiter Username" name="lastname"  required >

			    <div class="has-error" ng-show="create_waiter.$submitted || create_waiter.lastname.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_waiter.lastname.$error.required">Waiter Last Name  is required.</span>
			    </div>
			</div>
			

			<div class="form-group">
			<label for="contact"  class="control-label">Waiter Contact: <span class="symbol required"></span></label>
				<input type="text" class="form-control" ng-model="contact" ng-pattern="/^[0-9]{1,10}$/" id="contact" placeholder="Waiter Contact" name="contact" required >

			    <div class="has-error" ng-show="create_waiter.$submitted || create_waiter.contact.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_waiter.contact.$error.required">Waiter Contact is required.</span>
			       <span class="error text-small block ng-scope" ng-show="create_waiter.contact.$error.pattern">Not a valid number!</span>
			    </div>
			</div>

			<div class="form-group">
			<label for="email"  class="control-label">Waiter Email: <span class="symbol required"></span></label>
				<input type="email" class="form-control" ng-model="email" id="email" placeholder="Waiter Email" name="email" required >

			    <div class="has-error" ng-show="create_waiter.$submitted || create_waiter.email.$touched">
				  <span class="error text-small block ng-scope" ng-show="create_waiter.email.$error.required">Email is required.</span>
				  <span class="error text-small block ng-scope" ng-show="create_waiter.email.$error.email">Not a valid email.</span>
				</div>

			</div>

			<div class="form-group">
			<label for="email"  class="control-label">Password: <span class="symbol required"></span></label>
				<input type="password" class="form-control" ng-model="password" id="password" placeholder="Password" name="password" required >

			    <div class="has-error" ng-show="create_waiter.$submitted || create_waiter.email.$touched">
				  <span class="error text-small block ng-scope" ng-show="create_waiter.password.$error.required">Password is required.</span>
				  
				</div>

			</div>

			<div class="form-group">
			<label for="waiter_code"  class="control-label">Waiter Code: <span class="symbol required"></span></label>
				<input type="text" class="form-control" ng-model="waiter_code" id="waiter_code" placeholder="Contact Person Name" name="waiter_code" required >

			    <div class="has-error" ng-show="create_waiter.$submitted || create_waiter.waiter_code.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_waiter.waiter_code.$error.required">Waiter Code is required.</span>
			    </div>
			</div>

		
		</fieldset>
	
	</div>

	<div class="col-md-6">
		<fieldset>
			<legend>
				Address Details
			</legend>

			<div class="form-group">
				<label for="select-country"  class="control-label">Country: <span class="symbol required"></span></label>				
					<?php   
							//	$sqlAllCountries = "SELECT * FROM `location` WHERE `location_type` =0";
							$sqlAllCountries = $this->db->get_where('location', array('location_type' => 0));
					        $sqlAllCountriesResult = $sqlAllCountries->result_object();
			        ?>
					<select name="country" class="form-control" id="select-country"  ng-model="country" onchange="ajax_call('ajaxCall',{location_id:this.value,location_type:1}, 'state')" required>
			                <option value="">Select Country</option>
			                <?php
			                foreach ($sqlAllCountriesResult as $CountryDetails) {
			                    echo '<option value="' . $CountryDetails->location_id . '">' . $CountryDetails->name . '</option>';
			                }
			                ?>
			        </select>
			    <div class="has-error" ng-show="create_waiter.$submitted || create_waiter.country.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_waiter.country.$error.required">Country is required.</span>
			    </div>
			</div>

			<div class="form-group">
				<label for="state"  class="control-label">State: <span class="symbol required"></span></label>			
		        <select name="state" class="form-control" ng-model="state" id="state" onchange="ajax_call('ajaxCall',{location_id:this.value,location_type:2}, 'city')" required>
		        </select>
		        <div class="has-error" ng-show="create_waiter.$submitted || create_waiter.state.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_waiter.state.$error.required">State is required.</span>
			    </div>
		    </div>

		    <div class="form-group">
			    <label for="city" class="control-label">City: <span class="symbol required"></span></label>
		        <select name="city" class="form-control" id="city" ng-model="city" required>
		        </select>
		        <div class="has-error" ng-show="create_waiter.$submitted || create_waiter.city.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_waiter.city.$error.required">City is required.</span>
			    </div>
	        </div>


			<div class="form-group">
				<label for="address"  class="control-label">Address: <span class="symbol required"></span></label>
				<input type="text" class="form-control" ng-model="address" id="address" placeholder="Waiter Address" name="address"  required >
			    <div class="has-error" ng-show="create_waiter.$submitted || create_waiter.address.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_waiter.address.$error.required">Waiter address is required.</span>
			    </div>
			</div>

			<div class="form-group">
			<label for="pincode"  class="control-label">Pincode: <span class="symbol required"></span></label>
				<input type="text" class="form-control" ng-model="pincode" ng-pattern="/^[0-9]{1,6}$/" id="pincode" placeholder="Waiter Pincode" name="pincode" required >

			    <div class="has-error" ng-show="create_waiter.$submitted || create_waiter.pincode.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_waiter.pincode.$error.required">Waiter Pincode is required.</span>
			      <span class="error text-small block ng-scope" ng-show="create_waiter.pincode.$error.pattern">Not a valid pincode!</span>
			    </div>
			</div>

		</fieldset>


		<button type="submit" class="btn btn-primary" ng-disabled="create_waiter.$invalid" style="float:right" >Submit</button>

	</div>	

</div>


</form>


<script type="text/javascript">
            
            // Change Your home URL..
            //home_url = <?php //echo base_url();?>;
            
            /* *
             *     fileName - ajax file name to be called by ajax method.
             *     data - pass the infromation(like location-id , location-type) via data variable.
             *     loadDataToDiv - id of the div to which the ajax responce is to be loaded.
             * */

             function getParameterByName(name, url) {
			    if (!url) {
			     url = window.location.href;
			    }
			    var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(url);
			    if (!results) { 
			        return undefined;
			    }
			    return results[1] || undefined;
			}

            function ajax_call(fileName,data, loadDataToDiv) {

            	// alert('fileName'+fileName+'data'+data+'loadDataToDiv'+loadDataToDiv);
            	// console.log(data);

            	var $ = jQuery; 
            	var waiter_id = getParameterByName('waiter_id');
            	data.waiter_id = waiter_id;
            	//console.log(data);

            	//alert(waiter_id);
                jQuery("#"+loadDataToDiv).html('<option selected="selected">-- -- -- Loding Data -- -- --</option>');

                //  If you are changing counrty, make the state and city fields blank
                if(loadDataToDiv=='state'){
                    jQuery('#city').html('');
                    jQuery('#state').html('');                    
                }
                //  If you are changing state, make the city fields blank
                if(loadDataToDiv=='city'){
                    jQuery('#city').html('');
                }
                var home_url = 'index.php/waiter/get_state_and_city';

                jQuery.post(home_url + '/', data, function(result) {
                    jQuery('#' + loadDataToDiv).html(result);
                });
            }
        </script>