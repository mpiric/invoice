'use strict';
/**
 * controllers for ng-table
 * Simple table with sorting and filtering on AngularJS
 */
 app.factory('Excel',function($window){
        var uri='data:application/vnd.ms-excel;base64,',
            template='<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
            base64=function(s){return $window.btoa(unescape(encodeURIComponent(s)));},
            format=function(s,c){return s.replace(/{(\w+)}/g,function(m,p){return c[p];})};
        return {
            tableToExcel:function(tableId,worksheetName){
                var table=$(tableId),
                    ctx={worksheet:worksheetName,table:table.html()},
                    href=uri+base64(format(template,ctx));
                return href;
            }
        };
    });


app.controller('itemWiseSalesCtrl', ["$scope", "$http", "Excel","$timeout", function ($scope,$http,Excel,$timeout) {
	
	$scope.item_wise_report = [];
	$scope.adminBranch=false;
	$scope.today = function () {
        $scope.dt = new Date();
    };
    $scope.today();

    $scope.clear = function () {
        $scope.dt = null;
    };

    // Disable weekend selection
    $scope.disabled = function (date, mode) {
        return (mode === 'day' && (date.getDay() === 0 || date.getDay() === 6));
    };

    $scope.toggleMin = function () {
        $scope.minDate = $scope.minDate ? null : new Date();
    };
    $scope.toggleMin();

    $scope.open = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();

        $scope.opened = !$scope.opened;
    };
    $scope.endOpen = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.startOpened = false;
        $scope.endOpened = !$scope.endOpened;
    };
    $scope.startOpen = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.endOpened = false;
        $scope.startOpened = !$scope.startOpened;
    };

    $scope.dateOptions = {
        formatYear: 'yy',
        startingDay: 1
    };

    $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
    $scope.format = $scope.formats[0];

    $scope.hstep = 1;
    $scope.mstep = 15;

    // Time Picker
    $scope.options = {
        hstep: [1, 2, 3],
        mstep: [1, 5, 10, 15, 25, 30]
    };

    $scope.ismeridian = true;
    $scope.toggleMode = function () {
        $scope.ismeridian = !$scope.ismeridian;
    };

    $scope.update = function () {
        var d = new Date();
        d.setHours(14);
        d.setMinutes(0);
        $scope.dt = d;
    };

    $scope.changed = function () {
        $log.log('Time changed to: ' + $scope.dt);
    };

    $scope.clear = function () {
        $scope.dt = null;
    };

    $scope.exportToExcel=function(tableId){ // ex: '#my-table'
        var exportHref=Excel.tableToExcel(tableId,'WireWorkbenchDataExport');
        $timeout(function(){location.href=exportHref;},100); // trigger download
    };

    //item_wise_sales_all, data : {}

    // set from date
      // var fromDate = new Date(new Date().getTime() + 24 * 60 * 60 * 1000);
      // var fromday = fromDate.getDate()
      // var frommonth = fromDate.getMonth() + 1
      // var fromyear = fromDate.getFullYear()
      // $scope.start = fromday + "/" + frommonth + "/" + fromyear ;

    // set to date
      // var toDate = new Date(new Date().getTime() + 24 * 60 * 60 * 1000);
      // var today = toDate.getDate()
      // var tomonth = toDate.getMonth() + 1
      // var toyear = toDate.getFullYear()
      // $scope.end = today + "/" + tomonth + "/" + toyear ;

    $scope.finalQty = 0;
    $scope.finalTotal = 0;

     var param = $( "[name='report_form']" ).serialize();

          var request = $http({
                        method: "post",
                        url: "index.php/report/item_wise_sales_data", 
                        data: param,
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                    });

                request.success(function (response) {
                    
                          //console.log(response);
                          $scope.item_wise_report = response.data;
						  $scope.finalQty = response.finalQty;
                          $scope.finalTotal = response.finalTotal;

                    });

    var request = $http({
                  method: "post",
                  url: "index.php/report/branchwise_brand_item_wise_sales",
                  data: {},
                  headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
              });
          request.success(function (response) {
               
              $scope.brand_list_by_branch = response.brand_list_by_branch;
            });

          var request = $http({
                  method: "post",
                  url: "index.php/branch/getLoggedInBranchDetails",
                  data: {},
                  headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
              });

          request.success(function (response) {
            if(response.data.branch_type == 1)
            {
              $scope.adminBranch=true;
            }

                    $scope.branch_list = response.branch_list;

              });

    $scope.branchwiseBrand = function (branch_idObj)
    {
      var request = $http({
                  method: "post",
                  url: "index.php/report/branchwise_brand",
                  data: "branch_id="+branch_idObj.branch_id,
                  headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
              });
          request.success(function (response) {
               
              $scope.brand_list_by_branch = response.brand_list_by_branch;
            });
    }
    $scope.viewItemwiseSalesReport = function()
    {
      var param = $( "[name='report_form']" ).serialize();
		console.log(param);
      var request = $http({
                        method: "post",
                        url: "index.php/report/item_wise_sales_data",
                        data: param,
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                    });

                request.success(function (response) {
                    
                         
                          $scope.item_wise_report = response.data;
                          $scope.finalQty = response.finalQty;
                          $scope.finalTotal = response.finalTotal;
                           //console.log(item_wise_report);

                    });
    }

    $scope.viewItemwiseSalesReportPDF = function()
    {
      var param = $( "[name='report_form']" ).serialize();

       console.log(param);

        angular.element("#pdf_loader").show();

      var request = $http({
                        method: "post",
                        url: "index.php/report/item_wise_sales_data_pdf",
                        data: param,
                        responseType : 'arraybuffer',
                        headers: {
                                              accept: 'application/pdf',
                                              'Content-Type': 'application/x-www-form-urlencoded' 
                          },
                          cache: true,
                    });

                request.success(function (response, status, headers) {                      

                            var file = new Blob([response], {type: 'application/pdf'});
                            var fileURL = URL.createObjectURL(file);
                            window.open(fileURL);

                            angular.element("#pdf_loader").hide();

                      });
    }
	
	

    $scope.getItemwiseTotal = function(item)
    {
      //console.log('item'+item);
      //console.log(item.length);

      var final_total = 0;
      for(var i = 0; i < item.length; i++){
             
              var cat_name = item[i];
            
              final_total += parseFloat(cat_name.total!=null ? cat_name.total : 0);
			  
			  
          }
			
          return final_total;
    }

    $scope.getItemQuantity = function(item)
    {
      //console.log('item'+item);
      //console.log(item.length);

      var final_total = 0;
      for(var i = 0; i < item.length; i++){
             
              var cat_name = item[i];
            
              final_total += parseFloat(cat_name.quantity!=null ? cat_name.quantity : 0);
			  
         }
		
        return final_total;
    }
				
}]);

app.controller('waiterReportCtrl', ["$scope", "$http", "$state","$log","Excel","$timeout", function ($scope,$http,$state,$log,Excel,$timeout) {

  $scope.waiteralllist = [];
  $scope.waiter_list_by_branch = [];
  $scope.adminBranch=false;
  
    $scope.today = function () {
        $scope.dt = new Date();
    };
    $scope.today();

    $scope.clear = function () {
        $scope.dt = null;
    };

    // Disable weekend selection
    $scope.disabled = function (date, mode) {
        return (mode === 'day' && (date.getDay() === 0 || date.getDay() === 6));
    };

    $scope.toggleMin = function () {
        $scope.minDate = $scope.minDate ? null : new Date();
    };
    $scope.toggleMin();

    $scope.open = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();

        $scope.opened = !$scope.opened;
    };
    $scope.endOpen = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.startOpened = false;
        $scope.endOpened = !$scope.endOpened;
    };
    $scope.startOpen = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.endOpened = false;
        $scope.startOpened = !$scope.startOpened;
    };

    $scope.dateOptions = {
        formatYear: 'yy',
        startingDay: 1
    };

    $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
    $scope.format = $scope.formats[0];

    $scope.hstep = 1;
    $scope.mstep = 15;

    // Time Picker
    $scope.options = {
        hstep: [1, 2, 3],
        mstep: [1, 5, 10, 15, 25, 30]
    };

    $scope.ismeridian = true;
    $scope.toggleMode = function () {
        $scope.ismeridian = !$scope.ismeridian;
    };

    $scope.update = function () {
        var d = new Date();
        d.setHours(14);
        d.setMinutes(0);
        $scope.dt = d;
    };

    $scope.changed = function () {
        $log.log('Time changed to: ' + $scope.dt);
    };

    $scope.clear = function () {
        $scope.dt = null;
    };

    $scope.exportToExcel=function(tableId){ // ex: '#my-table'
        var exportHref=Excel.tableToExcel(tableId,'WireWorkbenchDataExport');
        $timeout(function(){location.href=exportHref;},100); // trigger download
    };
   //  var waiter_id = $state.params.waiter_id;

    var request = $http({
                  method: "post",
                  url: "index.php/branch/getLoggedInBranchDetails",
                  data: {},
                  headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
              });

          request.success(function (response) {
            if(response.data.branch_type == 1)
            {
              $scope.adminBranch=true;
            }

                    $scope.branch_list = response.branch_list;

              });

    // var request = $http({
    //                       method: "post",
    //                       url: "index.php/report/waiter_list_all",
    //                       data: {},
    //                       headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    //                   });

    //               request.success(function (response) {

    //                         $scope.waiteralllist = response.data;  
    //                         //console.log($scope.waiteralllist);  
                       
    //                   });

    // get waiter list by branch ( logged in )
    var request = $http({
        method: "post",
        url: "index.php/report/branchwise_waiter",
        data: "",
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    });

    request.success(function (response) {
  
          $scope.waiter_list_by_branch = response.waiter_list_by_branch;
          console.log($scope.waiter_list_by_branch);

    });


    $scope.branchwiseWaiter = function (branch_idObj)
    {

      var request = $http({
                  method: "post",
                  url: "index.php/report/branchwise_waiter",
                  data: "branch_id="+branch_idObj.branch_id,
                  headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
              });

          request.success(function (response) {
            
                    $scope.waiter_list_by_branch = response.waiter_list_by_branch;
                    console.log($scope.waiter_list_by_branch);

              });
    }


    $scope.viewReport = function(branch_id,waiter_id)
    {
         var param = $( "[name='report_form']" ).serialize();

         angular.element("#pdf_loader").show();

         //console.log(param);

         // var request = $http({
         //                  method: "post",
         //                  url: "index.php/report/waiter_report_byWaiter",
         //                  data: param,
         //                  headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
         //              });

         //          request.success(function (response) {
                      
         //                    $scope.waiteralllist = response.data;  
         //                    //console.log($scope.waiter_list);  
                       
         //              });

        var request = $http({
                          method: "post",
                          url: "index.php/report/report_waiter",
                          data: param,
                          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                      });

                  request.success(function (response) {

                          angular.element("#pdf_loader").hide();
                      
                            $scope.waiteralllist = response.data;  
                            //console.log($scope.waiter_list);  

                            
                      });
    }



    $scope.viewReportPDF = function(branch_id,waiter_id)
    {
        var param = $( "[name='report_form']" ).serialize();

        angular.element("#pdf_loader").show();

        var request = $http({
                          method: "post",
                          url: "index.php/report/report_waiter_pdf",
                          data: param,                        
                          responseType : 'arraybuffer',
                          headers: {
                                              accept: 'application/pdf',
                                              'Content-Type': 'application/x-www-form-urlencoded' 
                          },
                          cache: true,
                      });

                  request.success(function (response, status, headers) {                      

                            var file = new Blob([response], {type: 'application/pdf'});
                            var fileURL = URL.createObjectURL(file);
                            window.open(fileURL);

                            angular.element("#pdf_loader").hide();

                      });

    }

    $scope.getWaiterCommissionAmt = function()
    {
      //console.log($scope.waiteralllist.length);
      var final_total = 0;
      for(var i = 0; i < $scope.waiteralllist.length; i++){
             
              var order = $scope.waiteralllist[i];
             // console.log(order.waiter_commision);
              
              final_total += parseFloat(order.waiter_commision!=null ? order.waiter_commision : 0);
          }
         // console.log(final_total);
          return final_total;
    }
    $scope.getWaiterCommission = function()
    {
      var final_total = 0;
      for(var i = 0; i < $scope.waiteralllist.length; i++){
             
              var order = $scope.waiteralllist[i];

              var waiter_com = order.waiter_commision!=null ? order.waiter_commision : 0;
              var total_amt = order.total_amount!=null ? order.total_amount : 0;
              
              final_total += parseFloat((total_amt*waiter_com)/100);
          }
         // console.log(final_total);
          return final_total;
    }
      
}]);

app.controller('storeItemsReportCtrl', ["$scope", "$http", "$state","$log","Excel","$timeout", function ($scope,$http,$state,$log,Excel,$timeout){


    $scope.today = function() {
        $scope.dt = new Date();
    };
    $scope.today();

    $scope.clear = function() {
        $scope.dt = null;
    };

    // Disable weekend selection
    $scope.disabled = function(date, mode) {
        return (mode === 'day' && (date.getDay() === 0 || date.getDay() === 6));
    };

    $scope.toggleMin = function() {
        $scope.minDate = $scope.minDate ? null : new Date();
    };
    $scope.toggleMin();

    $scope.open = function($event) {
        $event.preventDefault();
        $event.stopPropagation();

        $scope.opened = !$scope.opened;
    };
    $scope.endOpen = function($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.startOpened = false;
        $scope.endOpened = !$scope.endOpened;
    };
    $scope.startOpen = function($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.endOpened = false;
        $scope.startOpened = !$scope.startOpened;
    };

    $scope.dateOptions = {
        formatYear: 'yy',
        startingDay: 1
    };

    $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
    $scope.format = $scope.formats[0];

    $scope.hstep = 1;
    $scope.mstep = 15;

    // Time Picker
    $scope.options = {
        hstep: [1, 2, 3],
        mstep: [1, 5, 10, 15, 25, 30]
    };

    $scope.ismeridian = true;
    $scope.toggleMode = function() {
        $scope.ismeridian = !$scope.ismeridian;
    };

    $scope.update = function() {
        var d = new Date();
        d.setHours(14);
        d.setMinutes(0);
        $scope.dt = d;
    };

    $scope.changed = function() {
        $log.log('Time changed to: ' + $scope.dt);
    };

    $scope.clear = function() {
        $scope.dt = null;
    };
    $scope.show_branch_dd = false;

    var request = $http({
                            method: "post",
                            url: "index.php/branch/getLoggedInBranchDetails",
                            data: {},
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                        });
    request.success(function (response) {

          //console.log(response.data);
          if(response.data.branch_type=="1")
          {
              // show dropdown
              $scope.show_branch_dd = true; 
          }
          else
          {
              $scope.show_branch_dd = false;
          }
                                 
          $scope.branch_list = response.branch_list;                           
                                
          // get index of current loggedin branch
          var index = $scope.branch_list.map(function(e) { return e.branch_id; }).indexOf(response.data.branch_id);

          if(response.data.branch_type=="1")
          {
            $scope.branch_id = $scope.branch_list[index];
            $scope.branch_id = $scope.branch_id.branch_id;
          }
          else
          {
             $scope.branch_id = $scope.branch_list[index];
          }
         
          //$scope.branch_id = $scope.branch_id.branch_id;


    });

    $scope.storeItemsReport = function()
    {

        var param = $( "[name='report_form']" ).serialize();
        console.log(param);

        var request = $http({
                          method: "post",
                          url: "index.php/report/store_items_data", 
                          data: param,
                          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                        });

        request.success(function (response) {
          //console.log(response.data);
          $scope.store_items_report = response.data;
          $scope.saleWithTax = response.data.sale_with_tax.sale_with_tax;
          
          var cons_amt = 0;
          var close_amt = 0;

          angular.forEach($scope.store_items_report, function(value, key){

            angular.forEach(value.store_items, function(value1, key1){
              

                if(value1.open_stock == null)
                {
                  value1.open_stock = parseFloat(value1.open_stock ? value1.open_stock : 0.00);
                }
                if(value1.cons_amt == null)
                {
                  value1.cons_amt = parseFloat(value1.cons_amt ? value1.cons_amt : 0.00);

                }
                if(value1.close_amt == null)
                {
                  value1.close_amt = parseFloat(value1.close_amt ? value1.close_amt : 0.00);
                }

                var consAmt = parseFloat(((value1.open_stock+value1.purchase)-value1.instock)*value1.price) ;
                var closingAmt = parseFloat(value1.instock*value1.price);
                 cons_amt += consAmt;
                 close_amt += closingAmt;

                 //console.log(cons_amt);
                 
                
            })
            
          })
          $scope.total_cons_amt = cons_amt;
          $scope.total_closing_amt = close_amt;

          $scope.foodCost = (($scope.total_cons_amt*100)/$scope.saleWithTax);
        });
    }


}]);

app.controller('dailyPurchaseReportCtrl', ["$scope", "$http", "$state","$log","Excel","$timeout", function ($scope,$http,$state,$log,Excel,$timeout){

    $scope.today = function () {
        $scope.dt = new Date();
    };
    $scope.today();

    $scope.clear = function () {
        $scope.dt = null;
    };

    // Disable weekend selection
    $scope.disabled = function (date, mode) {
        return (mode === 'day' && (date.getDay() === 0 || date.getDay() === 6));
    };

    $scope.toggleMin = function () {
        $scope.minDate = $scope.minDate ? null : new Date();
    };
    $scope.toggleMin();

    $scope.open = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();

        $scope.opened = !$scope.opened;
    };
    $scope.endOpen = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.startOpened = false;
        $scope.endOpened = !$scope.endOpened;
    };
    $scope.startOpen = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.endOpened = false;
        $scope.startOpened = !$scope.startOpened;
    };

    $scope.dateOptions = {
        formatYear: 'yy',
        startingDay: 1
    };

    $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
    $scope.format = $scope.formats[0];

    $scope.hstep = 1;
    $scope.mstep = 15;

    // Time Picker
    $scope.options = {
        hstep: [1, 2, 3],
        mstep: [1, 5, 10, 15, 25, 30]
    };

    $scope.ismeridian = true;
    $scope.toggleMode = function () {
        $scope.ismeridian = !$scope.ismeridian;
    };

    $scope.update = function () {
        var d = new Date();
        d.setHours(14);
        d.setMinutes(0);
        $scope.dt = d;
    };

    $scope.changed = function () {
        $log.log('Time changed to: ' + $scope.dt);
    };

    $scope.clear = function () {
        $scope.dt = null;
    };

     $scope.exportToExcel=function(tableId){ // ex: '#my-table'
            var exportHref=Excel.tableToExcel(tableId,'WireWorkbenchDataExport');
            $timeout(function(){location.href=exportHref;},100); // trigger download
        };
    $scope.message = "Select the date";
    $scope.is_msg = true;


    $scope.dailyPurchaseReport = function()
    {
      $scope.is_msg = false;

      var param = $( "[name='report_form']" ).serialize();

      var request = $http({
                            method: "post",
                            url: "index.php/report/daily_purchase_data", 
                            data: param,
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                          });
      request.success(function (response) {
        console.log(response.data);
            
            $scope.daily_purchase = response.data;

      });

    }

    $scope.getInstock = function()
    {
      var final_total = 0;
      for(var i = 0; i < $scope.daily_purchase.length; i++){
             
              var total = $scope.daily_purchase[i];

              var instock = total.instock!=null ? total.instock : 0;
              //var total_amt = total.today_qty!=null ? total.today_qty : 0;
              
              final_total += parseFloat(instock);
          }
         // console.log(final_total);
          return final_total;
    }

    $scope.getPurchaseToday = function()
    {
      var final_total = 0;
      for(var i = 0; i < $scope.daily_purchase.length; i++){
             
              var total = $scope.daily_purchase[i];

              var today_qty = total.today_qty!=null ? total.today_qty : 0;
              //var total_amt = total.today_qty!=null ? total.today_qty : 0;
              
              final_total += parseFloat(today_qty);
          }
         // console.log(final_total);
          return final_total;
    }

}]); 

app.controller('productRecipeReportCtrl', ["$scope", "$http", "$state","$log","Excel","$timeout", function ($scope,$http,$state,$log,Excel,$timeout){


  console.log('productrecipectrl');

    $scope.today = function () {
        $scope.dt = new Date();
    };
    $scope.today();

    $scope.clear = function () {
        $scope.dt = null;
    };

    // Disable weekend selection
    $scope.disabled = function (date, mode) {
        return (mode === 'day' && (date.getDay() === 0 || date.getDay() === 6));
    };

    $scope.toggleMin = function () {
        $scope.minDate = $scope.minDate ? null : new Date();
    };
    $scope.toggleMin();

    $scope.open = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();

        $scope.opened = !$scope.opened;
    };
    $scope.endOpen = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.startOpened = false;
        $scope.endOpened = !$scope.endOpened;
    };
    $scope.startOpen = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.endOpened = false;
        $scope.startOpened = !$scope.startOpened;
    };

    $scope.dateOptions = {
        formatYear: 'yy',
        startingDay: 1
    };

    $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
    $scope.format = $scope.formats[0];

    $scope.hstep = 1;
    $scope.mstep = 15;

    // Time Picker
    $scope.options = {
        hstep: [1, 2, 3],
        mstep: [1, 5, 10, 15, 25, 30]
    };

    $scope.ismeridian = true;
    $scope.toggleMode = function () {
        $scope.ismeridian = !$scope.ismeridian;
    };

    $scope.update = function () {
        var d = new Date();
        d.setHours(14);
        d.setMinutes(0);
        $scope.dt = d;
    };

    $scope.changed = function () {
        $log.log('Time changed to: ' + $scope.dt);
    };

    $scope.clear = function () {
        $scope.dt = null;
    };

     $scope.exportToExcel=function(tableId){ // ex: '#my-table'
            var exportHref=Excel.tableToExcel(tableId,'WireWorkbenchDataExport');
            $timeout(function(){location.href=exportHref;},100); // trigger download
        };

    $scope.show_branch_dd = false;

    var request = $http({
                            method: "post",
                            url: "index.php/branch/getLoggedInBranchDetails",
                            data: {},
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                        });
    request.success(function (response) {

          //console.log(response.data);
          if(response.data.branch_type=="1")
          {
              // show dropdown
              $scope.show_branch_dd = true; 
          }
          else
          {
              $scope.show_branch_dd = false;
          }
                                 
          $scope.branch_list = response.branch_list;                           
                                
          // get index of current loggedin branch
          var index = $scope.branch_list.map(function(e) { return e.branch_id; }).indexOf(response.data.branch_id);

          if(response.data.branch_type=="1")
          {
            $scope.branch_id = $scope.branch_list[index];
            $scope.branch_id = $scope.branch_id.branch_id;
          }
          else
          {
             $scope.branch_id = $scope.branch_list[index];
          }
         
          //$scope.branch_id = $scope.branch_id.branch_id;


    });

    $scope.message = "Select the date";
    $scope.is_msg = true;


    $scope.productRecipeReport = function()
    {
      $scope.is_msg = false;

      var param = $( "[name='report_form']" ).serialize();

      console.log(param);

      var request = $http({
                            method: "post",
                            url: "index.php/report/product_recipe_data", 
                            data: param,
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                          });
      request.success(function (response) {

        console.log(response.data);

        $scope.data = response.data;
        

      });

    }



}]); 