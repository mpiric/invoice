<!-- start: SIMPLE TABLE WITH PAGINATION -->
<div class="container-fluid container-fullw bg-white">
	<div class="row">
		<div class="col-md-12">
			<h5 class="over-title margin-bottom-15">Customer List</h5>
			<!-- /// controller:  'ngTableCtrl' -  localtion: assets/js/controllers/waiterCtrl.js /// -->
			<div class="table-responsive" ng-controller="customerCtrl">

				<table ng-table="tableParams"  show-filter="true" class="table table-striped"><!-- show-filter="true" -->

					<tr ng-repeat="row in $data" > <!-- | orderBy:predicate:reverse -->
							<td data-title="'Order'" sortable="'order_id'" filter="{ 'order_id': 'text' }" >{{row.order_code}}</td>

							<td data-title="'Order Type'" sortable="'order_type'" filter="{ 'order_type': 'text' }" >{{row.order_type}}</td>

							<td data-title="'Firstname'" sortable="'firstname'" filter="{ 'firstname': 'text' }" >{{row.firstname}}</td>

							<td data-title="'Lastname'" sortable="'lastname'" filter="{ 'lastname': 'text' }" >{{row.lastname}}</td>

							<td data-title="'Contact'" sortable="'contact'" filter="{ 'contact': 'text' }" >{{row.contact}}</td>

							<td data-title="'Email'" sortable="'email'" filter="{ 'email': 'text' }" >{{row.email}}</td>

							<td data-title="'Created'" sortable="'created'" filter="{ 'created': 'text' }" >{{row.created}}</td>
							
							<td class="center">
							<div class="visible-md visible-lg hidden-sm hidden-xs">
								<a ng-click="updatecustomer(row.customer_id)" class="btn btn-transparent btn-xs" tooltip-placement="top" tooltip="Edit"><i class="fa fa-pencil"></i></a>							
								<a ng-click="open('sm',row.customer_id);removeRow($index)" class="btn btn-transparent btn-xs tooltips" tooltip-placement="top" tooltip="Remove"><i class="fa fa-times fa fa-white"></i></a>
								<!-- <a ng-click="infoCustomer(row.customer_id)" class="btn btn-transparent btn-xs" tooltip-placement="top" tooltip="Info"><i class="fa fa-info"></i></a> -->	
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
										<!-- <li>
											<a ng-click="infoCustomer(row.customer_id)">
												Info 
											</a>
										</li> -->
									</ul>
								</div>
							</div></td>
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
		
