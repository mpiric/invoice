<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
$this->load-> helper('form');
?>


<div class="row" ng-controller="taxCreateCtrl">

<section id="page-title">
  <div class="row">
    <div class="col-sm-8">
      <h1 class="mainTitle">{{heading}} Tax</h1>
      <!-- <span class="mainDescription"></span> -->
    </div>
    <!-- <div ncy-breadcrumb></div> -->
  </div>
</section>
  
<div style="margin:10px" id="validation_err">
  
</div>


<br>
  
  <form action="javascript:void(0)" name="create_tax" novalidate="novalidate" ng-submit="createTax(create_tax)" method="post" >

  <div class="col-md-6">
    <fieldset>
      <legend>
        Tax Details
      </legend>

      <!-- <div ng-show="!show_branch_dd">
        <input type="hidden" name="branch_id" ng-model="branch_id">
      </div> -->

      <div class="form-group">
        <label for="branch_id"  class="control-label">Branch: <span class="symbol required"></span></label>
         <select class="form-control" name="branch_id" ng-options="item as item.name for item in branch_list track by item.branch_id" ng-model="branch_id" ng-change="viewtax(branch_id.branch_id)" ng-init="branch_id = branch_list[viewtax(1)]">
          </select>
          <div class="has-error" ng-show="create_tax.$submitted || create_tax.branch_id.$touched">
            <span class="error text-small block ng-scope" ng-show="create_tax.branch_id.$error.required"> Branch is required.</span>
          </div>
      </div>

      <div class="form-group">
      <label for="order_type"  class="control-label">Order Type: <span class="symbol required"></span></label>
        <!-- <input type="text" class="form-control" ng-model="order_type" id="order_type" placeholder="Order Type" name="order_type" required > -->
        <select class="form-control" name="order_type" ng-options="item as item.value for item in order_type_list track by item.key" ng-model="order_type" required>
         <option value="">Select Order Type</option>
          </select>
          <div class="has-error" ng-show="create_tax.$submitted || create_tax.order_type.$touched">
            <span class="error text-small block ng-scope" ng-show="create_tax.order_type.$error.required">Order Type is required.</span>
          </div>
      </div>

      <div class="form-group">
      <label for="tax_type"  class="control-label">Tax Type: <span class="symbol required"></span></label>
        <!-- <input type="text" class="form-control" ng-model="order_type" id="order_type" placeholder="Order Type" name="order_type" required > -->
        <select class="form-control" name="tax_type" ng-options="item as item.value for item in tax_type_list track by item.key" ng-model="tax_type" ng-change="selectedType()" required>
         <option value="">Select Tax Type</option>
          </select>
          <div class="has-error" ng-show="create_tax.$submitted || create_tax.tax_type.$touched">
            <span class="error text-small block ng-scope" ng-show="create_tax.tax_type.$error.required">Tax Type is required.</span>
          </div>
      </div>

     <!--  <div ng-show="!show_product_category_dd">
        <input type="hidden" name="product_category_id" ng-model="product_category_id" ng-show="tax_type_product_specific">
      </div> -->

      <div class="form-group" ng-show="tax_type_product_specific">
        <label for="product_category_id"  class="control-label">Product Category: <span class="symbol required"></span></label>
         <select class="form-control" name="product_category_id" ng-options="item as item.name for item in product_cat_list track by item.product_category_id" ng-model="product_category_id"  ng-required='tax_type_product_specific'>
         <option value="">Select Product Category</option>
          </select>
          <div class="has-error" ng-show="create_tax.$submitted || create_tax.product_category_id.$touched">
            <span class="error text-small block ng-scope" ng-show="create_tax.product_category_id.$error.required">Product Category is required.</span>
          </div>
      </div>


      <div class="form-group" ng-show="tax_type_product_specific">
        <label for="tax_id"  class="control-label">Tax: (product category specific) <span class="symbol required"></span></label>
         <select class="form-control" name="tax_id" ng-options="item as item.tax_name for item in tax_main_list track by item.tax_id" ng-model="tax_id" ng-required='tax_type_product_specific'>
         <option value="">Select Tax</option>
          </select>
          <div class="has-error" ng-show="create_tax.$submitted || create_tax.tax_id.$touched">
            <span class="error text-small block ng-scope" ng-show="create_tax.tax_id.$error.required">Tax(product category specific) is required.</span>
          </div>
      </div>

      <div class="form-group" ng-show="tax_type_branch_specific">
        <label for="branch_tax_id"  class="control-label">Tax: (branch specific) <span class="symbol required"></span></label>
         <select class="form-control" name="branch_tax_id" ng-options="item as item.tax_name for item in tax_main_list_by_branch track by item.tax_id" ng-model="branch_tax_id" ng-required='tax_type_branch_specific'>
         <option value="">Select Tax</option>
          </select>
         
      </div>

      <div class="form-group">
      <label for="tax_percent"  class="control-label">Tax Percent: <span class="symbol required"></span></label>
        <input type="text" class="form-control" ng-model="tax_percent" id="tax_percent" placeholder="Tax Percent" name="tax_percent" required >

          <div class="has-error" ng-show="create_tax.$submitted || create_tax.tax_percent.$touched">
            <span class="error text-small block ng-scope" ng-show="create_tax.tax_percent.$error.required">Tax Percent is required.</span>
          </div>
      </div>

    </fieldset>

    <button type="submit" class="btn btn-primary" ng-disabled="create_tax.$invalid" style="float:right" >Submit</button>

  </div>  
  </form>

  <div class="col-md-4">
    <fieldset>
      <legend>Taxes to Branch</legend>
        <div  ng-controller="taxCreateCtrl">
        <table id="sample-table-3" class="table table-condensed table-hover">
        <thead>
          <tr>
            <th class="hidden-xs">Tax</th>
            <th class="hidden-xs">Type</th>
            <th class="hidden-xs">Percent</th>
          </tr>
        </thead>
        <tbody>
          <tr ng-repeat="row in taxlist">
            <td class="hidden-xs" >{{row.tax_name}}</td>
            <td class="hidden-xs" >{{row.tax_type}}</td>
            <td class="hidden-xs" >{{row.tax_percent}}</td>
          </tr>
        </tbody>
        </table>
        </div>
    </fieldset>
  </div>
</div>


