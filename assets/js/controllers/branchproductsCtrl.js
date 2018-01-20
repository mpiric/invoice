'use strict';
/**
 * controllers for ng-table
 * Simple table with sorting and filtering on AngularJS
 */

app.controller('branchproductsCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {

    //var Elm = angular.element('input[name="product_base_price"]');

    //alert('dw');
   // $scope.example = {
   //      remember: true
   //  };


    $scope.createbranchproducts = function(create_branchproducts){

                     
            if(create_branchproducts.$valid) {

                var param = $( "[name='create_branchproducts']" ).serialize();
                //console.log(param);

                var product_id_arr = [];

                var paramArr = $( "[name='create_branchproducts']" ).serializeArray();

                //console.log(paramArr);
                angular.forEach(paramArr, function(value, key) {
                            

                            if(value.name=='product_id')
                            {
                               var product_id = (value.value);
                               //console.log(typeof product_id);
                               
                               // not allow duplicates
                               if(product_id_arr.indexOf(product_id) === -1)
                               {
                                    product_id_arr.push(product_id);
                               }
                            }

                        });

            //console.log(product_id_arr);
                                       
                var request = $http({
                    method: "post",
                    url: "index.php/branchProducts/create_branchproducts",
                    data: param+'&product_id_arr='+product_id_arr.toString(),
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                /* Successful HTTP post request or not */
                request.success(function (response) {
                    
                    //console.log(response);

                    if(response.status=="1")
                    {
                        //error             
                        alert(response.data);                   
                        $state.go('app.branchproducts.create');
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

    $scope.show_branch_dd = false;

                    // check logged in user type
                    // if its super admin then show branch drop down else send branch_id as hidden field
                        var request = $http({
                                method: "post",
                                url: "index.php/branch/getLoggedInBranchDetails",
                                data: {},
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                        request.success(function (response) {
                            //console.log(response);

                            if(response.data.branch_type=="1")
                            {
                                // show dropdown
                                $scope.show_branch_dd = true; 
                            }
                            else
                            {
                                $scope.show_branch_dd = false;
                                //$scope.branch_id = response.data.branch_id;
                            }
                             
                            $scope.branch_list = response.branch_list;                           
                            
                            // get index of current loggedin branch
                            var index = $scope.branch_list.map(function(e) { return e.branch_id; }).indexOf(response.data.branch_id);

                            $scope.branch_id = $scope.branch_list[index];
                           
			var request = $http({
                    method: "post",
                    url: "index.php/branchProducts/get_branch_products",
                    data: 'branch_id='+$scope.branch_id.branch_id,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                /* Successful HTTP post request or not */
                request.success(function (response) {
				
				//console.log(response.data);
					
					var data = response.data;
                    //console.log(data);

                    angular.forEach(data, function(value, key) {
                         
                       // value.is_available = 'is_available_'+value.product_id;
                      //  value.product_price = value.default_price;
                        value.product_price = value.product_price;

                        });



                    //  console.log(data);

                 
					$scope.tableParams = new ngTableParams({
						page: 1, // show first page
						count: 10, // count per page
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
							
							var filteredData = params.filter() ?
                            $filter('filter')(data, params.filter()) :
                            data;

                            var orderedData = params.sorting() ?
                                                $filter('orderBy')(filteredData, params.orderBy()) : filteredData;
                            var page=orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count());
                            $scope.data=page;

                            //console.log($scope.data);

        					params.total(orderedData.length);
                            $defer.resolve(page);
							
						}
					});

				});




              

                
                $scope.branchwiseProduct = function(branch_id){

                    

                    //   $scope.$watch('branchwiseProduct', function () { 
                    //     $scope.tableParams.settings().$scope = $scope; 
                    //     $scope.tableParams.reload(); 
                    // });

                    // refresh ng table data

                        $scope.tableParams.reload();
                        $scope.tableParams.page(1);
                        $scope.tableParams.sorting({});

              
                    var request = $http({
                    method: "post",
                    url: "index.php/branchProducts/get_branch_products",
                    data: 'branch_id='+$scope.branch_id.branch_id,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                    });
                    /* Successful HTTP post request or not */
                    request.success(function (response) {
                    
                    //console.log(response.data);
                    
                    var data = response.data;
                    //console.log(data);

                    angular.forEach(data, function(value, key) {
                         
                       // value.is_available = 'is_available_'+value.product_id;
                      //  value.product_price = value.default_price;
                        value.product_price = value.product_price;

                        });



                    //  console.log(data);

                 
                    $scope.tableParams = new ngTableParams({
                        page: 1, // show first page
                        count: 10, // count per page
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
                            
                            var filteredData = params.filter() ?
                            $filter('filter')(data, params.filter()) :
                            data;

                            var orderedData = params.sorting() ?
                                                $filter('orderBy')(filteredData, params.orderBy()) : filteredData;
                            var page=orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count());
                            $scope.data=page;

                            //console.log($scope.data);

                            params.total(orderedData.length);
                            $defer.resolve(page);
                            
                        }
                    });
                    $scope.tableParams.reload(); 

                });
                };
				// else data not found - error

			  });


		
	}]);