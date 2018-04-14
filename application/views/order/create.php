<div class="container-fluid container-fullw bg-white" >
    <a ng-click="reloadRoute()" ng-controller="reloadPageCtrl" class="navbar-brand" title="home" style="float:right" ><i class="fa fa-refresh"></i></a>
    <tabset class="tabbable" type="pills">
        <tab heading="Table Order">
            <div class="row" ng-controller="orderCtrl">
                <div class="col-md-3">
                    <!-- <input type="text" name="testname" ng-model="testname" ng-keydown="testnamefun($event)"> -->
                    <div style="font-size:18px;">
                        <span > Daily Income:  </span> {{dailyIncome}}
                        <br>
                        <span > Current Order Amount:  </span> {{cur_order_amt}}
                        <input type="hidden" name="cur_order_amt_hidden" value="0" id="cur_order_amt_hidden" ng-model="cur_order_amt_hidden">
                    </div>
                    <fieldset>
                        <legend>Table details</legend>
                        <!-- <button ng-click="print_pdf()">print_pdf</button> -->
                        <div class="table-responsive panel-scroll height-250" perfect-scrollbar wheel-propagation="false" suppress-scroll-x="true">
                            <table class="table table-hover" id="table_list">
                                <thead>
                                    <tr>
                                        <th>Table</th>
                                        <!-- <th>Bill No</th> -->
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="row in data" id="table_row_{{row.table_number}}" style="cursor:pointer" ng-click="chnageTable({{row.table_detail_id}},{{row.table_number}},row)" >
                                        <!-- b4 row ,{{row.max_capacity}} -->
                                        <td><span class="btn btn-primary btn-xs"><b>{{row.table_number}}</b></span></td>
                                        <!-- <td>{{row.order_code}}</td> -->
                                        <!-- <td>{{row.total_amount==0 ? '' : row.total_amount.toFixed(2)}}</td> -->
                                        <td>{{row.total_amount }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Recent Orders</legend>
                        <input type="text" class="form-control" ng-model="searchRecentOrder" placeholder="Search" >
                        <div class="table-responsive panel-scroll height-250" perfect-scrollbar wheel-propagation="false" suppress-scroll-x="true" >
                            <br>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tbl</th>
                                        <th>#</th>
                                        <th>Bill No</th>
                                        <th>Amnt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="row in recent_order | filter:searchRecentOrder" style="cursor: pointer;" ng-click="get_recent_order_details(row)">
                                        <td><span class="btn btn-primary btn-xs"><b>{{row.table_number}}</b></span></td>
                                        <td ng-click="paymentMethod(row.order_id)"><span class="btn btn-primary btn-xs"><i class="ti-credit-card"></i></span></td>
                                        <td>{{row.order_code}}</td>
                                        <!-- <td>{{row.total_amount==0 ? '' : row.total_amount.toFixed(2)}}</td> -->
                                        <td>{{row.roundoff}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </fieldset>
                </div>
                <div class="col-md-9" id="bill_div" >
                    <!-- style="pointer-events:none;opacity:0.6" -->
                    <form action="javascript:void(0)" name="order_form" novalidate method="post" >
                        <input type="hidden" id="branch_name" name="branch_name" ng-model="branch_name"> 
                        <fieldset>
                            <legend>
                                Bill details  <!-- - <span id="bill_number"></span> -->
                            </legend>
                            <div class="row">
                                <div class="col-md-4">
                                    <!-- <button class="btn btn-default btn-o"> -->
                                    <span style="width:50%">Table Number </span>
                                    <span style="width:50%">
                                        <!-- <b> -- </b> -->
                                        <input type="text" id="table_number" style="width:40px" ng-model="table_number" name="table_number" ng-change="make_table_row_active_by_table_number(table_number)" focus-name="1"  auto-focus required>
                                        <div class="has-error" ng-show="order_form.$submitted || order_form.table_number.$touched">
                                            <span class="error text-small block ng-scope" ng-show="order_form.table_number.$error.required">Table Number is required.</span>			      
                                        </div>
                                    </span>
                                    <!-- </button> -->
                                    <input type="hidden" class="form-control" ng-model="table_id" name="table_id" id="table_id"> 
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="text" class="form-control" ng-model="no_of_person" id="no_of_person" name="number_of_person" required placeholder="Number of person" ng-change="update_order()">	
                                            <!-- <span class="input-group-addon"> Number of person </span>	 -->	
                                            <div class="has-error" ng-show="order_form.$submitted || order_form.number_of_person.$touched">
                                                <span class="error text-small block ng-scope" ng-show="order_form.number_of_person.$error.required">Number of person is required.</span>											    			      
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="col-md-4 form-group">  
                                    <select class="form-control" name="waiter_id" ng-options="item as item.waiter_code for item in waiter_list track by item.waiter_id" ng-model="waiter_id" id="waiter_div" required>
                                    	 
                                    	 <option value="">Select Waiter</option>
                                    		</select>
                                    
                                    			
                                    
                                    
                                    		<div class="has-error" ng-show="order_form.$submitted || order_form.waiter_id.$touched">
                                        <span class="error text-small block ng-scope" ng-show="order_form.waiter_id.$error.required">Waiter is required.</span>						      
                                      </div>
                                    
                                    </div> -->
                                <div class="form-group col-md-4">
                                    <ui-select ng-model="waiter_list.selected" theme="bootstrap" ng-required="true" on-select="update_order()">
                                        <!-- ui-select-required -->
                                        <ui-select-match placeholder="Select Captain...">
                                            {{$select.selected.waiter_code}}
                                        </ui-select-match>
                                        <ui-select-choices repeat="item in waiter_list | propsFilter: {waiter_code: $select.search} "  >
                                            <!-- | filter: $select.search -->
                                            <div ng-bind-html="item.waiter_code | highlight: $select.search"></div>
                                        </ui-select-choices>
                                    </ui-select>
                                    <div class="has-error"><span class="error text-small block ng-scope" ng-show="waiter_list.selected.waiter_id==null || waiter_list.selected.waiter_id=='' " >Waiter is required</span></div>
                                    <!-- <div class="has-error" ng-show="order_form.$submitted || order_form.waiter_list.selected.$touched">
                                        <span class="error text-small block ng-scope" ng-show="order_form.waiter_list.selected.$error.ui-select-required">waiter_list.selected is required.
                                        </span>	
                                        </div> -->
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <ui-select ng-model="product_list.selected" id="product_list" theme="bootstrap" ng-required="true"  >
                                        <!-- ui-select-required -->
                                        <ui-select-match placeholder="Search by Product...">
                                            {{$select.selected.name}}
                                        </ui-select-match>
                                        <ui-select-choices repeat="item in product_list | propsFilter: {name: $select.search, product_code: $select.search}  "  >
                                            <!-- | filter: { name: $select.search }  -->
                                            <!-- <small ng-bind-html="item.product_code | highlight: $select.search"></small> -->
                                            <div ng-bind-html="item.name | highlight: $select.search"></div>
                                        </ui-select-choices>
                                    </ui-select>
                                    <div class="has-error"><span class="error text-small block ng-scope" ng-show="product_list.selected.product_id==null || product_list.selected.product_id=='' " >Product is required</span></div>
                                    <!-- <div class="has-error" ng-show="order_form.$submitted || order_form.product_list.selected.$touched">
                                        <span class="error text-small block ng-scope" ng-show="order_form.product_list.selected.$error.ui-select-required">product_list.selected is required.</span>	</div> -->
                                </div>
                                <div class="form-group col-md-3">
                                    <input type="text" class="form-control" ng-model="product_qty" ng-pattern="/^\d{0,9}(\.\d{1,9})?$/" name="product_qty" placeholder="Enter Product Quantity" required ng-enter="addProduct()" focus-name="5" next-focus="1" >
                                    <div class="has-error" ng-show="order_form.$submitted || order_form.product_qty.$touched">
                                        <span class="error text-small block ng-scope" ng-show="order_form.product_qty.$error.required">Quantity is required.</span>	
                                        <span class="error text-small block ng-scope" ng-show="order_form.product_qty.$error.pattern">Not a valid Number!</span>					      
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- <ul>
                                        <li ng-repeat="(key, errors) in order_form.$error track by $index"> <strong>{{ key }}</strong> errors
                                          <ul>
                                            <li ng-repeat="e in errors">{{ e.$name }} has an error: <strong>{{ key }}</strong>.</li>
                                          </ul>
                                        </li>
                                        </ul> -->
                                    <!-- <ul>
                                        <li ng-repeat="(key, errors) in parcel_order_form.$error track by $index"> <strong>{{ key }}</strong> errors
                                          <ul>
                                            <li ng-repeat="e in errors">{{ e.$name }} has an error: <strong>{{ key }}</strong>.</li>
                                          </ul>
                                        </li>
                                        </ul> -->
                                    <button type="submit" class="btn btn-primary btn-o" ng-disabled="order_form.$invalid"  ng-click="addProduct()">Add Product</button>
                                </div>
                            </div>
                            <div class="table-responsive" id="product_table_div">
                                <table class="table table-bordered table-hover" id="sample-table-1">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>#</th>
                                            <!--<th>Product ID</th>-->
                                            <th>Item Name</th>
                                            <th>Code</th>
                                            <th>Qty </th>
                                            <th>Amount</th>
                                            <th ng-show="show_tax_1">{{tax_1}}</th>
                                            <th ng-show="show_tax_2">{{tax_2}}</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!--| unique:'product_id'"-->
                                        <tr ng-repeat="productRows in products track by $index"  ng-init="calculateTotal(productRows)">
                                            <th style="text-align:center"><a tooltip="Remove" ng-click="removeProduct(productRows.product_id,productRows.service_tax_percent,productRows.other_tax_percent,productRows)"><i class="fa fa-times fa fa-white"></i></a></th>
                                            <td>{{$index+1}}</td>
                                            <!--<td>{{productRows.product_id}}</td>-->
                                            <td>{{productRows.name}}</td>
                                            <td>{{productRows.product_code}}</td>
                                            <td>
                                                <input type="text" ng-model="productRows.quantity"  style="width:40px" ng-change="quantityChange(productRows.product_id,productRows.quantity,productRows.order_item_id)" value="{{productRows.quantity}}" tabindex="-1" ><!-- name="product_quantity_{{productRows.product_id}}" -->
                                            </td>
                                            <td>{{productRows.price}}</td>
                                            <!-- <td>{{productRows.tax_percent}}</td>	 -->
                                            <td ng-show="show_tax_1">{{productRows.service_tax_percent}}</td>
                                            <td ng-show="show_tax_2">{{productRows.other_tax_percent}}</td>
                                            <!-- <th>{{(productRows.quantity*productRows.price)+((productRows.quantity*productRows.price)*(parseFloat((productRows.service_tax_percent==null) ? 0 : productRows.service_tax_percent)+parseFloat((productRows.other_tax_percent==null) ? 0 : productRows.other_tax_percent)))/100}}</th> -->
                                            <td>{{ getTotal(productRows) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div style="float:right" id="total_div">
                                <!-- <div class="form-group">
                                    <label for="order_type"  class="control-label">Discount Type:</label>					        		
                                     	<select class="form-control" name="discount_type" ng-model="discount_type" ng-options="item as item.value for item in discount_type_list track by item.key"  >
                                     		<option value="" ng-if ="false"></option>
                                     	 	</select>
                                    
                                     		<div ng-show="discount_type.key==2">
                                     			<label for="discount_amount" class="control-label">Discount Amount: </label>
                                    		<input type="text" class="form-control" ng-model="discount_amount" id="discount_amount" placeholder="Discount" name="discount_amount" only-digits> 
                                    	</div>          								
                                    </div> -->
                                <table>
                                    <tr>
                                        <td>Total Qty : </td>
                                        <td align="right">{{totalDisplayQty().toFixed(2)}}</td>
                                    </tr>
                                    <tr>
                                        <td>Total Amount : </td>
                                        <td align="right">{{totalAmount().toFixed(2)}}</td>
                                    </tr>
                                    <tr id="total_tax_1_div"  ng-show="show_tax_1">
                                        <td>{{tax_1}}  : </td>
                                        <td align="right">{{service_tax_total().toFixed(2)}}</td>
                                    </tr>
                                    <tr id="total_tax_2_div"  ng-show="show_tax_2">
                                        <td>{{tax_2}} : </td>
                                        <td align="right">{{other_tax_total().toFixed(2)}}</td>
                                    </tr>
                                    <tr id="final_amount_div">
                                        <td>Sub Total : </td>
                                        <td align="right">{{finalTotal().toFixed(2)}}</td>
                                    </tr>
                                    <tr>
                                        <td>Discount Type:</td>
                                        <td>
                                            <select  ng-change="requestDiscounttype()" class="form-control" name="discount_type" ng-model="discount_type" ng-options="item as item.value for item in discount_type_list track by item.key" ng-disabled="!online" tabindex="-1">
                                                <option value="" ng-if ="false"></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr ng-show=" discount_type.key==0 || discount_type.key==2 || discount_type.key==3 || discount_type.key==1">
                                        <td>Note: </td>
                                        <td>
                                            <input type="text" class="form-control" ng-model="discount_amount_dummy" ng-change="requestDiscounttype()" id="discount_amount_dummy" placeholder="Discount" name="discount_amount_dummy" ng-show="discountAmtEle" tabindex="-1" value="0" ng-disabled="!online" only-digits>
                                            <input type="hidden" class="form-control" ng-model="discount_amount"  id="discount_amount" placeholder="Discount" name="discount_amount" tabindex="-1" ng-disabled="!online" only-digits>
                                            <textarea  type="text" class="form-control" ng-model="note" ng-change="requestDiscounttype()" id="note" placeholder="Note" name="note" ng-disabled="!online" ></textarea>
                                            <div class="has-error" id="note_error" ng-show="discount_type.key==2 || discount_type.key==3 || discount_type.key==1">
                                                <span class="error text-small block ng-scope" >Note is required.</span>	
                                            </div>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Payment Type:</td>
                                        <td>
                                            <select  class="form-control" name="payment_type" ng-model="payment_type" ng-options="item as item.value for item in payment_type_list track by item.key"  tabindex="-1" ng-change="updatePaymentType()" ng-disabled="!online">
                                                <option value="" ng-if ="false"></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr ng-show="payment_type.key==2">
                                        <td>Credit Card Number: </td>
                                        <td><input style="width: 50%" type="text" class="form-control" ng-model="payment_card_number" id="payment_card_number" placeholder="Credit Card Number" name="payment_card_number" tabindex="-1" ng-keyup="changePayment_card_numberOnKeyup(payment_card_number)" ng-keydown="changePayment_card_numberOnKeydown(payment_card_number)" ng-disabled="!online" only-digits></td>
                                    </tr>
                                    <tr ng-show="payment_type.key==3">
                                        <td>Debit Card Number: </td>
                                        <td><input style="width: 50%" type="text" class="form-control" ng-model="payment_card_number" ng-keyup="changePayment_card_numberOnKeyup(payment_card_number)" ng-keydown="changePayment_card_numberOnKeydown(payment_card_number)"  id="payment_card_number" placeholder="Debit Card Number" name="payment_card_number" tabindex="-1" ng-disabled="!online" only-digits></td>
                                    </tr>
                                    <tr ng-repeat="bs_tax in branchSpecificTax_list">
                                        <td>{{bs_tax.tax_name}} ( {{bs_tax.tax_percent}} % ) : </td>
                                        <td align="right">	{{calculateBranchSpecificTax(bs_tax.tax_percent)}} </td>
                                    </tr>
                                    <tr>
                                        <td>TOTAL : </td>
                                        <td align="right" >{{finalOrderTotal().toFixed(2)}}</td>
                                    <tr style="font-size:18px;color:#5b5b60 ;padding:5px 0px;">
                                        <td><b>GRAND TOTAL : </b></td>
                                        <td align="right" ><b>{{round_off_finalOrderTotal().toFixed(2)}}</b></td>
                                    <tr >
                                        <td>Given Amount : </td>
                                        <td  ><input style="width:100%;" id="given_amount_div" type="text" name="given_amount" ng-keyup="changeGivenAmountOnKeyup(given_amount)" ng-keydown="changeGivenAmountOnKeydown(given_amount)" ng-model="given_amount" tabindex="-1" ng-disabled="!online" only-digits></td>
                                    <tr>
                                        <td>Return Amount : </td>
                                        <td align="right" >{{(given_amount-round_off_finalOrderTotal()).toFixed(2)}}</td>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <!-- <br><br><br><br><br><br><br>
                                    <div class="form-group">
                                       			<label for="order_type"  class="control-label">Discount:</label>
                                         		
                                        		 	<select class="form-control" name="discount_type" ng-options="item as item.value for item in discount_type_list track by item.key" ng-model="discount_type" >
                                         	 		<option value="" ng-if ="false"></option>
                                           	 	</select>
                                    
                                           		<label for="discount_amount"  class="control-label">Discount: </label>
                                    								<input type="text" class="form-control" ng-model="discount_amount" id="discount_amount" placeholder="Discount" name="discount_amount" >
                                      								
                                       		</div> -->
                                <button  ng-disabled="countTotalProductsLive() || !online" class="btn btn-lg btn-primary btn-o hidden-print" ng-click="resetOrder()">
                                    <!-- ng-click="placeOrder()" -->
                                    Reset
                                    <!-- <i class="fa fa-check"></i> -->
                                </button>
                                <a id="printButtonTbl" class="btn btn-lg btn-primary hidden-print" ng-disabled="countTotalProducts_printDiv() || !online" ng-click="printInvoice()" >
                                    Print <!-- my-enter="printInvoice()" -->
                                    <i class="fa fa-print"></i>
                                </a>
                                <hr>
                                Change Table No. :
                                <input type="text" id="change_table_number" style="width:70px" ng-model="change_table_number" name="change_table_number"  > 
                                <br><br>
                                <a id="change_table" class="btn btn-primary" ng-click="change_table()" ng-disabled="countTotalProducts_printDiv() || !online">Change</a>
                                <hr>
                                <a id="printButtonTbl" class="btn btn-lg btn-primary hidden-print" ng-disabled="countTotalProducts_printDiv() || !online" ng-click="printInvoiceAllBrands()" >
                                    KOT <!-- my-enter="printInvoice()" -->
                                    <i class="fa fa-print"></i>
                                </a>
                                <!-- <div ng-repeat="brand in brand_list">
                                    <a style="margin:3px" class="btn btn-lg btn-primary hidden-print" ng-disabled="countTotalProducts_printDiv() || !online" ng-click="printTableInvoiceByBrand(brand.brand_id)" >{{brand.brand_name}} <i class="fa fa-print"></i></a>
                                    </div> -->
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </tab>
        <tab heading="Parcel Order">
            <div class="row" ng-controller="parcelOrderCtrl">
                <div class="col-md-12" id="parcel_bill_div" >
                    <!-- style="pointer-events:none;opacity:0.6" -->
                    <form action="javascript:void(0)" name="parcel_order_form" novalidate method="post" >
                        <input type="hidden" id="branch_name" name="branch_name" ng-model="branch_name"> 
                        <fieldset>
                            <legend>
                                Bill details <!-- - <span id="parcel_bill_number"></span> -->
                            </legend>
                            <!-- <div class="row">
                                <div class="form-group col-md-4">	
                                	<input type="text" name="customer_name" placeholder="Customer Name" ng-model="customer_name" class="form-control" focus-name="p1" required>	
                                	<div class="has-error" ng-show="parcel_order_form.$submitted || parcel_order_form.customer_name.$touched">
                                      <span class="error text-small block ng-scope" ng-show="parcel_order_form.customer_name.$error.required">Customer Name is required.</span>
                                    </div>
                                
                                </div>
                                <div class="form-group col-md-4">	
                                	<input type="text" name="customer_contact" placeholder="Customer Contact" ng-model="customer_contact" class="form-control" ng-pattern="/^\d{0,9}?$/" required only-digits>	
                                	<div class="has-error" ng-show="parcel_order_form.$submitted || parcel_order_form.customer_contact.$touched">
                                      <span class="error text-small block ng-scope" ng-show="parcel_order_form.customer_contact.$error.required">Contact is required.</span>	
                                      <span class="error text-small block ng-scope" ng-show="parcel_order_form.customer_contact.$error.pattern">Not a valid Number!</span>					      
                                    </div>	
                                </div>
                                <div class="form-group col-md-4">
                                	<input type="text" name="customer_address" placeholder="Customer Address" ng-model="customer_address" class="form-control" required> 	
                                	<div class="has-error" ng-show="parcel_order_form.$submitted || parcel_order_form.customer_address.$touched">
                                      <span class="error text-small block ng-scope" ng-show="parcel_order_form.customer_address.$error.required">Customer Address is required.</span>
                                    </div>
                                
                                </div>
                                </div>	 -->
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <ui-select ng-model="waiter_list.selected" theme="bootstrap" ng-required="true" on-select="update_order_parcel()">
                                        <!-- ui-select-required -->
                                        <ui-select-match placeholder="Select Captain...">
                                            {{$select.selected.waiter_code}}
                                        </ui-select-match>
                                        <ui-select-choices repeat="item in waiter_list | propsFilter: {waiter_code: $select.search} "  >
                                            <!-- | filter: $select.search -->
                                            <div ng-bind-html="item.waiter_code | highlight: $select.search"></div>
                                        </ui-select-choices>
                                    </ui-select>
                                    <div class="has-error"><span class="error text-small block ng-scope" ng-show="waiter_list.selected.waiter_id==null || waiter_list.selected.waiter_id=='' " >Waiter is required</span></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <ui-select ng-model="parcel_product_list.selected" id="parcel_product_list" theme="bootstrap" ng-required="true"  >
                                        <!-- ui-select-required --> <!-- focus-name="p1" -->
                                        <ui-select-match placeholder="Search by Product...">
                                            {{$select.selected.name}}
                                        </ui-select-match>
                                        <ui-select-choices repeat="item in parcel_product_list | propsFilter: {name: $select.search, product_code: $select.search}    "  >
                                            <!-- <small ng-bind-html="item.product_code | highlight: $select.search"></small> -->
                                            <div ng-bind-html="item.name | highlight: $select.search"></div>
                                        </ui-select-choices>
                                    </ui-select>
                                </div>
                                <div class="form-group col-md-2">
                                    <input type="text" class="form-control" ng-model="parcel_product_qty" ng-pattern="/^\d{0,9}(\.\d{1,9})?$/" name="product_qty" placeholder="Enter Product Qty" required ng-enter="addParcelProduct()" ><!-- focus-name="p5" next-focus="p1"  -->
                                    <div class="has-error" ng-show="parcel_order_form.$submitted || parcel_order_form.product_qty.$touched">
                                        <span class="error text-small block ng-scope" ng-show="parcel_order_form.product_qty.$error.required">Quantity is required.</span>	
                                        <span class="error text-small block ng-scope" ng-show="parcel_order_form.product_qty.$error.pattern">Not a valid Number!</span>					      
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary btn-o" ng-disabled="parcel_order_form.$invalid"  ng-click="addParcelProduct()">Add Product</button>
                                </div>
                            </div>
                            <div class="table-responsive" id="product_table_div">
                                <table class="table table-bordered table-hover" id="sample-table-1">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>#</th>
                                            <!--<th>Product ID</th>-->
                                            <th>Item Name</th>
                                            <th>Code</th>
                                            <th>Qty </th>
                                            <th>Amount</th>
                                            <th ng-show="parcel_show_tax_1">{{parcel_tax_1}}</th>
                                            <th ng-show="parcel_show_tax_2">{{parcel_tax_2}}</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="productRows in parcel_products | unique:'product_id'" ng-init="calculateTotal(productRows)">
                                            <th style="text-align:center"><a tooltip="Remove" ng-click="parcel_removeProduct(productRows.product_id,productRows.service_tax_percent,productRows.other_tax_percent, productRows)"><i class="fa fa-times fa fa-white"></i></a></th>
                                            <td>{{$index+1}}</td>
                                            <!--<td>{{productRows.product_id}}</td>-->
                                            <td>{{productRows.name}}</td>
                                            <td>{{productRows.product_code}}</td>
                                            <td>
                                                <input type="text" ng-model="productRows.quantity" style="width:40px" ng-change="parcel_quantityChange(productRows.product_id,productRows.quantity)" value="{{productRows.quantity}}" tabindex="-1" ><!-- name="product_quantity_{{productRows.product_id}}" -->
                                            </td>
                                            <td>{{productRows.price}}</td>
                                            <!-- <td>{{productRows.tax_percent}}</td>	 -->
                                            <td ng-show="parcel_show_tax_1">{{productRows.service_tax_percent}}</td>
                                            <td ng-show="parcel_show_tax_2">{{productRows.other_tax_percent}}</td>
                                            <td>{{ parcel_getTotal(productRows) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div style="float:right" id="total_div">
                                <table>
                                    
                                    <tr>
                                        <td>Total Amount : </td>
                                        <td align="right">{{parcel_totalAmount().toFixed(2)}}</td>
                                    </tr>
                                    
                                    <tr id="parcel_total_tax_1_div"  ng-show="parcel_show_tax_1">
                                        <td>{{parcel_tax_1}}  : </td>
                                        <td align="right">{{parcel_service_tax_total().toFixed(2)}}</td>
                                    </tr>
                                    <tr id="parcel_total_tax_2_div"  ng-show="parcel_show_tax_2">
                                        <td>{{parcel_tax_2}} : </td>
                                        <td align="right">{{parcel_other_tax_total().toFixed(2)}}</td>
                                    </tr>
                                    
                                    <tr id="parcel_final_amount_div">
                                        <td>Sub Total : </td>
                                        <td align="right">{{parcel_finalTotal().toFixed(2)}}</td>
                                    </tr>

                                    <tr>
                                        <td>Discount Type:</td>
                                        <td align="right">
                                            <select  ng-change="parcel_requestDiscounttype()" class="form-control" name="discount_type" ng-model="parcel_discount_type" ng-options="item as item.value for item in parcel_discount_type_list track by item.key"  tabindex="-1" ng-disabled="!online">
                                                <option value="" ng-if ="false"></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr ng-show="parcel_discount_type.key==2">
                                        <td>Discount Amount: </td>
                                        <td align="right"><input type="text" class="form-control" ng-model="parcel_discount_amount" ng-change="parcel_requestDiscounttype()" id="parcel_discount_amount" placeholder="Discount" name="discount_amount" tabindex="-1" ng-disabled="!online" only-digits></td>
                                    </tr>
                                    


                                    <tr>
                                        <td>Payment Type:</td>
                                        <td>
                                            <select class="form-control" name="payment_type" ng-model="parcel_payment_type" ng-options="item as item.value for item in parcel_payment_type_list track by item.key"  tabindex="-1" ng-change="parcel_updatePaymentType()" ng-disabled="!online">
                                                <option value="" ng-if ="false"></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr ng-show="parcel_payment_type.key==2">
                                        <td>Credit Card Number: </td>
                                        <td><input  type="text" class="form-control" ng-model="parcel_payment_card_number" id="parcel_payment_card_number" placeholder="Credit Card Number" name="payment_card_number" tabindex="-1" ng-keyup="parcel_changePayment_card_numberOnKeyup(parcel_payment_card_number)" ng-keydown="parcel_changePayment_card_numberOnKeydown(parcel_payment_card_number)" ng-disabled="!online" only-digits></td>
                                    </tr>
                                    <tr ng-show="parcel_payment_type.key==3">
                                        <td>Debit Card Number: </td>
                                        <td><input  type="text" class="form-control" ng-model="parcel_payment_card_number" ng-keyup="changePayment_card_numberOnKeyup(parcel_payment_card_number)" ng-keydown="changePayment_card_numberOnKeydown(parcel_payment_card_number)"  id="parcel_payment_card_number" placeholder="Debit Card Number" name="payment_card_number" tabindex="-1" ng-disabled="!online" only-digits></td>
                                    </tr>


                                    <tr ng-repeat="bs_tax1 in parcel_branchSpecificTax_list">
                                        <td>{{bs_tax1.tax_name}} ( {{bs_tax1.tax_percent}} % ) :  </td>
                                        <td align="right">  {{calculateBranchSpecificTaxParcel(bs_tax1.tax_percent) | number:2}} </td>
                                    </tr>

                                    <tr>
                                        <td>TOTAL : </td>
                                        <td align="right" > {{parcel_finalOrderTotal().toFixed(2)}} </td>
                                    </tr>
                                    <tr style="font-size:18px;color:#5b5b60 ;padding:5px 0px;">
                                        <td><b>GRAND TOTAL : </b></td>
                                        <td align="right" ><b>{{round_off_parcel_finalOrderTotal().toFixed(2)}}</b></td>
                                    </tr>
                                    <tr >
                                        <td>Given Amount : </td>
                                        <td  ><input style="width:100%;" id="parcel_given_amount_div" type="text" name="given_amount" ng-keyup="parcel_changeGivenAmountOnKeyup(parcel_given_amount)" ng-keydown="parcel_changeGivenAmountOnKeydown(parcel_given_amount)" ng-model="parcel_given_amount" tabindex="-1" ng-disabled="!online" only-digits></td>
                                    </tr>
                                    <tr>
                                        <td>Return Amount : </td>
                                        <td align="right" >{{(parcel_given_amount-parcel_finalOrderTotal()).toFixed(2)}}</td>
                                    </tr>
                                </table>
                                
                            </div>
                            <div class="col-md-4">
                                <button  ng-disabled="countTotalProductsLive() || !online" class="btn btn-lg btn-primary btn-o hidden-print" ng-click="parcel_resetOrder()">Reset</button>
                                <a id="printButtonParcel" class="btn btn-lg btn-primary hidden-print" ng-disabled="!online || countTotalProducts_printDiv_parcel()" ng-click="parcel_printInvoice()">Print<i class="fa fa-print"></i></a>
                                <a id="printButtonParcel" class="btn btn-lg btn-primary hidden-print" ng-disabled="countTotalProducts_printDiv_parcel() || !online" ng-click="parcel_printInvoiceAllBrands()" >
                                    KOT <!-- my-enter="printInvoice()" -->
                                    <i class="fa fa-print"></i>
                                </a>
                                <!-- countTotalProducts_printDiv() || -->
                                <!-- ng-disabled="countTotalProducts_printDiv()" -->
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </tab>
        <tab heading="Delivery Order">
            <!-- Delivery Order : Coming Soon.......... -->
            <div class="row" ng-controller="deliveryOrderCtrl">
                <div class="col-md-12" id="delivery_bill_div" >
                    <!-- style="pointer-events:none;opacity:0.6" -->
                    <form action="javascript:void(0)" name="delivery_order_form" novalidate method="post" >
                        <input type="hidden" id="branch_name" name="branch_name" ng-model="branch_name"> 
                        <fieldset>

                            <legend>
                                Bill details <!-- - <span id="delivery_bill_number"></span> -->
                            </legend>
                            <div class="row">
                                <div class="col-md-12" style="font-size:18px;margin-bottom: 10px;">Customer Details</div>
                                <div class="form-group col-md-4">
                                    <label>Contact Number: </label>
                                    <input type="text" name="customer_contact" placeholder="Customer Contact" ng-model="customer_contact" class="form-control"  required only-digits autocomplete="off">   
                                    <!-- ng-pattern="/^\d{0,9}?$/" -->
                                    <div class="has-error" ng-show="delivery_order_form.$submitted || delivery_order_form.customer_contact.$touched">
                                        <span class="error text-small block ng-scope" ng-show="delivery_order_form.customer_contact.$error.required">Contact is required.</span>    
                                        <span class="error text-small block ng-scope" ng-show="delivery_order_form.customer_contact.$error.pattern">Not a valid Number!</span>                        
                                        </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Name: </label>
                                    <input type="text" name="customer_name" placeholder="Customer Name" ng-model="customer_name" class="form-control" focus-name="p1" required>	
                                    <div class="has-error" ng-show="(delivery_order_form.$submitted || delivery_order_form.customer_name.$touched) && (customer_contact!=0)">
                                        <span class="error text-small block ng-scope" ng-show="delivery_order_form.customer_name.$error.required">Customer Name is required.</span>
                                    </div>
                                </div>
                                
                                <div class="form-group col-md-4">
                                    <label>Delivery Address: </label>
                                    <textarea type="text" name="customer_address" placeholder="Customer Address" ng-model="customer_address" class="form-control" required></textarea>
                                    <div class="has-error" ng-show="(delivery_order_form.$submitted || delivery_order_form.customer_address.$touched) && (customer_contact!=0)">
                                        <span class="error text-small block ng-scope" ng-show="delivery_order_form.customer_address.$error.required">Customer Address is required.</span>
                                    </div>
                                </div>
                            </div>
                            <hr style="margin-top: 0px;">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <ui-select ng-model="delivery_product_list.selected" theme="bootstrap" ng-required="true"  >
                                        <!-- ui-select-required --> <!-- focus-name="p1" -->
                                        <ui-select-match placeholder="Search by Product...">
                                            {{$select.selected.name}}
                                        </ui-select-match>
                                        <ui-select-choices repeat="item in delivery_product_list | propsFilter: {name: $select.search, product_code: $select.search}    "  >
                                            <!-- <small ng-bind-html="item.product_code | highlight: $select.search"></small> -->
                                            <div ng-bind-html="item.name | highlight: $select.search"></div>
                                        </ui-select-choices>
                                    </ui-select>
                                </div>
                                <div class="form-group col-md-4">
                                    <input type="text" class="form-control" ng-model="delivery_product_qty" ng-pattern="/^\d{0,9}(\.\d{1,9})?$/" name="product_qty" placeholder="Enter Product Quantity" required ng-enter="addDeliveryProduct()" ><!-- focus-name="p5" next-focus="p1"  -->
                                    <div class="has-error" ng-show="delivery_order_form.$submitted || delivery_order_form.product_qty.$touched">
                                        <span class="error text-small block ng-scope" ng-show="delivery_order_form.product_qty.$error.required">Quantity is required.</span>	
                                        <span class="error text-small block ng-scope" ng-show="delivery_order_form.product_qty.$error.pattern">Not a valid Number!</span>					      
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary btn-o" ng-disabled="delivery_order_form.$invalid"  ng-click="addDeliveryProduct()">Add Product</button>
                                </div>
                            </div>
                            <div class="table-responsive" id="product_table_div">
                                <table class="table table-bordered table-hover" id="sample-table-1">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>#</th>
                                            <th>Product ID</th>
                                            <th>Product Name</th>
                                            <th>Code</th>
                                            <th>Qty </th>
                                            <th>Amount</th>
                                            <th ng-show="delivery_show_tax_1">{{delivery_tax_1}}</th>
                                            <th ng-show="delivery_show_tax_2">{{delivery_tax_2}}</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="productRows in delivery_products | unique:'product_id'" ng-init="calculateTotal(productRows)">
                                            <th style="text-align:center"><a tooltip="Remove" ng-click="delivery_removeProduct(productRows.product_id,productRows.service_tax_percent,productRows.other_tax_percent)"><i class="fa fa-times fa fa-white"></i></a></th>
                                            <td>{{$index+1}}</td>
                                            <td>{{productRows.product_id}}</td>
                                            <td>{{productRows.name}}</td>
                                            <td>{{productRows.product_code}}</td>
                                            <td>
                                                <input type="text" ng-model="productRows.quantity" style="width:40px" ng-change="delivery_quantityChange(productRows.product_id,productRows.quantity)" value="{{productRows.quantity}}" tabindex="-1" ><!-- name="product_quantity_{{productRows.product_id}}" -->
                                            </td>
                                            <td>{{productRows.price}}</td>
                                            <!-- <td>{{productRows.tax_percent}}</td>	 -->
                                            <td ng-show="delivery_show_tax_1">{{productRows.service_tax_percent}}</td>
                                            <td ng-show="delivery_show_tax_2">{{productRows.other_tax_percent}}</td>
                                            <td>{{ delivery_getTotal(productRows) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div style="float:right" id="total_div">

                                    <table>
                                    <tr>
                                        <td>Total Amount : </td>
                                        <td align="right">{{delivery_totalAmount().toFixed(2)}}</td>
                                    </tr>
                                    <tr id="delivery_total_tax_1_div"  ng-show="delivery_show_tax_1">
                                        <td>{{delivery_tax_1}}  : </td>
                                        <td align="right">{{delivery_service_tax_total().toFixed(2)}}</td>
                                    </tr>
                                    <tr id="delivery_total_tax_2_div"  ng-show="delivery_show_tax_2">
                                        <td>{{delivery_tax_2}} : </td>
                                        <td align="right">{{delivery_other_tax_total().toFixed(2)}}</td>
                                    </tr>
                                    <tr id="delivery_final_amount_div">
                                        <td>Sub Total : </td>
                                        <td align="right">{{delivery_finalTotal().toFixed(2)}}</td>
                                    </tr>
                                    <tr>
                                        <td>Discount Type:</td>
                                        <td align="right">
                                            <select ng-change="delivery_requestDiscounttype()" class="form-control" name="discount_type" ng-model="delivery_discount_type" ng-options="item as item.value for item in delivery_discount_type_list track by item.key" ng-disabled="!online" tabindex="-1">
                                                <option value="" ng-if ="false"></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr ng-show="delivery_discount_type.key==2">
                                        <td>Discount Amount: </td>
                                        <td align="right"><input type="text" class="form-control" ng-model="delivery_discount_amount" ng-change="delivery_requestDiscounttype()" id="delivery_discount_amount" placeholder="Discount" name="discount_amount" tabindex="-1" ng-disabled="!online" only-digits></td>
                                    </tr>
                                    <tr>
                                        <td>Payment Type:</td>
                                        <td>
                                            <select class="form-control" name="payment_type" ng-model="delivery_payment_type" ng-options="item as item.value for item in delivery_payment_type_list track by item.key"  tabindex="-1" ng-disabled="!online" ng-change="delivery_updatePaymentType()">
                                                <option value="" ng-if ="false"></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr ng-show="delivery_payment_type.key==2">
                                        <td>Credit Card Number: </td>
                                        <td><input  type="text" class="form-control" ng-model="delivery_payment_card_number" id="delivery_payment_card_number" placeholder="Credit Card Number" name="payment_card_number" tabindex="-1" ng-keyup="delivery_changePayment_card_numberOnKeyup(delivery_payment_card_number)" ng-keydown="delivery_changePayment_card_numberOnKeydown(delivery_payment_card_number)" ng-disabled="!online"  only-digits></td>
                                    </tr>
                                    <tr ng-show="delivery_payment_type.key==3">
                                        <td>Debit Card Number: </td>
                                        <td><input  type="text" class="form-control" ng-model="delivery_payment_card_number" ng-keyup="changePayment_card_numberOnKeyup(delivery_payment_card_number)" ng-keydown="changePayment_card_numberOnKeydown(delivery_payment_card_number)"  id="delivery_payment_card_number" placeholder="Debit Card Number" name="payment_card_number" tabindex="-1" ng-disabled="!online" only-digits></td>
                                    </tr>
                                    <tr ng-repeat="bs_tax2 in delivery_branchSpecificTax_list">
                                        <td>{{bs_tax2.tax_name}} ( {{bs_tax2.tax_percent}} % ) :  </td>
                                        <td align="right">{{calculateBranchSpecificTaxDelivery(bs_tax2.tax_percent)}} </td>
                                    </tr>

                                    <tr>
                                        <td>TOTAL : </td>
                                        <td align="right" > {{delivery_finalOrderTotal().toFixed(2)}} </td>
                                    </tr>
                                    <tr style="font-size:18px;color:#5b5b60 ;padding:5px 0px;">
                                        <td><b>GRAND TOTAL : </b></td>
                                        <td align="right" ><b>{{round_off_delivery_finalOrderTotal().toFixed(2)}}</b></td>
                                    </tr>
                                    <tr >
                                        <td>Given Amount : </td>
                                        <td><input style="width:100%;" id="delivery_given_amount_div" type="text" name="given_amount" ng-keyup="delivery_changeGivenAmountOnKeyup(delivery_given_amount)" ng-keydown="delivery_changeGivenAmountOnKeydown(delivery_given_amount)" ng-model="delivery_given_amount" tabindex="-1" ng-disabled="!online" only-digits></td>
                                    </tr>
                                    <tr>
                                        <td>Return Amount : </td>
                                        <td align="right" >{{(delivery_given_amount-delivery_finalOrderTotal()).toFixed(2)}}</td>
                                    </tr>
                                </table>
                            </div>
                            <span ng-click="openViewBillModal()"> click me</span>
                            <div class="col-md-4">
                                <button  ng-disabled="countTotalProductsLive() || !online" class="btn btn-lg btn-primary btn-o hidden-print" ng-click="delivery_resetOrder()">Reset</button>
                                <a id="printButtonDelivery" class="btn btn-lg btn-primary hidden-print" ng-disabled="!online || countTotalProducts_printDiv_delivery()" ng-click="delivery_printInvoice()">Print<i class="fa fa-print"></i></a>
                                <!-- countTotalProducts_printDiv() ||  -->
                                <a id="printButtonDelivery" class="btn btn-lg btn-primary hidden-print" ng-disabled="countTotalProducts_printDiv_delivery() || !online" ng-click="delivery_printInvoiceAllBrands()" >
                                    KOT <!-- my-enter="printInvoice()" -->
                                    <i class="fa fa-print"></i>
                                </a>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </tab>
    </tabset>
</div>
<!-- end: RESPONSIVE TABLE -->
<script type="text/ng-template" id="myModalContent.html">
    <div class="modal-body">
    	Do you want to add quantity to existing product ?
    </div>
    <div class="modal-footer">
    	<button class="btn btn-primary" ng-click="ok()">OK</button>
    	<button class="btn btn-primary btn-o" ng-click="cancel()">Cancel</button>
    </div>
</script>	
<script type="text/ng-template" id="myModalContent1.html">
    <form action="javascript:void(0)" name="payment_order_form" novalidate method="post" >
    		<div class="modal-body">
    		<tr><td>Payment Type:</td>
    		<td>
    		<select class="form-control" name="recent_payment_type" ng-model="recent_payment_type" ng-options="item as item.value for item in recent_payment_type_list track by item.key"  tabindex="-1" ng-disabled="!online">
    		 		<option value="" ng-if ="false"></option>
    	  	 	</select>
    	  	 </td></tr>
    	  	 <tr ng-show="recent_payment_type.key==2"><td>Transaction/Voucher Number: </td><td><input type="text" class="form-control" ng-model="payment_card_number" id="payment_card_number" placeholder="Transaction/Voucher Number" name="payment_card_number" tabindex="-1" ng-disabled="!online"></td></tr>
    	  
    	</div>
    	<div class="modal-footer">
    		<button class="btn btn-primary" ng-click="ok()">Save</button>
    		<button class="btn btn-primary btn-o" ng-click="cancel()">Cancel</button>
    	</div>
    
    </form>
    
</script>
<script type="text/ng-template" id="viewBillModal.html">
    <!-- Right Aside -->
    <!--<div class="modal fade modal-aside horizontal right bs-example-modal-right"  tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-sm">
            <div class="modal-content">-->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                </div>
                <div class="modal-body">
                    Modal Content
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-o" data-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary">
                        Save changes
                    </button>
                </div>
            <!--</div>
        </div>
    </div>-->
    <!-- /Right Aside -->
</script>