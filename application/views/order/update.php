

<div class="container-fluid container-fullw bg-white" >



			<div class="row" ng-controller="orderUpdateCtrl">

			<a ng-click="gotoOrderList()" class="btn btn-primary" title="Back" style="float:right" >Back</a>


				<div class="col-md-12" id="bill_div" ><!-- style="pointer-events:none;opacity:0.6" -->


				<form action="javascript:void(0)" name="order_form" novalidate="novalidate" method="post" >

					<input type="hidden" id="branch_name" name="branch_name" ng-model="branch_name"> 
					
						<fieldset>
							<legend>Bill details - <span id="bill_number"></span></legend>		
						
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
									<th ng-show="show_tax_1">{{tax_1}}</th>
									<th ng-show="show_tax_2">{{tax_2}}</th>
									<th>Total</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="productRows in products | unique:'product_id'" ng-init="calculateTotal(productRows)">
								  <th style="text-align:center"><a tooltip="Remove" ng-click="removeProduct(productRows.product_id,productRows.service_tax_percent,productRows.other_tax_percent,productRows)"><i class="fa fa-times fa fa-white"></i></a></th>
								  <td>{{$index+1}}</td>
							      <td>{{productRows.product_id}}</td>
							      <td>{{productRows.name}}</td>
							      <td>{{productRows.product_code}}</td>
							      <td>{{productRows.quantity}}</td>					      
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
						<a id="printButtonTbl" class="btn btn-lg btn-primary hidden-print" ng-disabled="countTotalProducts_printDiv() || !online" ng-click="printInvoice()" >
								Print <!-- my-enter="printInvoice()" -->
								<i class="fa fa-print"></i>
								</a>

						</fieldset>	
				</form>						
				</div>

			</div>

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
