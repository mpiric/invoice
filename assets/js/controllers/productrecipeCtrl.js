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


app.controller('productrecipeCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {



    $scope.createproductrecipe = function(create_productrecipe){

                     
        if(create_productrecipe.$valid) {

            var param = $( "[name='create_productrecipe']" ).serialize();
            //console.log(param);
            
            var store_product_id_arr = [];
            var store_product_id_array = [];
            var store_product_id_update = [];

            var paramArr = $( "[name='create_productrecipe']" ).serializeArray();

            
            // /return false;

            angular.forEach(paramArr, function(value, key) {

                if(value.name=='store_product_id')
                {

                   var store_product_id = (value.value);


                   // not allow duplicates
                   if(store_product_id_arr.indexOf(store_product_id) === -1)
                   {
                        store_product_id_arr.push(store_product_id);
                   }
                }

                //console.log(store_product_id_arr);

                angular.forEach(store_product_id_arr,function (value1,key1){

                    

                    if(value.name=='qty_'+value1)
                    {
                        //console.log(value.value);
                        if(value.value != '0' && value.value != null)
                        {
                            //console.log(value1);
                            store_product_id_array.push(value1);

                        }
                        else
                        {
                            store_product_id_update.push(value1);

                            var request = $http({
                                method: "post",
                                url: "index.php/productrecipe/get_added_products",
                                data: 'product_id='+$scope.product_id,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                            /* Successful HTTP post request or not */
                            request.success(function (response) {


                                var productData = response.data;

                                angular.forEach(productData,function (v,k)
                                {
                                    console.log(store_product_id_update);
                                    //return false;
                                    if(v.qty > 0)
                                    {
                                        var request = $http({
                                            method: "post",
                                            url: "index.php/productrecipe/delete_added_products",
                                            data: 'store_product_id='+store_product_id_update.toString()+'&product_id='+$scope.product_id,
                                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                                        });
                                        request.success(function (response) {

                                            console.log(response);

                                        });

                                    }
                                });

                                //console.log($scope.productData);
                                // console.log(value1);
                                // console.log(key1);

                            });
                        }
                       
                    }
                    

                });

            });
        
                                       
                var request = $http({
                    method: "post",
                    url: "index.php/productrecipe/create_productrecipe",
                    data: param+'&store_product_id_arr='+store_product_id_array.toString()+'&product_id='+$scope.product_id,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                /* Successful HTTP post request or not */
                request.success(function (response) {
                    
                    //console.log(response);

                    if(response.status=="1")
                    {
                        //error             
                                     
                        $state.go('app.productrecipe.create');
                        alert('Recipe Updated!!');      
                    }
                    else if(response.status=="-1")
                    {
                       // validation error;
                       $('#validation_err').html(response.data);
                    }
                    else
                    {
                       // alert(response.data);                                
                    }
                });

        }
    }

    $scope.show_div = false;
    $scope.product_id = [];

    var request = $http({
            method: "post",
            url: "index.php/product/product_details",
            data: {},
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });
    request.success(function (response) {
        //console.log(response);

         
        $scope.product_list = response.data;                           
        
        // get index of current loggedin branch
        //var index = $scope.product_list.map(function(e) { return e.product_id; }).indexOf(response.data.product_id);

        //$scope.product_id = $scope.product_list[index];

			
	});   


    $scope.storeProduct = function(product_id){

         angular.element("#pdf_loader").show();

        $scope.product_id = product_id;

        

        var request = $http({
                    method: "post",
                    url: "index.php/product/get_product_details",
                    data: 'product_id='+$scope.product_id,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            });
            request.success(function (response) {
                
                $scope.product_code = response.data.product_code;
            });



        if(product_id != null)
        {
            $scope.show_div = true;

            //store product list

            var request = $http({
                    method: "post",
                    url: "index.php/storeproduct/store_product_details",
                    data: 'product_id='+$scope.product_id,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            });
            request.success(function (response) {

                angular.element("#pdf_loader").hide();

                var data = response.data;
                $scope.data = response.data;

                 $scope.tableParams = new ngTableParams({
                        page: 1, // show first page
                        count: 10, // count per page
                        sorting: {
                              qty: 'desc' // initial sorting
                            },
                        filter: {
                             qty: '' // initial filter
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

           

        }
        else
        {
            $scope.show_div = false;
        }

    }

    $scope.addedProducts = function()
    {
        var request = $http({
            method: "post",
            url: "index.php/productrecipe/get_added_products",
            data: 'product_id='+$scope.product_id,
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });

        request.success(function (response) {
         
             var data = response.data;
                $scope.data = response.data;

                 $scope.tableParams = new ngTableParams({
                        page: 1, // show first page
                        count: 10, // count per page
                        sorting: {
                              qty: 'desc' // initial sorting
                            },
                        filter: {
                             qty: '' // initial filter
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
    }

    $scope.allProducts = function()
    {
        console.log('allProducts'); 
       var request = $http({
                    method: "post",
                    url: "index.php/storeproduct/store_product_details",
                    data: 'product_id='+$scope.product_id,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            });
            request.success(function (response) {
                var data = response.data;
                $scope.data = response.data;

                 $scope.tableParams = new ngTableParams({
                        page: 1, // show first page
                        count: 10, // count per page
                        sorting: {
                              qty: 'desc' // initial sorting
                            },
                        filter: {
                             qty: '' // initial filter
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

   
    }

        // $scope.filterToggle = true;
        // //start function.
        // $scope.addedProduct = function () 
        // {
        //     $scope.filterToggle = false;
        //     console.log('in added function.');
        // };
        // $scope.CallFunc = function () 
        // {
        //     $scope.filterToggle ? $scope.addedProduct() : $scope.allProduct();
        // };
        // // pause function.
        // $scope.allProduct = function () 
        // {
        //   $scope.filterToggle = true;
        //   console.log('in all function.');
        // };


		
	}]);

// CREATE TABLE `rst`.`product_recipe`( `product_recipe_id` INT(11) NOT NULL AUTO_INCREMENT, `product_id` INT(11), `store_product_id` INT(11), `qty` DOUBLE(10,2), PRIMARY KEY (`product_recipe_id`) ); 