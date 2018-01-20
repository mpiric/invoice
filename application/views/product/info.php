<div ng-controller="productInfoCtrl" >
<section id="page-title">
	<div class="row">
		<div class="col-sm-8">
			<h1 class="mainTitle">Product Details</h1>
			<!-- <span class="mainDescription"></span> -->
		</div>
		<div class="col-sm-4">
			<a style="float:right" ng-click="updateProduct(data.product_id)" class="btn btn-primary btn-dark-blue" tooltip-placement="top" tooltip="Update">Update</a>
		</div>
		<!-- <div ncy-breadcrumb></div> -->
	</div>
</section>



	<div class="row">
		<div class="col-md-6">
			<table class="table table-condensed">
				<thead>
					<tr>
						<th colspan="3">Product Details</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Product Name</td>
						<td>{{data.name}}</td>
					</tr>
					<tr>
						<td>Product Unit</td>
						<td>{{data.unit}}</td>
					</tr>	
					<tr>
						<td>Product Price</td>
						<td>{{data.price}}</td>
					</tr>					
					<tr>
						<td>Product Code</td>
						<td>{{data.product_code}}</td>
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
						<th colspan="3">Product Information</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Image</td>
						<td>{{data.image}}</td>
					</tr>
					<tr>
						<td>Description</td>
						<td><a href="#">{{data.description}}</a></td>
					</tr>
				</tbody>
			</table>
		</div>
		
		
	</div>



</div>