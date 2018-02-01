<!-- start: PAGE TITLE -->
<section id="page-title">
	<div class="row"  >
		<div class="col-sm-8">
		<h1 class ="mainTitle">Sales Report</h1>
			<!-- <h1 class="mainTitle" translate="sidebar.nav.forms.ELEMENTS">Waiter Sales Report</h1> -->
		</div>
	</div>
</section>
<!-- end: PAGE TITLE -->


<!-- start: DATE/TIME Picker -->
<div class="container-fluid container-fullw" ng-controller="salesCtrl">
	<div class="row">
		<div class="col-md-12" >
			
			<div class="panel panel-white no-radius" >
				<div class="panel-body">
					<!-- /// controller:  'DatepickerDemoCtrl' -  localtion: assets/js/controllers/bootstrapCtrl.js /// -->
					<div >
					<form action="javascript:void(0)" method="post" name="report_form">
						
						<div class="row">
							
							<div class="col-md-6">
								<h5 class="text-bold margin-top-25 margin-bottom-15">Date Range</h5>
								<div class="input-group">

									<input type="text" name="fromdate" class="form-control" datepicker-popup="yyyy/MM/dd" ng-model="start"  is-open="startOpened" ng-init="startOpened = false" min-date="'1970-12-31'" max-date="end"  close-text="Close"  ng-click="startOpen($event)"/>
									<span class="input-group-addon">to</span>
									<input type="text" name="todate" class="form-control" datepicker-popup="yyyy/MM/dd" ng-model="end" is-open="endOpened" ng-init="endOpened = false" min-date="start" close-text="Close"  ng-click="endOpen($event)" />
									
								</div>
								<div class="form-group " ng-show="adminBranch">
										<h5 class="text-bold margin-top-25 margin-bottom-15"> Select Branch:</h5>
								          <select class="form-control" name="branch_id" ng-options="item as item.name for item in branch_list track by item.branch_id" ng-model="branch_id" ng-change="branchwiseBrand(branch_id)">
								          	<option value="">Select Branch</option>
								          </select>
						      	</div>
                                
                                <div class="form-group">
                                        
                                        <h5 class="text-bold margin-top-25 margin-bottom-15"> Select Brand:</h5>
                                            <select class="form-control" name="brand_id" ng-options="item as item.brand_name for item in brand_list_by_branch track by item.brand_id" ng-model="brand_id" >
                                                <option value="">Select Brand</option>
                                            </select>
                                </div>
						      	<br>
					      		<div class="form-group col-md-4">
					      			<input type="submit" name="Submit" class="btn btn-primary" value="Generate" ng-click="viewReport(branch_id)">
					      		</div>
					      		<div class="form-group col-md-4">
					      			<input type="submit" name="Submit" class="btn btn-default" value="Generate PDF" ng-click="viewReportPDF(branch_id)">					      		
					      		</div>
					      		<div class="col-md-4" >
					      			<span id="pdf_loader" style="display: none"><img src="uploads/ajax-loader.gif"></span>
					      		</div>
								
							</div>
						</div>
					</form>
					<button class="btn btn-link" ng-click="exportToExcel('#sales_table')" style="float:right">
      				<span class="glyphicon glyphicon-share"></span>
 					Export to Excel
 					</button>
 					
					<div class="col-md-12">
					    <fieldset>
					      <legend>Report</legend>
					        <div>
					        <table id="sales_table" class="table table-condensed table-hover">
					        <thead>
					          <tr>
					          	<th class="hidden-xs" align="center">Created</th>
					            <th class="hidden-xs" align="center">Bill No.</th>
							  <th class="hidden-xs" align="center">Table No.</th>
								<th class="hidden-xs" align="center">No. Of Person</th>
					            <!-- <th class="hidden-xs">Tax(%)</th> -->
					            <th class="hidden-xs" align="center">Net Amt.</th>
					            <!-- <th class="hidden-xs" align="center">Tax free</th> -->
					            <th class="hidden-xs" align="center">Order Type</th>
					            <th class="hidden-xs" align="center">Payment Type</th>
					            <th class="hidden-xs" align="center">Credit Card</th>
					            <th class="hidden-xs" align="center">Discount</th>
					            <th ng-repeat="column in tax_list_all" class="hidden-xs" align="center">{{column.tax_name}}({{column.tax_percent}}%)</th>
					            <th class="hidden-xs" align="center">Bill Amount</th>
					            <th class="hidden-xs" align="center">Roundoff</th>
					            <th class="hidden-xs" align="center">Total</th>
					            
					          </tr>
					        </thead>
					        <tbody>
					          <tr ng-repeat="row in saleslist">
					            
					            <td class="hidden-xs" >{{row.created}}</td>
					            <td class="hidden-xs" align="left">{{row.order_code}}</td>
							  <td class="hidden-xs" align="left">{{row.table_no}}</td>
							  <td class="hidden-xs" align="left">{{row.number_of_person}}</td>
					            <!-- <td class="hidden-xs" >{{row.totalTax}}</td> -->
					            <td class="hideen-xs" align="left">{{row.sub_total}}</td>
					            <!-- <td class="hideen-xs" align="left">{{row.tax_free}}</td> -->
					            
					            
					            <!-- order type -->
					            <td class="hideen-xs" ng-if="row.orderType === '1'">Table order</td>
					            <td class="hideen-xs" ng-if="row.orderType === '2'">Delivery</td>
					            <td class="hideen-xs" ng-if="row.orderType === '3'">Parcel</td>
					            <!-- end of order type -->


					            <td class="hideen-xs" >{{row.paymentType}}</td>
					            <td class="hideen-xs" ng-show="row.paymentType=='Credit Card'" >{{row.roundoff | number:2}}</td>
					            <td class="hideen-xs" ng-show="row.paymentType!='Credit Card'"></td>
					            
					            <td class="hideen-xs" align="left">{{(row.sub_total * row.discount / 100) | number:2}}</td>
					            <td class="hidden-xs" ng-repeat="th in tax_list_all" align="left">
							        {{ (row.order_tax[th.tax_id]).tax_amount | number:2}}
							    </td>

					            <td class="hidden-xs" align="left">{{row.bill_amount | number:2}}</td>
					            <td class="hidden-xs" align="left">{{ ( row.roundoff - row.bill_amount ) | number:2}}</td>
					            <td class="hidden-xs" align="left">{{row.roundoff | number:2}}</td>
					            

					          </tr>

					          <tr>
					          	<td><b>Total:</b></td>
					          	<td></td>
								
								<td></td>
								<td align="left"><b>{{getTotalCover() }}</b></td>
								<td align="left"><b>{{ getSubtotal().toFixed(2)}}</b></td>
					          	<!-- <td align="left"><b>{{ getTaxfreetotal().toFixed(2)}}</b></td> -->
					          	<td></td>
					          	<td></td>

					          	<!-- id="subTotalTd" -->

					          	<td class="hideen-xs" ><b>{{getcreditcardTotal().toFixed(2)}}</b></td>

					          	<td  align="left"><b>{{ gettotalDiscountTotal().toFixed(2)}}</b></td>

					          	 <td class="hidden-xs" ng-repeat="th in tax_list_all" align="left">
							        <!-- {{ (row.order_tax[th.tax_id]).tax_amount}} -->
							        <!-- {{th.tax_id}} -->
							        <b>{{ gettotalTaxAmount(th.tax_id) | number:2}}</b>
							    </td>

					          		<!-- {{insTd()}} -->

					          	<td align="left"><b>{{ getbill_amountTotal().toFixed(2) }}</b></td>
					          	
					          	<td align="left"><b>{{ getRound().toFixed(2) }}</b></td>


					          	<td align="left"><b>{{ getroundoffTotal().toFixed(2) }}</b></td>
					          	<td></td>
					          </tr>
					        </tbody>
					        </table>
					        </div>
					    </fieldset>
					  </div>
					</div>
					
				</div>
			</div>
		</div>
	</div>
</div>
<!-- end: DATE/TIME Picker -->
