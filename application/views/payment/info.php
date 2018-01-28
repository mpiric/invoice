<div ng-controller="productcategoryInfoCtrl" >
<section id="page-title">
	<div class="row">
		<div class="col-sm-8">
			<h1 class="mainTitle">Product Category Details</h1>
			<!-- <span class="mainDescription"></span> -->
		</div>
		<div class="col-sm-4">
			<a style="float:right" ng-click="updateproductcategory(data.product_category_id)" class="btn btn-primary btn-dark-blue" tooltip-placement="top" tooltip="Update">Update</a>
		</div>
		<!-- <div ncy-breadcrumb></div> -->
	</div>
</section>

	<div class="row">
		<div class="col-md-6">
			<table class="table table-condensed">
				<thead>
					<tr>
						<th colspan="3">Product Category Details</th>
					</tr>
				</thead>
				<tbody>
					
					<!-- <tr>
						<td>Product Category</td>
						<td>{{data.product_category_id}}</td>
					</tr> -->
					<tr>
						<td>Parent Category</td>
						<td>{{data.parent_name}}</td>
					</tr>
					
					<tr>
						<td>Product Category name</td>
						<td>{{data.name}}</td>
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