<div ng-controller="taxInfoCtrl" >
<section id="page-title">
	<div class="row">
		<div class="col-sm-8">
			<h1 class="mainTitle">Tax Details</h1>
			<!-- <span class="mainDescription"></span> -->
		</div>
		<div class="col-sm-4">
			<a style="float:right" ng-click="updateTax(data.tax_master_id)" class="btn btn-primary btn-dark-blue" tooltip-placement="top" tooltip="Update">Update</a>
		</div>
		<!-- <div ncy-breadcrumb></div> -->
	</div>
</section>



	<div class="row">
		<div class="col-md-6">
			<table class="table table-condensed">
				<thead>
					<tr>
						<th colspan="3">Tax Details</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Branch</td>
						<td>{{data.branch_name}}</td>
					</tr>
					<tr>
						<td>Product Category</td>
						<td>{{data.product_category_name}}</td>
					</tr>
					<tr>
						<td>Order Type</td>
						<td>{{data.order_type}}</td>
					</tr>
					<tr>
						<td>Tax name</td>
						<td>{{data.tax_name}}</td>
					</tr>
					<tr>
						<td>Tax Percent</td>
						<td>{{data.tax_percent}}</td>
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