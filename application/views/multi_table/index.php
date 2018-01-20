  <!-- start: SIMPLE TABLE WITH PAGINATION -->
<div class="container-fluid container-fullw bg-white">
	<div class="row">
		<div class="col-md-12">
			<h5 class="over-title margin-bottom-15">Assign Table List</h5>
			<!-- /// controller:  'ngTableCtrl' -  localtion: assets/js/controllers/waiterCtrl.js /// -->
			<div class="table-responsive" ng-controller="tableCtrl">

				<table ng-table="tableParams"  show-filter="true" class="table table-striped"><!-- show-filter="true" -->

					<tr ng-repeat="row in $data" > <!-- | orderBy:predicate:reverse -->
							<td data-title="'Branch'" sortable="'branch_name'" filter="{ 'branch_name': 'text' }" >{{row.branch_name}}</td>

							<td data-title="'Brand'" sortable="'brand_name'" filter="{ 'brand_name': 'text' }" >{{row.brand_name}}</td>

							<td data-title="'Total Table'" sortable="'total_table'" filter="{ 'total_table': 'text' }" >{{row.total_table}}</td>

							<td data-title="'Starting Table No.'" sortable="'start_table'" filter="{ 'start_table': 'text' }" >{{row.start_table}}</td>
							
							<td data-title="'Ending Table No.'" sortable="'end_table'" filter="{ 'end_table': 'text' }" >{{row.end_table}}</td>

							<!--<td class="center">
							<div class="visible-md visible-lg hidden-sm hidden-xs">
								<a ng-click="updatecustomer(row.customer_id)" class="btn btn-transparent btn-xs" tooltip-placement="top" tooltip="Edit"><i class="fa fa-pencil"></i></a>							
								<a ng-click="open('sm',row.customer_id);removeRow($index)" class="btn btn-transparent btn-xs tooltips" tooltip-placement="top" tooltip="Remove"><i class="fa fa-times fa fa-white"></i></a>
									
							</div>
							<div class="visible-xs visible-sm hidden-md hidden-lg">
								<div class="btn-group" dropdown is-open="status.isopen">
									<button type="button" class="btn btn-primary btn-o btn-sm dropdown-toggle" dropdown-toggle>
										<i class="fa fa-cog"></i>&nbsp;<span class="caret"></span>
									</button>
									<ul class="dropdown-menu pull-right dropdown-light" role="menu">
										<li>
											<a ng-click="updatecustomer(row.customer_id)" >
												Edit
											</a>
										</li>									
										<li>
											<a ng-click="open('sm',row.customer_id);removeRow($index)">
												Remove 
											</a>
										</li>
										
									</ul>
								</div>
							</div></td>-->
						</tr>
					<!-- </tbody>   -->
				</table>
			</div>
		</div>
	</div>
</div>

	<script type="text/ng-template" id="myModalContent.html">
		<div class="modal-body">
			Are you sure you want to delete customer ?
		</div>
		<div class="modal-footer">
			<button class="btn btn-primary" ng-click="ok()">OK</button>
			<button class="btn btn-primary btn-o" ng-click="cancel()">Cancel</button>
		</div>
	</script>	
		
