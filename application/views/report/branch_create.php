<!-- start: PAGE TITLE -->
<section id="page-title">
	<div class="row"  >
		<div class="col-sm-8">
		<h1 class ="mainTitle">Branch Report</h1>
			<!-- <h1 class="mainTitle" translate="sidebar.nav.forms.ELEMENTS">Waiter Sales Report</h1> -->
		</div>
		
	</div>
</section>
<!-- end: PAGE TITLE -->

<!-- start: DATE/TIME Picker -->
<div class="container-fluid container-fullw" ng-controller="branchSalesCtrl">
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
								<div class="form-group col-md-12">
										<h5 class="text-bold margin-top-25 margin-bottom-15"> Select Branch:</h5>
								          <select class="form-control" name="branch_id" ng-options="item as item.name for item in branch_list track by item.branch_id" ng-model="branch_id" ng-change="branchwiseBrand(branch_id)">
								          <option value="">Select Branch</option>
								          </select>

						      	</div>

						      	<div class="form-group col-md-12">
										<h5 class="text-bold margin-top-25 margin-bottom-15"> Select Brand:</h5>
								          <select class="form-control" name="brand_id" ng-options="item as item.brand_name for item in brand_list_by_branch track by item.brand_id" ng-model="brand_id" >
								          <option value="">Select Brand</option>
								          </select>
						      	</div>
					      		<div class="form-group col-md-4">
					      		<input type="submit" name="Submit" class="btn btn-primary" value="Generate" ng-click="viewBranchReport(branch_id,brand_id)">
					      		</div>
					      		<div class="form-group col-md-4">
					      		<input type="submit" name="Submit" class="btn btn-default" value="Generate PDF" ng-click="viewBranchReportPDF(branch_id,brand_id)"><span id="pdf_loader" style="display: none"><img src="uploads/ajax-loader.gif"></span>
					      		</div>
							
								
							</div>
						</div>
					</form>

					<button class="btn btn-link" ng-click="exportToExcel('#sample-table-3')" style="float:right">
      				<span class="glyphicon glyphicon-share"></span>
 					Export to Excel
 					</button>

					<div class="col-md-12">
					    <fieldset>
					      <legend>Report</legend>
					        <div>
					        <table id="sample-table-3" class="table table-condensed table-hover">
					        <thead>
					          <tr>
					          	<th class="hidden-xs">Date</th>
					            <th class="hidden-xs">Bill No.</th>
					            <th class="hidden-xs">Net Amt.</th>
					            <th class="hidden-xs">Order Type</th>
					            <th class="hidden-xs">Discount</th>
					            <th ng-repeat="column in tax_list_all" class="hidden-xs" >{{column.tax_name}}({{column.tax_percent}}%)</th>
					            <!-- <th class="hidden-xs">Tax(%)</th> -->
					            <th class="hidden-xs">Total Bill Amount</th>
					            
					            <th class="hidden-xs">Roundoff</th>
					            <th class="hidden-xs">Total</th>
					            
					          </tr>
					        </thead>
					        <tbody>
					  
					          <tr ng-repeat="row in branchwisereport">
					          	<td class="hidden-xs" >{{row.created}}</td>
					            <td class="hidden-xs" >{{row.order_code}}</td>
					            <td class="hidden-xs" >{{row.orderType}}</td>
					            <td class="hideen-xs" >{{row.sub_total}}</td>
					            <td class="hidden-xs" >{{row.discount}}</td>
					            <td class="hidden-xs" ng-repeat="th in tax_list_all">
							        {{ (row.order_tax[th.tax_id]).tax_amount}}
							    </td>
							    <!-- <td class="hidden-xs">
							        {{ (row.order_tax[12]).tax_percent }}
							    </td>
							    <td class="hidden-xs">
							        {{ (row.order_tax[13]).tax_percent }}
							    </td> -->
					            <!-- <td class="hidden-xs" >{{row.tax_percent}}</td>
					            <td class="hidden-xs" >{{row.tax_percent}}</td> -->
					            <!-- <td class="hidden-xs" >{{row.totalTax}}</td> -->
					            <td class="hidden-xs" >{{row.bill_amount}}</td>
					            
					            <td class="hidden-xs" >{{row.roundoff_value}}</td>
					            <td class="hidden-xs" >{{row.roundoff}}</td>
					            
					          </tr>
					          <tr>
					          <td><b>Total:</b></td>
					          <td></td>
					          <td><b>{{ getSubtotal() }}</b></td>
					          <td ><b>{{ getTotaldiscount().toFixed(2) }}</b></td>
					          <td></td>

					          
					          <!-- {{insTd()}} id="subTotalTd" -->
					          <td class="hidden-xs" ng-repeat="th in tax_list_all">
							        <!-- {{ (row.order_tax[th.tax_id]).tax_amount}} -->
							        <!-- {{th.tax_id}} -->
							        <b>{{ gettotalTaxAmount(th.tax_id) }}</b>
							    </td>

					          
					          <td><b>{{ getTotalbillAmt().toFixed(2) }}</b></td>
					          <td><b>{{ getTotalRound().toFixed(2) }}</b></td>
					          
					          	<td><b>{{ getTotalRoundoff() }}</b></td>

					          	<!-- <td>{{ getTotal('sub_total') }}</td> -->
					          	<!-- <td>{{ getTotal('roundoff') }}</td> -->
					          </tr>


					        </tbody>
					      <!--   <tr>
					          <th>Total:</th>
					          <td></td>
					          <td></td>
					          </tr> -->
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
