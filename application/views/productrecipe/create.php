<?php $this->load-> helper('form'); ?>
  
<div style="margin:10px" id="validation_err">
  
</div>

<br>

<div ng-controller="productrecipeCtrl">
  
  <form action="javascript:void(0)" name="create_productrecipe" novalidate="novalidate" ng-submit="createproductrecipe(create_productrecipe)" method="post" >

  <div class="row">

      <!-- <div class="form-group col-md-6">
        <label for="product_id"  class="control-label">Product: <span class="symbol required"></span></label>
          <select class="form-control" name="product_id" ng-options="item as item.name for item in product_list track by item.product_id" ng-model="product_id" ng-change="storeProduct()">
          <option value="">Select Product</option>
          </select>
      </div> -->
      
      <div class="form-group col-md-6">           
        <label for="product_id"  class="control-label">Select Product:</label>
                <ui-select ng-model="product_id" name="product_id" id="product_id" theme="bootstrap" on-select="storeProduct(product_id.product_id)" > 
                  <ui-select-match placeholder="Search by Product...">
                    {{$select.selected.name}}
                  </ui-select-match>
                  <ui-select-choices repeat="product in product_list | propsFilter: {name: $select.search}  "  >
                    </small><div ng-bind-html="product.name | highlight: $select.search"></div>
                    
                  </ui-select-choices>
                </ui-select>

                
                <div class="has-error" ng-show="order_form.$submitted || order_form.product_list.selected.$touched">
                    <span class="error text-small block ng-scope" ng-show="order_form.product_list.selected.$error.ui-select-required">product_list.selected is required.</span>  
                </div> 
                <span id="pdf_loader" style="display: none"><img src="uploads/ajax-loader.gif"></span>  
      </div>

      


      <br>
  </div>

  <div ng-show="show_div">
      <div class="row">

        <div class="form-group col-md-4">
          <b>Product Code: {{product_code}} </b>

        </div>
        
        
        

        <div class="form-group col-md-8"> 
          <button type="submit" class="btn btn-primary" ng-disabled="create_productrecipe.$invalid" style="float:right" >Submit</button>

            <a class="btn btn-primary" style="float: right; margin-right: 3%;" ng-click="addedProducts()">Added Products</a>
            <a class="btn btn-primary" style="float: right; margin-right: 3%;" ng-click="allProducts()">All Products</a>
          <!-- <div ng-click="CallFunc()" >
          <a class="btn btn-primary" ng-hide="filterToggle" style="float: right; margin-right: 3%;">All Products</a>
          <a class="btn btn-primary" ng-show="filterToggle" style="float: right; margin-right: 3%;">Added Products</a>
        </div> -->

        </div>

      </div>
     
      <div class="table-responsive">

        <table ng-table="tableParams"  show-filter="true" class="table table-striped"><!-- show-filter="true" -->

          <tr ng-repeat="row in data"> 

            <input type="hidden" class="form-control" ng-model="store_product_id" id="store_product_id" name="store_product_id" value= "{{row.store_product_id}}">

            <td data-title="'Store Product'" sortable="'name'" filter="{ 'name': 'text' }" readonly>{{row.name}}</td>

            <td data-title="'Store Product Code'" sortable="'product_code'" filter="{ 'product_code': 'text' }" readonly>{{row.product_code}}</td>

            <td data-title="'Quantity'" sortable="'qty'" filter="{ 'qty': 'text' }" >
              
              <input type="text" name="qty_{{row.store_product_id}}" value="{{(row.qty == null) ? 0:row.qty }}" />

            </td>

           <!--  <td data-title="'Quantity'" sortable="'qty'" filter="{ 'qty': 'text' }" readonly><input type="text" ng-readonly="row.is_available == 'N'" name="qty_{{row.store_product_id}}" value="{{(row.qty == null) ? 0:row.qty }}" aria-label="Readonly field"/>

              <div class="has-error" ng-show="create_productrecipe.$submitted || create_productrecipe.qty.$touched">
                <span class="error text-small block ng-scope" ng-show="create_productrecipe.qty.$error.required">Quantity is required.
                </span>
              </div>
            </td> -->
          </tr>
        </table>

      </div>
  </div>
  </form>
</div>



       <!--<div class="form-group col-md-6">           
                <ui-select ng-model="product_id" id="product_id" theme="bootstrap"  > 
                  <ui-select-match placeholder="Search by Product...">
                    {{$item.name}}
                  </ui-select-match>
                  <ui-select-choices repeat="product in product_list | propsFilter: {name: $select.search, product_code: $select.search}  "  >
                    <small ng-bind-html="product.product_code | highlight: $select.search"></small><div ng-bind-html="product.name | highlight: $select.search"></div>
                    
                  </ui-select-choices>
                </ui-select>
                
                <div class="has-error" ng-show="order_form.$submitted || order_form.product_list.selected.$touched">
                    <span class="error text-small block ng-scope" ng-show="order_form.product_list.selected.$error.ui-select-required">product_list.selected is required.</span>  </div> 
              </div> -->