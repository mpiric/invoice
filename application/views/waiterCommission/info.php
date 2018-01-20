<div ng-controller="waitercommissionInfoCtrl" >
<section id="page-title">
	<div class="row">
		<div class="col-sm-8">
			<h1 class="mainTitle">Waiter Commission Details</h1>
			<!-- <span class="mainDescription"></span> -->
		</div>
		<div class="col-sm-4">
			<a style="float:right" ng-click="updatewaitercommission(data.waiter_commission_id)" class="btn btn-primary btn-dark-blue" tooltip-placement="top" tooltip="Update">Update</a>
		</div>
		<!-- <div ncy-breadcrumb></div> -->
	</div>
</section>

	<div class="row">
		<div class="col-md-6">
			<table class="table table-condensed">
				<thead>
					<tr>
						<th colspan="3">Waiter Commission Details</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Waiter</td>
						<td>{{data.waitercode}}</td>
					</tr>
					<tr>
						<td>Order</td>
						<td>{{data.ordercode}}</td>
					</tr>
					<tr>
						<td>Product Quantity</td>
						<td>{{data.product_qty}}</td>
					</tr>
					<tr>
						<td>Commision Amount</td>
						<td>{{data.commission_amount}}</td>
					</tr>	

					<tr>
						<td>Product Category</td>
						<td>{{data.product_category_name}}</td>
					</tr>
					<tr>
						<td>Commission Date</td>
						<td>{{data.commission_date}}</td>
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

</div>