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


app.controller('storeinwardCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", "Excel","$timeout", function ($scope, $filter, $http,$state,$modal, ngTableParams, Excel, $timeout) {

    //var Elm = angular.element('input[name="product_base_price"]');

    //alert('dw');
   // $scope.example = {
   //      remember: true

   //  };
   $scope.message = "Select the date and click on filter button.";
   $scope.is_msg = true;

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

    $scope.formats = ['dd-MM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
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

        angular.element('#filterBtn').hide();
        angular.element('#submitBtn').hide();
    $scope.showBtn = function()
    {
      angular.element('#filterBtn').show();
    }

    $scope.is_editable = false;

  
    // set from date
      // var fromDate = new Date(new Date().getTime());
      // var fromday = fromDate.getDate()
      // var frommonth = fromDate.getMonth() + 1
      // var fromyear = fromDate.getFullYear()
      // $scope.start = fromday + "/" + frommonth + "/" + fromyear ;


        // get last updated date

    var request = $http({
                                method: "post",
                                url: "index.php/storeinward/check_max_date",
                                data: {},
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                        request.success(function (response) {

                             //alert(response.data.created);
                            $scope.created = response.data.created;  

                         });

    
    $scope.currDate = new Date();
    //$scope.formattedDate =   $filter('date')($scope.currDate, "dd-MM-yyyy");
    //$scope.today = (new Date(), 'dd/MM/yy');
   // $scope.todayDate=date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
    //console.log($scope.todayDate);    
   function getStoreproducts(filterdate)
   {

    // var filterdate = $scope.start;


     if(typeof filterdate=='string')
              {
                    function replaceAll(str, find, replace) 
                    {
                      return str.replace(new RegExp(find, 'g'), replace);
                    }
                    filterdate = replaceAll(filterdate,'\/','-');
              }
              else
              {
                    // set from date
                     // var fromDate = new Date(new Date().getTime() + 24 * 60 * 60 * 1000);
                      var day = filterdate.getDate()
                      var month = filterdate.getMonth() + 1
                      var year = filterdate.getFullYear()
                      filterdate = day + "-" + month + "-" + year ;
              }


      var request = $http({
                      method: "post",
                      url: "index.php/storeinward/get_store_product_list_by_date",
                      data: 'filterdate='+filterdate,
                      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                  });

      angular.element("#pdf_loader1").show();
     request.success(function (response) {

      angular.element("#pdf_loader1").hide();
                  
        //console.log(response.data);

        var data = response.data;

        if(data.name != null)
        {
          angular.forEach(data, function(value, key) {  

            value.is_available = false;    
            value.purchase_qty = '0';                      

          });

        }

         // if any item is not purchased then the form is not valid

          
                

          $scope.tableParams = new ngTableParams({
              page: 1, // show first page
              count: 100, // count per page
              sorting: {
                    today_qty: 'desc' // initial sorting
                  },
              filter: {
                   today_qty: '' // initial filter
               }

          }, {
              total: data.length, // length of data
              getData: function ($defer, params) {
                  var orderedData;
                  
                  //console.log(params);
                  var filteredData = params.filter() ?
                  $filter('filter')(data, params.filter()) :
                  data;

                  var orderedData = params.sorting() ?
                                      $filter('orderBy')(filteredData, params.orderBy()) : filteredData;
                  var page=orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count());
                  $scope.data=page;
                  params.total(orderedData.length);
                  $defer.resolve(page);
                  
              }
          });

        });

    // check for the fields, whether they are editable or readonly
            var request = $http({
                        method: "post",
                        url: "index.php/storeinward/check_for_editable_field",
                        data: 'filterdate='+filterdate,
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                    });
                    /* Successful HTTP post request or not */
                    request.success(function (response) {
                      //console.log(response);
                      
                        
                        if(response.total_rows>0 )
                        {
                            $scope.is_editable = true;
                        }
                        else
                        {
                            $scope.is_editable = false;

                        }

                    });
   }
   //getStoreproducts($scope.start);

  


    $scope.createstoreinward = function(create_storeinward){

      angular.element("#pdf_loader").show();

                     
            if(create_storeinward.$valid) {

                var param = $( "[name='create_storeinward']" ).serialize();

                //console.log(param);
                
                var store_product_inward_id_arr = [];

                var paramArr = $( "[name='create_storeinward']" ).serializeArray();
                console.log(paramArr);
                //return false;

                angular.forEach(paramArr, function(value, key) {
                            

                            if(value.name=='store_product_inward_id')
                            {
                               var store_product_inward_id = (value.value);
                               //console.log(typeof store_product_inward_id);
                               //console.log(store_product_inward_id);
                               
                               // not allow duplicates
                               if(store_product_inward_id_arr.indexOf(store_product_inward_id) === -1)
                               {
                                    store_product_inward_id_arr.push(store_product_inward_id);
                               }
                            }

                        });
            
                var request = $http({
                    method: "post",
                    url: "index.php/storeinward/create_storeinward",
                    data: param+'&store_product_inward_id_arr='+store_product_inward_id_arr.toString(),
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                /* Successful HTTP post request or not */
                request.success(function (response) {
                    
                    //console.log(response.data);
                    angular.element("#pdf_loader").hide();

                    if(response.status=="1")
                    {
                        //error             
                        //alert(response.data);       
                               
                        $state.go('app.storeinward.create');
                    }
                    else if(response.status=="-1")
                    {
                       // validation error;
                       $('#validation_err').html(response.data);
                    }
                    else
                    {
                        //alert(response.data);                                
                    }

                    $scope.tableParams.reload();
                        $scope.tableParams.page(1);
                        $scope.tableParams.sorting({});

                       // $scope.purchase_qty = '';

                        getStoreproducts($scope.start);
                });

             }

        }

     
    $scope.filterBydate = function(filterdate)
    {
      
      angular.element('#submitBtn').show();
      $scope.is_msg = false;
      // console.log(filterdate);
      // console.log(typeof filterdate);

      getStoreproducts(filterdate);
      $scope.tableParams.reload();
      $scope.tableParams.page(1);
      $scope.tableParams.sorting({});

    }


    $scope.validatePuchaseQty = function(instock,purchase_qty) {

        purchase_qty = parseFloat(purchase_qty);
        instock = parseFloat(instock);

        if(purchase_qty>instock)
        {
          // make the form invalid
          $scope.create_storeinward.$invalid = true;
        }
       
    };

}]);