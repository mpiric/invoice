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
<div class="container-fluid container-fullw" ng-controller="productRecipeReportCtrl">
	<div class="row">
		<div class="col-md-12" >
			
			<div class="panel panel-white no-radius" >
				<div class="panel-body">
					
					<div >
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
				              <input type="text" name="filterdate" class="form-control" datepicker-popup="dd/MM/yyyy" ng-model="start" is-open="startOpened" ng-init="startOpened = false" min-date="'1970-12-31'" max-date="end"  close-text="Close"  ng-click="startOpen($event)" datepicker-options="dateOptions" value="<?php echo date('Y-m-d'); ?>" />
				            </div>   
				          
								<br>
								

					      		<div class="form-group col-md-4">
					      		<input type="submit" name="Submit" class="btn btn-primary" value="Generate" ng-click="productRecipeReport()">
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

					<!-- <button class="btn btn-link" ng-click="exportToExcel('#daily_purchase_report')" style="float:right">
      				<span class="glyphicon glyphicon-share"></span>
 					Export to Excel
 					</button> -->
				
					<div class="col-md-12">
					    <fieldset>
					    
					      <legend>Product Recipe</legend>
					        <div  ng-show="!is_msg">
					        <table id="product_recipe_report" class="table table-condensed table-hover" >
						        <thead>
						          <tr>
							          <th class="hidden-xs" align="center">Product Name</th>
							          <th class="hidden-xs" align="center">Unit</th>
							          <th class="hidden-xs" align="center">Ideal Weight</th>
							          <th class="hidden-xs" align="center">Actual Weight</th>
							          <th class="hidden-xs" align="center">Varience</th>
							          <th class="hidden-xs" align="center">Opening_stock</th>
							          <th class="hidden-xs" align="center">Closing_stock</th>
							          
						          </tr>
						        </thead>
						        <tbody>
						        
						            <tr ng-repeat="row in data">
						            	<td class="hidden-xs" >{{row.store_product_name}}</td>
				            			<td class="hidden-xs" >{{row.unit}}</td>
				            			<td class="hidden-xs" >{{row.ideal_weight}}</td>
				            			<td class="hidden-xs" >{{row.actual_weight}}</td>
				            			<td class="hidden-xs" >{{row.varience}}</td>
				            			<td class="hidden-xs" >{{row.opening_stock}}</td>
				            			<td class="hidden-xs" >{{row.closing_stock}}</td>
				            			
						            </tr>
									
									<tr>
										<td align="left"><b> Total :</b> </td>
										<td></td>
										<td align="left"><b>{{ getInstock() | number:2 }} </b></td>
										<td></td>
										<td align="left"><b>{{ getPurchaseToday() | number:2}}</b></td>
										
									
									</tr>

						            
						        </tbody>
					        </table>
					        </div>
					        <div ng-show="is_msg">
						        {{message}}
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
