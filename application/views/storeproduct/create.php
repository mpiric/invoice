<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
$this->load-> helper('form');
?>


<div class="row" ng-controller="storeproductCreateCtrl">

<section id="page-title">
  <div class="row">
    <div class="col-sm-8">
      <h1 class="mainTitle">{{heading}} Store Product</h1>
      <!-- <span class="mainDescription"></span> -->
    </div>
    <!-- <div ncy-breadcrumb></div> -->
  </div>
</section>
  
<div style="margin:10px" id="validation_err">
  
</div>

<br>
  
  <form action="javascript:void(0)" name="create_storeproduct" novalidate="novalidate" ng-submit="createstoreproduct(create_storeproduct)" method="post" >

  <div class="col-md-6">
    <fieldset>
      <legend>
        Store Product Details
      </legend>

      <div class="form-group">
        <label for="category_id" class="control-label">Store Category: <span class="symbol required"></span></label>
         <select class="form-control" name="category_id" ng-options="item as item.cat_name for item in cat_list track by item.category_id" ng-model="category_id" required>
         <option value="">Select Store Category</option>
          </select>
          <div class="has-error" ng-show="create_storeproduct.$submitted || create_storeproduct.category_id.$touched">
            <span class="error text-small block ng-scope" ng-show="create_storeproduct.category_id.$error.required">Category is required.</span>
          </div>
      </div>

      <div class="form-group">
      <label for="name"  class="control-label">Store Product Name: <span class="symbol required"></span></label>
        <input type="text" class="form-control" ng-model="name" id="name" placeholder="Store Product Name" name="name" ng-change="checkName(name)" required >

          <div class="has-error" ng-show="create_storeproduct.$submitted || create_storeproduct.name.$touched">
            <span class="error text-small block ng-scope" ng-show="create_storeproduct.name.$error.required">Store Product Name is required.</span>
          </div>
          <div class="has-error" ng-show="create_storeproduct.$submitted || create_storeproduct.name.$touched">
            <span class="error text-small block ng-scope" ng-show="checkN">Store Product Name is already in use.</span>
          </div>
      </div>

      <div class="form-group">
      <label for="product_code" class="control-label">Store Product Code: </label>
        <input type="text" class="form-control" ng-model="product_code" id="product_code" placeholder="Store Product Code" name="product_code" ng-change="checkCode(product_code)">
        <div class="has-error" ng-show="create_storeproduct.$submitted || create_storeproduct.product_code.$touched">
            <span class="error text-small block ng-scope" ng-show="checkC">Product code is already in use.</span>
        </div>
      </div>

      <div class="form-group">
      <label for="unit"  class="control-label">Product Unit: <span class="symbol required"></span></label>
        
        <select class="form-control" name="unit" ng-options="item as item.value for item in unit_list track by item.key" ng-model="unit">
         <!-- <option value="">Select Unit</option> -->
          <option value="" ng-if="false"></option>
          </select>

          <!-- <div class="has-error" ng-show="create_storeproduct.$submitted || create_storeproduct.unit.$touched">
            <span class="error text-small block ng-scope" ng-show="create_storeproduct.unit.$error.required">Product Unit  is required.</span>
          </div> -->
      </div>

      <div class="form-group">
      <label for="price"  class="control-label">Store Product Price: <span class="symbol required"></span></label>
        <input type="text" class="form-control" ng-model="price" ng-pattern="/^\d{0,9}(\.\d{1,9})?$/" id="price" placeholder="Product Price" name="price" required >

          <div class="has-error" ng-show="create_storeproduct.$submitted || create_storeproduct.price.$touched">
            <span class="error text-small block ng-scope" ng-show="create_storeproduct.price.$error.required">Store Product Price is required.</span>
            <span class="error text-small block ng-scope" ng-show="create_storeproduct.price.$error.pattern">Not a valid number!</span>
          </div>
      </div>

    </fieldset>

    <button type="submit" class="btn btn-primary" ng-disabled="create_storeproduct.$invalid || checkN || checkC" style="float:right" >Submit</button>

  </div>  

</div>

</form>
