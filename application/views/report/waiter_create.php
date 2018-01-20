<!-- start: PAGE TITLE -->
<section id="page-title">
	<div class="row"  >
		<div class="col-sm-8">
		<h1 class ="mainTitle">Waiter Sales Report</h1>
			<!-- <h1 class="mainTitle" translate="sidebar.nav.forms.ELEMENTS">Waiter Sales Report</h1> -->
		</div>
		
	</div>
</section>
<!-- end: PAGE TITLE -->

<!-- start: DATE/TIME Picker -->

<!-- end: DATE/TIME Picker -->

<div ng-controller="waiterReportCtrl">
	<table id="waiter_table" class="table table-condensed table-hover">
					        <thead>
					          <tr>
					          	<th class="hidden-xs">Date</th>
					            <th class="hidden-xs">Order No.</th>
					            <th class="hidden-xs">Waiter Commission (%)</th>
					            <th class="hidden-xs">Waiter Commission Amount</th>
					            
					          </tr>
					        </thead>
					        <tbody>
					          <tr ng-repeat="row in waiteralllist">
					          	<td class="hidden-xs" >{{row.created}}</td>
					            <td class="hidden-xs" >{{row.order_id}}</td>
					            <td class="hidden-xs" >{{row.waiter_commision}}</td>
					            <td class="hidden-xs" >{{(row.total_amount*row.waiter_commision/100).toFixed(2)}}</td>
					            
					          </tr>
					          <tr>
					          <td><b>Total:</b></td>
					          <td></td>
					        
					         
					          <td><b>{{ getWaiterCommissionAmt() }}</b></td>
					          <!-- <td><b>{{ getWaiterCommission().toFixed(2) }}</b></td> -->
					          </tr>
					        </tbody>
					        </table>
</div>
