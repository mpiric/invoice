<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
$this->load-> helper('form');
?>


<div class="row" ng-controller="brandCreateCtrl">

<section id="page-title">
  <div class="row">
    <div class="col-sm-8">
      <h1 class="mainTitle">{{heading}} Brand</h1>
      <!-- <span class="mainDescription"></span> -->
    </div>
    <!-- <div ncy-breadcrumb></div> -->
  </div>
</section>
  
<div style="margin:10px" id="validation_err">
  
</div>

<br>
  
  <form action="javascript:void(0)" name="create_brand" novalidate="novalidate" ng-submit="createbrand(create_brand)" method="post" >

  <div class="col-md-6">
    <fieldset>
      <legend>
        Brand Details
      </legend>


     
      <div class="form-group">
      <label for="name"  class="control-label">Brand Name: <span class="symbol required"></span></label>
        <input type="text" class="form-control" ng-model="brand_name" id="brand_name" placeholder="Brand Name" name="brand_name" required >

          <div class="has-error" ng-show="create_brand.$submitted || create_brand.brand_name.$touched">
            <span class="error text-small block ng-scope" ng-show="create_brand.brand_name.$error.required">Brand Name is required.</span>
          </div>
      </div>

    </fieldset>

    <button type="submit" class="btn btn-primary" ng-disabled="create_brand.$invalid" style="float:right" >Submit</button>

  </div>  

</div>

</form>
