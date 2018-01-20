<!-- start: PAGE TITLE -->
<section id="page-title">
	<div class="row"  >
		<div class="col-sm-8">
		<h1 class ="mainTitle">Waiter Sales Report</h1>
			<!-- <h1 class="mainTitle" translate="sidebar.nav.forms.ELEMENTS">Waiter Sales Report</h1> -->
		</div>
	</div>
</section>
<!-- end: PAGE TITLE -->

<!-- start: DATE/TIME Picker -->

<div class="container-fluid container-fullw" ng-controller="waiterReportCtrl">
	<div class="row">
		<div class="col-md-12" >
			
			<div class="panel panel-white no-radius">
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
								<div class="form-group col-md-12" ng-show="adminBranch">
										<h5 class="text-bold margin-top-25 margin-bottom-15"> Select Branch:</h5>
								          <select class="form-control" name="branch_id" ng-options="item as item.name for item in branch_list track by item.branch_id" ng-model="branch_id" ng-change="branchwiseWaiter(branch_id)">
								          <option value="">Select Branch</option>
								          </select>

						      	</div>
								<div class="form-group col-md-12">
										<h5 class="text-bold margin-top-25 margin-bottom-15"> Select Waiter:</h5>
								          <select class="form-control" name="waiter_id" ng-options="item as item.waiter_code for item in waiter_list_by_branch track by item.waiter_id" ng-model="waiter_id">
								          <option value="">Select Waiter</option>
								          </select>

						      	</div>
					      		<div class="form-group col-md-4">
					      			<input type="submit" name="Submit" class="btn btn-primary" value="Generate" ng-click="viewReport(branch_id,waiter_id)">
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

					<button class="btn btn-link" ng-click="exportToExcel('#waiter_table')" style="float:right">
      				<span class="glyphicon glyphicon-share"></span>
 					Export to Excel
 					</button>
 					
					<div class="col-md-12">
					    <fieldset>
					      <legend>Report</legend>
					        <!-- <div>
					        <table id="waiter_table" class="table table-condensed table-hover">
					        <thead>
					          <tr>
					          	<th class="hidden-xs">Date</th>
					            <th class="hidden-xs">Bill No.</th>
					            <th class="hidden-xs">Waiter Commission (%)</th>
					            <th class="hidden-xs">Waiter Commission Amount</th>
					            
					          </tr>
					        </thead>
					        <tbody>
					          <tr ng-repeat="row in waiteralllist">
					          	<td class="hidden-xs" >{{row.created}}</td>
					            <td class="hidden-xs" >{{row.order_code}}</td>
					            <td class="hidden-xs" >{{row.waiter_commision}}</td>
					            <td class="hidden-xs" >{{(row.total_amount*row.waiter_commision/100).toFixed(2)}}</td>
					            
					          </tr>
					          <tr>
					          <td><b>Total:</b></td>
					          <td></td>
					         
					          <td><b>{{ getWaiterCommissionAmt() }}</b></td>
					          <td><b>{{ getWaiterCommission().toFixed(2) }}</b></td>
					          </tr>
					        </tbody>
					        </table>
					        </div> -->

					        <div class="container-fluid container-fullw bg-white">
								<div class="row">
									<div class="col-md-12">
										
										<!-- /// controller:  'ngTableCtrl' -  localtion: assets/js/controllers/ngTableCtrl.js /// -->
										
											
										<table id="waiter_table" ng-table="tableParams" class="table table-striped">
										<thead>
								          <tr>
								          	<th class="hidden-xs">Date</th>
								            <th class="hidden-xs">Bill No.</th>
								           <!--  <th class="hidden-xs">Waiter Commission (%)</th> -->
								            <th class="hidden-xs">Waiter Commission Amount</th>
								            
								          </tr>
								        </thead>
								        <tbody>
								          <tr ng-repeat="row in waiteralllist">
								          	<td class="hidden-xs" >{{row.created}}</td>
								            <td class="hidden-xs" >{{row.order_code}}</td>
								            <td class="hidden-xs" >{{row.waiter_commision}}</td>
								            <!-- <td class="hidden-xs" >{{(row.total_amount*row.waiter_commision/100).toFixed(2)}}</td> -->
								            
								          </tr>
								          <tr>
								          <td><b>Total:</b></td>
								          <td></td>
								         
								          <td><b>{{ getWaiterCommissionAmt().toFixed(2) }}</b></td>
								         <!--  <td><b>{{ getWaiterCommission().toFixed(2) }}</b></td> -->
								          </tr>
								        </tbody>
										</table>
										
									</div>
								</div>
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

<!-- <div ng-controller="waiterReportCtrl">
	<table id="waiter_table" class="table table-condensed table-hover">
					        <thead>
					          <tr>
					          	<th class="hidden-xs">Date</th>
					            <th class="hidden-xs">Order No.</th>
					            <th class="hidden-xs">Waiter Commission (%)</th>
					            <th class="hidden-xs">Waiter Commission Amount</th>
					            
					          </tr>
					        </thead>
					        <tbody>
					          <tr ng-repeat="row in waiteralllist">
					          	<td class="hidden-xs" >{{row.created}}</td>
					            <td class="hidden-xs" >{{row.order_id}}</td>
					            <td class="hidden-xs" >{{row.waiter_commision}}</td>
					            <td class="hidden-xs" >{{(row.total_amount*row.waiter_commision/100).toFixed(2)}}</td>
					            
					          </tr>
					          <tr>
					          <td><b>Total:</b></td>
					          <td></td>
					        
					         
					          <td><b>{{ getWaiterCommissionAmt() }}</b></td>
					          <td><b>{{ getWaiterCommission().toFixed(2) }}</b></td>
					          </tr>
					        </tbody>
					        </table>
</div> -->
