<!-- start: PAGE TITLE -->
<section id="page-title">
	<div class="row"  >
		<div class="col-sm-8">
		<h1 class ="mainTitle">Daily Sales Report</h1>
			<!-- <h1 class="mainTitle" translate="sidebar.nav.forms.ELEMENTS">Waiter Sales Report</h1> -->
		</div>
		
	</div>
</section>
<!-- end: PAGE TITLE -->

<!-- start: DATE/TIME Picker -->
<div class="container-fluid container-fullw" ng-controller="dailySalesReportCtrl">
	<div class="row">
		<div class="col-md-12" >
			
			<div class="panel panel-white no-radius" >
				<div class="panel-body">
					<!-- /// controller:  'DatepickerDemoCtrl' -  localtion: assets/js/controllers/bootstrapCtrl.js /// -->
					<div >
					<form action="javascript:void(0)" method="post" name="report_form">
						
						<div class="row">
							
							<div class="col-md-6">
								<h5 class="text-bold margin-top-25 margin-bottom-15">Date</h5>
								<div class="input-group">

									<input type="text" name="fromdate" class="form-control" datepicker-popup="yyyy/MM" ng-model="start"  is-open="startOpened" ng-init="startOpened = false" min-date="'1970-12-31'" max-date="end"  close-text="Close"  ng-click="startOpen($event)" datepicker-options="{minMode: 'month'}" datepicker-mode="'month'"/>
									
									
						      		
								</div>
								<div class="form-group col-md-12" ng-show="adminBranch">
										<h5 class="text-bold margin-top-25 margin-bottom-15"> Select Branch:</h5>
								          <select class="form-control" name="branch_id" ng-options="item as item.name for item in branch_list track by item.branch_id" ng-model="branch_id" ng-change="branchwiseWaiter(branch_id)">
								          <option value="">Select Branch</option>
								          </select>

						      	</div>
						      	<br>
					      		<div class="form-group col-md-4">
					      			<input type="submit" name="Submit" class="btn btn-primary" value="Generate" ng-click="viewReport(waiter_id)">
					      		</div>

					      		<div class="form-group col-md-4">
					      			<input type="submit" name="Submit" class="btn btn-default" value="Generate PDF" ng-click="viewReportPDF(branch_id,waiter_id)">
					      		</div>

					      		<div class="col-md-4" >
					      			<span id="pdf_loader" style="display: none"><img src="uploads/ajax-loader.gif"></span>
					      		</div>

								
							</div>
						</div>
					</form>


					<button class="btn btn-link" ng-click="exportToExcel('#daily_sales_table')" style="float:right">
      				<span class="glyphicon glyphicon-share"></span>
 					Export to Excel
 					</button>
 					
					<div class="col-md-12">
					    <fieldset>
					      <legend>Report</legend>
					        <div>
					        <table id="daily_sales_table" class="table table-condensed table-hover">
					        <thead>
					          <tr>
					          	<th class="hidden-xs" align="center">Created</th>
					            <th class="hidden-xs" align="center">Net Amt.</th>
					            <!-- <th class="hidden-xs" align="center">Tax Free</th> -->
					            <th class="hidden-xs" align="center">Discount</th>
					            <th ng-repeat="column in tax_list_all" class="hidden-xs" align="center">{{column.tax_name}}({{column.tax_percent}}%)</th>
					            <!-- <th class="hidden-xs">Tax(%)</th> -->
					            <th class="hidden-xs" align="center">Bill Amount</th>
					            <th class="hidden-xs" align="center">Roundoff</th>
					            <th class="hidden-xs" align="center">Total</th>
					            
					          </tr>
					        </thead>
					        <tbody>
					          <tr ng-repeat="row in saleslist">
					            
					            <td class="hidden-xs" >{{row.created}}</td>
					            
					            <td class="hideen-xs" align="left">{{row.net_amount ? row.net_amount : 0 | number:2}}</td>
					            <!-- <td class="hideen-xs" align="right">{{row.tax_free | number:2}}</td> -->

					            <td class="hideen-xs" align="left">{{row.discount | number:2}}</td>
					            
							    <!--<td class="hidden-xs" ng-repeat="th in tax_list_all" align="left">
							        {{ (row.order_tax[th.tax_id]).tax_amount | number:2 }}
							    </td>-->
								<td class="hidden-xs" align="left">
							        {{row.SGST | number:2}}
							    </td>
							    <td class="hidden-xs" align="left">
							        {{ row.CGST | number:2}}
							    </td>
							    <!-- <td class="hidden-xs" align="left">
							        {{row.VAT | number:2}}
							    </td> -->
							    <!-- <td class="hidden-xs">
							        {{ (row.order_tax[12]).tax_percent }}
							    </td>
							    <td class="hidden-xs">
							        {{ (row.order_tax[13]).tax_percent }}
							    </td> -->
					            <!-- <td class="hidden-xs" >{{row.totalTax}}</td> -->
					            <td class="hidden-xs" align="left">{{row.bill_amount | number:2}}</td>
					            <td class="hidden-xs" align="left">{{row.round_off | number:2}}</td>
					            <td class="hidden-xs" align="left">{{row.total | number:2}}</td>
					            

					          </tr>

					          <tr>
					          	<td><b>Total:</b></td>
					          	 
					          	<td align="left"><b>{{ getNettotal().toFixed(2) }}</b></td>
					          	<!-- <td align="left"><b>{{ getTaxfree().toFixed(2) }}</b></td> -->
					          	<td align="left"><b>{{ getTotaldiscount().toFixed(2) }}</b></td>
         
         						<!--<td class="hidden-xs" ng-repeat="th in tax_list_all" align="left">
							        <!-- {{ (row.order_tax[th.tax_id]).tax_amount}} -->
							        <!-- {{th.tax_id}} -->
							        <!--<b>{{ gettotalTaxAmount(th.tax_id).toFixed(2) }}</b>
							    </td>-->
							    
								<td align="left"><b>{{ getTotalSGST().toFixed(2) }}</b></td>		
								<td align="left"><b>{{ getTotalCGST().toFixed(2) }}</b></td>		
								
					          	
					          	<td align="left"><b>{{ getbill_amountTotal().toFixed(2) }}</b></td>
					          	<td align="left"><b>{{ getroundoffTotal().toFixed(2) }}</b></td>
					          	<td align="left"><b>{{ getRound().toFixed(2) }}</b></td>
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
