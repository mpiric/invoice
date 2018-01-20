'use strict';
/**
 * controllers for ng-table
 * Simple table with sorting and filtering on AngularJS
 */

app.controller('waiterCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function($scope, $filter, $http, $state, $modal, ngTableParams) {


    var request = $http({
        method: "post",
        url: "index.php/waiter/waiter_list",
        data: {},
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    });
    /* Successful HTTP post request or not */
    request.success(function(response) {

        //console.log(response.data);

        var data = response.data;

        $scope.tableParams = new ngTableParams({
            page: 1, // show first page
            count: 5, // count per page
            sorting: {
                firstname: 'asc' // initial sorting
            },
            filter: {
                firstname: '' // initial filter
            }

        }, {
            total: data.length, // length of data
            getData: function($defer, params) {
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

                var page = orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count());
                $scope.data = page;
                params.total(orderedData.length);
                $defer.resolve(page);

            }
        });

        $scope.updateWaiter = function(waiter_id) {

            $state.go('app.waiter.update', { waiter_id: waiter_id });
        }

    });
    // else data not found - error

    $scope.open = function(size, waiter_id) {

        var modalInstance = $modal.open({
            templateUrl: 'myModalContent.html',
            controller: 'ModalInstanceCtrl',
            size: size,
            resolve: {
                waiter_id: function() {
                    return waiter_id;
                },
                removeRow: function() {
                    return $scope.tableParams.data;
                }
            }
        });
    };

    $scope.infoWaiter = function(waiter_id) {
        $state.go('app.waiter.info', { waiter_id: waiter_id });
    }


}]);


app.controller('waiterCreateCtrl', ["$scope", "$http", "$state", function($scope, $http, $state) {

    $scope.heading = 'Create';
    $scope.show_branch_dd = false;

    // check logged in user type
    // if its super admin then show branch drop down else send branch_id as hidden field
    var request = $http({
        method: "post",
        url: "index.php/branch/getLoggedInBranchDetails",
        data: {},
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    });
    request.success(function(response) {
        //console.log(response);

        if (response.data.branch_type == "1") {
            // show dropdown
            $scope.show_branch_dd = true;
        } else {
            $scope.show_branch_dd = false;
            //$scope.branch_id = response.data.branch_id;
        }

        $scope.branch_list = response.branch_list;

        // get index of current loggedin branch
        var index = $scope.branch_list.map(function(e) { return e.branch_id; }).indexOf(response.data.branch_id);

        // make the current branch selected in dd
        $scope.branch_id = $scope.branch_list[index];

        if (($state.params.waiter_id != "") && (typeof $state.params.waiter_id != 'undefined') && ($state.params.waiter_id != 'undefined') && ($state.params.waiter_id != null)) {
            $scope.heading = 'Update';
            var waiter_id = $state.params.waiter_id;
            // get details by id

            var request = $http({
                method: "post",
                url: "index.php/waiter/get_waiter_details",
                data: 'waiter_id=' + waiter_id,
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            });
            /* Successful HTTP post request or not */
            request.success(function(response) {

                if (response.data) {
                    // items have value
                    //console.log(response.data);
                    $scope.firstname = response.data.firstname;
                    $scope.lastname = response.data.lastname;
                    $scope.contact = response.data.contact;
                    $scope.waiter_code = response.data.waiter_code;
                    $scope.email = response.data.email;
                    $scope.password = response.data.password;
                    $scope.address = response.data.address;
                    $scope.pincode = response.data.pincode;
                    $scope.country = response.data.country_id;

                    //console.log($scope.branch_list);
                    var index = $scope.branch_list.map(function(e) { return e.branch_id; }).indexOf(response.data.branch_id);

                    $scope.branch_id = $scope.branch_list[index];

                    ajax_call('ajaxCall', { location_id: response.data.country_id, location_type: 1 }, 'state');
                    $scope.state = response.data.state_id;

                    ajax_call('ajaxCall', { location_id: response.data.state_id, location_type: 2 }, 'city');
                    $scope.city = response.data.city_id;

                }
            });

            $scope.createWaiter = function(create_waiter) {
                if (create_waiter.$valid) {

                    var param = $("[name='create_waiter']").serialize();

                    var request = $http({
                        method: "post",
                        url: "index.php/waiter/update_waiter",
                        data: param + '&waiter_id=' + waiter_id,
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                    });

                    request.success(function(response) {

                        if (response.status == "1") {
                            //error                                
                            $state.go('app.waiter.list');
                        } else if (response.status == "-1") {
                            // validation error;
                            $('#validation_err').html(response.data);
                        } else {
                            alert(response.data);
                        }
                    });
                }
            }

        } else {

            $scope.createWaiter = function(create_waiter) {
                if (create_waiter.$valid) {

                    var param = $("[name='create_waiter']").serialize();
                    //console.log(param);

                    var request = $http({
                        method: "post",
                        url: "index.php/waiter/create_waiter",
                        data: param,
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                    });
                    /* Successful HTTP post request or not */
                    request.success(function(response) {

                        //alert(data.status);
                        //console.log(response);

                        if (response.status == "1") {
                            //error                                
                            $state.go('app.waiter.list');
                        } else if (response.status == "-1") {
                            // validation error;
                            $('#validation_err').html(response.data);
                        } else {
                            alert(response.data);
                        }
                    });

                }
            }
        }

    });

}]);

app.controller('ModalInstanceCtrl', ["$scope", "$http", "$state", "waiter_id", "removeRow", "$modalInstance", function($scope, $http, $state, waiter_id, removeRow, $modalInstance) {

    $scope.ok = function() {
        $modalInstance.close();
        // delete waiter 

        var request = $http({
            method: "post",
            url: "index.php/waiter/waiter_delete",
            data: 'waiter_id=' + waiter_id,
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });
        /* Successful HTTP post request or not */
        request.success(function(response) {


            if (response.status == "1") {
                // success

                // remove the selected row
                //console.log('delete');

                var index = -1;
                var comArr = eval(removeRow);
                for (var i = 0; i < comArr.length; i++) {
                    if (comArr[i].waiter_id === waiter_id) {
                        index = i;
                        break;
                    }
                }
                if (index === -1) {
                    alert("Something gone wrong");
                }
                removeRow.splice(index, 1);

            }
        });

    };

    $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
    };
}]);

app.controller('waiterInfoCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function($scope, $filter, $http, $state, $modal, ngTableParams) {


    var waiter_id = $state.params.waiter_id;


    var request = $http({
        method: "post",
        url: "index.php/waiter/get_waiter_details",
        data: 'waiter_id=' + waiter_id,
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    });
    /* Successful HTTP post request or not */
    request.success(function(response) {

        $scope.data = response.data;

    });

    $scope.updateWaiter = function(waiter_id) {
        $state.go('app.waiter.update', { waiter_id: waiter_id });
    }
}]);