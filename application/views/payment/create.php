<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
$this->load-> helper('form');
?>


<div class="row" ng-controller="paymentCreateCtrl">

<section id="page-title">
  <div class="row">
    <div class="col-sm-8">
      <h1 class="mainTitle">{{heading}} Patment Type Master</h1>
      <!-- <span class="mainDescription"></span> -->
    </div>
    <!-- <div ncy-breadcrumb></div> -->
  </div>
</section>
  
<div style="margin:10px" id="validation_err">
  
</div>

<br>
  
  <form action="javascript:void(0)" name="create_payment" novalidate="novalidate" ng-submit="createpayment(create_payment)" method="post" >

  <div class="col-md-6">
    <fieldset>
      <legend>
        Payment Type Master Details
      </legend>

      <!--<div class="form-group">
      <label for="name"  class="control-label">Tax type: <span class="symbol required"></span></label>
      <br>
      <label for="tax_type">On Product
        <input type="radio" class="form-control" ng-model="tax_type" id="tax_type" name="tax_type" value="1" ng-checked="true" ></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <label for="tax_type">On Branch
        <input type="radio" class="form-control" ng-model="tax_type" id="tax_type" name="tax_type" value="2" ></label>
      </div>-->
     
      <div class="form-group">
      <label for="name"  class="control-label">Payment Type Name: <span class="symbol required"></span></label>
        <input type="text" class="form-control" ng-model="payment_type" id="payment_type" placeholder="Payment Type" name="payment_type" required >

          <div class="has-error" ng-show="create_payment.$submitted || create_payment.payment_type.$touched">
            <span class="error text-small block ng-scope" ng-show="create_payment.payment_type.$error.required">Payment type is required.</span>
          </div>
      </div>

    </fieldset>

    <button type="submit" class="btn btn-primary" ng-disabled="create_payment.$invalid" style="float:right" >Submit</button>

  </div>  

</div>

</form>
