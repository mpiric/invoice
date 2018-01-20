

<div class="container-fluid container-fullw" ng-controller="storeItemsReportCtrl">
	<div class="row">

		<div class="col-md-12" >
			<div class="panel panel-white no-radius" >
				<div class="panel-body">
					<div>
					<form action="javascript:void(0)" method="post" name="report_form">
						<div class="row">

							
							<div ng-show="show_branch_dd" class="col-md-6">
						        <label for="branch_id"  class="control-label">Branch: <span class="symbol required"></span></label>
						          <select class="form-control" name="branch_id" ng-options="item as item.name for item in branch_list track by item.branch_id" ng-model="branch_id">
						          <option value="">Select Branch</option>
						          </select>
						    </div>
							
							
						</div>
						<div class="row">
						<br>
							<div class="col-md-6">


								<h5 class="text-bold margin-top-25 margin-bottom-15">Date Range</h5>
								<div class="input-group">

									<input type="text" name="fromdate" class="form-control" datepicker-popup="yyyy/MM" ng-model="start"  is-open="startOpened" ng-init="startOpened = false" min-date="'1970-12-31'" max-date="end"  close-text="Close"  ng-click="startOpen($event)" datepicker-options="{minMode: 'month'}" datepicker-mode="'month'"/>
									
								</div>


								<br>
								<!-- <div class="form-group col-md-12" ng-show="adminBranch">
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
						      	</div> -->

					      		<div class="form-group col-md-4">
					      		<input type="submit" name="Submit" class="btn btn-primary" value="Generate" ng-click="storeItemsReport()">
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

					<button class="btn btn-link" ng-click="exportToExcel('#store_items_report')" style="float:right">
      				<span class="glyphicon glyphicon-share"></span>
 					Export to Excel
 					</button>
				
					<div class="col-md-12">
					    <fieldset>
					      <legend>Store Items</legend>
					        <div>
					        <table id="store_items_report" class="table table-condensed table-hover" >
					        <thead>
					          <tr>
								<th class="hidden-xs" align="left" style="text-align:left;" width="25%">Name</th>
					            <th class="hidden-xs center" align="left" style="text-align:left;">Unit</th>
					            <th class="hidden-xs center" align="left" style="text-align:left;">Open</th>
					            <th class="hidden-xs center" align="left" style="text-align:left;">Purchase</th>
					            <th class="hidden-xs center" align="left" style="text-align:left;">Closing</th>
					            <th class="hidden-xs center" align="left" style="text-align:left;">Cons.</th>
					            <th class="hidden-xs center" align="left" style="text-align:left;">Rate</th>	
					            <th class="hidden-xs center" align="left" style="text-align:left;">Cons. Amt</th>
					            <th class="hidden-xs center" align="left" style="text-align:left;">Closing Amt</th>

					            <!-- <th class="hidden-xs">&nbsp;</th>	            -->
					          </tr>
					        </thead>
					        <tbody>
					            <tr ng-repeat="row in store_items_report">
					            	<td class="hidden-xs" colspan="9" style="text-align: center;" >
					            		<b style="font-size:18px"> {{row.category_id}} . {{row.cat_name}} </b>
					            		
					            		<table class="table table-condensed table-hover">
					            			<tr ng-repeat="item in row.store_items">
						            			<td align="left" width="25%">{{item.name}} </td>
						            			<td align="left" width="7%">{{item.unit}} </td>
						            			<td align="left" width="7%">{{item.open_stock ? item.open_stock : 0.00 }} </td>
						            			<td align="left" width="10%">{{item.purchase ? item.purchase : 0.00 | number:2}} </td>
						            			<td align="left" width="10%">{{item.instock ? item.instock : 0.00}} </td>
						            			<td align="left" width="8%" >{{((item.open_stock+item.purchase)-item.instock) | number:2}} </td>
						            			<!-- {{(item.open_stock+item.purchase)-item.instock}} -->
						            			<td align="left" width="10%">{{item.price}} </td>
						            			<td align="left">{{((item.open_stock+item.purchase)-item.instock)*item.price | number:2}} </td>
						            			<td align="left">{{(item.instock*item.price) | number:2}} </td>
					            			</tr>
					            			
					            		</table>
					            	</td>
					            </tr>
					            <tr>
		            				<td align="left"><b>Total:</b></td>
		            				<td></td>
		            				<td></td>
		            				<td></td>
		            				<td></td>
		            				<td></td>
		            				<td></td>
		            				<td align="center">{{total_cons_amt | number:2}}</td>
		            				<td align="left">{{ total_closing_amt | number:2}}</td>
		            			</tr>
								
								 <tr>
									<td align="left"><b> Sale with Tax :</b> </td>
									<td></td>
									<td align="right"><b> </b></td>
									<td align="right"><b>{{saleWithTax}}</b></td>
								</tr>
								 <tr>
									<td align="left"><b> Food Cost :</b> </td>
									<td></td>
									<td align="right"><b> </b></td>
									<td align="right"><b>{{foodCost | number:2}}</b></td>
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