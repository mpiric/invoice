'use strict';
/**
 * controllers for ng-table
 * Simple table with sorting and filtering on AngularJS
 */

app.controller('waitercommissionCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {

			var request = $http({
                    method: "post",
                    url: "index.php/waiterCommission/waitercommission_list",
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
					          name: 'asc' // initial sorting
					        },
					    filter: {
				             name: '' // initial filter
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
					$scope.updatewaitercommission = function(waiter_commission_id) {   

						$state.go('app.waitercommission.update', {waiter_commission_id: waiter_commission_id});			                
		            }

				});
				// else data not found - error

			  $scope.open = function (size,waiter_commission_id) {

                    var modalInstance = $modal.open({
                        templateUrl: 'myModalContent.html',
                        controller: 'ModalInstanceCtrl',
                        size: size,
                        resolve: {
                            waiter_commission_id: function () {
                                return waiter_commission_id;
                            }
                            ,
                            removeRow: function()
                            {					
            					return $scope.tableParams.data;
                            }
                        }
                    });
            };

            $scope.infoWaitercommission= function(waiter_commission_id)
            {
                $state.go('app.waitercommission.info', {waiter_commission_id: waiter_commission_id}); 
            }

				
	}]);


app.controller('waitercommissionCreateCtrl', ["$scope", "$http", "$state", function ($scope, $http, $state) {

    //$scope.show_product_category_dd = false;

                // var order_type_list = [{key:"1", value:"Table Order"},{key:"2", value:"Home Delivery"},{key:"3", value:"Parcel"}];
                // $scope.order_type_list = order_type_list;
                //console.log($scope.order_type_list);
                

                var request = $http({
                                method: "post",
                                url: "index.php/order/order_list_for_dd",
                                data: {},
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                        request.success(function (response) {
                            //console.log(response);
                             
                            $scope.order_list = response.data;                            

                            });  

                var request = $http({
                                method: "post",
                                url: "index.php/productCategory/getProductCatList",
                                data: {},
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                        request.success(function (response) {
                            //console.log(response);
                             
                            $scope.product_cat_list = response.product_cat_list;                            

                            });  


                var request = $http({
                                method: "post",
                                url: "index.php/waiter/getWaiterList",
                                data: {},
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                        request.success(function (response) {
                            //console.log(response);
                             
                            $scope.waiter_list = response.waiter_list;   


                            //}); 
               

			if( ( $state.params.waiter_commission_id!="" ) && ( typeof $state.params.waiter_commission_id != 'undefined' ) && ( $state.params.waiter_commission_id != 'undefined' ) && ( $state.params.waiter_commission_id != null ) )
                {
                    var waiter_commission_id = $state.params.waiter_commission_id;
                    // get details by id

                    var request = $http({
                                method: "post",
                                url: "index.php/waiterCommission/get_waitercommission_details",
                                data: 'waiter_commission_id='+waiter_commission_id,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                            /* Successful HTTP post request or not */
                            request.success(function (response) {
                            	
                            		if (response.data) {
									    // items have value
									    console.log(response.data);
                                        //console.log($scope.waiter_list);

                                        $scope.waiter_id = response.data.waiter_id;
                                        $scope.product_category_id = response.data.product_category_id;
                                        $scope.order_id = response.data.order_id;
									    $scope.commission_date = response.data.commission_date;
									    $scope.commission_amount = response.data.commission_amount;
                                        $scope.product_qty = response.data.product_qty;

                                        var index = $scope.waiter_list.map(function(e) { return e.waiter_id; }).indexOf(response.data.waiter_id);

                                        $scope.waiter_id = $scope.waiter_list[index]; 


                                        var index1 = $scope.product_cat_list.map(function(e) { return e.product_category_id; }).indexOf(response.data.product_category_id);

                                        $scope.product_category_id = $scope.product_cat_list[index1];


                                        var index2 = $scope.order_list.map(function(e) { return e.order_id; }).indexOf(response.data.order_id);

                                        $scope.order_id = $scope.order_list[index2];
                                        //console.log($scope.order_type_list);
                                        //console.log(response.data.order_type);
                                        //console.log(index2); 
									  
									}
                            });
					
					$scope.createwaitercommission = function(create_waitercommission)
					{						
						if(create_waitercommission.$valid) {

							var param = $( "[name='create_waitercommission']" ).serialize();
							
							var request = $http({
                                method: "post",
                                url: "index.php/waiterCommission/update_waitercommission",
                                data: param+'&waiter_commission_id='+waiter_commission_id,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });

                            request.success(function (response) {

                                if(response.status=="1")
                                {
                                    //error                                
                                    $state.go('app.waitercommission.list');
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
                    $scope.createwaitercommission = function(create_waitercommission)
                    {
                        if(create_waitercommission.$valid) {

                            var param = $( "[name='create_waitercommission']" ).serialize();
                            //console.log(param);
                                                   
                            var request = $http({
                                method: "post",
                                url: "index.php/waiterCommission/create_waitercommission",
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
                                    $state.go('app.waitercommission.list');
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


app.controller('ModalInstanceCtrl', ["$scope","$http","$state","waiter_commission_id","removeRow", "$modalInstance", function ($scope,$http,$state,waiter_commission_id,removeRow, $modalInstance) {

    $scope.ok = function () {
        $modalInstance.close();
        // delete waiter 

        var request = $http({
                    method: "post",
                    url: "index.php/waitercommission/waitercommission_delete",
                    data: 'waiter_commission_id='+waiter_commission_id,
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
                                         if( comArr[i].waiter_commission_id === waiter_commission_id ) {
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

app.controller('waitercommissionInfoCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {

        
        var waiter_commission_id = $state.params.waiter_commission_id;

      
            var request = $http({
                    method: "post",
                    url: "index.php/waitercommission/get_waitercommission_details",
                    data: 'waiter_commission_id='+waiter_commission_id,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                /* Successful HTTP post request or not */
                request.success(function (response) {
                                   
                    $scope.data = response.data;
                    
                });     

                $scope.updatewaitercommission = function(waiter_commission_id) {  
                        $state.go('app.waitercommission.update', {waiter_commission_id: waiter_commission_id});                         
                    }  
    }]);