<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
$this->load-> helper('form');
?>


<div class="row" ng-controller="categoryCreateCtrl">

<section id="page-title">
  <div class="row">
    <div class="col-sm-8">
      <h1 class="mainTitle">{{heading}} Category</h1>
      <!-- <span class="mainDescription"></span> -->
    </div>
    <!-- <div ncy-breadcrumb></div> -->
  </div>
</section>
  
<div style="margin:10px" id="validation_err">
  
</div>

<br>
  
  <form action="javascript:void(0)" name="create_category" novalidate="novalidate" ng-submit="createCategory(create_category)" method="post" >

  <div class="col-md-6">
    <fieldset>
      <legend>
        Category Details
      </legend>

 
     
      <div class="form-group">
      <label for="name"  class="control-label">Category Name: <span class="symbol required"></span></label>
        <input type="text" class="form-control" ng-model="cat_name" id="cat_name" placeholder="Category Name" name="cat_name" required >

          <div class="has-error" ng-show="create_category.$submitted || create_category.cat_name.$touched">
            <span class="error text-small block ng-scope" ng-show="create_category.cat_name.$error.required">Category Name is required.</span>
          </div>
      </div>

    </fieldset>

    <button type="submit" class="btn btn-primary" ng-disabled="create_category.$invalid" style="float:right" >Submit</button>

  </div>  

</div>

</form>
