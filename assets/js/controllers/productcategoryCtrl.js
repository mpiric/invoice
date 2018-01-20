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


app.controller('productcategoryCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {

            var request = $http({
                    method: "post",
                    url: "index.php/productCategory/product_category_list",
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
                              product_category_id: 'asc' // initial sorting
                            },
                        filter: {
                             product_category_id: '' // initial filter
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
                    $scope.updateproductcategory = function(product_category_id) {   

                        $state.go('app.productcategory.update', {product_category_id: product_category_id});                            
                    }

                });
                // else data not found - error

              $scope.open = function (size,product_category_id) {

                    var modalInstance = $modal.open({
                        templateUrl: 'myModalContent.html',
                        controller: 'ModalInstanceCtrl',
                        size: size,
                        resolve: {
                            product_category_id: function () {
                                return product_category_id;
                            }
                            ,
                            removeRow: function()
                            {                   
                                return $scope.tableParams.data;
                            }
                        }
                    });
            };

            $scope.infoProductcategory = function(product_category_id)
            {
                $state.go('app.productcategory.info', {product_category_id: product_category_id}); 
            }
      
    }]);


app.controller('productcategoryCreateCtrl', ["$scope", "$http", "$state", function ($scope, $http, $state) {

    $scope.heading='Create';

     $scope.selected = {};

    //$scope.show_product_category_dd = false;
     $scope.brand_list = [];

      var request = $http({
                                method: "post",
                                url: "index.php/brand/getbrandList",
                                data: {},
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                        request.success(function (response) {
                             
                            $scope.brand_list = response.brand_list;

                            console.log($scope.brand_list);  

                             //console.log(response.brand_list);
                            //var index = $scope.brand_list.map(function(e) { return e.brand_id; }).indexOf(response.data.brand_id);
  
                            //$scope.brand_id = $scope.brand_list[index]; 


                        });

               
                var request = $http({
                                method: "post",
                                url: "index.php/productCategory/getProductCatList",
                                data: {},
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                        request.success(function (response) {
                            //console.log(response);
                             
                            $scope.product_cat_list = response.product_cat_list;                            

                            


                // var request = $http({
                //                 method: "post",
                //                 url: "index.php/branch/getLoggedInBranchDetails",
                //                 data: {},
                //                 headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                //             });
                //         request.success(function (response) {
                //             //console.log(response);

                //             $scope.branch_list = response.branch_list;                           
                            
                //             // get index of current loggedin branch
                //             var index = $scope.branch_list.map(function(e) { return e.branch_id; }).indexOf(response.data.branch_id);

                //              // make the current branch selected in dd
                //              $scope.branch_id = $scope.branch_list[index]; 

            if( ( $state.params.product_category_id!="" ) && ( typeof $state.params.product_category_id != 'undefined' ) && ( $state.params.product_category_id != 'undefined' ) && ( $state.params.product_category_id != null ) )
                {
                    $scope.heading='Update';

                    var product_category_id = $state.params.product_category_id;
                    // get details by id


                    var request = $http({
                                method: "post",
                                url: "index.php/productCategory/get_product_category_details",
                                data: 'product_category_id='+product_category_id,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });
                            /* Successful HTTP post request or not */
                            request.success(function (response) {
                                
                                    if (response.data) 
                                    {
                                        // items have value
                                        //console.log(response.data);

                                        //$scope.branch_id = response.data.branch_id;
                                        $scope.product_category_id = response.data.product_category_id;
                                        $scope.name = response.data.name;
                                       // $scope.parent = response.data.product_category_id;
                                        

                                        // var index = $scope.branch_list.map(function(e) { return e.branch_id; }).indexOf(response.data.branch_id);

                                        // $scope.branch_id = $scope.branch_list[index]; 

                                        var index1 = $scope.product_cat_list.map(function(e) { return e.product_category_id; }).indexOf(response.data.parent);

                                        $scope.parent = $scope.product_cat_list[index1];

                                        //console.log($scope.product_cat_list);
                                        //console.log(response.data.parent);
                                        //console.log(index1); 

                                         var index = $scope.brand_list.map(function(e) { return e.brand_id; }).indexOf(response.data.brand_id);
                                        console.log(index);       
                                        
                                       $scope.brand_id = $scope.brand_list[index];

                                        //console.log($scope.brand_id);


                                        // if( ( response.data.brand_id!="" ) && ( typeof response.data.brand_id != 'undefined' ) && ( response.data.brand_id != 'undefined' ) && ( response.data.brand_id != null ) )
                                        // {


                                        //     var selectedBrandIdArr = response.data.brand_id;
                                        //     //console.log(selectedBrandIdArr);

                                        //     var myarray = selectedBrandIdArr.split(','); 

                                        //     var selectedBrandIdArray = [];

                                        //     for(var i = 0; i < myarray.length; i++) 
                                        //     { 
                                        //         //console.log(myarray[i]);

                                        //         var index = $scope.brand_list.map(function(e) { return e.brand_id; }).indexOf(myarray[i]);

                                        //         selectedBrandIdArray.push($scope.brand_list[index]);

                                        //     }


                                        //     $scope.selected.selectedBrand = selectedBrandIdArray;

                                            

                                        //     // angular.forEach(selectedBrandIdArr, function(value, key) {

                                        //     //     // angular.forEach(selectedBrandIdArr, function(value, key) {
                                        //     //     var index = $scope.brand_list.map(function(e) { return e.brand_id; }).indexOf(response.data.brand_id);

                                        //     //     var index = $scope.brand_list.map(function(e) { return e.brand_id; });

                                        //     //     console.log(index);

                                        //     //     $scope.brand_id = [$scope.brand_list[index]];
                                        //     //     //console.log($scope.brand_id);
                                        //     // });
                                    
                                        //     // }); 
                                                                              
                                        // } 
                                    }
                                });
                    
                    $scope.createproductcategory = function(create_productcategory)
                    {                       
                        if(create_productcategory.$valid) {

                            var param = $( "[name='create_productcategory']" ).serialize();

                            // var selectedBrandIdArr = $scope.create_productcategory.selectedBrand.$modelValue;

                            // var brand_id_csv = '';

                            //  angular.forEach(selectedBrandIdArr, function(value, key) {
                                 
                            //          if(value.brand_id!='' && key!=0)
                            //          {
                            //             brand_id_csv += ',';
                            //          }
                            //          brand_id_csv += value.brand_id;                                      
                            //     });


                            
                            var request = $http({
                                method: "post",
                                url: "index.php/productCategory/update_productcategory",
                                data: param+'&product_category_id='+product_category_id,
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            });

                            request.success(function (response) {

                                console.log(response.data);

                                if(response.status=="1")
                                {
                                    //error                                
                                    $state.go('app.productcategory.list');
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
                    $scope.createproductcategory = function(create_productcategory)
                    {
                        if(create_productcategory.$valid) {

                            var param = $( "[name='create_productcategory']" ).serialize();
                            //console.log(param);

                            // var selectedBrandIdArr = $scope.create_productcategory.selectedBrand.$modelValue;

                            // console.log(selectedBrandIdArr);

                            // var brand_id_csv = '';
                            // angular.forEach(selectedBrandIdArr, function(value, key) {
                                 
                            //          if(value.brand_id!='' && key!=0)
                            //          {
                            //             brand_id_csv += ',';
                            //          }
                            //          brand_id_csv += value.brand_id;                                      
                            //     });
                            // console.log(brand_id_csv);
                            
                                                   
                            var request = $http({
                                method: "post",
                                url: "index.php/productCategory/create_productcategory",
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
                                    $state.go('app.productcategory.list');
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


app.controller('ModalInstanceCtrl', ["$scope","$http","$state","product_category_id","removeRow", "$modalInstance", function ($scope,$http,$state,product_category_id,removeRow, $modalInstance) {

    $scope.ok = function () {
        $modalInstance.close();
        // delete product category 

        var request = $http({
                    method: "post",
                    url: "index.php/productCategory/product_category_delete",
                    data: 'product_category_id='+product_category_id,
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
                                         if( comArr[i].product_category_id === product_category_id ) {
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

app.controller('productcategoryInfoCtrl', ["$scope", "$filter", "$http", "$state", "$modal", "ngTableParams", function ($scope, $filter, $http,$state,$modal, ngTableParams) {

        
        var product_category_id = $state.params.product_category_id;

      
            var request = $http({
                    method: "post",
                    url: "index.php/productCategory/get_product_category_details",
                    data: 'product_category_id='+product_category_id,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                /* Successful HTTP post request or not */
                request.success(function (response) {
                                   
                    $scope.data = response.data;
                    
                });     

                $scope.updateproductcategory = function(product_category_id) {  
                        $state.go('app.productcategory.update', {product_category_id: product_category_id});                         
                    }  
    }]);