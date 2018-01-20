'use strict';
/**
 * controllers for ng-table
 * Simple table with sorting and filtering on AngularJS
 */

app.controller('brandCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function($scope, $filter, $http, $state, $modal, ngTableParams) {

    var request = $http({
        method: "post",
        url: "index.php/brand/brand_list",
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
                brand_id: 'asc' // initial sorting
            },
            filter: {
                brand_id: '' // initial filter
            }

        }, {
            total: data.length, // length of data
            getData: function($defer, params) {
                var orderedData;

                //console.log(params);

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
        $scope.updatebrand = function(brand_id) {

            $state.go('app.brand.update', { brand_id: brand_id });
        }

    });
    // else data not found - error

    $scope.open = function(size, brand_id) {

        var modalInstance = $modal.open({
            templateUrl: 'myModalContent.html',
            controller: 'ModalInstanceCtrl',
            size: size,
            resolve: {
                brand_id: function() {
                    return brand_id;
                },
                removeRow: function() {
                    return $scope.tableParams.data;
                }
            }
        });
    };

    $scope.infobrand = function(brand_id) {
        $state.go('app.brand.info', { brand_id: brand_id });
    }

}]);


app.controller('brandCreateCtrl', ["$scope", "$http", "$state", function($scope, $http, $state) {

    $scope.heading = 'Create';

    if (($state.params.brand_id != "") && (typeof $state.params.brand_id != 'undefined') && ($state.params.brand_id != 'undefined') && ($state.params.brand_id != null)) {
        $scope.heading = 'Update';
        var brand_id = $state.params.brand_id;
        // get details by id


        var request = $http({
            method: "post",
            url: "index.php/brand/get_brand_details",
            data: 'brand_id=' + brand_id,
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });
        /* Successful HTTP post request or not */
        request.success(function(response) {

            if (response.data) {
                // items have value
                //console.log(response.data);

                $scope.brand_id = response.data.brand_id;
                $scope.brand_name = response.data.brand_name;

            }
        });

        $scope.createbrand = function(create_brand) {
            if (create_brand.$valid) {

                var param = $("[name='create_brand']").serialize();

                var request = $http({
                    method: "post",
                    url: "index.php/brand/update_brand",
                    data: param + '&brand_id=' + brand_id,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });

                request.success(function(response) {

                    if (response.status == "1") {
                        //error                                
                        $state.go('app.brand.list');
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

        $scope.is_create = true;
        $scope.createbrand = function(create_brand) {
            if (create_brand.$valid) {

                var param = $("[name='create_brand']").serialize();
                console.log(param);

                var request = $http({
                    method: "post",
                    url: "index.php/brand/create_brand",
                    data: param,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                /* Successful HTTP post request or not */
                request.success(function(response) {

                    //alert(data.status);
                    //console.log(response);

                    if (response.status == "1") {
                        //error                                
                        $state.go('app.brand.list');
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
    //});  
}]);


app.controller('ModalInstanceCtrl', ["$scope", "$http", "$state", "brand_id", "removeRow", "$modalInstance", function($scope, $http, $state, brand_id, removeRow, $modalInstance) {

    $scope.ok = function() {
        $modalInstance.close();
        // delete product category 

        var request = $http({
            method: "post",
            url: "index.php/brand/brand_delete",
            data: 'brand_id=' + brand_id,
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
                    if (comArr[i].brand_id === brand_id) {
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

app.controller('brandInfoCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function($scope, $filter, $http, $state, $modal, ngTableParams) {


    var brand_id = $state.params.brand_id;


    var request = $http({
        method: "post",
        url: "index.php/brand/get_brand_details",
        data: 'brand_id=' + brand_id,
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    });
    /* Successful HTTP post request or not */
    request.success(function(response) {

        $scope.data = response.data;

    });

    $scope.updatebrand = function(brand_id) {
        $state.go('app.brand.update', { brand_id: brand_id });
    }
}]);