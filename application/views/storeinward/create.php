<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php
$this->load-> helper('form');
?>

  <!-- <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.0/angular-aria.js"></script> -->
  
<!-- <div style="margin:10px" id="validation_err">
  <h5 class="over-title margin-bottom-15">STORE INWARD</h5>
</div>

<br> -->

<div class="container-fluid container-fullw bg-white">
  <div class="row">

    <div class="col-md-12" ng-controller="storeinwardCtrl">

    <h5 class="over-title margin-bottom-15">Store Inward</h5>
  
  <form action="javascript:void(0)" name="create_storeinward" novalidate="novalidate" ng-submit="createstoreinward(create_storeinward)" method="post" ng-disabled="create_storeinward.$invalid">

  <!-- <div class="row">

      <div class="form-group col-md-12"> 
      <button type="submit" class="btn btn-primary" ng-disabled="create_branchproducts.$invalid" style="float:right" >Submit</button>
      </div>

  </div> -->
     
     <div class="row">
            <div class="col-md-2">
              <div class="input-group">
              <input type="text" name="inward_date" class="form-control" datepicker-popup="dd/MM/yyyy" ng-model="start" is-open="startOpened" ng-init="startOpened = false" min-date="'1970-12-31'" max-date="end"  close-text="Close"  ng-click="startOpen($event)" datepicker-options="dateOptions" value="<?php echo date('Y-m-d'); ?>" ng-change="showBtn()"/>
            </div>            
            </div> 
            <div class="col-md-2">
                <a class="btn btn-default" ng-click="filterBydate(start)" id="filterBtn">Filter</a>
                <span id="pdf_loader1" style="display: none"><img src="uploads/ajax-loader.gif"></span>
              </div>
              <!-- <div class="col-md-4">
                <a class="btn btn-default" ng-click="filterBydate(start)" >Filter</a>
                <label style="float:left" class="label label-inverse">Last Updated:</label>
              </div>   -->   
            <div class="col-md-8">

              <button type="submit" class="btn btn-primary" ng-disabled="create_storeinward.$invalid" style="float:right"  id="submitBtn">Submit</button>
              <span id="pdf_loader" style="padding-left: 80%; display: none"><img src="uploads/ajax-loader.gif"></span>
                    
            </div>            
         </div>

         <br>
         <div class="row">
          <div class = "col-md-6" >
            Last Inward on : {{created}}
          </div>
          
         </div>
         <br>


      <div class="table-responsive">

        <table ng-table="tableParams"  show-filter="true" class="table table-striped" id="storeinward_table"><!-- show-filter="true" -->

          <tr ng-repeat="row in $data">

              <input type="hidden" class="form-control" ng-model="store_product_inward_id" id="store_product_inward_id" name="store_product_inward_id" value= "{{row.store_product_inward_id}}">

              <td data-title="'Product'" sortable="'name'" filter="{ 'name': 'text' }" readonly>{{row.name}}</td>

              <td data-title="'Unit'" sortable="'unit'" filter="{ 'unit': 'text' }" readonly>{{row.unit}}</td>

              <td data-title="'Instock'" sortable="'instock'" filter="{ 'instock': 'text' }" readonly>{{(row.instock == null) ? 0:row.instock}}</td>

              <td data-title="'Price'" sortable="'price'" filter="{ 'price': 'text' }" readonly>{{(row.price == null) ? 0:row.price}}</td>

               <!-- <td data-title="'Price'" sortable="'price'" filter="{ 'price': 'text' }" readonly><input type="text" name="price_{{row.store_product_id}}" ng-disabled="!row.is_available || !is_editable" value="{{(row.price == null) ? 0:row.price }}"/></td> -->

               <!-- <td data-title="'Price'" sortable="'price'" filter="{ 'price': 'text' }" readonly>
                <input type="text" name="price_{{row.store_product_inward_id}}" ng-model="row.price" ng-disabled="!row.is_available || !is_editable" value="{{(row.price == null) ? 0:row.price }}" ng-required='row.is_available'>
                 <div class="has-error" ng-show="create_storeinward.$submitted || create_storeinward.price_{{row.store_product_inward_id}}.$touched">
                  <span class="error text-small block ng-scope" ng-show="create_storeinward.price_{{row.store_product_inward_id}}.$error.required">Price is required.
                  </span>
                </div>
              </td> -->

              <td class="center" data-title="'Purchased'" sortable="'is_available'" filter="{ 'is_available': 'text' }"><input type="checkbox" ng-model="row.is_available" name="is_available_{{row.store_product_inward_id}}" ng-disabled="!is_editable" ></td>

              <!-- <td data-title="'Purchase Quantity'" sortable="'purchase_qty'" filter="{ 'purchase_qty': 'text' }" readonly>
              <input type="text" ng-disabled="!row.is_available || !is_editable" name="purchase_qty_{{row.store_product_inward_id}}" value="{{(row.purchase_qty == null) ? 0:row.purchase_qty }}"    /> 
              </td> -->

              <td data-title="'Purchase Quantity'" sortable="'purchase_qty'" filter="{ 'purchase_qty': 'text' }" readonly>
                <input type="text" name="purchase_qty_{{row.store_product_inward_id}}" ng-model="row.purchase_qty" ng-disabled="!row.is_available || !is_editable" value="{{(row.purchase_qty == null) ? 0:row.purchase_qty }}" ng-required='row.is_available'>
                <!-- ng-change="validatePuchaseQty(row.instock,row.purchase_qty)" -->
                 <div class="has-error" ng-show="create_storeinward.$submitted || create_storeinward.purchase_qty_{{row.store_product_inward_id}}.$touched">
                  <span class="error text-small block ng-scope" ng-show="create_storeinward.purchase_qty_{{row.store_product_inward_id}}.$error.required">Puchase Quantity is required.
                  </span>
                </div>
              </td>

              <td data-title="'Purchased Today'" sortable="'today_qty'" filter="{ 'today_qty': 'text' }" readonly>
                {{(row.purchase_qty == null) ? 0:row.today_qty }}
              </td>


            </tr>


          <!-- </tbody>   -->
        </table>

      </div>
      <div ng-show="is_msg">

              {{message}}
            
      </div>
      </form>
      </div>
  
   <!--  </fieldset>

    <button type="submit" class="btn btn-primary" ng-disabled="create_branchproducts.$invalid" style="float:right" >Submit</button> -->
</div>
</div>