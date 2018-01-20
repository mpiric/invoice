<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
$this->load-> helper('form');
?>


<div class="row" ng-controller="productcategoryCreateCtrl">

<section id="page-title">
  <div class="row">
    <div class="col-sm-8">
      <h1 class="mainTitle">{{heading}} Product Category</h1>
      <!-- <span class="mainDescription"></span> -->
    </div>
    <!-- <div ncy-breadcrumb></div> -->
  </div>
</section>
  
<div style="margin:10px" id="validation_err">
  
</div>

  

<br>
  
  <form action="javascript:void(0)" name="create_productcategory" novalidate="novalidate" ng-submit="createproductcategory(create_productcategory)" method="post" >

  <div class="col-md-6">
    <fieldset>
      <legend>
        Product Category Details
      </legend>

      <!-- <div ng-show="!show_branch_dd">
        <input type="hidden" name="branch_id" ng-model="branch_id">
      </div> -->


      <div ng-show="!show_product_category_dd">
        <input type="hidden" name="product_category_id" ng-model="product_category_id">
      </div>

      <div class="form-group">
        <label for="parent"  class="control-label">Product Category:(select none for parent category) <span class="symbol required"></span></label>
         <select class="form-control" name="parent" ng-options="item as item.name for item in product_cat_list track by item.product_category_id" ng-model="parent">
         <option value="">Select Product Category</option>
          </select>
          <div class="has-error" ng-show="create_productcategory.$submitted || create_productcategory.product_category_id.$touched">
            <!-- <span class="error text-small block ng-scope" ng-show="create_productcategory.product_category_id.$error.required">Product Category is required.</span> -->
          </div>
      </div>


      <div class="form-group">
      <label for="name"  class="control-label">Product Category Name: <span class="symbol required"></span></label>
        <input type="text" class="form-control" ng-model="name" id="name" placeholder="Product Category Name" name="name" required >

          <div class="has-error" ng-show="create_productcategory.$submitted || create_productcategory.name.$touched">
            <span class="error text-small block ng-scope" ng-show="create_productcategory.name.$error.required">Product Category Name is required.</span>
          </div>
      </div>

      <!-- <div class="form-group" >
        <label for="brand_name" class="control-label">Brand: <span class="symbol required"></span></label>
            <span></span>
            <ui-select multiple ng-model="selected.selectedBrand" name="selectedBrand" theme="bootstrap" ng-disabled="disabled">
            <ui-select-match placeholder="Select brand" >
              {{$item.brand_name}} 
            </ui-select-match>
            <ui-select-choices repeat="brand in brand_list | propsFilter: {brand_name: $select.search}">
              <div ng-bind-html="brand.brand_name | highlight: $select.search"></div>
              <!-- <small> Address: {{branch.address}}</small> -->
            <!--</ui-select-choices>
          </ui-select>
        
      </div> -->

           <div class="form-group">
            <label for="brand_id"  class="control-label">Brand: <span class="symbol required"></span></label>
             <select class="form-control" id="brand_id" name="brand_id" ng-options="item as item.brand_name for item in brand_list track by item.brand_id" ng-model="brand_id" required>
             <option value="">Select Brand</option>
              <option value="" ng-if ="false">Select Brand</option> 
              </select>
              <div class="has-error" ng-show="create_branch.$submitted || create_branch.brand_id.$touched">
                <span class="error text-small block ng-scope" ng-show="create_branch.brand_id.$error.required">Brand is required.</span>
              </div>
          </div> 



    </fieldset>

    <button type="submit" class="btn btn-primary" ng-disabled="create_productcategory.$invalid" style="float:right" >Submit</button>

  </div>  

</div>

</form>
