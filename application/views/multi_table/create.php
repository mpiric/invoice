<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
$this->load-> helper('form');
  ?>


<div class="row" ng-controller="tableCtrl">

<section id="page-title">
  <div class="row">
    <div class="col-sm-8">
      <h1 class="mainTitle">Assign Tables</h1>
      <!-- <span class="mainDescription"></span> -->
    </div>
    <!-- <div ncy-breadcrumb></div> -->
  </div>
</section>
  
<div style="margin:10px" id="validation_err">
  
</div>

<br>
  
  <form action="javascript:void(0)" name="assign_table" novalidate ng-submit="assignTable()" method="post" >

  <div class="col-md-6">
    <fieldset>
      <legend>
        Assign Tables
      </legend>

      <!-- <div ng-show="!show_branch_dd">
        <input type="hidden" name="branch_id" ng-model="branch_id">
      </div> -->

		
		
	  <div class="form-group">
		<label for="branch"  class="control-label">Select Branch <span class="symbol required"></span></label>
        <select class="form-control" name="branch_id" ng-options="item as item.name for item in branch_list track by item.branch_id" ng-model="branch_id" ng-change="branchwiseBrand(branch_id)" required>
         <option value="">Select Branch</option>
          </select>
          <!--<div class="has-error" ng-show="create_customer.$submitted || create_customer.order_type.$touched">
            <span class="error text-small block ng-scope" ng-show="create_customer.order_type.$error.required">Order Type is required.</span>
          </div>-->
      </div>
	  
	  
		<div class="form-group">
				<label for="brand"  class="control-label">Select Brand <span class="symbol required"></span></label>
					<select class="form-control" name="brand_id" ng-options="item as item.brand_name for item in brand_list_by_branch track by item.brand_id" ng-model="brand_id" >
						<option value="">Select Brand</option>
					</select>
		</div>
		
		
		<div class="form-group">
			<label for="total_table"  class="control-label">Total Number Of Tables: <span class="symbol required"></span></label>
				<input type="text" class="form-control" ng-model="total_table" id="total_table" placeholder="Total Tables" name="total_table" ng-change="changeEndTable()"  required >

			    <!--<div class="has-error" ng-show="create_waiter.$submitted || create_waiter.lastname.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_waiter.lastname.$error.required">Waiter Last Name  is required.</span>
			    </div>-->
		</div>
		
		<div class="form-group">
			<label for="start_table"  class="control-label">Starting Number Of Tables: <span class="symbol required"></span></label>
				<input type="text" class="form-control" ng-model="start_table" id="start_table" placeholder="Start Number" name="start_table" ng-change="changeEndTable()"  required >

			    <!--<div class="has-error" ng-show="create_waiter.$submitted || create_waiter.lastname.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_waiter.lastname.$error.required">Waiter Last Name  is required.</span>
			    </div>-->
		</div>
		
		<div class="form-group">
			<label for="end_table"  class="control-label">Ending Number Of Tables: <span class="symbol required"></span></label>
				<input type="text" class="form-control" ng-model="end_table" id="end_table" placeholder="End Number" name="end_table"  value="0" required >

			    <!--<div class="has-error" ng-show="create_waiter.$submitted || create_waiter.lastname.$touched">
			      <span class="error text-small block ng-scope" ng-show="create_waiter.lastname.$error.required">Waiter Last Name  is required.</span>
			    </div>-->
		</div>

      

    </fieldset>

    <button type="submit" class="btn btn-primary" ng-disabled="create_customer.$invalid" style="float:right" >Submit</button>

  </div>  

</div>

</form>
