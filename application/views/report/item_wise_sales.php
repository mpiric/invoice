<!-- start: PAGE TITLE -->
<!-- <section id="page-title">
	<div class="row"  >
		<div class="col-sm-8">
		<h1 class ="mainTitle">Item Wise Sales Report</h1>			
		</div>		
	</div>
</section> -->
<!-- end: PAGE TITLE -->
<!-- start: DATE/TIME Picker -->
<div class="container-fluid container-fullw" ng-controller="itemWiseSalesCtrl">
	<div class="row">
		<div class="col-md-12" >
			
			<div class="panel panel-white no-radius" >
				<div class="panel-body">
					
					<div >
					<form action="javascript:void(0)" method="post" name="report_form">
						
						<div class="row">
							
							<div class="col-md-6">
								<h5 class="text-bold margin-top-25 margin-bottom-15">Date Range</h5>
								<div class="input-group">

									<input type="text" name="fromdate" class="form-control" datepicker-popup="yyyy/MM/dd" ng-model="start"  is-open="startOpened" ng-init="startOpened = false" min-date="'1970-12-31'" max-date="end"  close-text="Close"  ng-click="startOpen($event)"  />
									<span class="input-group-addon">to</span>
									<input type="text" name="todate" class="form-control" datepicker-popup="yyyy/MM/dd" ng-model="end" is-open="endOpened" ng-init="endOpened = false" min-date="start" close-text="Close"  ng-click="endOpen($event)" />
									
								</div>
								<br>
								<div class="form-group col-md-12" ng-show="adminBranch">
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
					      		<input type="submit" name="Submit" class="btn btn-primary" value="Generate" ng-click="viewItemwiseSalesReport()">
					      		</div>

					      		<div class="col-md-4" >
					      			<span id="pdf_loader" style="display: none"><img src="uploads/ajax-loader.gif"></span>
					      		</div>

					      		<!-- <div class="form-group col-md-4">
					      		<input type="submit" name="Submit" class="btn btn-default" value="Generate PDF" ng-click="viewItemwiseSalesReportPDF(branch_id,waiter_id)"><span id="pdf_loader" style="display: none"><img src="uploads/ajax-loader.gif"></span>
					      		</div> -->
								
							</div>
						</div>
					</form>

					<button class="btn btn-link" ng-click="exportToExcel('#item_wise_sales_report')" style="float:right">
      				<span class="glyphicon glyphicon-share"></span>
 					Export to Excel
 					</button>
				
					<div class="col-md-12">
					    <fieldset>
					    
					      <legend>Item Wise Sales</legend>
					        <div>
					        <table id="item_wise_sales_report" class="table table-condensed table-hover" >
					        <thead>
					          <tr>
								<th class="hidden-xs " align="left" style="text-align:left;" width="15%">Product Code</th>
					            <th class="hidden-xs center" align="left" style="text-align:left;" width="55%">Category Name</th>
					            <th class="hidden-xs center" align="right" style="text-align:right;" width="15%">Quantity</th>
					            <th class="hidden-xs center" align="right" style="text-align:right;" width="15%">Total</th>
					            <!-- <th class="hidden-xs">&nbsp;</th>	            -->
					          </tr>
					        </thead>
					        <tbody>
					          
					          


					            <tr ng-repeat="row in item_wise_report">
					            	<td class="hidden-xs" colspan="4" style="text-align: center;" >
					            		<b style="font-size:18px"> {{row.product_category_id}} . {{row.name}} </b>					            		
					            		<table class="table table-condensed table-hover">
					            			<tr ng-repeat="item in row.items">
						            			<td align="left" width="15%">{{item.product_code}} </td>
						            			<td align="left" width="55%">{{item.product_name}} </td>
						            			<td align="right" width="15%">{{item.quantity}} </td>
						            			<td align="right" width="15%">{{item.total | number:2}} </td>
					            			</tr>
					            			<tr>
					            				<td align="left">Total:</td>
					            				<td></td>
					            				<td align="right">{{getItemQuantity(row.items) }}</td>
					            				<td align="right">{{ getItemwiseTotal(row.items) | number:2}}</td>
					            			</tr>
					            		</table>
					            	</td>
					            </tr>
								
								<tr>
									<td align="left"><b> Total :</b> </td>
									<td>&nbsp;  </td>
									<td align="right"><b>{{ finalQty }} </b></td>
									<td align="right"><b>{{ finalTotal | number:2}}</b></td>
									
								
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
