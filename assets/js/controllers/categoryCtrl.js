'use strict';
/**
 * controllers for ng-table
 * Simple table with sorting and filtering on AngularJS
 */

app.controller('categoryCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {

            var request = $http({
                    method: "post",
                    url: "index.php/category/category_list",
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
                              category_id: 'asc' // initial sorting
                            },
                        filter: {
                             category_id: '' // initial filter
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
                    $scope.updatecategory = function(category_id) {   

                        $state.go('app.category.update', {category_id: category_id});                            
                    }

                });
                // else data not found - error

              $scope.open = function (size,category_id) {

                    var modalInstance = $modal.open({
                        templateUrl: 'myModalContent.html',
                        controller: 'ModalInstanceCtrl',
                        size: size,
                        resolve: {
                            category_id: function () {
                                return category_id;
                            }
                            ,
                            removeRow: function()
                            {                   
                                return $scope.tableParams.data;
                            }
                        }
                    });
            };

            $scope.infocategory = function(category_id)
            {
                $state.go('app.category.info', {category_id: category_id}); 
            }
      
    }]);


app.controller('categoryCreateCtrl', ["$scope", "$http", "$state", function ($scope, $http, $state) {

 $scope.heading='Create';

            if( ( $state.params.category_id!="" ) && ( typeof $state.params.category_id != 'undefined' ) && ( $state.params.category_id != 'undefined' ) && ( $state.params.category_id != null ) )
                {

                   $scope.heading = 'Update';  
                    var category_id = $state.params.category_id;
                    // get details by id
                    console.log(category_id);

                    var request = $http({
                                method: "post",
                                url: "index.php/category/get_category_details",
                                data: 'category_id='+category_id,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                            /* Successful HTTP post request or not */
                            request.success(function (response) {
                                
                                    if (response.data) {
                                        // items have value
                                        console.log(response.data);

                                        //$scope.branch_id = response.data.branch_id;
                                        $scope.category_id = response.data.category_id;
                                        $scope.cat_name = response.data.cat_name;
                                       
                                    } 
                            });
                    
                    $scope.createCategory = function(create_category)
                    {                       
                        if(create_category.$valid) {

                            var param = $( "[name='create_category']" ).serialize();
                            
                            var request = $http({
                                method: "post",
                                url: "index.php/category/update_category",
                                data: param+'&category_id='+category_id,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });

                            request.success(function (response) {

                                if(response.status=="1")
                                {
                                    //error                                
                                    $state.go('app.category.list');
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
                    $scope.createCategory = function(create_category)
                    {
                        if(create_category.$valid) {

                            var param = $( "[name='create_category']" ).serialize();
                            console.log(param);
                                                   
                            var request = $http({
                                method: "post",
                                url: "index.php/category/create_category",
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
                                    $state.go('app.category.list');
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


app.controller('ModalInstanceCtrl', ["$scope","$http","$state","category_id","removeRow", "$modalInstance", function ($scope,$http,$state,category_id,removeRow, $modalInstance) {

    $scope.ok = function () {
        $modalInstance.close();
        // delete product category 

        var request = $http({
                    method: "post",
                    url: "index.php/category/category_delete",
                    data: 'category_id='+category_id,
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
                                         if( comArr[i].category_id === category_id ) {
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
