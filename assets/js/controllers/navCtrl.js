'use strict';

app.controller('navCtrl', ["$scope", "$filter", "$http", "$state", "$modal", function ($scope, $filter, $http,$state,$modal) {

		 $scope.show_dashboard_li = false;

			var request = $http({
                                method: "post",
                                url: "index.php/branch/getLoggedInBranchDetails",
                                data: {},
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                        request.success(function (response) {
                            //console.log(response.data);

                            if(response.data)
                            {
                                if(response.data.branch_type=="1")
                                {
                                    // show dropdown
                                    $scope.show_dashboard_li = true; 
                                }
                                else
                                {
                                    $scope.show_dashboard_li = false;
                                   
                                }
                            }

                            
                        });

}]);

app.controller('topnavCtrl', ["$scope", "$filter", "$http", "$state", "$modal", function ($scope, $filter, $http,$state,$modal) {

         $scope.show_dashboard_admin = false;

            var request = $http({
                                method: "post",
                                url: "index.php/waiter/waiter_list",
                                data: {},
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                        request.success(function (response) {
                            //console.log(response.data);

                            if(response.data.branch_type=="1")
                            {
                                // show dropdown
                                $scope.show_dashboard_admin = true; 
                            }
                            else
                            {
                                $scope.show_dashboard_admin = false;
                               
                            }
                        });

}]);

