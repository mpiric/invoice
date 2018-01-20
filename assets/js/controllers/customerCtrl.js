'use strict';
/**
 * controllers for ng-table
 * Simple table with sorting and filtering on AngularJS
 */

app.controller('customerCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {

			var request = $http({
                    method: "post",
                    url: "index.php/customer/customer_list",
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
					$scope.updatecustomer = function(customer_id) {   

						$state.go('app.customer.update', {customer_id: customer_id});			                
		            }

				});
				// else data not found - error

			  $scope.open = function (size,customer_id) {

                    var modalInstance = $modal.open({
                        templateUrl: 'myModalContent.html',
                        controller: 'ModalInstanceCtrl',
                        size: size,
                        resolve: {
                            customer_id: function () {
                                return customer_id;
                            }
                            ,
                            removeRow: function()
                            {					
            					return $scope.tableParams.data;
                            }
                        }
                    });
            };

            $scope.infoWaitercommission= function(customer_id)
            {
                $state.go('app.customer.info', {customer_id: customer_id}); 
            }

				
	}]);


app.controller('customerCreateCtrl', ["$scope", "$http", "$state", function ($scope, $http, $state) {

    //$scope.show_product_category_dd = false;
    $scope.heading = 'Create';  
                

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

                var order_type_list = [{key:"1", value:"Table Order"},{key:"2", value:"Home Delivery"},{key:"3", value:"Parcel"}];
                $scope.order_type_list = order_type_list;


                            //}); 
               

			if( ( $state.params.customer_id!="" ) && ( typeof $state.params.customer_id != 'undefined' ) && ( $state.params.customer_id != 'undefined' ) && ( $state.params.customer_id != null ) )
                {

                    $scope.heading = 'Update';  
                    var customer_id = $state.params.customer_id;
                    // get details by id

                    var request = $http({
                                method: "post",
                                url: "index.php/customer/get_customer_details",
                                data: 'customer_id='+customer_id,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                            /* Successful HTTP post request or not */
                            request.success(function (response) {
                            	
                            		if (response.data) {
									    // items have value
									    console.log(response.data);
                                        //console.log($scope.waiter_list);

                                        
                                        $scope.order_id = response.data.order_id;
                                        $scope.order_type = response.data.order_type;
									    $scope.firstname = response.data.firstname;
									    $scope.lastname = response.data.lastname;
                                        $scope.contact = response.data.contact;
                                        $scope.email = response.data.email;

                                        

                                        var index1 = $scope.order_type_list.map(function(e) { return e.key; }).indexOf(response.data.order_type);

                                        $scope.order_type = $scope.order_type_list[index1];


                                        var index2 = $scope.order_list.map(function(e) { return e.order_id; }).indexOf(response.data.order_id);

                                        $scope.order_id = $scope.order_list[index2];
                                        //console.log($scope.order_type_list);
                                        //console.log(response.data.order_type);
                                        //console.log(index2); 
									  
									}
                            });
					
					$scope.createCustomer = function(create_customer)
					{						
						if(create_customer.$valid) {

							var param = $( "[name='create_customer']" ).serialize();
							
							var request = $http({
                                method: "post",
                                url: "index.php/customer/update_customer",
                                data: param+'&customer_id='+customer_id,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });

                            request.success(function (response) {

                                if(response.status=="1")
                                {
                                    //error                                
                                    $state.go('app.customer.list');
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
                    $scope.createCustomer = function(create_customer)
                    {
                        if(create_customer.$valid) {

                            var param = $( "[name='create_customer']" ).serialize();
                            //console.log(param);
                                                   
                            var request = $http({
                                method: "post",
                                url: "index.php/customer/create_customer",
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
                                    $state.go('app.customer.list');
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


app.controller('ModalInstanceCtrl', ["$scope","$http","$state","customer_id","removeRow", "$modalInstance", function ($scope,$http,$state,customer_id,removeRow, $modalInstance) {

    $scope.ok = function () {
        $modalInstance.close();
        // delete waiter 

        var request = $http({
                    method: "post",
                    url: "index.php/customer/customer_delete",
                    data: 'customer_id='+customer_id,
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
                                         if( comArr[i].customer_id === customer_id ) {
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

app.controller('customerInfoCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {

        
        var customer_id = $state.params.customer_id;

      
            var request = $http({
                    method: "post",
                    url: "index.php/customer/get_customer_details",
                    data: 'customer_id='+customer_id,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                /* Successful HTTP post request or not */
                request.success(function (response) {
                                   
                    $scope.data = response.data;
                    
                });     

                $scope.updatecustomer = function(customer_id) {  
                        $state.go('app.customer.update', {customer_id: customer_id});                         
                    }  
    }]);