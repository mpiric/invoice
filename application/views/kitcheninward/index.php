<!-- start: SIMPLE TABLE WITH PAGINATION -->
<div class="container-fluid container-fullw bg-white">
	<div class="row">
		<div class="col-md-12" ng-controller="kitcheninwardCtrl">
			<h5 class="over-title margin-bottom-15">Kitchen Inward</h5>
			
			<form action="javascript:void(0)" name="form_kitcheninward" novalidate="novalidate" ng-submit="dokitcheninward(form_kitcheninward)" method="post" ng-disabled="form_kitcheninward.$invalid">

			   <div class="row">
			   		<div class="col-md-2">
			   			<div class="input-group">
							<input type="text" name="inward_date" class="form-control" datepicker-popup="dd/MM/yyyy" ng-model="start" is-open="startOpened" ng-init="startOpened = false" min-date="'1970-12-31'" max-date="end" close-text="Close"  ng-click="startOpen($event)" datepicker-options="dateOptions" value="<?php echo date('Y-m-d'); ?>" ng-change="showBtn()"/>
						</div>			      
			    	</div> 
			    	<div class="col-md-2">
			      		<a class="btn btn-default" ng-click="filterBydate(start)" id="filterBtn">Filter</a>
			      		<span id="pdf_loader1" style="display: none"><img src="uploads/ajax-loader.gif"></span>
			      	</div>   
			    	<div class="col-md-8">
			      		<button type="submit" class="btn btn-primary" ng-disabled="form_kitcheninward.$invalid" style="float:right" id="submitBtn">Submit</button>
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
			   
				<div class="table-responsive" >

					<table ng-table="tableParams"  show-filter="true" class="table table-striped"><!-- show-filter="true" -->

						<tr ng-repeat="row in $data" > <!-- | orderBy:predicate:reverse -->

              					<input type="hidden" class="form-control" ng-model="store_product_inward_id" id="store_product_inward_id" name="store_product_inward_id" value= "{{row.store_product_inward_id}}">

								<td data-title="'Product'" sortable="'name'" filter="{ 'name': 'text' }" >{{row.name}}</td>

								<td data-title="'Unit'" sortable="'unit'" filter="{ 'unit': 'text' }" readonly>{{row.unit}}</td>

								<td data-title="'Store Instock'" sortable="'storeInstock'" filter="{ 'storeInstock': 'text' }" >{{row.storeInstock==null ? 0:row.storeInstock }}</td>

								<td data-title="'Kitchen Instock'" sortable="'instock'" filter="{ 'instock': 'text' }" >{{row.instock==null ? 0:row.instock}}</td>

								<!-- <td class="center" data-title="'Inwarded'" sortable="'is_available'" filter="{ 'is_available': 'text' }"><input type="checkbox" ng-model="row.is_available" name="is_available_{{row.store_product_inward_id}}" ng-disabled="!is_editable" ></td> -->
						
								<td data-title="'Inward Qty'" sortable="'inward_qty'" filter="{ 'inward_qty': 'text' }" >	
					                <input type="text" style="width:80px" name="inward_qty_{{row.store_product_inward_id}}" ng-model="row.inward_qty" ng-readonly="!is_editable" ng-disabled="validateInward(row.remaining_qty)" value="{{row.inward_qty}}" ng-blur="validateInwardQty(row.storeInstock,row.instock,row.inward_qty)" id="inward_qty_{{row.store_product_inward_id}}">  <!--  ng-disabled="!is_editable || disableInward" -->

					                 <div class="has-error" ng-show="form_kitcheninward.$submitted || form_kitcheninward.inward_qty_{{row.store_product_inward_id}}.$touched">
					                  <span class="error text-small block ng-scope" ng-show="form_kitcheninward.inward_qty_{{row.store_product_inward_id}}.$error.required">Inward Quantity is required.
					                  </span>
					                  <span class="error text-small block ng-scope" ng-show="err_inward_qty">Inward Quantity must be less than Instock.
					                  </span>
					                </div> 
					              </td>

								<td data-title="'Prepared Qty'" sortable="'prepared_qty'" filter="{ 'prepared_qty': 'text' }" >
									<input type="text" style="width:80px" name="prepared_qty_{{row.store_product_inward_id}}" value="{{row.prepared_qty}}" ng-readonly="!is_editable" ng-disabled="!is_editable" />
								</td>

								<td data-title="'Remaining Qty'" sortable="'remaining_qty'" filter="{ 'remaining_qty': 'text' }" >
									<input type="text" style="width:80px" name="remaining_qty_{{row.store_product_inward_id}}" value="{{row.remaining_qty}}" ng-readonly="!is_editable" ng-disabled="!is_editable" />
								</td>

								<td data-title="'Waste Qty'" sortable="'waste_qty'" filter="{ 'waste_qty': 'text' }" >
									<input type="text" style="width:80px" name="waste_qty_{{row.store_product_inward_id}}" value="{{row.waste_qty}}" ng-readonly="!is_editable" ng-disabled="!is_editable" />
								</td>

								<td data-title="'Inward Today'" sortable="'today_inward_qty'" filter="{ 'today_inward_qty': 'text' }" readonly>
					                {{(row.today_inward_qty == null) ? 0:row.today_inward_qty }}
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
	</div>
</div>

	