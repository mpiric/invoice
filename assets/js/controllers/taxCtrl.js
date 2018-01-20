'use strict';
/**
 * controllers for ng-table
 * Simple table with sorting and filtering on AngularJS
 */

app.controller('taxCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {

			var request = $http({
                    method: "post",
                    url: "index.php/tax/tax_list",
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
					$scope.updateTax = function(tax_master_id) {   

						$state.go('app.tax.update', {tax_master_id: tax_master_id});			                
		            }

				});
				// else data not found - error

			  $scope.open = function (size,tax_master_id) {

                    var modalInstance = $modal.open({
                        templateUrl: 'myModalContent.html',
                        controller: 'ModalInstanceCtrl',
                        size: size,
                        resolve: {
                            tax_master_id: function () {
                                return tax_master_id;
                            }
                            ,
                            removeRow: function()
                            {					
            					return $scope.tableParams.data;
                            }
                        }
                    });
            };

            $scope.infoTax = function(tax_master_id)
            {
                $state.go('app.tax.info', {tax_master_id: tax_master_id}); 
            }

				
	}]);


app.controller('taxCreateCtrl', ["$scope", "$http", "$state", function ($scope, $http, $state) {

    $scope.heading = 'Create';  

    //$scope.show_product_category_dd = false;

    // $scope.firstReset = function(create_tax){
    //     $scope.create_tax = {};
    // };


    $scope.selectedType = function(){

                    console.log($scope.tax_type);

                    $scope.tax_type_product_specific = false;
                    $scope.tax_type_branch_specific = false;

                    $scope.selected_tax_type= $scope.tax_type.key;

                    if($scope.selected_tax_type=="1")
                    {
                        $scope.tax_type_product_specific = true;
                        $scope.tax_type_branch_specific = false;
                    }
                    else
                    {
                        $scope.tax_type_product_specific = false;
                        $scope.tax_type_branch_specific = true;
                    }

                    
                };

    $scope.viewtax = function(branch_id){

                    //console.log(branch_id);
                    
                    //var branch_id = branch_id_obj.branch_id;

                    var request = $http({

                        method: "post",
                        url: "index.php/tax/branch_wise_tax",
                        data: 'branch_id='+branch_id,
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }

                    });
                    request.success(function (response) {

                                if(response.status=="1")
                                {
                                    $scope.taxlist = response.data;
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

                var order_type_list = [{key:"1", value:"Table Order"},{key:"2", value:"Home Delivery"},{key:"3", value:"Parcel"}];
                $scope.order_type_list = order_type_list;
                //console.log($scope.order_type_list);

                var tax_type_list = [{key:"1", value:"Product Specific"},{key:"2", value:"Branch Specific"}];
                $scope.tax_type_list = tax_type_list;
                //console.log($scope.order_type_list);

                


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
                                url: "index.php/taxmain/gettaxmainList",
                                data: {},
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                        request.success(function (response) {
                                //console.log(response);
                             
                            $scope.tax_main_list = response.tax_main_list;                            

                            }); 


                var request = $http({
                                method: "post",
                                url: "index.php/taxmain/gettaxmainListbybranch",
                                data: {},
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                        request.success(function (response) {
                                //console.log(response);
                             
                            $scope.tax_main_list_by_branch = response.tax_main_list_by_branch;                            

                            }); 


                var request = $http({
                                method: "post",
                                url: "index.php/branch/getLoggedInBranchDetails",
                                data: {},
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                        request.success(function (response) {
                            //console.log(response);

                            $scope.branch_list = response.branch_list;                           
                            
                            // get index of current loggedin branch
                            var index = $scope.branch_list.map(function(e) { return e.branch_id; }).indexOf(response.data.branch_id);

                             // make the current branch selected in dd
                             $scope.branch_id = $scope.branch_list[index]; 

			if( ( $state.params.tax_master_id!="" ) && ( typeof $state.params.tax_master_id != 'undefined' ) && ( $state.params.tax_master_id != 'undefined' ) && ( $state.params.tax_master_id != null ) )
                {

                   $scope.heading = 'Update';  
                    var tax_master_id = $state.params.tax_master_id;
                    // get details by id

                    var request = $http({
                                method: "post",
                                url: "index.php/tax/get_tax_details",
                                data: 'tax_master_id='+tax_master_id,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                            /* Successful HTTP post request or not */
                            request.success(function (response) {
                            	
                            		if (response.data) {
									    // items have value
									    console.log(response.data);

                                        $scope.branch_id = response.data.branch_id;
                                        $scope.product_category_id = response.data.product_category_id;
                                        $scope.order_type = response.data.order_type;
									    $scope.tax_id = response.data.tax_id;
                                        $scope.branch_tax_id = response.data.branch_tax_id;
									    $scope.tax_percent = response.data.tax_percent;

                                        var index = $scope.branch_list.map(function(e) { return e.branch_id; }).indexOf(response.data.branch_id);

                                        $scope.branch_id = $scope.branch_list[index]; 

                                        $scope.viewtax($scope.branch_id);
                                        var index1 = $scope.product_cat_list.map(function(e) { return e.product_category_id; }).indexOf(response.data.product_category_id);

                                        $scope.product_category_id = $scope.product_cat_list[index1];

                                        var index2 = $scope.order_type_list.map(function(e) { return e.key; }).indexOf(response.data.order_type);

                                        $scope.order_type = $scope.order_type_list[index2];

                                        var index3 = $scope.tax_main_list.map(function(e) { return e.key; }).indexOf(response.data.tax_id);

                                        $scope.tax_id = $scope.tax_main_list[index3];

                                        var index4 = $scope.tax_main_list_by_branch.map(function(e) { return e.key; }).indexOf(response.data.branch_tax_id);

                                        $scope.branch_tax_id = $scope.tax_main_list_by_branch[index4];

                                        var index5 = $scope.tax_type_list.map(function(e) { return e.key; }).indexOf(response.data.tax_type);

                                        $scope.tax_type = $scope.tax_type_list[index5];

                                        
                                        //console.log($scope.tax_main_list);
                                        //console.log(response.data.order_type);
                                        //console.log(index3); 
									  
									} 
                            });
					
					$scope.createTax = function(create_tax)
					{						
						if(create_tax.$valid) {

							var param = $( "[name='create_tax']" ).serialize();
							
							var request = $http({
                                method: "post",
                                url: "index.php/tax/update_tax",
                                data: param+'&tax_master_id='+tax_master_id,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });

                            request.success(function (response) {

                                if(response.status=="1")
                                {
                                    //error                                
                                    $state.go('app.tax.list');
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
                    $scope.createTax = function(create_tax)
                    {
                        if(create_tax.$valid) {

                            var param = $( "[name='create_tax']" ).serialize();
                            //console.log(param);
                                                   
                            var request = $http({
                                method: "post",
                                url: "index.php/tax/create_tax",
                                data: param,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                            /* Successful HTTP post request or not */
                            request.success(function (response) {

                                
                                //console.log(response);

                                if(response.status=="1")
                                {
                                    //error             
                                    alert(response.data); 

 

                                       
                                    $state.go('app.tax.create');
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


app.controller('ModalInstanceCtrl', ["$scope","$http","$state","tax_master_id","removeRow", "$modalInstance", function ($scope,$http,$state,tax_master_id,removeRow, $modalInstance) {

    $scope.ok = function () {
        $modalInstance.close();
        // delete waiter 

        var request = $http({
                    method: "post",
                    url: "index.php/tax/tax_delete",
                    data: 'tax_master_id='+tax_master_id,
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
                                         if( comArr[i].tax_master_id === tax_master_id ) {
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

app.controller('taxInfoCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {

        
        var tax_master_id = $state.params.tax_master_id;

      
            var request = $http({
                    method: "post",
                    url: "index.php/tax/get_tax_details_info",
                    data: 'tax_master_id='+tax_master_id,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                /* Successful HTTP post request or not */
                request.success(function (response) {
                                   
                    $scope.data = response.data;
                    
                });     

                $scope.updateTax = function(tax_master_id) {  
                        $state.go('app.tax.update', {tax_master_id: tax_master_id});                         
                    }  
    }]);