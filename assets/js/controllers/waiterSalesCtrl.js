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

app.controller('waiterTestCtrl', ["$scope", "$http", function ($scope,$http) {
        console.log('response');
          var request = $http({
                          method: "post",
                          url: "index.php/report/waiter_list_all",
                          data: {},
                          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                      });

                  request.success(function (response) {
                      
                           // $scope.waiteralllist = response.data;  
                            //console.log('YESSSS');
                           // console.log(response.data); 
                            $scope.waiteralllist = response.data; 
                       
                      });

    $scope.getWaiterCommissionAmt = function()
    {
      console.log('len='+$scope.waiteralllist.length);
      // var final_total = 0;
      // for(var i = 0; i < $scope.waiteralllist.length; i++){
             
      //         var order = $scope.waiteralllist[i];
              
      //         final_total += parseFloat(order.waiter_commision);
      //     }
      //    // console.log(final_total);
      //     return final_total;
    }
    // $scope.getWaiterCommission = function()
    // {
    //   var final_total = 0;
    //   for(var i = 0; i < $scope.waiteralllist.length; i++){
             
    //           var order = $scope.waiteralllist[i];
              
    //           final_total += parseFloat((order.total_amount*order.waiter_commision)/100);
    //       }
    //      // console.log(final_total);
    //       return final_total;
    // }

                  
}]);

// app.controller('waiterSalesCtrl', ["$scope", "$http", "$state","$log","Excel","$timeout", function ($scope,$http,$state,$log,Excel,$timeout) {

// 	$scope.today = function () {
//         $scope.dt = new Date();
//     };
//     $scope.today();

//     $scope.clear = function () {
//         $scope.dt = null;
//     };

//     // Disable weekend selection
//     $scope.disabled = function (date, mode) {
//         return (mode === 'day' && (date.getDay() === 0 || date.getDay() === 6));
//     };

//     $scope.toggleMin = function () {
//         $scope.minDate = $scope.minDate ? null : new Date();
//     };
//     $scope.toggleMin();

//     $scope.open = function ($event) {
//         $event.preventDefault();
//         $event.stopPropagation();

//         $scope.opened = !$scope.opened;
//     };
//     $scope.endOpen = function ($event) {
//         $event.preventDefault();
//         $event.stopPropagation();
//         $scope.startOpened = false;
//         $scope.endOpened = !$scope.endOpened;
//     };
//     $scope.startOpen = function ($event) {
//         $event.preventDefault();
//         $event.stopPropagation();
//         $scope.endOpened = false;
//         $scope.startOpened = !$scope.startOpened;
//     };

//     $scope.dateOptions = {
//         formatYear: 'yy',
//         startingDay: 1
//     };

//     $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
//     $scope.format = $scope.formats[0];

//     $scope.hstep = 1;
//     $scope.mstep = 15;

//     // Time Picker
//     $scope.options = {
//         hstep: [1, 2, 3],
//         mstep: [1, 5, 10, 15, 25, 30]
//     };

//     $scope.ismeridian = true;
//     $scope.toggleMode = function () {
//         $scope.ismeridian = !$scope.ismeridian;
//     };

//     $scope.update = function () {
//         var d = new Date();
//         d.setHours(14);
//         d.setMinutes(0);
//         $scope.dt = d;
//     };

//     $scope.changed = function () {
//         $log.log('Time changed to: ' + $scope.dt);
//     };

//     $scope.clear = function () {
//         $scope.dt = null;
//     };

//     $scope.exportToExcel=function(tableId){ // ex: '#my-table'
//         var exportHref=Excel.tableToExcel(tableId,'WireWorkbenchDataExport');
//         $timeout(function(){location.href=exportHref;},100); // trigger download
//     };
//    //  var waiter_id = $state.params.waiter_id;
    
//     var request = $http({
//                           method: "post",
//                           url: "index.php/waiter/waiter_list_by_branch",
//                           data: {},
//                           headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
//                       });

//                   request.success(function (response) {
                      
//                             $scope.waiter_list = response.data;  
//                             //console.log($scope.waiter_list);  
                       
//                       });

//     var request = $http({
//                           method: "post",
//                           url: "index.php/report/waiter_list_all",
//                           data: {},
//                           headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
//                       });

//                   request.success(function (response) {
                      
//                             $scope.waiteralllist = response.data;  
//                             console.log($scope.waiteralllist);  
                       
//                       });


//     $scope.viewReport = function(waiter_id)
//     {
//          var param = $( "[name='report_form']" ).serialize();

//          //console.log(param);

//          // var request = $http({
//          //                  method: "post",
//          //                  url: "index.php/report/waiter_report_byWaiter",
//          //                  data: param,
//          //                  headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
//          //              });

//          //          request.success(function (response) {
                      
//          //                    $scope.waiteralllist = response.data;  
//          //                    //console.log($scope.waiter_list);  
                       
//          //              });

//         var request = $http({
//                           method: "post",
//                           url: "index.php/report/report_waiter",
//                           data: param,
//                           headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
//                       });

//                   request.success(function (response) {
                      
//                             $scope.waiteralllist = response.data;  
//                             //console.log($scope.waiter_list);  
                       
//                       });



//     }
//     $scope.getWaiterCommissionAmt = function()
//     {
//       var final_total = 0;
//       for(var i = 0; i < $scope.waiteralllist.length; i++){
             
//               var order = $scope.waiteralllist[i];
              
//               final_total += parseFloat(order.waiter_commision);
//           }
//          // console.log(final_total);
//           return final_total;
//     }
//     $scope.getWaiterCommission = function()
//     {
//       var final_total = 0;
//       for(var i = 0; i < $scope.waiteralllist.length; i++){
             
//               var order = $scope.waiteralllist[i];
              
//               final_total += parseFloat((order.total_amount*order.waiter_commision)/100);
//           }
//          // console.log(final_total);
//           return final_total;
//     }


				
// 	}]);
