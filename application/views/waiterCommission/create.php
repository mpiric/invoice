<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
$this->load-> helper('form');
?>
<section id="page-title">
  <div class="row">
    <div class="col-sm-8">
      <h1 class="mainTitle">Create Waiter Commission</h1>
      <!-- <span class="mainDescription"></span> -->
    </div>
    <!-- <div ncy-breadcrumb></div> -->
  </div>
</section>
  
<div style="margin:10px" id="validation_err">
  
</div>

<br>

<div class="row" ng-controller="waitercommissionCreateCtrl">
  
  <form action="javascript:void(0)" name="create_waitercommission" novalidate="novalidate" ng-submit="createwaitercommission(create_waitercommission)" method="post" >

  <div class="col-md-6">
    <fieldset>
      <legend>
        Waiter Commission Details
      </legend>

      <!-- <div ng-show="!show_branch_dd">
        <input type="hidden" name="branch_id" ng-model="branch_id">
      </div> -->

      <div class="form-group">
        <label for="waiter_id"  class="control-label">Waiter: <span class="symbol required"></span></label>
         <select class="form-control" name="waiter_id" ng-options="item as item.waiter_code for item in waiter_list track by item.waiter_id" ng-model="waiter_id">
         <option value="">Select Waiter</option>
          </select>
          <div class="has-error" ng-show="create_waitercommission.$submitted || create_waitercommission.waiter_id.$touched">
            <span class="error text-small block ng-scope" ng-show="create_waitercommission.waiter_id.$error.required"> Waiter is required.</span>
          </div>
      </div>

      <div ng-show="!show_product_category_dd">
        <input type="hidden" name="product_category_id" ng-model="product_category_id">
      </div>

      <div class="form-group">
        <label for="product_category_id"  class="control-label">Product Category: <span class="symbol required"></span></label>
         <select class="form-control" name="product_category_id" ng-options="item as item.name for item in product_cat_list track by item.product_category_id" ng-model="product_category_id" required>
         <option value="">Select Product Category</option>
          </select>
          <div class="has-error" ng-show="create_waitercommission.$submitted || create_waitercommission.product_category_id.$touched">
            <span class="error text-small block ng-scope" ng-show="create_waitercommission.product_category_id.$error.required">Product Category is required.</span>
          </div>
      </div>

      <div class="form-group">
        <label for="order_id"  class="control-label">Order: <span class="symbol required"></span></label>
         <select class="form-control" name="order_id" ng-options="item as item.order_code for item in order_list track by item.order_id" ng-model="order_id" required>
         <option value="">Select Order</option>
          </select>
          <div class="has-error" ng-show="create_waitercommission.$submitted || create_waitercommission.order_id.$touched">
            <span class="error text-small block ng-scope" ng-show="create_waitercommission.order_id.$error.required">Order is required.</span>
          </div>
      </div>

      <div class="form-group">
      <label for="commission_date"  class="control-label">Commission Date: <span class="symbol required"></span></label>
        <input type="text" class="form-control" ng-model="commission_date" id="commission_date" placeholder="Commission Date" name="commission_date" required >

          <div class="has-error" ng-show="create_waitercommission.$submitted || create_waitercommission.commission_date.$touched">
            <span class="error text-small block ng-scope" ng-show="create_waitercommission.commission_date.$error.required">Commission Date is required.</span>
          </div>
      </div>

      <div class="form-group">
      <label for="commission_amount"  class="control-label">Commission Amount: <span class="symbol required"></span></label>
        <input type="text" class="form-control" ng-model="commission_amount" id="commission_amount" placeholder="Commission Amount" name="commission_amount" required >

          <div class="has-error" ng-show="create_waitercommission.$submitted || create_waitercommission.commission_amount.$touched">
            <span class="error text-small block ng-scope" ng-show="create_waitercommission.commission_amount.$error.required">Commission Amount is required.</span>
          </div>
      </div>

            <div class="form-group">
      <label for="product_qty"  class="control-label">Product Quantity: <span class="symbol required"></span></label>
        <input type="text" class="form-control" ng-model="product_qty" id="product_qty" placeholder="Product Quantity" name="product_qty" required >

          <div class="has-error" ng-show="create_waitercommission.$submitted || create_waitercommission.product_qty.$touched">
            <span class="error text-small block ng-scope" ng-show="create_waitercommission.product_qty.$error.required">Product Quantity is required.</span>
          </div>
      </div>

    </fieldset>

    <button type="submit" class="btn btn-primary" ng-disabled="create_waitercommission.$invalid" style="float:right" >Submit</button>
    

  </div>  

</div>

</form>
