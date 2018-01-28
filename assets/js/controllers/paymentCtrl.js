'use strict';
/**
 * controllers for ng-table
 * Simple table with sorting and filtering on AngularJS
 */

app.controller('paymentCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {

            var request = $http({
                    method: "post",
                    url: "index.php/payment/payment_list",
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
                              payment_id: 'asc' // initial sorting
                            },
                        filter: {
                             payment_id: '' // initial filter
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
                    $scope.updatepayment = function(payment_id) {   

                        $state.go('app.payment.update', {payment_id: payment_id});                            
                    }

                });
                // else data not found - error

              $scope.open = function (size,payment_id) {

                    var modalInstance = $modal.open({
                        templateUrl: 'myModalContent.html',
                        controller: 'ModalInstanceCtrl',
                        size: size,
                        resolve: {
                            payment_id: function () {
                                return payment_id;
                            }
                            ,
                            removeRow: function()
                            {                   
                                return $scope.tableParams.data;
                            }
                        }
                    });
            };

            $scope.infotaxmain = function(payment_id)
            {
                $state.go('app.payment.info', {payment_id: payment_id}); 
            }
      
    }]);


app.controller('paymentCreateCtrl', ["$scope", "$http", "$state", function ($scope, $http, $state) {

 $scope.heading='Create';

            if( ( $state.params.payment_id!="" ) && ( typeof $state.params.payment_id != 'undefined' ) && ( $state.params.payment_id != 'undefined' ) && ( $state.params.payment_id != null ) )
                {

                   $scope.heading = 'Update';  
                    var payment_id = $state.params.payment_id;
                    // get details by id


                    var request = $http({
                                method: "post",
                                url: "index.php/payment/get_payment_details",
                                data: 'payment_id='+payment_id,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                            /* Successful HTTP post request or not */
                            request.success(function (response) {
                                
                                    if (response.data) {
                                        // items have value
                                        console.log(response.data);

                                        //$scope.branch_id = response.data.branch_id;
                                        $scope.payment_id = response.data.payment_id;
                                        $scope.payment_type = response.data.payment_type;
                                        

                                      
                                    } 
                            });
                    
                    $scope.createpayment = function(create_payment)
                    {                       
                        if(create_payment.$valid) {

                            var param = $( "[name='create_payment']" ).serialize();
                            
                            var request = $http({
                                method: "post",
                                url: "index.php/payment/update_payment",
                                data: param+'&payment_id='+payment_id,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });

                            request.success(function (response) {

                                if(response.status=="1")
                                {
                                    //error                                
                                    $state.go('app.payment.list');
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
                    $scope.createpayment = function(create_payment)
                    {
                        if(create_payment.$valid) {

                            var param = $( "[name='create_payment']" ).serialize();
                            console.log(param);
                                                   
                            var request = $http({
                                method: "post",
                                url: "index.php/payment/create_payment",
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
                                    $state.go('app.payment.list');
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


app.controller('ModalInstanceCtrl', ["$scope","$http","$state","payment_id","removeRow", "$modalInstance", function ($scope,$http,$state,payment_id,removeRow, $modalInstance) {

    $scope.ok = function () {
        $modalInstance.close();
        // delete product category 

        var request = $http({
                    method: "post",
                    url: "index.php/payment/payment_delete",
                    data: 'payment_id='+payment_id,
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
                                         if( comArr[i].payment_id === payment_id ) {
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

app.controller('paymentInfoCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {

        
        var payment_id = $state.params.payment_id;

      
            var request = $http({
                    method: "post",
                    url: "index.php/payment/get_payment_details",
                    data: 'payment_id='+payment_id,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                /* Successful HTTP post request or not */
                request.success(function (response) {
                                   
                    $scope.data = response.data;
                    
                });     

                $scope.updatepayment = function(payment_id) {  
                        $state.go('app.payment.update', {payment_id: payment_id});                         
                    }  
    }]);