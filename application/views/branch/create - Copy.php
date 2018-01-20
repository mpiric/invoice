<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
$this->load-> helper('form');
?>

<div class="row" ng-controller="branchCreateCtrl">

<section id="page-title">
	<div class="row">
		<div class="col-sm-8">
			<h1 class="mainTitle">{{heading}} Branch</h1>
			<!-- <span class="mainDescription"></span> -->
		</div>
		<!-- <div ncy-breadcrumb></div> -->
	</div>
</section>
	
<div style="margin:10px" id="validation_err">
	
</div>

	

<br>


	<?php //echo validation_errors(); ?>

	<?php //echo '<pre>'; print_r($details); die;?>
	<?php // $controller_function = $this->router->fetch_method(); ?>

	<?php // if($controller_function=='create') { ?>
	<!-- <form action="javascript:void(0)" name="create_branch" novalidate="novalidate" ng-submit="createBranch(create_branch)" method="post" > -->
	    <?php // echo form_open('#',array('name'=>'create_branch','novalidate'=>'novalidate','ng-submit'=>'createBranch(create_branch)')); ?>
	<?php //} ?>

	<?php //if($controller_function=='update') { ?>
	    <?php //echo form_open('branch/update/'.$details['branch_id'],array('name'=>'create_branch','novalidate'=>'novalidate','ng-submit'=>'createBranch(create_branch)')); ?>
	<?php // } ?>
	<form action="javascript:void(0)" name="create_branch" novalidate="novalidate" ng-submit="createBranch(create_branch)" method="post" >

	<div class="col-md-6">
		<fieldset>
			<legend>
				Branch Details
			</legend>
			<div class="form-group">
			<label for="name"  class="control-label">Branch Name: <span class="symbol required"></span></label>
				<input type="text" class="form-control" ng-model="name" id="name" placeholder="Branch Name" name="name" required >

			    <div class="has-error" ng-show="create_branch.$submitted || create_branch.name.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_branch.name.$error.required">Branch Name is required.</span>
			    </div>
			</div>

			<div class="form-group">
			<label for="username"  class="control-label">Username: <span class="symbol required"></span></label>
				<input type="text" class="form-control" ng-model="username" id="username" placeholder="Branch Username" name="username"  required >

			    <div class="has-error" ng-show="create_branch.$submitted || create_branch.username.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_branch.username.$error.required">Username is required.</span>
			    </div>
			</div>

			<div class="form-group" ng-if="is_create">
			<label for="password"  class="control-label">Password: <span class="symbol required"></span></label>
				<input type="password" class="form-control" ng-model="password" id="password" placeholder="Branch Password" name="password" required >

			    <div class="has-error" ng-show="create_branch.$submitted || create_branch.password.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_branch.password.$error.required">Password is required.</span>
			    </div>
			</div>
			<!-- <input type="hidden" ng-model="is_create"> -->

			<div class="form-group">
			<label for="contact"  class="control-label">Branch Contact: <span class="symbol required"></span></label>
				<input type="text" class="form-control" ng-model="contact" ng-pattern="/^[0-9]{1,10}$/" id="contact" placeholder="Branch Contact" name="contact" required >

			    <div class="has-error" ng-show="create_branch.$submitted || create_branch.contact.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_branch.contact.$error.required">Branch Contact is required.</span>
			       <span class="error text-small block ng-scope" ng-show="create_branch.contact.$error.pattern">Not a valid number!</span>
			    </div>
			</div>

			<div class="form-group">
				<label for="email"  class="control-label">Branch Email: <span class="symbol required"></span></label>
				<input type="email" class="form-control" ng-model="email" id="email" placeholder="Branch Email" name="email" required >

			    <div class="has-error" ng-show="create_branch.$submitted || create_branch.email.$touched">
				  <span class="error text-small block ng-scope" ng-show="create_branch.email.$error.required">Email is required.</span>
				  <span class="error text-small block ng-scope" ng-show="create_branch.email.$error.email">Not a valid email.</span>
				</div>

			</div>



			<div class="form-group">
				<label for="no_of_tables"  class="control-label">Number of Tables: <span class="symbol required"></span></label>
				<input type="text" class="form-control" ng-model="no_of_tables" id="no_of_tables" placeholder="Enter Number of Tables" name="no_of_tables" required >

			    <div class="has-error" ng-show="create_branch.$submitted || create_branch.no_of_tables.$touched">
				  <span class="error text-small block ng-scope" ng-show="create_branch.no_of_tables.$error.required">Number of Tables is required.</span>
				</div>

			</div>

			<div class="form-group" >
				<label for="brand_name" class="control-label">Brand: </label>
			
	    			<ui-select multiple ng-model="selected.selectedBrand" name="selectedBrand" theme="bootstrap" ng-disabled="disabled">
						<ui-select-match placeholder="Select brand" >
							{{$item.brand_name}}
						</ui-select-match>
						<ui-select-choices repeat="brand in brand_list | propsFilter: {brand_name: $select.search}">
							<div ng-bind-html="brand.brand_name | highlight: $select.search"></div>
							
						</ui-select-choices>
					</ui-select>
				
			</div>

<!-- 			<div class="form-group">
		        <label for="brand_id"  class="control-label">Brand: <span class="symbol required"></span></label>
		         <select class="form-control" id="brand_id" name="brand_id" ng-options="item as item.brand_name for item in brand_list track by item.brand_id" ng-model="brand_id" required>
		         <option value="">Select Brand</option>
		         <!-- <option value="" ng-if ="false">Select Brand</option> 
		          </select>
		          <div class="has-error" ng-show="create_branch.$submitted || create_branch.brand_id.$touched">
		            <span class="error text-small block ng-scope" ng-show="create_branch.brand_id.$error.required">Brand is required.</span>
		          </div>
		      </div> -->


			<!-- </div> -->

			<div class="form-group">
			<label for="service_tax_number"  class="control-label">CIN number: </span></label>
				<input type="text" class="form-control" ng-model="service_tax_number" id="service_tax_number" placeholder="CIN Number" name="service_tax_number" >

			  <!--   <div class="has-error" ng-show="create_branch.$submitted || create_branch.service_tax_number.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_branch.service_tax_number.$error.required">Service Tax Number is required.</span>
			    </div> -->
			</div>

			<div class="form-group">
			<label for="other_number" class="control-label">GSTIN number: </span></label>
				<input type="text" class="form-control" ng-model="other_number" id="other_number" placeholder="GSTIN Number" name="other_number" >
			</div>

			<div class="form-group">
			<label for="contact_person_name"  class="control-label">Contact Person Name: <span class="symbol required"></span></label>
				<input type="text" class="form-control" ng-model="contact_person_name" id="contact_person_name" placeholder="Contact Person Name" name="contact_person_name" required >

			    <div class="has-error" ng-show="create_branch.$submitted || create_branch.contact_person_name.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_branch.contact_person_name.$error.required">Contact Person Name is required.</span>
			    </div>
			</div>

			<div class="form-group">
			<label for="contact_person_phone"  class="control-label">Contact Person Phone: <span class="symbol required"></span></label>
				<input type="text" class="form-control" ng-model="contact_person_phone" ng-pattern="/^[0-9]{1,10}$/" id="contact_person_phone" placeholder="Contact Person Phone" name="contact_person_phone" required >

			    <div class="has-error" ng-show="create_branch.$submitted || create_branch.contact_person_phone.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_branch.contact_person_phone.$error.required">Contact Person Phone is required.</span>
			      <span class="error text-small block ng-scope" ng-show="create_branch.contact_person_phone.$error.pattern">Not a valid number!</span>
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
			    <div class="has-error" ng-show="create_branch.$submitted || create_branch.country.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_branch.country.$error.required">Country is required.</span>
			    </div>
			</div>

			<div class="form-group">
				<label for="state"  class="control-label">State: <span class="symbol required"></span></label>			
		        <select name="state" class="form-control" ng-model="state" id="state" onchange="ajax_call('ajaxCall',{location_id:this.value,location_type:2}, 'city')" required>
		        </select>
		        <div class="has-error" ng-show="create_branch.$submitted || create_branch.state.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_branch.state.$error.required">State is required.</span>
			    </div>
		    </div>

		    <div class="form-group">
			    <label for="city" class="control-label">City: <span class="symbol required"></span></label>
		        <select name="city" class="form-control" id="city" ng-model="city" required>
		        </select>
		        <div class="has-error" ng-show="create_branch.$submitted || create_branch.city.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_branch.city.$error.required">City is required.</span>
			    </div>
	        </div>


			<div class="form-group">
				<label for="address"  class="control-label">Address: <span class="symbol required"></span></label>
				<input type="text" class="form-control" ng-model="address" id="address" placeholder="Branch Address" name="address" required >
			    <div class="has-error" ng-show="create_branch.$submitted || create_branch.address.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_branch.address.$error.required">Branch address is required.</span>
			    </div>
			</div>

			<div class="form-group">
			<label for="pincode"  class="control-label">Branch Pincode: <span class="symbol required"></span></label>
				<input type="text" class="form-control" ng-model="pincode" ng-pattern="/^[0-9]{1,6}$/" id="pincode" placeholder="Branch Pincode" name="pincode" required >

			    <div class="has-error" ng-show="create_branch.$submitted || create_branch.pincode.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_branch.pincode.$error.required">Branch Pincode is required.</span>
			      <span class="error text-small block ng-scope" ng-show="create_branch.pincode.$error.pattern">Not a valid pincode!</span>
			    </div>
			</div>


			<div class="form-group">
      		<label for="order_type"  class="control-label">Status:<span class="symbol required"></span></label>
        		<!-- <input type="text" class="form-control" ng-model="order_type" id="order_type" placeholder="Order Type" name="order_type" required > -->
       		 <select class="form-control" name="is_active" ng-options="item as item.value for item in is_active_list track by item.key" ng-model="is_active">
        	 <option value="" ng-if ="false"></option>
          	 </select>
          	
      		</div>

		</fieldset>


		<button type="submit" class="btn btn-primary" ng-disabled="create_branch.$invalid" style="float:right" >Submit</button>

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
            	var branch_id = getParameterByName('branch_id');
            	data.branch_id = branch_id;
            	//console.log(data);

            	//alert(branch_id);
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
                var home_url = 'index.php/branch/get_state_and_city';

                jQuery.post(home_url + '/', data, function(result) {
                    jQuery('#' + loadDataToDiv).html(result);
                });
            }
        </script>