<!-- start: SIMPLE TABLE WITH PAGINATION -->

<style>

.color-red {
  color: red;
}
</style>


<div class="container-fluid container-fullw bg-white">
	<div class="row">
		<div class="col-md-12">
			<h5 class="over-title margin-bottom-15">Branch List</h5>
			<!-- /// controller:  'ngTableCtrl' -  localtion: assets/js/controllers/branchCtrl.js /// -->
			<div class="table-responsive" ng-controller="branchCtrl">
<!-- 				<p>
					<strong>Page:</strong> {{tableParams.page()}}
				</p>
				<p>
					<strong>Count per page:</strong> {{tableParams.count()}}
				</p> -->
				<table ng-table="tableParams"  show-filter="true" class="table table-striped"><!-- show-filter="true" -->

					<!-- <thead>  
						<th><a href="" ng-click="order('name')"> Name</a> </th>  
						<th><a href="" ng-click="order('username')"> Username</a> </th>  
						<th><a href="" ng-click="order('contact')"> Contact</a> </th>  
						<th><a href="" ng-click="order('email')"> Email</a> </th>  
						<th><a href="" ng-click="order('country')"> Country</a> </th>  
						<th><a href="" ng-click="order('state')"> State</a> </th>  
						<th><a href="" ng-click="order('city')"> City</a> </th>  
						<th><a href="" ng-click="order('contact_person_name')"> CP Name</a> </th>  
						<th><a href="" ng-click="order('contact_person_phone')"> CP Phone</a> </th>  
					</thead>   -->

					<!-- <tbody>   -->
						<!-- <tr>  
				           <td> <input type="text" class="form-control" ng-model="search.name" /></td>    
				           <td> <input type="text" class="form-control" ng-model="search.username" /></td>    
				           <td> <input type="text" class="form-control" ng-model="search.contact" /></td>
				           <td> <input type="text" class="form-control" ng-model="search.email" /></td>
				           <td> <input type="text" class="form-control" ng-model="search.country" /></td>
				           <td> <input type="text" class="form-control" ng-model="search.state" /></td>
				           <td> <input type="text" class="form-control" ng-model="search.city" /></td>
				           <td> <input type="text" class="form-control" ng-model="search.contact_person_name" /></td>
				           <td> <input type="text" class="form-control" ng-model="search.contact_person_phone" /></td>
				         </tr>   -->

						<tr ng-repeat="row in $data" > <!-- | orderBy:predicate:reverse -->
							<td data-title="'Name'" sortable="'name'" filter="{ 'name': 'text' }" >{{row.name}}</td><!-- filter="{ 'name': 'text' }" -->
							<td data-title="'Username'" sortable="'username'" filter="{ 'username': 'text' }" >{{row.username}}</td><!-- sortable="'username'" -->
							<td data-title="'Contact'"  sortable="'contact'" filter="{ 'contact': 'text' }" >{{row.contact}}</td> <!-- sortable="'contact'" -->
							<td data-title="'Email'" sortable="'email'" filter="{ 'email': 'text' }" >{{row.email}}</td><!-- sortable="'email'" -->
							<!-- <td data-title="'Country'" sortable="'country'" filter="{ 'country': 'text' }" >{{row.country}}</td> --><!-- sortable="'country'" -->
							<td data-title="'State'" sortable="'state'" filter="{ 'state': 'text' }" >{{row.state}}</td><!-- sortable="'state'" -->
							<td data-title="'City'" sortable="'city'" filter="{ 'city': 'text' }" >{{row.city}}</td><!-- sortable="'city'" -->
							<td data-title="'CP'" sortable="'contact_person_name'" filter="{ 'contact_person_name': 'text' }" >{{row.contact_person_name}}</td><!-- sortable="'contact_person_name'" -->
							<td data-title="'CP Phone'" sortable="'contact_person_phone'" filter="{ 'contact_person_phone': 'text' }" >{{row.contact_person_phone}}</td><!-- sortable="'contact_person_phone'" -->

							<td data-title="'Status'" sortable="'branch_status'" filter="{ 'branch_status': 'text' }" ><span ng-class="{'color-red': row.branch_status === 'Inactive'}">{{row.branch_status}}</span></td>



							<td class="center">
							<div class="visible-md visible-lg hidden-sm hidden-xs">
								<a ng-click="updateBranch(row.branch_id)" class="btn btn-transparent btn-xs" tooltip-placement="top" tooltip="Edit"><i class="fa fa-pencil"></i></a>							
								<a ng-click="open('sm',row.branch_id);removeRow($index)" class="btn btn-transparent btn-xs tooltips" tooltip-placement="top" tooltip="Remove"><i class="fa fa-times fa fa-white"></i></a>
								<a ng-click="infoBranch(row.branch_id)" class="btn btn-transparent btn-xs" tooltip-placement="top" tooltip="Info"><i class="fa fa-info"></i></a>
							</div>
							<div class="visible-xs visible-sm hidden-md hidden-lg">
								<div class="btn-group" dropdown is-open="status.isopen">
									<button type="button" class="btn btn-primary btn-o btn-sm dropdown-toggle" dropdown-toggle>
										<i class="fa fa-cog"></i>&nbsp;<span class="caret"></span>
									</button>
									<ul class="dropdown-menu pull-right dropdown-light" role="menu">
										<li>
											<a ng-click="updateBranch(row.branch_id)" >
												Edit
											</a>
										</li>									
										<li>
											<a ng-click="open('sm',row.branch_id);removeRow($index)">
												Remove 
											</a>
										</li>
										<li>
											<a ng-click="infoBranch(row.branch_id)">
												Info 
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
			Are you sure you want to delete branch ?
		</div>
		<div class="modal-footer">
			<button class="btn btn-primary" ng-click="ok()">OK</button>
			<button class="btn btn-primary btn-o" ng-click="cancel()">Cancel</button>
		</div>
	</script>	
		
