<!-- start: SIMPLE TABLE WITH PAGINATION -->
<div class="container-fluid container-fullw bg-white">
	<div class="row">
		<div class="col-md-12">
			<h5 class="over-title margin-bottom-15">Order List</h5>
			<!-- /// controller:  'ngTableCtrl' -  localtion: assets/js/controllers/waiterCtrl.js /// -->
			<div class="table-responsive" ng-controller="orderListCtrl">

				<table ng-table="tableParams"  show-filter="true" class="table table-striped"><!-- show-filter="true" -->

					<tr ng-repeat="row in $data" > <!-- | orderBy:predicate:reverse -->

						<td data-title="'Branch'" sortable="'branch_name'" filter="{ 'branch_name': 'text' }" >{{row.branch_name}}</td>

						<td data-title="'Order'" sortable="'order_code'" filter="{ 'order_code': 'text' }" >{{row.order_code}}</td>

						<td data-title="'Order Type'" sortable="'orderType'" filter="{ 'orderType': 'text' }" >{{row.orderType}}</td>

						<td data-title="'Waiter'" sortable="'waiter_name'" filter="{ 'waiter_name': 'text' }" >{{row.waiter_name}}</td>

						<td data-title="'Total Amount'" sortable="'total_amount'" filter="{ 'total_amount': 'text' }" >{{row.total_amount}}</td>

						<td data-title="'Order Date'" sortable="'order_date_time'" filter="{ 'order_date_time': 'text' }" >{{row.order_date_time}}</td>

						<td data-title="'Created'" sortable="'created'" filter="{ 'created': 'text' }" >{{row.created}}</td>

						

						<td class="center" ng-show="is_admin_branch">
							<div class="visible-md visible-lg hidden-sm hidden-xs">
								<a ng-click="updateOrder(row.order_id)" class="btn btn-transparent btn-xs" tooltip-placement="top" tooltip="Edit"><i class="fa fa-pencil"></i></a>
							</div>
							<div class="visible-xs visible-sm hidden-md hidden-lg">
								<div class="btn-group" dropdown is-open="status.isopen">
									<button type="button" class="btn btn-primary btn-o btn-sm dropdown-toggle" dropdown-toggle>
										<i class="fa fa-cog"></i>&nbsp;<span class="caret"></span>
									</button>
									<ul class="dropdown-menu pull-right dropdown-light" role="menu">
										<li>
											<a ng-click="updateOrder(row.order_id)" >
												Edit
											</a>
										</li>
									</ul>
								</div>
							</div>
						</td>

					</tr>
					<!-- </tbody>   -->
				</table>
			</div>
		</div>
	</div>
</div>