<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
$this->load-> helper('form');
?>


<div class="row" ng-controller="customerCreateCtrl">

<section id="page-title">
  <div class="row">
    <div class="col-sm-8">
      <h1 class="mainTitle">{{heading}} Customer</h1>
      <!-- <span class="mainDescription"></span> -->
    </div>
    <!-- <div ncy-breadcrumb></div> -->
  </div>
</section>
  
<div style="margin:10px" id="validation_err">
  
</div>

<br>
  
  <form action="javascript:void(0)" name="create_customer" novalidate="novalidate" ng-submit="createCustomer(create_customer)" method="post" >

  <div class="col-md-6">
    <fieldset>
      <legend>
        Customer Details
      </legend>

      <!-- <div ng-show="!show_branch_dd">
        <input type="hidden" name="branch_id" ng-model="branch_id">
      </div> -->


      <div class="form-group">
        <label for="order_id"  class="control-label">Order: <span class="symbol required"></span></label>
         <select class="form-control" name="order_id" ng-options="item as item.order_code for item in order_list track by item.order_id" ng-model="order_id" required>
         <option value="">Select Order</option>
          </select>
          <div class="has-error" ng-show="create_customer.$submitted || create_customer.order_id.$touched">
            <span class="error text-small block ng-scope" ng-show="create_customer.order_id.$error.required">Order is required.</span>
          </div>
      </div>

      <div class="form-group">
      <label for="order_type"  class="control-label">Order Type: <span class="symbol required"></span></label>
        <!-- <input type="text" class="form-control" ng-model="order_type" id="order_type" placeholder="Order Type" name="order_type" required > -->
        <select class="form-control" name="order_type" ng-options="item as item.value for item in order_type_list track by item.key" ng-model="order_type" required>
         <option value="">Select Order Type</option>
          </select>
          <div class="has-error" ng-show="create_customer.$submitted || create_customer.order_type.$touched">
            <span class="error text-small block ng-scope" ng-show="create_customer.order_type.$error.required">Order Type is required.</span>
          </div>
      </div>

      <div class="form-group">
      <label for="firstname"  class="control-label">Firstname: <span class="symbol required"></span></label>
        <input type="text" class="form-control" ng-model="firstname" id="firstname" placeholder="Firstname" name="firstname" required >

          <div class="has-error" ng-show="create_customer.$submitted || create_customer.firstname.$touched">
            <span class="error text-small block ng-scope" ng-show="create_customer.firstname.$error.required">Firstname is required.</span>
          </div>
      </div>

      <div class="form-group">
      <label for="lastname"  class="control-label">Lastname: <span class="symbol required"></span></label>
        <input type="text" class="form-control" ng-model="lastname" id="lastname" placeholder="Lastname" name="lastname" required >

          <div class="has-error" ng-show="create_customer.$submitted || create_customer.lastname.$touched">
            <span class="error text-small block ng-scope" ng-show="create_customer.lastname.$error.required">Lastname is required.</span>
          </div>
      </div>

      <div class="form-group">
      <label for="contact"  class="control-label">Contact: <span class="symbol required"></span></label>
        <input type="text" class="form-control" ng-model="contact" id="contact" placeholder="Contact" name="contact" required >

          <div class="has-error" ng-show="create_customer.$submitted || create_customer.contact.$touched">
            <span class="error text-small block ng-scope" ng-show="create_customer.contact.$error.required">Contact is required.</span>
          </div>
      </div>

      <div class="form-group">
      <label for="email"  class="control-label">Email: <span class="symbol required"></span></label>
        <input type="text" class="form-control" ng-model="email" id="email" placeholder="Email" name="email" required >

          <div class="has-error" ng-show="create_customer.$submitted || create_customer.email.$touched">
            <span class="error text-small block ng-scope" ng-show="create_customer.email.$error.required">Email is required.</span>
          </div>
      </div>

    </fieldset>

    <button type="submit" class="btn btn-primary" ng-disabled="create_customer.$invalid" style="float:right" >Submit</button>

  </div>  

</div>

</form>
