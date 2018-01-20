<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
$this->load-> helper('form');
?>

  <!-- <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.0/angular-aria.js"></script> -->
  
<div style="margin:10px" id="validation_err">
  
</div>

<br>

<div ng-controller="branchproductsCtrl">
  
  <form action="javascript:void(0)" name="create_branchproducts" novalidate="novalidate" ng-submit="createbranchproducts(create_branchproducts)" method="post" >

  <div class="row">
    <!-- <fieldset>-->


      <!-- <div ng-show="!show_branch_dd">
        <input type="hidden" name="branch_id" ng-model="branch_id">
      </div> -->

      <div ng-show="show_branch_dd" class="form-group col-md-6">
        <label for="branch_id"  class="control-label">Branch: <span class="symbol required"></span></label>
          <select class="form-control" name="branch_id" ng-options="item as item.name for item in branch_list track by item.branch_id" ng-model="branch_id" ng-change="branchwiseProduct(branch_id)">
          </select>
      </div>
      <br>
      <div class="form-group col-md-6"> 
      <button type="submit" class="btn btn-primary" ng-disabled="create_branchproducts.$invalid" style="float:right" >Submit</button>
      </div>

  </div>
     

      <div class="table-responsive">

        <table ng-table="tableParams"  show-filter="true" class="table table-striped"><!-- show-filter="true" -->

          <tr ng-repeat="row in $data"> 

              <input type="hidden" class="form-control" ng-model="product_id" id="product_id" name="product_id" value= "{{row.product_id}}">

              <td data-title="'Product'" sortable="'product_name'" filter="{ 'product_name': 'text' }" readonly>{{row.product_name}}</td>

              <td data-title="'Product Category'" sortable="'catName'" filter="{ 'catName': 'text' }" readonly>{{row.catName}}</td>

              <td data-title="'Product Code'" sortable="'productcode'" filter="{ 'productcode': 'text' }" readonly>{{row.productcode}}</td>


              <td class="center" data-title="'Available'" sortable="'is_available'" filter="{ 'is_available': 'text' }">
              <input type="checkbox"  ng-model="row.is_available" ng-true-value="'Y'" ng-false-value="'N'" name="is_available_{{row.product_id}}" ng-checked="row.is_available=='Y'" "></td>

              <!-- <td class="center" data-title="'Available'" sortable="'is_available'" filter="{ 'is_available': 'text' }"><input type="checkbox" name="is_available" ng-class="class" ng-model="row.product_id" ng-checked="true" ng-click="changeClass()"/></td> -->

              <td data-title="'Product Price(default)'" sortable="'default_price'" filter="{ 'default_price': 'text' }" >{{row.default_price}}</td>


              <td data-title="'Product Price(branch spf)'" sortable="'product_price'" filter="{ 'product_price': 'text' }" >
              
                <input type="text" name="product_price_{{row.product_id}}" ng-readonly="row.is_available == 'N'" value="{{row.product_price}}" aria-label="Readonly field"  /><!-- ng-init="is_available=true" editable-checkbox="createbranchproducts.is_available"--><!-- ng-readonly="row.is_available != 'Y' && is_available === false " -->

                <div class="has-error" ng-show="create_branchproducts.$submitted || create_branchproducts.product_price.$touched">
                  <span class="error text-small block ng-scope" ng-show="create_branchproducts.product_price.$error.required">Branch Specific price is required.
                  </span>
                </div>
              </td>

              <td data-title="'Waiter Commission(branch spf)'" sortable="'waiter_commission_branch'" filter="{ 'waiter_commission_branch': 'text' }" readonly><input type="text" ng-readonly="row.is_available == 'N'" name="waiter_commission_branch_{{row.product_id}}" value="{{(row.waiter_commission_branch == null) ? 0:row.waiter_commission_branch }}" aria-label="Readonly field"/></td>

            </tr>
          <!-- </tbody>   -->
        </table>

      </div>
      </form>
      </div>
  
   <!--  </fieldset>

    <button type="submit" class="btn btn-primary" ng-disabled="create_branchproducts.$invalid" style="float:right" >Submit</button> -->

