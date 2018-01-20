'use strict';
/**
 * controllers for ng-table
 * Simple table with sorting and filtering on AngularJS
 */

app.controller('taxmainCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {

            var request = $http({
                    method: "post",
                    url: "index.php/taxmain/tax_main_list",
                    data: {},
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                /* Successful HTTP post request or not */
                request.success(function (response) {
                
                //console.log(response.data);
                    
                    var data = response.data;

                    $scope.tableParams = new ngTableParams({
                        page: 1, // show first page
                        count: 5, // count per page
                        sorting: {
                              tax_id: 'asc' // initial sorting
                            },
                        filter: {
                             tax_id: '' // initial filter
                         }

                    }, {
                        total: data.length, // length of data
                        getData: function ($defer, params) {
                            var orderedData;
                            
                            //console.log(params);
                            
                            
                            /*  orderedData = params.sorting() ? $filter('orderBy')(data, params.orderBy()) : data;
                                $defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));
                                
                             orderedData = params.filter() ? $filter('filter')(data, params.filter()) : data;
                            $scope.data = orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count());
                            params.total(orderedData.length);
                            // set total for recalc pagination
                            $defer.resolve($scope.data);*/
                            
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
                    $scope.updatetaxmain = function(tax_id) {   

                        $state.go('app.taxmain.update', {tax_id: tax_id});                            
                    }

                });
                // else data not found - error

              $scope.open = function (size,tax_id) {

                    var modalInstance = $modal.open({
                        templateUrl: 'myModalContent.html',
                        controller: 'ModalInstanceCtrl',
                        size: size,
                        resolve: {
                            tax_id: function () {
                                return tax_id;
                            }
                            ,
                            removeRow: function()
                            {                   
                                return $scope.tableParams.data;
                            }
                        }
                    });
            };

            $scope.infotaxmain = function(tax_id)
            {
                $state.go('app.taxmain.info', {tax_id: tax_id}); 
            }
      
    }]);


app.controller('taxmainCreateCtrl', ["$scope", "$http", "$state", function ($scope, $http, $state) {

 $scope.heading='Create';

            if( ( $state.params.tax_id!="" ) && ( typeof $state.params.tax_id != 'undefined' ) && ( $state.params.tax_id != 'undefined' ) && ( $state.params.tax_id != null ) )
                {

                   $scope.heading = 'Update';  
                    var tax_id = $state.params.tax_id;
                    // get details by id


                    var request = $http({
                                method: "post",
                                url: "index.php/taxmain/get_tax_main_details",
                                data: 'tax_id='+tax_id,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                            /* Successful HTTP post request or not */
                            request.success(function (response) {
                                
                                    if (response.data) {
                                        // items have value
                                        console.log(response.data);

                                        //$scope.branch_id = response.data.branch_id;
                                        $scope.tax_id = response.data.tax_id;
                                        $scope.tax_name = response.data.tax_name;
                                        $scope.tax_type = response.data.tax_type;

                                      
                                    } 
                            });
                    
                    $scope.createtaxmain = function(create_taxmain)
                    {                       
                        if(create_taxmain.$valid) {

                            var param = $( "[name='create_taxmain']" ).serialize();
                            
                            var request = $http({
                                method: "post",
                                url: "index.php/taxmain/update_taxmain",
                                data: param+'&tax_id='+tax_id,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });

                            request.success(function (response) {

                                if(response.status=="1")
                                {
                                    //error                                
                                    $state.go('app.taxmain.list');
                                }
                                else if(response.status=="-1")
                                {
                                   // validation error;
                                   $('#validation_err').html(response.data);
                                }
                                else
                                {
                                    alert(response.data);                                
                                }
                            });
                        }
                    }
    
                }
                else
                {

                    $scope.is_create = true;
                    $scope.createtaxmain = function(create_taxmain)
                    {
                        if(create_taxmain.$valid) {

                            var param = $( "[name='create_taxmain']" ).serialize();
                            console.log(param);
                                                   
                            var request = $http({
                                method: "post",
                                url: "index.php/taxmain/create_taxmain",
                                data: param,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                            /* Successful HTTP post request or not */
                            request.success(function (response) {

                                //alert(data.status);
                                //console.log(response);

                                if(response.status=="1")
                                {
                                    //error                                
                                    $state.go('app.taxmain.list');
                                }
                                else if(response.status=="-1")
                                {
                                   // validation error;
                                   $('#validation_err').html(response.data);
                                }
                                else
                                {
                                    alert(response.data);                                
                                }
                            });

                         }
                    }
                }
            //});  
    }]);


app.controller('ModalInstanceCtrl', ["$scope","$http","$state","tax_id","removeRow", "$modalInstance", function ($scope,$http,$state,tax_id,removeRow, $modalInstance) {

    $scope.ok = function () {
        $modalInstance.close();
        // delete product category 

        var request = $http({
                    method: "post",
                    url: "index.php/taxmain/tax_main_delete",
                    data: 'tax_id='+tax_id,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                /* Successful HTTP post request or not */
                request.success(function (response) {
                
                                    
                    if (response.status=="1") {
                        // success

                        // remove the selected row
                        //console.log('delete');
                        
                         var index = -1;
                                   var comArr = eval( removeRow );
                                   for( var i = 0; i < comArr.length; i++ ) {
                                         if( comArr[i].tax_id === tax_id ) {
                                             index = i;
                                             break;
                                          }
                                   }
                                   if( index === -1 ) {
                                        alert( "Something gone wrong" );
                                   }
                          removeRow.splice( index, 1 );
                                  
                    }
                });

    };

    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
}]);

app.controller('taxmainInfoCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {

        
        var tax_id = $state.params.tax_id;

      
            var request = $http({
                    method: "post",
                    url: "index.php/taxmain/get_tax_main_details",
                    data: 'tax_id='+tax_id,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                /* Successful HTTP post request or not */
                request.success(function (response) {
                                   
                    $scope.data = response.data;
                    
                });     

                $scope.updatetaxmain = function(tax_id) {  
                        $state.go('app.taxmain.update', {tax_id: tax_id});                         
                    }  
    }]);