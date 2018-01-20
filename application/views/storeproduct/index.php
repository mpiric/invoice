<!-- start: SIMPLE TABLE WITH PAGINATION -->
<div class="container-fluid container-fullw bg-white">
	<div class="row">
		<div class="col-md-12">
			<h5 class="over-title margin-bottom-15">Store Product List</h5>
			
			<div class="table-responsive" ng-controller="storeproductCtrl">

				<table ng-table="tableParams"  show-filter="true" class="table table-striped"><!-- show-filter="true" -->

					<tr ng-repeat="row in $data" > <!-- | orderBy:predicate:reverse -->
					
							<td data-title="'Store Product Name'" sortable="'name'" filter="{ 'name': 'text' }" >{{row.name}}</td>


							<td data-title="'Store Product Code'" sortable="'product_code'" filter="{ 'product_code': 'text' }" >{{row.product_code}}</td>

							<!-- <td data-title="'Unit'" sortable="'unit'" filter="{ 'unit': 'text' }" >{{row.unit}}</td> -->

							<td data-title="'Price'" sortable="'price'" filter="{ 'price': 'text' }" >{{row.price}}</td>

							<td class="center">
							<div class="visible-md visible-lg hidden-sm hidden-xs">

								<a ng-click="updatestoreproduct(row.store_product_id)" class="btn btn-transparent btn-xs" tooltip-placement="top" tooltip="Edit"><i class="fa fa-pencil"></i></a>		

								<a ng-click="open('sm',row.store_product_id);removeRow($index)" class="btn btn-transparent btn-xs tooltips" tooltip-placement="top" tooltip="Remove"><i class="fa fa-times fa fa-white"></i></a>
								
							</div>
							
							<div class="visible-xs visible-sm hidden-md hidden-lg">
							
								<div class="btn-group" dropdown is-open="status.isopen">
									<button type="button" class="btn btn-primary btn-o btn-sm dropdown-toggle" dropdown-toggle>
										<i class="fa fa-cog"></i>&nbsp;<span class="caret"></span>
									</button>
									<ul class="dropdown-menu pull-right dropdown-light" role="menu">
										<li>
											<a ng-click="updatestoreproduct(row.store_product_id)" >
												Edit
											</a>
										</li>									
										<li>
											<a ng-click="open('sm',row.store_product_id);removeRow($index)">
												Remove 
											</a>
										</li>
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
			Are you sure you want to delete store product?
		</div>
		<div class="modal-footer">
			<button class="btn btn-primary" ng-click="ok()">OK</button>
			<button class="btn btn-primary btn-o" ng-click="cancel()">Cancel</button>
		</div>
	</script>	
		
