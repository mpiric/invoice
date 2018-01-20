'use strict';
/** 
  * controllers used for the dashboard
*/

app.controller('dashboardAdminCtrl',["$scope","$http","$state", function ($scope,$http,state) {



                    var request = $http({

                        method: "post",
                        url: "index.php/branch/dashboardData",
                        data: {},
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }

                    });

                    request.success(function (response) {

                                if(response.status=="1")
                                {
                                    $scope.incomelist = response.data;
                                    //console.log($scope.incomelist);
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

                    

                    // var request = $http({

                    //     method: "post",
                    //     url: "index.php/waiter/waiter_list_by_branch",
                    //     data: {},
                    //     headers: { 'Content-Type': 'application/x-www-form-urlencoded' }

                    // });

                    // request.success(function (response) {

                    //     //console.log(response.data);

                    //             if(response.status=="1")
                    //             {
                    //                 $scope.waiterlist = response.data;
                    //                 //console.log($scope.waiterlist);
                    //             }
                    //             else if(response.status=="-1")
                    //             {
                    //                // validation error;
                    //                $('#validation_err').html(response.data);
                    //             }
                    //             else
                    //             {
                    //                 alert(response.data);                                
                    //             }
                    //         });
                // }

}]);