'use strict';
/**
 * controllers for ng-table
 * Simple table with sorting and filtering on AngularJS
 */

app.controller('storeproductCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {

            var request = $http({
                    method: "post",
                    url: "index.php/storeproduct/storeproduct_list",
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
                              store_product_id: 'asc' // initial sorting
                            },
                        filter: {
                             store_product_id: '' // initial filter
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
                    $scope.updatestoreproduct = function(store_product_id) {   

                        $state.go('app.storeproduct.update', {store_product_id: store_product_id});                            
                    }

                });
                // else data not found - error

              $scope.open = function (size,store_product_id) {

                    var modalInstance = $modal.open({
                        templateUrl: 'myModalContent.html',
                        controller: 'ModalInstanceCtrl',
                        size: size,
                        resolve: {
                            store_product_id: function () {
                                return store_product_id;
                            }
                            ,
                            removeRow: function()
                            {                   
                                return $scope.tableParams.data;
                            }
                        }
                    });
            };

            $scope.infostoreproduct = function(store_product_id)
            {
                $state.go('app.storeproduct.info', {store_product_id: store_product_id}); 
            }
      
    }]);


app.controller('storeproductCreateCtrl', ["$scope", "$http", "$state", function ($scope, $http, $state) {

    $scope.heading='Create';

    $scope.checkName = function(name)
    {
        $scope.checkN = false;

        var request = $http({
              method: "post",
              url: "index.php/storeproduct/check_name",
              data: 'name='+name,
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
          });

        request.success(function (response) {

            if(response.status == false)
            {
                $scope.checkN = true;
        
            }
            else
            {
                $scope.checkN = false;
        
            }

        });
    }

    $scope.checkCode = function(product_code)
    {
        $scope.checkC = false;

        var request = $http({
              method: "post",
              url: "index.php/storeproduct/check_code",
              data: 'code='+product_code,
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
          });

        request.success(function (response) {

            if(response.status == false)
            {
                $scope.checkC = true;
                
            }
            else
            {
                $scope.checkC = false;
            }

        });
    }



            var unit_list = [{key:"0", value:"NONE"},{key:"2", value:"GRAM"},{key:"1", value:"KG"},{key:"3", value:"LTR"},{key:"4", value:"ML"},{key:"5", value:"NOS"}];
            $scope.unit_list = unit_list;

            var request = $http({
                  method: "post",
                  url: "index.php/branch/getLoggedInBranchDetails",
                  data: {},
                  headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
              });

          request.success(function (response) {

            // if(response.data.branch_type == 1)
            // {
            //   $scope.adminBranch=true;
            // }

            $scope.branch_id = response.data.branch_id;

          });


              var request = $http({
                                method: "post",
                                url: "index.php/category/getCategoryList",
                                data: {},
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                        request.success(function (response) {

                          
                            $scope.cat_list = response.category_list;   
                            


            if( ( $state.params.store_product_id!="" ) && ( typeof $state.params.store_product_id != 'undefined' ) && ( $state.params.store_product_id != 'undefined' ) && ( $state.params.store_product_id != null ) )
                {

                   $scope.heading = 'Update';  
                    var store_product_id = $state.params.store_product_id;
                    // get details by id


                    var request = $http({
                                method: "post",
                                url: "index.php/storeproduct/get_storeproduct_details",
                                data: 'store_product_id='+store_product_id,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                            /* Successful HTTP post request or not */
                            request.success(function (response) {
                                
                                    if (response.data) {
                                        // items have value
                                        console.log(response.data);

                                        //$scope.branch_id = response.data.branch_id;
                                        $scope.store_product_id = response.data.store_product_id;
                                        $scope.name = response.data.name;
                                        $scope.product_code = response.data.product_code;

                                        var index1 = $scope.cat_list.map(function(e) { return e.category_id; }).indexOf(response.data.category_id);

                                        $scope.category_id = $scope.cat_list[index1];

                                        var index2 = $scope.unit_list.map(function(e) { return e.key; }).indexOf(response.data.unit);

                                        $scope.unit   = $scope.unit_list[index2];
                                        $scope.price = response.data.price;

                                      
                                    } 
                            });
                    
                    $scope.createstoreproduct = function(create_storeproduct)
                    {                       
                        if(create_storeproduct.$valid) {

                            var param = $( "[name='create_storeproduct']" ).serialize();
                            
                            var request = $http({
                                method: "post",
                                url: "index.php/storeproduct/update_storeproduct",
                                data: param+'&store_product_id='+store_product_id+'&branch_id='+$scope.branch_id,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });

                            request.success(function (response) {

                                if(response.status=="1")
                                {
                                    //error                                
                                    $state.go('app.storeproduct.list');
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
                    $scope.createstoreproduct = function(create_storeproduct)
                    {
                        if(create_storeproduct.$valid) {

                            var param = $( "[name='create_storeproduct']" ).serialize();
                            console.log(param);
                                                   
                            var request = $http({
                                method: "post",
                                url: "index.php/storeproduct/create_storeproduct",
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
                                    $state.go('app.storeproduct.list');
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
            });  
}]);


app.controller('ModalInstanceCtrl', ["$scope","$http","$state","store_product_id","removeRow", "$modalInstance", function ($scope,$http,$state,store_product_id,removeRow, $modalInstance) {

    $scope.ok = function () {
        $modalInstance.close();
        // delete product category 

        var request = $http({
                    method: "post",
                    url: "index.php/storeproduct/storeproduct_delete",
                    data: 'store_product_id='+store_product_id,
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
                                         if( comArr[i].store_product_id === store_product_id ) {
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

// app.controller('storeproductInfoCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {

        
//         var store_product_id = $state.params.store_product_id;

      
//             var request = $http({
//                     method: "post",
//                     url: "index.php/storeproduct/get_tax_main_details",
//                     data: 'store_product_id='+store_product_id,
//                     headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
//                 });
//                 /* Successful HTTP post request or not */
//                 request.success(function (response) {
                                   
//                     $scope.data = response.data;
                    
//                 });     

//                 $scope.updatestoreproduct = function(store_product_id) {  
//                         $state.go('app.storeproduct.update', {store_product_id: store_product_id});                         
//                     }  
//     }]);