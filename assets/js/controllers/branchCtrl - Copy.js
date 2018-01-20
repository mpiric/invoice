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


app.controller('branchCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {

			var request = $http({
                    method: "post",
                    url: "index.php/branch/branch_list",
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
					$scope.updateBranch = function(branch_id) {   

						$state.go('app.branch.update', {branch_id: branch_id});			                
		            }

				});
				// else data not found - error

			  $scope.open = function (size,branch_id) {

                    var modalInstance = $modal.open({
                        templateUrl: 'myModalContent.html',
                        controller: 'ModalInstanceCtrl',
                        size: size,
                        resolve: {
                            branch_id: function () {
                                return branch_id;
                            }
                            ,
                            removeRow: function()
                            {					
            					return $scope.tableParams.data;
                            }
                        }
                    });
            };

            $scope.infoBranch = function(branch_id)
            {
                $state.go('app.branch.info', {branch_id: branch_id}); 
            }

				
	}]);


app.controller('branchCreateCtrl', ["$scope", "$http", "$state", function ($scope, $http, $state) {

    $scope.heading = 'Create';

            var is_active_list = [{key:"1", value:"Active"},{key:"0", value:"Inactive"}];
                $scope.is_active_list = is_active_list;

            $scope.selected = {};
                
            $scope.brand_list = [];



            var request = $http({
                                method: "post",
                                url: "index.php/brand/getbrandList",
                                data: {},
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                        request.success(function (response) {
                             
                            $scope.brand_list = response.brand_list;  

                            
                            //console.log(response.brand_list);
                           
                            // var index = $scope.brand_list.map(function(e) { return e.brand_id; }).indexOf(response.data.brand_id);

                            //  // make the current branch selected in dd
                            // $scope.branch_id = $scope.branch_list[index];  

                            //  $scope.selectedBrand = [$scope.brand_list[index]]; 

                             // find which is brand is associated with current branch

                            // var request = $http({
                            //     method: "post",
                            //     url: "index.php/branch/getLoggedInBranchDetails",
                            //     data: {},
                            //     headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            // });
                            // request.success(function (response) {
                                

                            //     var index = $scope.brand_list.map(function(e) { return e.brand_id; }).indexOf(response.data.brand_id);

                            //     $scope.selectedBrand = $scope.brand_list[index]; 
                            // });

                         });


			if( ( $state.params.branch_id!="" ) && ( typeof $state.params.branch_id != 'undefined' ) && ( $state.params.branch_id != 'undefined' ) && ( $state.params.branch_id != null ) )
                {



                    $scope.heading = 'Update';
                    
                    $scope.is_create = false;
                    var branch_id = $state.params.branch_id;
                    // get details by id

                    var request = $http({
                        
                                method: "post",
                                url: "index.php/branch/get_branch_details",
                                data: 'branch_id='+branch_id,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                            /* Successful HTTP post request or not */
                            request.success(function (response) {
                            	
                            		if (response.data) {
									    // items have value
									    //console.log(response.data);
									    $scope.name = response.data.name;
									    $scope.username = response.data.username;
									    $scope.password = response.data.password;
									    $scope.contact = response.data.contact;
									    $scope.email = response.data.email;
									    $scope.address = response.data.address;
									    $scope.pincode = response.data.pincode;
									    $scope.country = response.data.country_id;
                                        $scope.no_of_tables = response.data.no_of_tables;

										
										ajax_call('ajaxCall',{location_id:response.data.country_id,location_type:1}, 'state');
									    $scope.state = response.data.state_id;
																				
										ajax_call('ajaxCall',{location_id:response.data.state_id,location_type:2}, 'city');
									    $scope.city = response.data.city_id;
										
									    $scope.contact_person_name = response.data.contact_person_name;
									    $scope.contact_person_phone = response.data.contact_person_phone;
                                        $scope.service_tax_number = response.data.service_tax_number;
                                        $scope.other_number = response.data.other_number;

                                      
                                        //  var index1 = $scope.brand_list.map(function(e) { return e.brand_id; }).indexOf(response.data.brand_id);

                                        // $scope.brand_id = $scope.brand_list[index1];

                                         console.log(response.data.brand_id);
                                         
                                        if( ( response.data.brand_id!="" ) && ( typeof response.data.brand_id != 'undefined' ) && ( response.data.brand_id != 'undefined' ) && ( response.data.brand_id != null ) )
                                        {


                                            // var selectedBrandIdArr = response.data.brand_id;
                                            // console.log(selectedBrandIdArr);

                                            // var myarray = selectedBrandIdArr.split(','); 

                                            // var selectedBrandIdArray = [];

                                            // for(var i = 0; i < myarray.length; i++) 
                                            // { 
                                            //     console.log(myarray[i]);

                                            //     var index = $scope.brand_list.map(function(e) { return e.brand_id; }).indexOf(myarray[i]);

                                            //     selectedBrandIdArray.push($scope.brand_list[index]);

                                            // }


                                            // $scope.selected.selectedBrand = selectedBrandIdArray;

                                            //////////////////////

                                            var selectedBrandIdArr = response.data.brand_id;
                                           console.log(selectedBrandIdArr);

                                           var myarray = selectedBrandIdArr.split(',');

                                           var selectedBrandIdArray = [];

                                           for(var i = 0; i < myarray.length; i++)
                                           {
                                               //console.log(myarray[i]);

                                               var index = $scope.brand_list.map(function(e) { return e.brand_id; }).indexOf(myarray[i]);

                                               selectedBrandIdArray.push($scope.brand_list[index]);

                                           }


                                           $scope.selected.selectedBrand = selectedBrandIdArray;

                                            

                                            // angular.forEach(selectedBrandIdArr, function(value, key) {

                                            //     // angular.forEach(selectedBrandIdArr, function(value, key) {
                                            //     var index = $scope.brand_list.map(function(e) { return e.brand_id; }).indexOf(response.data.brand_id);

                                            //     var index = $scope.brand_list.map(function(e) { return e.brand_id; });

                                            //     console.log(index);

                                            //     $scope.brand_id = [$scope.brand_list[index]];
                                            //     //console.log($scope.brand_id);
                                            // });
                                    
                                            // }); 
                                                                              
                                        } 

                                        
                                        // console.log('response.data.brand_id='+response.data.brand_id);
                                         
                                        //  var index = $scope.brand_list.map(function(e) { return e.brand_id; }).indexOf(response.data.brand_id);        
                                        // //console.log('index='+index);
                                        // $scope.selectedBrand = [$scope.brand_list[index]]; 
                                        
                                        //console.log($scope.brand_id);

                                        var index2 = $scope.is_active_list.map(function(e) { return e.key; }).indexOf(response.data.is_active);

                                        $scope.is_active = $scope.is_active_list[index2];
									} 
                            });
					
					$scope.createBranch = function(create_branch)
					{						
						if(create_branch.$valid) {

							var param = $( "[name='create_branch']" ).serialize();

                            var selectedBrandIdArr = $scope.create_branch.selectedBrand.$modelValue;

                            var brand_id_csv = '';

                            angular.forEach(selectedBrandIdArr, function(value, key) {
                                 
                                     if(value.brand_id!='' && key!=0)
                                     {
                                        brand_id_csv += ',';
                                     }
                                     brand_id_csv += value.brand_id;                                      
                                });
							
							var request = $http({
                                method: "post",
                                url: "index.php/branch/update_branch",
                                data: param+'&branch_id='+branch_id+'&brand_id='+brand_id_csv,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                                request.success(function (response) {

                                    //console.log(response.data);

                                if(response.status=="1")
                                {
                                    //error                                
                                    $state.go('app.branch.list');
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
                    $scope.createBranch = function(create_branch)
                    {
                        if(create_branch.$valid) {

                            var param = $( "[name='create_branch']" ).serialize();
                             console.log(param);
                            // return false;

                            var selectedBrandIdArr = $scope.create_branch.selectedBrand.$modelValue;

                            //console.log(selectedBrandIdArr);

                            

                            var brand_id_csv = '';
                            angular.forEach(selectedBrandIdArr, function(value, key) {
                                 
                                     if(value.brand_id!='' && key!=0)
                                     {
                                        brand_id_csv += ',';
                                     }
                                     brand_id_csv += value.brand_id;                                      
                                });
                            console.log(brand_id_csv);
                         
                            //return false;
                                
                                //var file = $scope.image;
                               // console.log('file is ' );
                                //console.dir(file);

                                //var uploadUrl = "index.php/product/create_product";
                               
                               //$scope.selectedBrand = response.data.brand_id;

                                                   
                            var request = $http({
                                method: "post",
                                url: "index.php/branch/create_branch",
                                data: param+'&brand_id='+brand_id_csv,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });


                            /* Successful HTTP post request or not */
                            request.success(function (response) {

                                //alert(data.status);
                                console.log(response);

                                if(response.status=="1")
                                {
                                    //error                                
                                    $state.go('app.branch.list');
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


	}]);

// app.controller('branchDeleteCtrl', ["$scope", "$modal", function ($scope, $modal) {

//     $scope.open = function (size,branch_id) {

//         var modalInstance = $modal.open({
//             templateUrl: 'myModalContent.html',
//             controller: 'ModalInstanceCtrl',
//             size: size,
//             resolve: {
//                 branch_id: function () {
//                     return branch_id;
//                 }
//                 ,
//                 removeRow: function()
//                 {
// 					 var index = -1;
// 						           var comArr = eval( $scope.row );
// 						           for( var i = 0; i < comArr.length; i++ ) {
// 						                 if( comArr[i].branch_id === branch_id ) {
// 						                     index = i;
// 						                     break;
// 						                  }
// 						           }
// 						           if( index === -1 ) {
// 						                alert( "Something gone wrong" );
// 						           }
// 						          $scope.companies.splice( index, 1 );
// 					return 'func';
//                 }
//             }
//         });
//     };

// }]);

app.controller('ModalInstanceCtrl', ["$scope","$http","$state","branch_id","removeRow", "$modalInstance", function ($scope,$http,$state,branch_id,removeRow, $modalInstance) {

	//console.log(removeRow);
	
    $scope.ok = function () {
        $modalInstance.close();
        // delete branch 

        var request = $http({
                    method: "post",
                    url: "index.php/branch/branch_delete",
                    data: 'branch_id='+branch_id,
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
						                 if( comArr[i].branch_id === branch_id ) {
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

app.controller('branchInfoCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {

		$scope.success = false;
		$scope.error = false;

        var branch_id = $state.params.branch_id;

            var request = $http({
                    method: "post",
                    url: "index.php/branch/get_branch_details",
                    data: 'branch_id='+branch_id,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                /* Successful HTTP post request or not */
                request.success(function (response) {
                                   
                    $scope.data = response.data;
                    // console.log(response.data);

                });     

                $scope.updateBranch = function(branch_id) {  
                        $state.go('app.branch.update', {branch_id: branch_id});                         
                    }  

        $scope.updatePasswordDiv = false;

            $scope.updatePassword = function(branch_id) { 
                        //$scope.updatePasswordDiv = true;
						$scope.updatePasswordDiv = !$scope.updatePasswordDiv;                                              
                    } 
            $scope.updateBranchPassword = function(update_pass_form)
            {
                var param = $( "[name='update_pass_form']" ).serialize();
                var request = $http({
                    method: "post",
                    url: "index.php/branch/update_branch_password",
                    data: param+'&branch_id='+branch_id,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                request.success(function (response) {                                   
                    $scope.data = response.data;
                     //console.log(response.data);
					 
					 if(response.status=="1")
					 {
						$scope.success = true;
						$scope.successDiv = response.data;
					 }
					 else
					 {
						$scope.error = true;
						$scope.errorDiv = response.data;
					 }
                });
            }                   
            
    }]);