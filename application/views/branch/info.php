<div ng-controller="branchInfoCtrl" >
<section id="page-title">
	<div class="row">
		<div class="col-sm-8">
			<h1 class="mainTitle">Branch Details</h1>
			<!-- <span class="mainDescription"></span> -->
		</div>
		<div class="col-sm-4">
			<a style="float:right" ng-click="updateBranch(data.branch_id)" class="btn btn-primary btn-dark-blue" tooltip-placement="top" tooltip="Update">Update</a>
		</div>
		<!-- <div ncy-breadcrumb></div> -->
	</div>
</section>



	<div class="row">
		<div class="col-md-6">
			<table class="table table-condensed">
				<thead>
					<tr>
						<th colspan="3">Branch Details</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Name</td>
						<td>{{data.name}}</td>
					</tr>
					<tr>
						<td>Username</td>
						<td>{{data.username}}</td>
					</tr>					
				</tbody>
			</table>
		</div>

		<div class="col-md-6">
			<table class="table table-condensed">
				<thead>
					<tr>
						<th colspan="3">Date Details</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Created Date</td>
						<td>{{data.created}}</td>
					</tr>
					<tr>
						<td>Modified Date</td>
						<td>{{data.updated}}</td>
					</tr>
					
				</tbody>
			</table>
		</div>
	</div>

	<div class="row" >

		<div class="col-md-6">
			<table class="table table-condensed">
				<thead>
					<tr>
						<th colspan="3">Contact Information</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Contact</td>
						<td>{{data.contact}}</td>
					</tr>
					<tr>
						<td>Email</td>
						<td><a href="#">{{data.email}}</a></td>
					</tr>
					<tr>
						<td>Contact Person Name</td>
						<td>{{data.contact_person_name}}</td>
					</tr>
					<tr>
						<td>Contact Person Phone</td>
						<td>{{data.contact_person_phone}}</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-md-6">
			<table class="table table-condensed">
				<thead>
					<tr>
						<th colspan="3">Address Details</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Country</td>
						<td>{{data.country}}</td>
					</tr>
					<tr>
						<td>State</td>
						<td>{{data.state}}</td>
					</tr>
					<tr>
						<td>City</td>
						<td class="ng-binding">{{data.city}}</td>
					</tr>
					<tr>
						<td>Address</td>
						<td>{{data.address}}</td>
					</tr>
					<tr>
						<td>Pincode</td>
						<td>{{data.pincode}}</td>
					</tr>
				</tbody>
			</table>
		</div>
		
	</div>

	<div class="row" >
		
		<div class="col-md-6">
			<a ng-click="updatePassword(data.branch_id)" class="btn btn-wide btn-o btn-azure" tooltip-placement="top" tooltip="Update">Update Password</a>
			<br><br>
			<div ng-if="updatePasswordDiv">
				<div class="panel panel-white">
						
						<div class="panel-body">	

							<div ng-if="success" ng-bind-html="successDiv"  class="alert alert-success fade in">
							</div>
							<div ng-if="error" ng-bind-html="errorDiv" class="alert alert-danger fade in">
							</div>

							<form class="form-inline" name="update_pass_form" ng-submit="updateBranchPassword(update_pass_form)">
								<div class="form-group">
									<div class="input-group">										
										<label for="password"  class="control-label">Password: <span class="symbol required"></span></label>
										<input type="password" class="form-control" ng-model="password" id="password" placeholder="Branch Password" name="password" required >
									    <div class="has-error" ng-show="update_pass_form.$submitted || update_pass_form.password.$touched">
									      <span class="error text-small block ng-scope" ng-show="update_pass_form.password.$error.required">Password is required.</span>
									    </div>									
									</div>
								</div>
								
								<button type="submit" style="margin-top:20px" class="btn btn-primary" ng-disabled="update_pass_form.$invalid"  >Submit</button>
								
							</form>
						</div>
					</div>


				
					<!-- <div class="form-group" >
						<label for="password"  class="control-label">Password: <span class="symbol required"></span></label>
							<input type="password" class="form-control" ng-model="password" id="password" placeholder="Branch Password" name="password" required >

						    <div class="has-error" ng-show="update_pass_form.$submitted || update_pass_form.password.$touched">
						      <span class="error text-small block ng-scope" ng-show="update_pass_form.password.$error.required">Password is required.</span>
						    </div>
						</div> -->
					<!-- <input type="password" name="password" ng-model="password"> -->
					<!-- <input type="submit" > -->
					<!-- <button type="submit" class="btn btn-wide btn-azure" ng-disabled="update_pass_form.$invalid" style="float:right" >Submit</button>
				</form> -->
			</div>
		</div>						
	</div>

</div>