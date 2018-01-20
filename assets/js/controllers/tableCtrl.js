
app.controller('tableCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {

	var request = $http({
						method: "post",
						url: "index.php/branch/getLoggedInBranchDetails",
						data: {},
						headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
					});

	request.success(function (response) {
	if(response.data.branch_type == 1)
	{
		$scope.adminBranch=true;
	}

		$scope.branch_list = response.branch_list;
	});	
	
	
	
	$scope.branchwiseBrand = function (branch_idObj)
    {
      var request = $http({
                  method: "post",
                  url: "index.php/report/branchwise_brand",
					data: "branch_id="+branch_idObj.branch_id,
                  headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
              });
          request.success(function (response) {
               
              $scope.brand_list_by_branch = response.brand_list_by_branch;
            });
    }
	
	$scope.changeEndTable = function(){
		
		$scope.end_table = parseFloat($scope.total_table) + parseFloat($scope.start_table) - 1;
	}
	
	
	$scope.assignTable = function()
	{
		

			var param = $( "[name='assign_table']" ).serialize();
			//console.log(param);
								   
			var request = $http({
				method: "post",
				url: "index.php/table/assign_table",
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
					$state.go('app.table.list');
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
	
	
	var request = $http({
                    method: "post",
                    url: "index.php/table/assign_table_list",
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


/**********************/




