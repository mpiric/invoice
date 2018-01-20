'use strict';
/**
 * controllers for ng-table
 * Simple table with sorting and filtering on AngularJS
 */

app.filter('propsFilter', function () {
    return function (items, props) {
        var out = [];

        if (angular.isArray(items)) {
            items.forEach(function (item) {
                var itemMatches = false;

                var keys = Object.keys(props);
                for (var i = 0; i < keys.length; i++) {
                    var prop = keys[i];
                    var text = props[prop].toLowerCase();
                    if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
                        itemMatches = true;
                        break;
                    }
                }

                if (itemMatches) {
                    out.push(item);
                }
            });
        } else {
            // Let the output be the input untouched
            out = items;
        }

        return out;
    };
});

app.directive('fileModel', ['$parse', function ($parse) {
    return {
    restrict: 'A',
    link: function(scope, element, attrs) {
        var model = $parse(attrs.fileModel);
        var modelSetter = model.assign;

        element.bind('change', function(){
            scope.$apply(function(){
                modelSetter(scope, element[0].files[0]);
            });
        });
    }
   };
}]);


app.controller('productCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {


			var request = $http({
                    method: "post",
                    url: "index.php/product/product_list",
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

        					$scope.updateProduct = function(product_id) {   

        						$state.go('app.product.update', {product_id: product_id});			                
        		            }

				});
				// else data not found - error

			  $scope.open = function (size,product_id) {

                    var modalInstance = $modal.open({
                        templateUrl: 'myModalContent.html',
                        controller: 'ModalInstanceCtrl',
                        size: size,
                        resolve: {
                            product_id: function () {
                                return product_id;
                            }
                            ,
                            removeRow: function()
                            {					
            					return $scope.tableParams.data;
                            }
                        }
                    });
            };

            $scope.infoProduct = function(product_id)
            {
                $state.go('app.product.info', {product_id: product_id}); 
            }

				
	}]);


app.controller('productCreateCtrl', ["$scope", "$http", "$state", function ($scope, $http, $state) {

    $scope.show_branch_dd = false;
 
   $scope.branch_list = [];

   $scope.heading = 'Create';  


    $scope.checkName = function(name)
    {
        $scope.checkN = false;

        var request = $http({
              method: "post",
              url: "index.php/product/check_name",
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
              url: "index.php/product/check_code",
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
                            
    
                 var unit_list = [{key:"0", value:"none"},{key:"1", value:"kg"},{key:"2", value:"gm"},{key:"3", value:"lt"},{key:"4", value:"ml"}];
                 $scope.unit_list = unit_list;
                // console.log($scope.unit_list);


                var request = $http({
                                method: "post",
                                url: "index.php/branch/getLoggedInBranchDetails",
                                data: {},
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                        request.success(function (response) {


                             
                            $scope.branch_list = response.branch_list;                           
                            
                            // get index of current loggedin branch
                            var index = $scope.branch_list.map(function(e) { return e.branch_id; }).indexOf(response.data.branch_id);

                             // make the current branch selected in dd
                            // $scope.branch_id = $scope.branch_list[index];  

                             $scope.selectedBranch = [$scope.branch_list[index]];     

           
                    // check logged in user type
                    // if its super admin then show branch drop down else send product_category_id as hidden field
                        var request = $http({
                                method: "post",
                                url: "index.php/productCategory/getProductCatList",
                                data: {},
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                        request.success(function (response) {
                            //console.log(response);
                             
                            $scope.product_cat_list = response.product_cat_list;                            

                          


			if( ( $state.params.product_id!="" ) && ( typeof $state.params.product_id != 'undefined' ) && ( $state.params.product_id != 'undefined' ) && ( $state.params.product_id != null ) )
                {
                    $scope.heading = 'Update';
                    
                    var product_id = $state.params.product_id;
                    // get details by id

                    var request = $http({
                                method: "post",
                                url: "index.php/product/get_product_details",
                                data: 'product_id='+product_id,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                            /* Successful HTTP post request or not */
                            request.success(function (response) {
                            	
                            		if (response.data) {
									    // items have value
									    //console.log(response.data);
									    $scope.name = response.data.product_name;
									    //$scope.unit = response.data.unit;									    
									    $scope.image_path = "uploads/products/"+(response.data.image);

                                        $scope.description = response.data.description;
									    $scope.price = response.data.price;
                                        $scope.product_code = response.data.product_code;
									    
                                        var index1 = $scope.product_cat_list.map(function(e) { return e.product_category_id; }).indexOf(response.data.product_category_id);

                                        $scope.product_category_id = $scope.product_cat_list[index1];

                                        var index2 = $scope.unit_list.map(function(e) { return e.key; }).indexOf(response.data.unit);

                                        $scope.unit   = $scope.unit_list[index2];
									    
									}
                            });
					
					$scope.createProduct = function(create_product)
					{						
						if(create_product.$valid) {



                            var param = $( "[name='create_product']" ).serialize();

                            

                            //console.log($scope.create_product.selectedBranch.$modelValue);

                            var selectedBranchIdArr = $scope.create_product.selectedBranch.$modelValue;

                            var branch_id_csv = '';
                            angular.forEach(selectedBranchIdArr, function(value, key) {
                                 
                                     if(value.branch_id!='' && key!=0)
                                     {
                                        branch_id_csv += ',';
                                     }
                                     branch_id_csv += value.branch_id;                                      
                                });
                            //console.log(branch_id_csv);
                         
                            //return false;
                                
                                var file = $scope.image;
                               // console.log('file is ' );
                                //console.dir(file);

                                var uploadUrl = "index.php/product/update_product";
                               
                                var fd = new FormData();
                                 fd.append('image', file);
                                 fd.append('params', param);
                                 fd.append('branch_id_csv', branch_id_csv);
                                 fd.append('product_id', product_id);
                                
                                 $http.post(uploadUrl, fd, {
                                     transformRequest: angular.identity,
                                     headers: {'Content-Type': undefined,'Process-Data': false}
                                 })
                                 .success(function(response){
                                        console.log(response);

                                        if(response.status=="1")
                                        {
                                            //error                                
                                            $state.go('app.product.list');
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

							// var param = $( "[name='create_product']" ).serialize();
							
							// var request = $http({
                               //                          method: "post",
                               //                          url: "index.php/product/update_product",
                               //                          data: param+'&product_id='+product_id,
                               //                          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                               //                      });

                               //                      request.success(function (response) {

                               //                          if(response.status=="1")
                               //                          {
                               //                              //error                                
                               //                              $state.go('app.product.list');
                               //                          }
                               //                          else if(response.status=="-1")
                               //                          {
                               //                             // validation error;
                               //                             $('#validation_err').html(response.data);
                               //                          }
                               //                          else
                               //                          {
                               //                              alert(response.data);                                
                               //                          }
                               //                      });
						}
					}
	
                }
                else
                {
                    $scope.show_branch_dd = true;

                    $scope.createProduct = function(create_product)
                    {

                        if(create_product.$valid) {

                            var param = $( "[name='create_product']" ).serialize();

                            

                            //console.log($scope.create_product.selectedBranch.$modelValue);

                            var selectedBranchIdArr = $scope.create_product.selectedBranch.$modelValue;

                            var branch_id_csv = '';
                            angular.forEach(selectedBranchIdArr, function(value, key) {
                                 
                                     if(value.branch_id!='' && key!=0)
                                     {
                                        branch_id_csv += ',';
                                     }
                                     branch_id_csv += value.branch_id;                                      
                                });
                            //console.log(branch_id_csv);
                         
                            //return false;
                                
                                var file = $scope.image;
                               // console.log('file is ' );
                                //console.dir(file);

                                var uploadUrl = "index.php/product/create_product";
                               
                                var fd = new FormData();
                                 fd.append('image', file);
                                 fd.append('params', param);
                                 fd.append('branch_id_csv', branch_id_csv);
                                
                                 $http.post(uploadUrl, fd, {
                                     transformRequest: angular.identity,
                                     headers: {'Content-Type': undefined,'Process-Data': false}
                                 })
                                 .success(function(response){
                                        //console.log(response);

                                        if(response.status=="1")
                                        {
                                            //error                                
                                            $state.go('app.product.list');
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
                                 // .error(function(){
                                 //    console.log("Error");
                                 // });

                           
                            // return false;
                                                   
                            // var request = $http({
                            //     method: "post",
                            //     url: "index.php/product/create_product",
                            //     data: param,
                            //     headers: { 'Content-Type': 'application/x-www-form-urlencoded;' }
                            // });
                            // /* Successful HTTP post request or not */
                            // request.success(function (response) {

                            //     //alert(data.status);
                            //     console.log(response);

                            //     if(response.status=="1")
                            //     {
                            //         //error                                
                            //         $state.go('app.product.list');
                            //     }
                            //     else if(response.status=="-1")
                            //     {
                            //        // validation error;
                            //        $('#validation_err').html(response.data);
                            //     }
                            //     else
                            //     {
                            //         alert(response.data);                                
                            //     }
                            // });

                         }
                    }
                }

                });     

            });


	}]);

app.controller('ModalInstanceCtrl', ["$scope","$http","$state","product_id","removeRow", "$modalInstance", function ($scope,$http,$state,product_id,removeRow, $modalInstance) {

    $scope.ok = function () {
        $modalInstance.close();
        // delete product 

        var request = $http({
                    method: "post",
                    url: "index.php/product/product_delete",
                    data: 'product_id='+product_id,
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
						                 if( comArr[i].product_id === product_id ) {
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

app.controller('productInfoCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {

		
        var product_id = $state.params.product_id;

      
            var request = $http({
                    method: "post",
                    url: "index.php/product/get_product_details",
                    data: 'product_id='+product_id,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                /* Successful HTTP post request or not */
                request.success(function (response) {
                                   
                    $scope.data = response.data;
                    
                });     

                $scope.updateProduct = function(product_id) {  
                        $state.go('app.product.update', {product_id: product_id});                         
                    }  
    }]);