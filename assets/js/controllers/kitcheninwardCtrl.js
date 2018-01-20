'use strict';
/**
 * controllers for ng-table
 * Simple table with sorting and filtering on AngularJS
 */

app.controller('kitcheninwardCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {

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

    $scope.is_editable = false;

    // set from date
      // var fromDate = new Date(new Date().getTime());
      // var fromday = fromDate.getDate()
      // var frommonth = fromDate.getMonth() + 1
      // var fromyear = fromDate.getFullYear()
      // $scope.start = fromday + "/" + frommonth + "/" + fromyear ;

    angular.element('#filterBtn').hide();
    angular.element('#submitBtn').hide();
    
    $scope.showBtn = function()
    {
      angular.element('#filterBtn').show();
    }
    // get last updated date

    var request = $http({
                            method: "post",
                            url: "index.php/kitcheninward/check_max_date",
                            data: {},
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                        });
                    request.success(function (response) {

                         //alert(response.data.created);
                        $scope.created = response.data.created;  

                     });

    // get store product

    function get_store_products(filterdate)
    {
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
                    url: "index.php/kitcheninward/get_kitchen_product_list_by_date",
                    data: 'filterdate='+filterdate,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
        angular.element("#pdf_loader1").show();
                /* Successful HTTP post request or not */
            request.success(function (response) {
            
            //console.log(response.data);
            angular.element("#pdf_loader1").hide();
                
                var data = response.data;      
                //$scope.disableInward = false;  

                angular.forEach(data, function(value, key) {

                    value.inward_qty = '';

                });           
             
                $scope.tableParams = new ngTableParams({
                    page: 1, // show first page
                    count: 100, // count per page
                    sorting: {
                          updated: 'desc' // initial sorting
                        },
                    filter: {
                         name: '' // initial filter
                     }

                }, {
                    total: data.length, // length of data
                    getData: function ($defer, params) {
                        var orderedData;
                        
                        var filteredData = params.filter() ?
                        $filter('filter')(data, params.filter()) :
                        data;

                        var orderedData = params.sorting() ? $filter('orderBy')(filteredData, params.orderBy()) : filteredData;
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
                        url: "index.php/kitcheninward/check_for_editable_field",
                        data: 'filterdate='+filterdate,
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                    });
                    /* Successful HTTP post request or not */
                    request.success(function (response) {
                        
                        if(response.total_rows>0)
                        {
                            $scope.is_editable = true;
                        }
                        else
                        {
                            $scope.is_editable = false;
                        }

                    });
    }

   $scope.validateInward = function(remaining_qty)
   {
        // console.log('remaining:');
        // console.log(remaining_qty);

        if($scope.is_editable==true)
        {            
            if(remaining_qty==null)
            {
                return false;
            }
            else
            {
                 return true;
            }
            //return false;

        }
        else
        {
            
            return true;
            
        }

   }

    $scope.dokitcheninward = function(form_kitcheninward)
    {
        
        angular.element("#pdf_loader").show();
        var param = $( "[name='form_kitcheninward']" ).serialize();
           // console.log(param);

            var product_id_arr = [];

            var paramArr = $( "[name='form_kitcheninward']" ).serializeArray();

             angular.forEach(paramArr, function(value, key) {



                        if(value.name=='store_product_inward_id')
                        {
                            
                           var store_product_inward_id = (value.value);
                           //console.log(typeof product_id);
                           
                           // not allow duplicates
                           if(product_id_arr.indexOf(store_product_inward_id) === -1)
                           {
                                product_id_arr.push(store_product_inward_id);
                           }
                        }

                    });


             // console.log(product_id_arr);
             // return false;
        
            var request = $http({
                method: "post",
                url: "index.php/kitcheninward/dokitcheninward",
                data: param+'&product_id_arr='+product_id_arr.toString(),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            });


            /* Successful HTTP post request or not */
            request.success(function (response) {

                //console.log(response);
                angular.element("#pdf_loader").hide();
                
                if(response.status=="1")
                {
                    //error             
                    //alert(response.data);                   
                    $state.go('app.kitcheninward.create');
                    // $scope.inward_qty = '';
                    // angular.forEach(data, function(value, key) {

                    //     console.log(value);

                    //     value.inward_qty = '';

                       

                    // });
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


                // reload data
                $scope.tableParams.reload();
                $scope.tableParams.page(1);
                $scope.tableParams.sorting({});

                get_store_products($scope.start);

            });
    }
        

    $scope.filterBydate = function(filterdate)
    {
        $scope.is_msg = false;
        angular.element('#submitBtn').show();
       
        get_store_products(filterdate);
        $scope.tableParams.reload();
        $scope.tableParams.page(1);
        $scope.tableParams.sorting({});

    }

    $scope.err_inward_qty = false;

    $scope.validateInwardQty = function(storeInstock,instock,inward_qty) {

       // alert(inward_qty);
        

        if(inward_qty=='' || inward_qty==null)
        {
           // alert('if'+inward_qty);
            $scope.err_inward_qty = true;
        }
        else
        {
            inward_qty = parseFloat(inward_qty);
            storeInstock = parseFloat(storeInstock);
            instock = isNaN(parseFloat(instock)) ? 0 : parseFloat(instock);    
            //alert('else'+inward_qty);
            if(instock == 0) 
            {
                if(storeInstock<inward_qty)
                {
                    $scope.form_kitcheninward.$invalid = true;
                    $scope.err_inward_qty = true;
                }
                else
                {
                    $scope.err_inward_qty = false;
                }
            }
            else
            {
                // if(inward_qty>instock)
                // {
                //     $scope.form_kitcheninward.$invalid = true;
                //     $scope.err_inward_qty = true;
                // }
                // else
                // {
                //     $scope.err_inward_qty = false;
                // }
                if(storeInstock<inward_qty)
                {
                    $scope.form_kitcheninward.$invalid = true;
                    $scope.err_inward_qty = true;
                }
                else
                {
                    $scope.err_inward_qty = false;
                }
                
            }
        }

        
       
    };   


		
	}]);