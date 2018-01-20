<div ng-controller="waiterInfoCtrl" >
<section id="page-title">
	<div class="row">
		<div class="col-sm-8">
			<h1 class="mainTitle">Waiter Details</h1>
			<!-- <span class="mainDescription"></span> -->
		</div>
		<div class="col-sm-4">
			<a style="float:right" ng-click="updateWaiter(data.waiter_id)" class="btn btn-primary btn-dark-blue" tooltip-placement="top" tooltip="Update">Update</a>
		</div>
		<!-- <div ncy-breadcrumb></div> -->
	</div>
</section>



	<div class="row">
		<div class="col-md-6">
			<table class="table table-condensed">
				<thead>
					<tr>
						<th colspan="3">Waiter Details</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Firstname</td>
						<td>{{data.firstname}}</td>
					</tr>
					<tr>
						<td>Lastname</td>
						<td>{{data.lastname}}</td>
					</tr>	
					<tr>
						<td>waiter_code</td>
						<td>{{data.waiter_code}}</td>
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



</div>