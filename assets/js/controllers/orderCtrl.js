'use strict';
/**
 * controllers for ng-table
 * Simple table with sorting and filtering on AngularJS
 */

// app.directive('myEnter', function () {
//     return function (scope, element, attrs) {
//         element.bind("keydown keypress", function (event) {
//             if(event.which === 80) {
//                 scope.$apply(function (){
//                     scope.$eval(attrs.myEnter);
//                 });

//                 event.preventDefault();
//             }
//         });
//     };
// });


app.filter('propsFilter', function() {
    return function(items, props) {
        var out = [];

        if (angular.isArray(items)) {
            var keys = Object.keys(props);

            items.forEach(function(item) {
                var itemMatches = false;

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

// app.directive('uiSelectFocusInput', function($timeout){
//     return {
//         require: 'ui-select',
//         link: function (scope, $element, $attributes, selectController) {

//             scope.$on('uis:activate',function(){
//                 // Give it time to appear before focus
//                 $timeout(function(){
//                     scope.$select.searchInput[0].focus();
//                 },500);
//             });
//         }
//     }
// });

// ------------------ tab order for table ------------------

//This first directive is used to assign a name to a 'focusable' element.
app.directive('focusName', function() {
    return {
        restrict: 'A',
        link: function($scope, element, attributes) {
            $scope.focusRegistry = $scope.focusRegistry || {};
            $scope.focusRegistry[attributes.focusName] = element[0];
        }
    };
});

// this directive is used declare the which element will be focused when the tab key is pressed. 

app.directive('nextFocus', function() {
    return {
        restrict: 'A',
        link: function($scope, element, attributes) {
            element.bind('keydown keypress', function(event) {
                if (event.which === 9) { // Tab
                    var focusElement = $scope.focusRegistry[attributes.nextFocus];
                    if (focusElement) {
                        if (!focusElement.hidden && !focusElement.disabled) {
                            focusElement.focus();
                            event.preventDefault();
                            return;
                        }
                    }

                    console.log('Unable to focus on target: ' + attributes.nextFocus);
                }
            });
        }
    };
});


// give focus to an element on page load
app.directive('autoFocus', function($timeout) {
    return {
        restrict: 'AC',
        link: function(_scope, _element) {
            $timeout(function() {
                _element[0].focus();
            }, 0);
        }
    };
});

// -------------------------------------------------------------



// unique filter to avoid duplicated in table rows

app.filter('unique', function() {
    return function(collection, keyname) {
        var output = [],
            keys = [];

        angular.forEach(collection, function(item) {
            var key = item[keyname];
            if (keys.indexOf(key) === -1) {
                keys.push(key);
                output.push(item);
            }
        });

        return output;
    };
});


// type only numbers in textbox
app.directive('onlyDigits', function() {
    return {
        require: 'ngModel',
        restrict: 'A',
        link: function(scope, element, attr, ctrl) {
            function inputValue(val) {
                if (val) {
                    var digits = val.replace(/[^0-9.]/g, '');

                    if (digits.split('.').length > 2) {
                        digits = digits.substring(0, digits.length - 1);
                    }

                    if (digits !== val) {
                        ctrl.$setViewValue(digits);
                        ctrl.$render();
                    }
                    return parseFloat(digits);
                }
                return undefined;
            }
            ctrl.$parsers.push(inputValue);
        }
    };
});

app.directive('ngEnter', function() {
    return function(scope, element, attrs) {
        element.bind("keydown keypress", function(event) {
            if (element.val() != '') {
                if (event.which === 13) {

                    //scope.$watch('order_form.$valid', function(validity) {

                    scope.$apply(function() {
                        scope.$eval(attrs.ngEnter, {
                            'event': event
                        });
                    });

                    event.preventDefault();
                    // })

                }
            }

        });
    };
});



app.controller('reloadPageCtrl', ["$scope", "$window", function($scope, $window) {

    $scope.reloadRoute = function() {
        $window.location.reload();
    }


}]);

app.controller('orderListCtrl', ["$scope", "$http", "$state", "$modal", "ngTableParams", "$filter", function($scope, $http, $state, $modal, ngTableParams, $filter) {

    $scope.is_admin_branch = false;
    // get branch name
    var request = $http({
        method: "post",
        url: "index.php/branch/getLoggedInBranchDetails",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });
    request.success(function(response) {

        if (response.data.branch_type == 1) {
            $scope.is_admin_branch = true;
        }

    });


    var request = $http({
        method: "post",
        url: "index.php/order/order_list",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });
    /* Successful HTTP post request or not */
    request.success(function(response) {

        //console.log(response.data);

        var data = response.data;

        $scope.tableParams = new ngTableParams({
            page: 1, // show first page
            count: 10, // count per page
            sorting: {
                name: 'asc' // initial sorting
            },
            filter: {
                name: '' // initial filter
            }

        }, {
            total: data.length, // length of data
            getData: function($defer, params) {
                var orderedData;


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
        // $scope.updateOrder = function(order_id)
        // {
        //   $state.go('app.order.orderUpdate', {order_id: order_id});
        // }


    });

    $scope.updateOrder = function(order_id) {

        // var request = $http({
        //                        method: "post",
        //                        url: "index.php/order/orderUpdatefromList",
        //                        data: 'order_id='+order_id,
        //                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        //                    });
        // request.success(function (response){


        //    console.log(response.order);

        //    $scope.products = response.order;

        // });
        $state.go('app.order.orderUpdate', {
            order_id: order_id
        });

    }

}]);

app.controller('orderUpdateCtrl', ["$scope", "$http", "$state", "$rootScope", function($scope, $http, $state, $rootScope) {

    if (($state.params.order_id != "") && (typeof $state.params.order_id != 'undefined') && ($state.params.order_id != 'undefined') && ($state.params.order_id != null)) {
        $scope.show_tax_1 = false;
        $scope.show_tax_2 = false;

        var order_id = $state.params.order_id;
        // console.log($state.params.order_id);
        // return false;

        var request = $http({
            method: "post",
            url: "index.php/order/orderUpdatefromList",
            data: 'order_id=' + order_id,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });
        request.success(function(response) {


            //console.log(response.data);

            //angular.element("#bill_number").html(response.order_detail.order_code);

            $scope.products = response.order_items;

        });

        var request = $http({
            method: "post",
            url: "index.php/tax/tax_by_branch",
            data: {},
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });
        request.success(function(response) {
            //console.log(response.data[0].tax_name);
            if (response.data[0]) {
                $scope.tax_1 = response.data[0].tax_name;
                $scope.show_tax_1 = true;
                $scope.tax_1_tax_id = response.data[0].tax_id;
            }
            if (response.data[1]) {
                $scope.tax_2 = response.data[1].tax_name;
                $scope.show_tax_2 = true;
                $scope.tax_2_tax_id = response.data[1].tax_id;
            }


        });



        $scope.getTotal = function(product) {

            //for(var i = 0; i < $scope.products.length; i++){
            var total = 0;
            //  var product = $scope.products;
            total += (product.quantity * product.price) + ((product.quantity * product.price) * (parseFloat((product.service_tax_percent == null) ? 0 : product.service_tax_percent) + parseFloat((product.other_tax_percent == null) ? 0 : product.other_tax_percent))) / 100
            //}
            return total;
        }

        $scope.removeProduct = function(product_id, service_tax_percent, other_tax_percent, productRows) {
            console.log(productRows);
            
            // get the total which should be removed

            // console.log('service_tax_percent'+service_tax_percent);
            //  console.log('other_tax_percent'+other_tax_percent);

            //$scope.$watch('online', function(){

            // if($rootScope.online == false)
            // {
            //   $window.alert('Please Connect to Internet');
            //   return false;
            // }
            // else
            // {

            if ($rootScope.online == true) {


                var tax_arr = [];

                if ((productRows.service_tax_id != "") && (typeof productRows.service_tax_id != 'undefined') && (productRows.service_tax_id != 'undefined') && (productRows.service_tax_id != null)) {
                    tax_arr.push({
                        "tax_id": productRows.service_tax_id,
                        "tax_percent": productRows.service_tax_percent
                    });
                }
                if ((productRows.other_tax_id != "") && (typeof productRows.other_tax_id != 'undefined') && (productRows.other_tax_id != 'undefined') && (productRows.other_tax_id != null)) {
                    tax_arr.push({
                        "tax_id": productRows.other_tax_id,
                        "tax_percent": productRows.other_tax_percent
                    });
                }
                //  console.log(tax_arr);


                var tax = parseFloat((service_tax_percent == null) ? 0 : service_tax_percent) + parseFloat((other_tax_percent == null) ? 0 : other_tax_percent);

                var given_amount = ($scope.given_amount == null ? 0 : $scope.given_amount);

                // remove order items from db
                var request = $http({
                    method: "post",
                    url: "index.php/order/delete_order_items",
                    data: 'tax=' + tax + '&given_amount=' + given_amount + '&product_id=' + product_id + '&order_id=' + order_id + '&tax_arr=' + JSON.stringify(tax_arr),
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                });

                request.success(function(response) {

                    // $scope.products.splice( index, 1 );
                    // remove row from table
                    var index = -1;
                    var comArr = eval($scope.products);
                    for (var i = 0; i < comArr.length; i++) {
                        if (comArr[i].product_id === product_id) {
                            index = i;
                            break;
                        }
                    }
                    if (index === -1) {
                        alert("Something gone wrong");
                    }
                    $scope.products.splice(index, 1);

                    $scope.selected_table_id = angular.element('#table_id').val();
                    // change - table list details; total_amount
                    angular.forEach($scope.data, function(value, key) {

                        // console.log(value);

                        if ($scope.selected_table_id == value.table_detail_id) {
                            value.order_id = order_id;
                            value.order_code = $scope.order_code;
                            value.total_amount = $scope.finalOrderTotal();
                        }

                        $scope.is_bill_saved = true;

                    });

                });
                //}

            }

            // })

        }


        $scope.gotoOrderList = function() {
            $state.go('app.order.list');
        }

        //   $scope.saveOrder = function(){

        //   if($scope.products.length>0) 
        //   {

        //         var request = $http({
        //                           method: "post",
        //                           url: "index.php/order/print_order",
        //                           data: 'order_id='+order_id,
        //                           headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        //                       });

        //                   request.success(function (response) { 
        //                   });
        //   }

        // }



        // print the bill
        $scope.printInvoice = function() {

            //console.log($state.params.order_id);

            var order_id = $state.params.order_id;


            if ($rootScope.online == true) {

                //$scope.saveOrder();

                // if($scope.is_bill_saved==true)
                // {
                //   $scope.saveBtnDisabled = true;
                // }
                var order_id = $state.params.order_id;
                //var order_id = $scope.order_id;
                var order_code = $scope.order_code;
                $scope.printOrderCode = $scope.order_code;



                var request = $http({
                    method: "post",
                    url: "index.php/order/get_details_to_print_invoive_admin",
                    data: 'order_id=' + order_id,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                });

                request.success(function(response) {


                    console.log("there");

                    $scope.branch_specific_taxes = '';
                    // $scope.dicount_percent = 0;
                    var service_tax_number_html = '';
                    var other_number_html = '';

                    if (response.data) {
                        $scope.captain = response.data.waiter_name;
                        // $scope.covers = response.data.max_capacity;
                        $scope.branch_name = response.data.branch_name;
                        $scope.branch_address = response.data.branch_address;
                        $scope.covers = response.data.number_of_person;
                        $scope.order_date = response.data.order_date;
                        $scope.order_time = response.data.order_time;
                        $scope.table_number = response.data.table_number;
                        $scope.invoice_items = response.invoice_items;
                        $scope.invoice_total = response.invoice_total;
                        $scope.dicount_percent = response.data.discount_amount;

                        $scope.printOrderCode = response.data.order_code;

                        if (response.branch_specific_taxes) {
                            $scope.branch_specific_taxes = response.branch_specific_taxes;
                        }

                        if (response.branch_details) {
                            $scope.service_tax_number = response.branch_details.service_tax_number;
                            if ($scope.service_tax_number != '' && $scope.service_tax_number != null) {
                                service_tax_number_html = '<br>Service Code – 996331  CIN.No : ' + $scope.service_tax_number;
                            }
                            $scope.other_number = response.branch_details.other_number;
                            if ($scope.other_number != '' && $scope.other_number != null) {
                                other_number_html = '<br>No Reverse Charge GSTIN : ' + $scope.other_number;
                            }
                        }

                        $scope.grand_total = response.grand_total;
                    }

                    var total_order_items = '';

                    if (response.total_order_items) {
                        total_order_items = response.total_order_items;
                    }

                    var dicount_percent = 0;
                    if ($scope.dicount_percent != 0) {
                        dicount_percent = $scope.dicount_percent;
                    }


                    var branch_name = $scope.branch_name;
                    var branch_address = $scope.branch_address;
                    var captain = $scope.captain;
                    var covers = $scope.covers;
                    var order_date = $scope.order_date;
                    var order_time = $scope.order_time;
                    var table_number = $scope.table_number;
                    var invoice_items = $scope.invoice_items;
                    var invoice_total = $scope.invoice_total;
                    var branch_specific_taxes = '';

                    if (($scope.branch_specific_taxes != "") && (typeof $scope.branch_specific_taxes != 'undefined') && ($scope.branch_specific_taxes != 'undefined') && ($scope.branch_specific_taxes != null)) {
                        branch_specific_taxes = $scope.branch_specific_taxes;
                    }


                    var grand_total = $scope.grand_total;

                    var popupWin = window.open('', '_blank', 'width=300,height=300');
                    popupWin.document.open();


                    popupWin.document.write('<style type="text/css">table.tableizer-table{font-size: 14px;border: none; font-family: Arial, Helvetica, sans-serif;}.tableizer-table td{padding: 4px;margin: 3px;border: none;font-weight:600;}.tableizer-table th{font-weight: bold;}.dotted{border-bottom-style: dotted !important;  border-width:2px;}.dotted-top{border-bottom-style: dotted !important;border-top-style: dotted !important;border-width:2px;}</style><table class="tableizer-table"><thead><tr class="tableizer-firstrow"><th></th><th>&nbsp;</th><th colspan="2">' + branch_name + '</th><th>&nbsp;</th></tr></thead><tbody> <tr><td>&nbsp;</td><td>&nbsp;</td><td>' + branch_address + '</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr style="border-style: solid !important;border-bottom-style: dotted !important;"><td class="dotted">TAX INVOICE</td><td class="dotted">&nbsp;</td><td class="dotted">&nbsp;</td><td class="dotted">&nbsp;</td><td class="dotted">&nbsp;</td></tr><tr><td class="dotted">Bill:</td><td class="dotted">' + $scope.printOrderCode + '</td><td class="dotted">Covers</td><td class="dotted">' + covers + '</td><td class="dotted">' + order_date + '</td></tr><tr><td class="dotted">Table No:</td><td class="dotted">' + table_number + '</td><td class="dotted">Captain</td><td class="dotted">' + captain + '</td><td class="dotted">' + order_time + '</td></tr><tr><td colspan="2" class="dotted"><b>DESCRIPTION</b></td><td class="dotted"><b>QTY</b></td><td class="dotted"><b>RATE</b></td><td class="dotted"><b>VALUE</b></td></tr>' + invoice_items + '<tr><td colspan="2"></td><td class="dotted-top"><b>' + total_order_items + '</b></td><td></td><td></td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>Sub Total:</td><td>&nbsp;</td><td class="dotted-top">' + invoice_total + '</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td colspan="2">Discount(' + dicount_percent + '%)</td><td>' + ((invoice_total * dicount_percent) / 100) + '</td></tr>' + branch_specific_taxes + '<!--<tr><td>cashier</td><td>Mataji</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>--><tr><td>&nbsp;</td><td>&nbsp;</td><td><b>Total:</b></td><td>&nbsp;</td><td class="dotted-top" style="font-size:15px;height:auto;"><b>' + grand_total + '</b></td></tr></tbody></table>' + service_tax_number_html + other_number_html + '');




                    popupWin.document.close();

                    // reset everything
                    //$scope.waiter_id = '';
                    //$scope.waiter_list.selected = '';
                    //$scope.product_list.selected = '';
                    //$scope.products = [];

                    $scope.selected_table_id = angular.element('#table_id').val();

                    angular.forEach($scope.data, function(value, key) {

                        // console.log(value);

                        if ($scope.selected_table_id == value.table_detail_id) {
                            value.order_id = '';
                            value.order_code = '';
                            value.total_amount = '';
                        }

                    });
                    //angular.element("#bill_number").html("");
                    //angular.element("#table_number").html("<b> -- </b>");
                    //angular.element('#table_id').val("");
                    //angular.element("#no_of_person").val("");

                    angular.element("#table_list tr").css('background-color', '#FFF');

                    // chnage color of selected table

                    //angular.element("#bill_div").css({'pointer-events':'none','opacity':0.6});

                });

            }
            // else
            // {

            //   angular.element("#printButtonTbl").attr('disabled','disabled');
            //   $window.alert('Please Connect to Internet print');
            //   return false;
            // }

            //$rootScope.$emit("CallParcelGetNextBill");
            //$rootScope.$emit("CallDeliveryGetNextBill");
            //});


            //angular.element("#printButtonTbl").attr('disabled','disabled');

        }

    }

}]);

app.controller('orderCtrl', ["$scope", "$http", "$state", "$modal", "$window", "$rootScope", "hotkeys", function($scope, $http, $state, $modal, $window, $rootScope, hotkeys) {

    //$scope.roundtest = Math.round(23.336);

    $scope.show_tax_1 = false;
    $scope.show_tax_2 = false;
    $scope.product_list = [];
    $scope.order_id = '';

    $scope.cur_order_amt = 0;
    var live_amount = function() {
        var amount;
        var request = $http({
            method: "post",
            url: "index.php/order/live_table_total_amount",
            data: {},
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });
        request.success(function(response) {
            amount = response.live_amount;
            $scope.cur_order_amt = amount;

            console.log(amount);

        });

        return amount;
    }
    live_amount();
    //$scope.cur_order_amt = live_amount();

    // $scope.adminBranch = false;



    $scope.testnamefun = function(event) {
        if ($event.keyCode === 80) {
            console.log('testnamefun' + event.keyCode);
        }

    }


    $scope.print_pdf = function() {

        var array = [];
        var headers = [];
        $('#table_list th').each(function(index, item) {
            headers[index] = $(item).html();
        });
        $('#table_list tr').has('td').each(function() {
            var arrayItem = {};
            $('td', $(this)).each(function(index, item) {
                arrayItem[headers[index]] = $(item).html();
            });
            array.push(arrayItem);
        });


        console.log(array[0]);
        //return false;



        // var request = $http({
        //          method: "post",
        //          url: "index.php/branch/getLoggedInBranchDetails",
        //          data: {},
        //          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        //      });

        //  request.success(function (response) {
        //    if(response.data.branch_type == 1)
        //    {
        //      $scope.adminBranch=true;
        //    }

        //            $scope.branch_list = response.branch_list;

        //      });


        var docDefinition = {
            content: [{
                // table: {
                //   // headers are automatically repeated if the table spans over multiple pages
                //   // you can declare how many rows should be treated as headers
                //   headerRows: 1,
                //   widths: [ '*', 'auto', 100, '*' ],

                //   body: [
                //     [ 'First', 'Second', 'Third', 'The last one' ],
                //     [ 'Value 1', 'Value 2', 'Value 3', 'Value 4' ],
                //     array[0],
                //     [ { text: 'Bold value', bold: true }, 'Val 2', 'Val 3', 'Val 4' ]
                //   ]
                // }
                style: 'tableExample',
                table: {
                    body: [
                        ['Column 1', 'Column 2', 'Column 3'],
                        [{
                                stack: [
                                    'Let\'s try an unordered list',
                                    {
                                        ul: [
                                            'item 1',
                                            'item 2'
                                        ]
                                    }
                                ]
                            },
                            [
                                'or a nested table',
                                {
                                    table: {
                                        body: [
                                            ['Col1', 'Col2', 'Col3'],
                                            ['1', '2', '3'],
                                            ['1', '2', '3']
                                        ]
                                    },
                                }
                            ],
                            {
                                text: [
                                    'Inlines can be ',
                                    {
                                        text: 'styled\n',
                                        italics: true
                                    },
                                    {
                                        text: 'easily as everywhere else',
                                        fontSize: 10
                                    }
                                ]
                            }
                        ]
                    ]
                }

            }]
        };

        // open the PDF in a new window
        pdfMake.createPdf(docDefinition).open();
        // print the PDF (temporarily Chrome-only)
        pdfMake.createPdf(docDefinition).print();
        // download the PDF (temporarily Chrome-only)
        pdfMake.createPdf(docDefinition).download('optionalName.pdf');

    }



    // angular.element("#table_number").focus();

    $scope.dailyIncome = 0;
    var request = $http({
        method: "post",
        url: "index.php/order/get_daily_income_of_branch",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });
    request.success(function(response) {
        $scope.dailyIncome = response.dailyIncome;
    });


    var discount_type_list = [{
        key: "0",
        value: "None"
    }, {
        key: "1",
        value: "Complementary"
    }, {
        key: "2",
        value: "Discount Percentage"
    }, {
        key: "3",
        value: "Discount Amount"
    }];
    $scope.discount_type_list = discount_type_list;


    //ar index2 = $scope.discount_type_list.map(function(e) { return e.key; }).indexOf(response.data.is_active);

    $scope.discount_type = $scope.discount_type_list[0];

    /*var payment_type_list = [{
        key: "1",
        value: "Cash"
    }, {
        key: "2",
        value: "Credit Card"
    }, {
        key: "3",
        value: "Debit Card"
    }];
    $scope.payment_type_list = payment_type_list;*/

    var request = $http({
                    method: "post",
                    url: "index.php/payment/getPaymentTypeForOrder",
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                });
                request.success(function(response) {
                    //console.log(response.payment_list);
                    $scope.payment_type_list = response.payment_list;
                    $scope.payment_type = response.payment_list[0];
                });
    


    // for use of parseFloat in expression
    $scope.parseFloat = parseFloat;

    $scope.products = [];

    // get table list by branch
    var request = $http({
        method: "post",
        url: "index.php/table/table_list_by_branch",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });
    request.success(function(response) {



        //$scope.data = response.data;

        // $scope.table_list = response.table_list;


        // angular.forEach(response.data, function(value, key) {

        //           value.order_id = $scope.order_id;
        //           value.total_amount = $scope.finalTotal();                                     

        //     });
        $scope.data = response.data;
        // console.log('**********');
        // console.log($scope.data);
        //  console.log('**********');

    });

    // get recent orders list 
    var request = $http({
        method: "post",
        url: "index.php/order/recent_orders",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });
    request.success(function(response) {

        $scope.recent_order = response.data;
    });

    // get waiter list by logged in branch
    var request = $http({
        method: "post",
        url: "index.php/waiter/waiter_list_by_branch",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });
    request.success(function(response) {

        $scope.waiter_list = response.data;
        //console.log($scope.waiter_list);                         
    });

    // get products
    var request = $http({
        method: "post",
        url: "index.php/product/product_list_by_branch",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });
    request.success(function(response) {

        if (response.status == "1") {
            if (response.data) {
                $scope.product_list = response.data;
            }
        }


    });

    // get tax_names
    var request = $http({
        method: "post",
        url: "index.php/tax/tax_by_branch",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });
    request.success(function(response) {
        //console.log(response.data[0].tax_name);
        if (response.data[0]) {
            $scope.tax_1 = response.data[0].tax_name;
            $scope.show_tax_1 = true;
            $scope.tax_1_tax_id = response.data[0].tax_id;
        }
        if (response.data[1]) {
            $scope.tax_2 = response.data[1].tax_name;
            $scope.show_tax_2 = true;
            $scope.tax_2_tax_id = response.data[1].tax_id;
        }


    });

    // get branch name
    var request = $http({
        method: "post",
        url: "index.php/branch/getLoggedInBranchDetails",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });
    request.success(function(response) {
        // console.log(response.data.name);
        $scope.branch_name = response.data.name;
        $scope.branch_address = response.data.address;
        angular.element("#branch_name").val(response.data.name);
    });


    // get brand list
    var request = $http({
        method: "post",
        url: "index.php/branch/get_brand_list_by_branch",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });
    request.success(function(response) {

        //console.log(response.data);

        if (response.data) {
            $scope.brand_list = response.data;
        }



    });

    $scope.printTableInvoiceByBrand = function(brand_id) {
        if ($rootScope.online == true) {
            // if($scope.is_bill_saved==true)
            // {
            //   $scope.saveBtnDisabled = true;
            // }

            var order_id = $scope.order_id;
            var order_code = $scope.order_code;
            $scope.printOrderCode = $scope.order_code;



            var request = $http({
                method: "post",
                url: "index.php/order/get_details_to_print_invoive_brand",
                data: 'order_id=' + $scope.order_id + '&brand_id=' + brand_id,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {

                $scope.branch_specific_taxes = '';
                $scope.dicount_percent = 0;
                var service_tax_number_html = '';
                var other_number_html = '';


                // if(response.latest_printed_bill==response.data.order_code)
                // {



                if (response.data) {
                    $scope.captain = response.data.waiter_name;
                    // $scope.covers = response.data.max_capacity;
                    $scope.covers = response.data.number_of_person;
                    $scope.order_date = response.data.order_date;
                    $scope.order_time = response.data.order_time;
                    $scope.table_number = response.data.table_number;
                    $scope.invoice_items = response.invoice_items;
                    $scope.invoice_total = response.invoice_total;
                    $scope.dicount_percent = response.data.discount_amount;

                    $scope.printOrderCode = response.data.order_code;

                    if (response.branch_specific_taxes) {
                        $scope.branch_specific_taxes = response.branch_specific_taxes;
                    }

                    if (response.branch_details) {
                        $scope.service_tax_number = response.branch_details.service_tax_number;
                        if ($scope.service_tax_number != '' && $scope.service_tax_number != null) {
                            service_tax_number_html = '<br>Service Code – 996331  CIN.No : ' + $scope.service_tax_number;
                        }
                        scope.other_number = response.branch_details.other_number;
                        if ($scope.other_number != '' && $scope.other_number != null) {
                            other_number_html = '<br>No Reverse Charge GSTIN : ' + $scope.other_number;
                        }
                    }

                    $scope.grand_total = response.grand_total;
                }

                var total_order_items = '';

                if (response.total_order_items) {
                    total_order_items = response.total_order_items;
                }

                var dicount_percent = 0;
                if ($scope.dicount_percent != 0) {
                    dicount_percent = $scope.dicount_percent;
                }


                var branch_name = $scope.branch_name;
                var branch_address = $scope.branch_address;
                var captain = $scope.captain;
                var covers = $scope.covers;
                var order_date = $scope.order_date;
                var order_time = $scope.order_time;
                var table_number = $scope.table_number;
                var invoice_items = $scope.invoice_items;
                var invoice_total = $scope.invoice_total;
                var branch_specific_taxes = '';
                if (($scope.branch_specific_taxes != "") && (typeof $scope.branch_specific_taxes != 'undefined') && ($scope.branch_specific_taxes != 'undefined') && ($scope.branch_specific_taxes != null)) {
                    branch_specific_taxes = $scope.branch_specific_taxes;
                }

                var grand_total = $scope.grand_total;

                var brand_name = response.brand_name;


                //success


                var popupWin = window.open('', '_blank', 'width=300,height=300');
                popupWin.document.open();


                popupWin.document.write('<style type="text/css"> table.tableizer-table{font-size: 14px; border: none; font-family: Arial, Helvetica, sans-serif;}.tableizer-table td{padding: 4px; margin: 3px; border: none; font-weight: 600;}.tableizer-table th{font-weight: bold;}.dotted{border-bottom-style: dotted !important; border-width: 2px;}.dotted-top{border-bottom-style: dotted !important; border-top-style: dotted !important; border-width: 2px;}</style><table class="tableizer-table"> <thead> <tr class="tableizer-firstrow"> <th></th> <th>&nbsp;</th> <th colspan="2">' + branch_name + '</th> <th>&nbsp;</th> </tr></thead> <tbody> <tr> <td>&nbsp;</td><td>&nbsp;</td><td colspan="3"> Brand ' + brand_name + '</td></tr><tr> <td colspan="2" class="dotted"><b>DESCRIPTION</b></td><td class="dotted"><b>QTY</b></td><td class="dotted"><b>RATE</b></td><td class="dotted"><b>VALUE</b></td></tr>' + invoice_items + ' <tr> <td colspan="2"></td><td class="dotted-top"><b>' + total_order_items + '</b></td><td></td><td></td></tr><tr> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr> <td>&nbsp;</td><td>&nbsp;</td></tr></tbody></table>');


                popupWin.document.close();




                // }
                // else
                // {
                //   $scope.saveBtnDisabled = false;
                // }

            });


        }
    }

    $scope.printInvoiceAllBrands = function() {

        // console.log('here');
        // return false;
        if ($rootScope.online == true) {
            // if($scope.is_bill_saved==true)
            // {
            //   $scope.saveBtnDisabled = true;
            // }

            var order_id = $scope.order_id;
            var order_code = $scope.order_code;
            $scope.printOrderCode = $scope.order_code;



            var request = $http({
                method: "post",
                url: "index.php/order/get_details_to_print_invoive_all_brand",
                data: 'order_id=' + $scope.order_id,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {

                //console.log(response);return false;

                $scope.branch_specific_taxes = '';
                $scope.dicount_percent = 0;
                var service_tax_number_html = '';
                var other_number_html = '';

                // if(response.latest_printed_bill==response.data.order_code)
                // {



                if (response.data) {
                    $scope.captain = response.data.waiter_name;
                    // $scope.covers = response.data.max_capacity;
                    $scope.covers = response.data.number_of_person;
                    $scope.order_date = response.data.order_date;
                    $scope.order_time = response.data.order_time;

                    $scope.table_number = response.data.table_number;
                    $scope.invoice_items = response.invoice_items;
                    $scope.invoice_total = response.invoice_total;
                    $scope.dicount_percent = response.data.discount_amount;

                    //console.log(response.data);
                    //console.log(response.order_date_time);

                    $scope.printOrderCode = response.data.order_code;

                    if (response.branch_specific_taxes) {
                        $scope.branch_specific_taxes = response.branch_specific_taxes;
                    }

                    if (response.branch_details) {
                        $scope.service_tax_number = response.branch_details.service_tax_number;
                        if ($scope.service_tax_number != '' && $scope.service_tax_number != null) {
                            service_tax_number_html = '<br>Service Code – 996331  CIN.No : ' + $scope.service_tax_number;
                        }
                        if ($scope.other_number != '' && $scope.other_number != null) {
                            other_number_html = '<br>No Reverse Charge GSTIN : ' + $scope.other_number;
                        }
                    }

                    $scope.grand_total = response.grand_total;
                }

                var total_order_items = '';

                if (response.total_order_items) {
                    total_order_items = response.total_order_items;
                }

                var dicount_percent = 0;
                if ($scope.dicount_percent != 0) {
                    dicount_percent = $scope.dicount_percent;
                }


                var branch_name = $scope.branch_name;
                var branch_address = $scope.branch_address;
                var captain = $scope.captain;
                var covers = $scope.covers;
                var order_date = $scope.order_date;
                var order_time = $scope.order_time;

                var table_number = $scope.table_number;
                var invoice_items = $scope.invoice_items;
                var invoice_total = $scope.invoice_total;
                var branch_specific_taxes = '';
                if (($scope.branch_specific_taxes != "") && (typeof $scope.branch_specific_taxes != 'undefined') && ($scope.branch_specific_taxes != 'undefined') && ($scope.branch_specific_taxes != null)) {
                    branch_specific_taxes = $scope.branch_specific_taxes;
                }

                var grand_total = $scope.grand_total;

                var brand_name = response.brand_name;


                //success


                var popupWin = window.open('', '_blank', 'width=300,height=300');
                popupWin.document.open();


                popupWin.document.write('<style type="text/css">table.tableizer-table{min-width:250px;font-size: 12px; font-family: Arial, Helvetica, sans-serif;text-transform: uppercase;}.tableizer-table td{padding: 2px 0px;margin: 0px;vertical-align: initial;}.dotted{border-bottom-style: dashed !important; border-width:1px;}.dotted-top{border-bottom-style: dashed !important;border-top-style: dashed !important;border-width:1px;}.dotted-top-only{border-top-style: dashed !important;border-width:1px;}.title{ margin-right:15px;}</style><table class="tableizer-table"> <thead> <tr class="tableizer-firstrow"> <th colspan="5" style="text-align:center"><b>' + branch_name + '</b></th> </tr> </thead> <tbody> ' + invoice_items + ' </tbody></table>');


                popupWin.document.close();


                // }
                // else
                // {
                //   $scope.saveBtnDisabled = false;
                // }

            });


        }
    }

    $scope.printTableInvoiceByBrandnew = function(brand_id) {
        if ($rootScope.online == true) {
            // if($scope.is_bill_saved==true)
            // {
            //   $scope.saveBtnDisabled = true;
            // }

            var order_id = $scope.order_id;
            var order_code = $scope.order_code;
            $scope.printOrderCode = $scope.order_code;



            var request = $http({
                method: "post",
                url: "index.php/order/get_details_to_print_invoive_brand_new",
                data: 'order_id=' + $scope.order_id + '&brand_id=' + brand_id,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {

                $scope.branch_specific_taxes = '';
                $scope.dicount_percent = 0;
                var service_tax_number_html = '';
                var other_number_html = '';

                // if(response.latest_printed_bill==response.data.order_code)
                // {

                if (response.data) {
                    $scope.captain = response.data.waiter_name;
                    // $scope.covers = response.data.max_capacity;
                    $scope.covers = response.data.number_of_person;
                    $scope.order_date = response.data.order_date;
                    $scope.order_time = response.data.order_time;
                    $scope.table_number = response.data.table_number;
                    $scope.invoice_items = response.invoice_items;
                    $scope.invoice_total = response.invoice_total;
                    $scope.dicount_percent = response.data.discount_amount;

                    $scope.printOrderCode = response.data.order_code;

                    if (response.branch_specific_taxes) {
                        $scope.branch_specific_taxes = response.branch_specific_taxes;
                    }

                    if (response.branch_details) {
                        $scope.service_tax_number = response.branch_details.service_tax_number;
                        if ($scope.service_tax_number != '' && $scope.service_tax_number != null) {
                            service_tax_number_html = '<br>Service Code – 996331  CIN NO : ' + $scope.service_tax_number;
                        }
                        $scope.other_number = response.branch_details.other_number;
                        if ($scope.other_number != '' && $scope.other_number != null) {
                            other_number_html = '<br>No Reverse Charge GSTIN : ' + $scope.other_number;
                        }
                    }

                    $scope.grand_total = response.grand_total;
                }

                var total_order_items = '';

                if (response.total_order_items) {
                    total_order_items = response.total_order_items;
                }

                var dicount_percent = 0;
                if ($scope.dicount_percent != 0) {
                    dicount_percent = $scope.dicount_percent;
                }

                var branch_name = $scope.branch_name;
                var branch_address = $scope.branch_address;
                var captain = $scope.captain;
                var covers = $scope.covers;
                var order_date = $scope.order_date;
                var order_time = $scope.order_time;
                var table_number = $scope.table_number;
                var invoice_items = $scope.invoice_items;
                var invoice_total = $scope.invoice_total;
                var branch_specific_taxes = '';
                if (($scope.branch_specific_taxes != "") && (typeof $scope.branch_specific_taxes != 'undefined') && ($scope.branch_specific_taxes != 'undefined') && ($scope.branch_specific_taxes != null)) {
                    branch_specific_taxes = $scope.branch_specific_taxes;
                }

                var grand_total = $scope.grand_total;

                var brand_name = response.brand_name;

                //success

                var popupWin = window.open('', '_blank', 'width=300,height=300');
                popupWin.document.open();


                popupWin.document.write('<style type="text/css"> table.tableizer-table{font-size: 14px; border: none; font-family: Arial, Helvetica, sans-serif;}.tableizer-table td{padding: 4px; margin: 3px; border: none; font-weight: 600;}.tableizer-table th{font-weight: bold;}.dotted{border-bottom-style: dotted !important; border-width: 2px;}.dotted-top{border-bottom-style: dotted !important; border-top-style: dotted !important; border-width: 2px;}</style><table class="tableizer-table"> <thead> <tr class="tableizer-firstrow"> <th></th> <th>&nbsp;</th> <th colspan="2">' + branch_name + '</th> <th>&nbsp;</th> </tr></thead> <tbody> <tr> <td>&nbsp;</td><td>&nbsp;</td><td colspan="3"> Brand ' + brand_name + '</td></tr><tr> <td colspan="2" class="dotted"><b>DESCRIPTION</b></td><td class="dotted"><b>QTY</b></td><td class="dotted"><b>RATE</b></td><td class="dotted"><b>VALUE</b></td></tr>' + invoice_items + ' <tr> <td colspan="2"></td><td class="dotted-top"><b>' + total_order_items + '</b></td><td></td><td></td></tr><tr> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr> <td>&nbsp;</td><td>&nbsp;</td></tr></tbody></table>');


                popupWin.document.close();

            });

        }
    }

    $scope.change_table = function() {
        /*console.log("oid "+$scope.order_id);
        console.log("old_table_id "+$scope.table_id);
        console.log("new_table_id "+$scope.change_table_number);*/

        var request = $http({
            method: "post",
            url: "index.php/order/change_table",
            data: 'order_id=' + $scope.order_id + '&new_table_id=' + $scope.change_table_number,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });

        request.success(function(response) {
            //console.log(response);
            if (response.result == 1) {
                //console.log("Done.");
                $window.location.reload();
            } else if (response.result == 2) {
                alert("Please Enter Table Number.");
                angular.element('#change_table_number').focus();
            } else {
                // return 0
                alert("Table number " + $scope.change_table_number + " is not empty.");
            }
        });
    }

    // removed event- the first elem of function ,no_of_person b4 table_detail
    $scope.chnageTable = function(table_id, table_number, table_detail) {

        // get table_bill_id
        // console.log(table_detail);
         // console.log('table_detail');
         // console.log(table_detail);
         // console.log(table_id);
         // console.log(table_number);


        $scope.saveBtnDisabled = false;

        // table_list
        angular.element("#table_list tr").css('background-color', '#FFF');

        // chnage color of selected table
        // $(event.target).parent().css('background-color','#E7E7E9');
        angular.element("#table_row_" + table_number).css('background-color', '#E7E7E9');

        angular.element("#bill_div").css({
            'pointer-events': '',
            'opacity': 1
        });

        if ((table_detail.order_id != "") && (typeof table_detail.order_id != 'undefined') && (table_detail.order_id != 'undefined') && (table_detail.order_id != null)) {
            // angular.element('#bill_number').html(table_detail.order_code);
            //angular.element('#table_number').html(table_number);
            angular.element('#table_number').val(table_number);

            angular.element('#table_id').val(table_id);
            // console.log('table_id'+table_id);

            //$scope.no_of_person = no_of_person;
            $scope.order_id = table_detail.order_id;
            $scope.order_code = table_detail.order_code;

            // find out waiter by bill 
            var request = $http({
                method: "post",
                url: "index.php/order/get_order_details_by_id",
                data: 'order_id=' + $scope.order_id,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {

                //console.log(response);

                // make waiter selected

                if (response.order) {
                    $scope.no_of_person = response.order.number_of_person;

                    angular.forEach($scope.waiter_list, function(value, key) {

                        if (value.waiter_id == response.order.waiter_id) {
                            $scope.waiter_list.selected = value;
                        }

                    });

                    $scope.products = response.order_items;

                    $scope.discount_type = $scope.discount_type_list[response.order.discount_type];

                    $scope.discount_amount = response.order.discount_amount;

                    // console.log(response.order.payment_type);
                    $scope.discount_amount_dummy = response.order.discount_amount;
                    $scope.note = response.order.notes;

                    $scope.payment_type = $scope.payment_type_list[parseInt(response.order.payment_type) - 1];

                    $scope.payment_card_number = response.order.payment_card_number;

                }




            });

        } else {

            // first make the whole form-calculation empty
            $scope.waiter_id = '';
            $scope.waiter_list.selected = '';
            $scope.product_list.selected = '';
            $scope.products = [];
            $scope.discount_type ='';
            //$scope.product_qty = '';

            // get bill number
            // var request = $http({
            //               method: "post",
            //               url: "index.php/order/get_next_bill_id",
            //               data: {},
            //               headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            //           });
            //       request.success(function (response) {                        
            //                   angular.element('#bill_number').html(response.next_bill_id);

            //                   angular.element('#table_number').val(table_number);

            //                   $scope.table_number = table_number;

            //                   angular.element('#table_id').val(table_id);

            //                   $scope.order_id = '';
            //                   $scope.order_code = response.next_bill_id;

            //                   $scope.no_of_person = '';
            //           });

            angular.element('#table_number').val(table_number);
            $scope.table_number = table_number;
            angular.element('#table_id').val(table_id);
            $scope.order_id = '';
            $scope.order_code = '';
            $scope.no_of_person = '';
            //console.log($scope.table_number);
            // console.log('form err:');
            // console.log(+$scope.order_form.$error);
        }
    };


    $scope.counter = 1;
    
    $scope.addProduct = function() {

        if ($rootScope.online == true) {
            if ($scope.order_form.$valid) {

                // get selected product from drop down
                var selected_products = $scope.product_list.selected;
                var selected_product_id = '';
                selected_product_id = selected_products.product_id;

                // console.log($scope.products);
                // console.log(selected_products);

                var product_id_arr = [];
                angular.forEach($scope.products, function(value, key) {

                    if (product_id_arr.indexOf(value.product_id) === -1) {
                        product_id_arr.push(value.product_id);
                    }
                    if (value.product_id == selected_products.product_id) {
                        selected_products = value;
                    }

                });

                //console.log(product_id_arr);

                if (false && product_id_arr.indexOf(selected_product_id) !== -1) {
                    // a is NOT in array1
                    //array1.push(a);
                    // console.log('YO');

                    var modalInstance = $modal.open({
                        templateUrl: 'myModalContent.html',
                        controller: 'ModalInstanceCtrl',
                        size: 'sm',
                        resolve: {
                            product_id: function() {
                                return selected_produsct_id;
                            },
                            selected_products: function() {
                                return selected_products;
                            },
                            quantity: function() {
                                return $scope.product_qty;
                            }
                        }
                    });

                    modalInstance.result.then(function() {

                        //$scope.bool_for_order_update = bool_for_order_update;                      
                        insertIntoDb();
                    });


                } else {

                    selected_products.quantity = $scope.product_qty;

                    insertIntoDb();

                }

                //console.log(selected_products);

                function insertIntoDb() {
                    // add the quantity inputted by user in the selected product
                    selected_products.total = $scope.getTotal(selected_products);

                    //console.debug('before');

                    //console.log($scope.products);
                    if (true || $scope.products.indexOf(selected_products) === -1) {
                        //$scope.products.push(selected_products);

                        var temp = selected_products;
                        delete temp.$$hashKey;
                        $scope.products.push(temp);


                    }   

                    $scope.product_qty = '';


                    $scope.waiter_id = $scope.waiter_list.selected.waiter_id;


                    // insert into live table        

                    var param = $("[name='order_form']").serialize();
                    //console.log(param);

                    var param_tax = $scope.service_tax_total() + $scope.other_tax_total();
                    //var param_final_amount = $scope.finalTotal();
                    var param_final_amount = $scope.finalOrderTotal();

                    // $scope.given_amount = $scope.finalOrderTotal();

                    // console.log('GIVEN:'+$scope.given_amount);

                    var param_return_amount = Math.round(($scope.given_amount == null ? 0 : $scope.given_amount) - $scope.finalTotal() - $scope.branchSpecificTotal());

                    // var discount_type = $scope.discount_type.key;
                    // var discount_amount = $scope.discount_amount;

                    $scope.tax_arr = [];


                    angular.forEach($scope.branchSpecificTax_list, function(value, key) {

                        var obj = {};
                        obj.tax_id = value.tax_id;
                        obj.tax_percent = value.tax_percent;
                        $scope.tax_arr.push(obj);

                    });

                    if (($scope.tax_1_tax_id != "") && (typeof $scope.tax_1_tax_id != 'undefined') && ($scope.tax_1_tax_id != 'undefined') && ($scope.tax_1_tax_id != null)) {
                        $scope.tax_arr.push({
                            "tax_id": $scope.tax_1_tax_id,
                            "tax_percent": $scope.service_tax_total()
                        });
                    }

                    if (($scope.tax_2_tax_id != "") && (typeof $scope.tax_2_tax_id != 'undefined') && ($scope.tax_2_tax_id != 'undefined') && ($scope.tax_2_tax_id != null)) {
                        $scope.tax_arr.push({
                            "tax_id": $scope.tax_2_tax_id,
                            "tax_percent": $scope.other_tax_total()
                        });
                    }



                    var sub_total = $scope.finalTotal();


                    //console.log(JSON.stringify($scope.tax_arr));

                    var request = $http({
                        method: "post",
                        url: "index.php/order/add_order",
                        data: param + '&order_type=1&' + '&waiter_id=' + $scope.waiter_id + '&total_items=' + $scope.products.length + '&order_code=' + $scope.order_code + '&total_amount=' + param_final_amount + '&return_amount=' + param_return_amount + '&sub_total=' + sub_total + '&order_id=' + $scope.order_id,
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    });
                    request.success(function(response) {
                        if(response.status == 1){
                            // get inserted order_id

                            $scope.order_id = response.order_id;


                            // order items
                            var request = $http({
                                method: "post",
                                url: "index.php/order/add_order_items",
                                data: 'order_id=' + $scope.order_id + '&products=' + JSON.stringify(selected_products),
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                }
                            });

                            request.success(function(response) {
                                // console.log(response);

                                $scope.selected_table_id = angular.element('#table_id').val();
                                $scope.selected_table_number = angular.element('#table_number').val();


                                angular.forEach($scope.data, function(value, key) {

                                    // console.log(value);

                                    if ($scope.selected_table_id == value.table_detail_id) {
                                        value.order_id = $scope.order_id;
                                        value.order_code = $scope.order_code;
                                        //value.total_amount = $scope.finalTotal();
                                        value.total_amount = $scope.finalOrderTotal();
                                    }
                                    $scope.is_bill_saved = true;
                                });

                                setTimeout(function() {
                                    $('#table_row_' + angular.element('#table_number').val()).click();
                                }, 1000);

                            });

                            // order tax

                            //console.log($scope.tax_arr);

                            var request = $http({
                                method: "post",
                                url: "index.php/order/add_order_tax",
                                data: 'order_id=' + $scope.order_id + '&tax_arr=' + JSON.stringify($scope.tax_arr),
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                }
                            });

                            request.success(function(response) {
                                
                            });




                            // $rootScope.$emit("CallParcelGetNextBill");
                            // $rootScope.$emit("CallDeliveryGetNextBill");
                        } else {
                            alert("Error: Item is not inserted. Please try again.");
                        }
                        
                    });
                    //focus back on product list
                    var result = document.getElementById("product_list");
                    var uiSelect = angular.element(result).controller('uiSelect');
                    uiSelect.focusser[0].focus();
                    uiSelect.focus = true;
                    uiSelect.setFocus();
                    uiSelect.open = false;
                }
            }
        }

        // console.log($scope.order_form.$valid);
        // add product iff the form is valid
    }

    $scope.addProduct_olld = function() {

        if ($rootScope.online == true) {
            if ($scope.order_form.$valid) {

                // get selected product from drop down
                var selected_products = $scope.product_list.selected;
                var selected_product_id = '';
                selected_product_id = selected_products.product_id;

                // console.log($scope.products);
                // console.log(selected_products);

                var product_id_arr = [];
                angular.forEach($scope.products, function(value, key) {

                    if (product_id_arr.indexOf(value.product_id) === -1) {
                        product_id_arr.push(value.product_id);
                    }
                    if (value.product_id == selected_products.product_id) {
                        selected_products = value;
                    }

                });

                //console.log(product_id_arr);

                if (product_id_arr.indexOf(selected_product_id) !== -1) {
                    // a is NOT in array1
                    //array1.push(a);
                    // console.log('YO');

                    var modalInstance = $modal.open({
                        templateUrl: 'myModalContent.html',
                        controller: 'ModalInstanceCtrl',
                        size: 'sm',
                        resolve: {
                            product_id: function() {
                                return selected_product_id;
                            },
                            selected_products: function() {
                                return selected_products;
                            },
                            quantity: function() {
                                return $scope.product_qty;
                            }
                        }
                    });

                    modalInstance.result.then(function() {

                        //$scope.bool_for_order_update = bool_for_order_update;                      
                        insertIntoDb();
                    });


                } else {

                    selected_products.quantity = $scope.product_qty;

                    insertIntoDb();

                }

                //console.log(selected_products);

                function insertIntoDb() {
                    // add the quantity inputted by user in the selected product
                    selected_products.total = $scope.getTotal(selected_products);

                    //console.log(JSON.stringify(selected_products));

                    //console.log($scope.products);
                    if (true || $scope.products.indexOf(selected_products) === -1) {
                        //$scope.products.push(selected_products);

                        var temp = selected_products;
                        delete temp.$$hashKey;
                        $scope.products.push(temp);

                        /*var temp={
                         createdd: "2016-10-05 11:47:43",
                          deleted:null,
                          description:"AERATED SOFT DRINK",
                          image:null,
                          name:
                          "AERATED SOFT DRINK",
                          order_id
                          :
                          "18533",
                          order_item_id
                          :
                          "68394",
                          price
                          :
                          "40.00",
                          print_kot
                          :
                          "0",
                          product_category_id
                          :
                          "2",
                          product_code
                          :
                          "57",
                          product_id
                          :
                          "36",
                          quantity
                          :
                          "1",
                          total
                          :
                          40,
                          unit
                          :
                          "4",
                          updated
                          :
                          "2017-02-07 14:41:22"
                        };*/
                        /* $scope.products.push({
                          createdd: "2016-10-05 11:47:43",
                           deleted:null,
                           description:"AERATED SOFT DRINK",
                           image:null,
                           name:
                           "AERATED SOFT DRINK",
                           order_id
                           :
                           "18533",
                           order_item_id
                           :
                           "68394",
                           price
                           :
                           "40.00",
                           print_kot
                           :
                           "0",
                           product_category_id
                           :
                           "2",
                           product_code
                           :
                           "57",
                           product_id
                           :
                           "36",
                           quantity
                           :
                           "1",
                           total
                           :
                           40,
                           unit
                           :
                           "4",
                           updated
                           :
                           "2017-02-07 14:41:22"
                         });*/

                    }
                    //console.debug('after');
                    //console.log($scope.products);
                    //selected_products.total = selected_products.getTotal;         

                    $scope.product_qty = '';


                    $scope.waiter_id = $scope.waiter_list.selected.waiter_id;


                    // insert into live table        

                    var param = $("[name='order_form']").serialize();
                    //console.log(param);

                    var param_tax = $scope.service_tax_total() + $scope.other_tax_total();
                    //var param_final_amount = $scope.finalTotal();
                    var param_final_amount = $scope.finalOrderTotal();

                    // $scope.given_amount = $scope.finalOrderTotal();

                    // console.log('GIVEN:'+$scope.given_amount);

                    var param_return_amount = Math.round(($scope.given_amount == null ? 0 : $scope.given_amount) - $scope.finalTotal() - $scope.branchSpecificTotal());

                    // var discount_type = $scope.discount_type.key;
                    // var discount_amount = $scope.discount_amount;

                    $scope.tax_arr = [];


                    angular.forEach($scope.branchSpecificTax_list, function(value, key) {

                        var obj = {};
                        obj.tax_id = value.tax_id;
                        obj.tax_percent = value.tax_percent;
                        $scope.tax_arr.push(obj);

                    });

                    if (($scope.tax_1_tax_id != "") && (typeof $scope.tax_1_tax_id != 'undefined') && ($scope.tax_1_tax_id != 'undefined') && ($scope.tax_1_tax_id != null)) {
                        $scope.tax_arr.push({
                            "tax_id": $scope.tax_1_tax_id,
                            "tax_percent": $scope.service_tax_total()
                        });
                    }

                    if (($scope.tax_2_tax_id != "") && (typeof $scope.tax_2_tax_id != 'undefined') && ($scope.tax_2_tax_id != 'undefined') && ($scope.tax_2_tax_id != null)) {
                        $scope.tax_arr.push({
                            "tax_id": $scope.tax_2_tax_id,
                            "tax_percent": $scope.other_tax_total()
                        });
                    }



                    var sub_total = $scope.finalTotal();

                    //console.log(JSON.stringify($scope.tax_arr));

                    var request = $http({
                        method: "post",
                        url: "index.php/order/add_order",
                        data: param + '&order_type=1&' + '&waiter_id=' + $scope.waiter_id + '&total_items=' + $scope.products.length + '&order_code=' + $scope.order_code + '&total_amount=' + param_final_amount + '&return_amount=' + param_return_amount + '&sub_total=' + sub_total + '&order_id=' + $scope.order_id,
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    });
                    request.success(function(response) {
                        if(response.status == 1){
                            // get inserted order_id

                            $scope.order_id = response.order_id;


                            // order items
                            var request = $http({
                                method: "post",
                                url: "index.php/order/add_order_items",
                                data: 'order_id=' + $scope.order_id + '&products=' + JSON.stringify(selected_products),
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                }
                            });

                            request.success(function(response) {
                                // console.log(response);

                                $scope.selected_table_id = angular.element('#table_id').val();
                                $scope.selected_table_number = angular.element('#table_number').val();


                                angular.forEach($scope.data, function(value, key) {

                                    // console.log(value);

                                    if ($scope.selected_table_id == value.table_detail_id) {
                                        value.order_id = $scope.order_id;
                                        value.order_code = $scope.order_code;
                                        //value.total_amount = $scope.finalTotal();
                                        value.total_amount = $scope.finalOrderTotal();
                                    }
                                    $scope.is_bill_saved = true;
                                });

                            });

                            setTimeout(function() {
                                $('#table_row_' + angular.element('#table_number').val()).click();
                            }, 400);

                            // order tax

                            //console.log($scope.tax_arr);

                            var request = $http({
                                method: "post",
                                url: "index.php/order/add_order_tax",
                                data: 'order_id=' + $scope.order_id + '&tax_arr=' + JSON.stringify($scope.tax_arr),
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                }
                            });

                            request.success(function(response) {
                                //console.log(response);

                            });


                            // $rootScope.$emit("CallParcelGetNextBill");
                            // $rootScope.$emit("CallDeliveryGetNextBill");
                        } else { 
                            alert("Error: Item is not inserted. Please try again.");
                        }
                        




                    });
                }
            }
        }

        // console.log($scope.order_form.$valid);
        // add product iff the form is valid
    }



    $scope.removeProduct = function(product_id, service_tax_percent, other_tax_percent, productRows) {
       // console.log(product_id);
        //console.log(productRows);
        // $scope.$watch('online', function(){

        //   console.log($rootScope.online);

        //   if($rootScope.online == false)
        // {
        //   $window.alert('Please Connect to Internet');
        //   return false;
        // }
        // else
        // {
        //  console.log(productRows);
        // get the total which should be removed

        // console.log('service_tax_percent'+service_tax_percent);
        //  console.log('other_tax_percent'+other_tax_percent);

        if ($rootScope.online == true) {

            var order_item_id = productRows.order_item_id;

            var tax_arr = [];

            if ((productRows.service_tax_id != "") && (typeof productRows.service_tax_id != 'undefined') && (productRows.service_tax_id != 'undefined') && (productRows.service_tax_id != null)) {
                tax_arr.push({
                    "tax_id": productRows.service_tax_id,
                    "tax_percent": productRows.service_tax_percent
                });
            }
            if ((productRows.other_tax_id != "") && (typeof productRows.other_tax_id != 'undefined') && (productRows.other_tax_id != 'undefined') && (productRows.other_tax_id != null)) {
                tax_arr.push({
                    "tax_id": productRows.other_tax_id,
                    "tax_percent": productRows.other_tax_percent
                });
            }
            //  console.log(tax_arr);


            var tax = parseFloat((service_tax_percent == null) ? 0 : service_tax_percent) + parseFloat((other_tax_percent == null) ? 0 : other_tax_percent);

            var given_amount = ($scope.given_amount == null ? 0 : $scope.given_amount);

            // remove order items from db
            var request = $http({
                method: "post",
                url: "index.php/order/delete_order_items",
                data: 'tax=' + tax + '&given_amount=' + given_amount + '&product_id=' + product_id + '&order_id=' + $scope.order_id + '&order_item_id='+order_item_id+'&tax_arr=' + JSON.stringify(tax_arr),
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {

                // $scope.products.splice( index, 1 );
                // remove row from table
                var index = -1;
                var comArr = eval($scope.products);
                for (var i = 0; i < comArr.length; i++) {
                    if (comArr[i].product_id === product_id) {
                        index = i;
                        break;
                    }
                }
                if (index === -1) {
                    alert("Something gone wrong");
                }
                $scope.products.splice(index, 1);

                $scope.selected_table_id = angular.element('#table_id').val();
                // change - table list details; total_amount
                angular.forEach($scope.data, function(value, key) {

                    // console.log(value);

                    if ($scope.selected_table_id == value.table_detail_id) {
                        value.order_id = $scope.order_id;
                        value.order_code = $scope.order_code;
                        value.total_amount = $scope.finalOrderTotal();
                    }

                    $scope.is_bill_saved = true;

                });

            });
        }
        // }

        //})  
    }

    $scope.quantityChange = function(product_id, quantity, order_item_id) {
        //console.log('product_id:'+product_id+'quantity:'+quantity+'order_id:'+$scope.order_id);

        var param_tax = $scope.service_tax_total() + $scope.other_tax_total();
        //var param_final_amount = $scope.finalTotal();
        var param_final_amount = $scope.finalOrderTotal();
        var param_return_amount = Math.round(($scope.given_amount == null ? 0 : $scope.given_amount) - $scope.finalOrderTotal());

        // update order items
        var request = $http({
            method: "post",
            url: "index.php/order/update_order_items",
            data: 'order_item_id=' + order_item_id + '&tax=' + param_tax + '&total_amount=' + param_final_amount + '&return_amount=' + param_return_amount + '&product_id=' + product_id + '&quantity=' + quantity + '&order_id=' + $scope.order_id,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });

        request.success(function(response) {
            //  console.log(response);

            $scope.selected_table_id = angular.element('#table_id').val();

            angular.forEach($scope.data, function(value, key) {

                // console.log(value);

                if ($scope.selected_table_id == value.table_detail_id) {
                    value.order_id = $scope.order_id;
                    value.order_code = $scope.order_code;
                    value.total_amount = $scope.finalOrderTotal();
                }

                $scope.is_bill_saved = true;

            });


        });
    }

    //Run javascript function when user finishes typing instead of on key up

    $scope.typingTimer; //timer identifier
    $scope.doneTypingInterval = 1000; //time in ms, 5 second for example      

    $scope.changeGivenAmountOnKeyup = function(given_amount) {
        clearTimeout($scope.typingTimer);
        $scope.typingTimer = setTimeout(function() {

            // console.log(given_amount);

            if ((given_amount != "") && (typeof given_amount != 'undefined') && (given_amount != 'undefined') && (given_amount != null)) {
                var param_return_amount = Math.round((given_amount == null ? 0 : given_amount) - $scope.finalOrderTotal());

                var request = $http({
                    method: "post",
                    url: "index.php/order/change_given_amount",
                    data: 'return_amount=' + param_return_amount + '&order_id=' + $scope.order_id + '&given_amount=' + given_amount,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                });

                request.success(function(response) {
                    //  console.log(response);
                });
            }


        }, $scope.doneTypingInterval);
    }


    $scope.changeGivenAmountOnKeydown = function(given_amount) {
        clearTimeout($scope.typingTimer);
    }

    $scope.branchSpecificTax_list = [];
    var request = $http({
        method: "post",
        url: "index.php/tax/branch_specific_tax_list",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });

    request.success(function(response) {
        //  console.log(response);
        $scope.branchSpecificTax_list = response.data;
    });


    $scope.calculateBranchSpecificTax = function(tax_percent) {
        var discount_type;
        if ($scope.discount_type.key == 3) {
            discount_type = 2;
        } else {
            discount_type = $scope.discount_type.key;
        }

        var amt = 0;

        if ((discount_type == "") || (typeof discount_type == 'undefined') || (discount_type == 'undefined') || (discount_type == null)) {
            discount_type = 0;
        }

        if (discount_type == 0) {
            //none
            amt = $scope.finalTotal();
        } else if (discount_type == 1) {
            // complementary
            // total would be without all taxes
            //amt = $scope.totalAmount();
            amt = $scope.totalAmount() - $scope.totalAmount();
        } else {
            if (($scope.discount_amount != "") && (typeof $scope.discount_amount != 'undefined') && ($scope.discount_amount != 'undefined') && ($scope.discount_amount != null)) {
                //amt = $scope.finalTotal()+(($scope.finalTotal()*$scope.discount_amount)/100);
                amt = $scope.finalTotal() - (($scope.finalTotal() * $scope.discount_amount) / 100);
            } else {
                amt = $scope.finalTotal();
            }
        }

        // var amt = $scope.finalTotal()+(($scope.finalTotal()*$scope.discount_amount)/100);

        return ((amt * tax_percent) / 100).toFixed(2);
    }

    // final amount after branch specific tax
    $scope.branchSpecificTotal = function() {

        var total = 0;
        for (var i = 0; i < $scope.branchSpecificTax_list.length; i++) {

            var tax = $scope.branchSpecificTax_list[i];

            var discount_type = $scope.discount_type.key;

            if ((discount_type == "") || (typeof discount_type == 'undefined') || (discount_type == 'undefined') || (discount_type == null)) {
                discount_type = 0;
            }

            if (discount_type == 0) {
                //none
                var finTotal = $scope.finalTotal();
            } else if (discount_type == 1) {
                // complementary
                // total would be without all taxes
                //var finTotal = $scope.totalAmount();
                var finTotal = $scope.totalAmount() - $scope.totalAmount();
            } else {
                if (($scope.discount_amount == "") || (typeof $scope.discount_amount == 'undefined') || ($scope.discount_amount == 'undefined') || ($scope.discount_amount == null)) {
                    $scope.discount_amount = 0;
                }
                var discount_amount = $scope.discount_amount;
                // var finTotal = $scope.finalTotal()+(($scope.finalTotal()*discount_amount)/100);
                var finTotal = $scope.finalTotal() - (($scope.finalTotal() * discount_amount) / 100);
            }

            //total += parseFloat((parseFloat($scope.finalTotal())*parseFloat(tax.tax_percent))/100);  
            total += parseFloat((parseFloat(finTotal) * parseFloat(tax.tax_percent)) / 100);

        }
        //return Math.round(total);
        return total;

    }


    $scope.totalAmount = function() {

        var total = 0;
        for (var i = 0; i < $scope.products.length; i++) {
            var product = $scope.products[i];
            total += (product.quantity * product.price);
        }
        return total;
    }

    $scope.totalDisplayQty = function() {

        var total = 0;
        for (var i = 0; i < $scope.products.length; i++) {
            var product = $scope.products[i];
            //console.log(product);
            total += parseFloat(product.quantity);
        }
        return total;
    }

    $scope.getTotal = function(product) {

        //for(var i = 0; i < $scope.products.length; i++){
        var total = 0;

        var sub_total = product.quantity * product.price;


        //  var product = $scope.products;
        total += (sub_total) + ((sub_total) * (parseFloat((product.service_tax_percent == null) ? 0 : product.service_tax_percent) + parseFloat((product.other_tax_percent == null) ? 0 : product.other_tax_percent))) / 100;



        //}
        return total;
    }

    $scope.finalTotal = function() {
        var final_total = 0;
        for (var i = 0; i < $scope.products.length; i++) {

            var product = $scope.products[i];

            final_total += $scope.getTotal(product);
        }
        return final_total;
    }

    // total of service_tax
    $scope.service_tax_total = function() {
        var service_tax_total = 0;
        for (var i = 0; i < $scope.products.length; i++) {

            var product = $scope.products[i];

            service_tax_total += product.service_tax_percent == null ? 0 : parseFloat(product.service_tax_percent);
        }
        return service_tax_total;
    }

    $scope.other_tax_total = function() {
        var other_tax_total = 0;
        for (var i = 0; i < $scope.products.length; i++) {

            var product = $scope.products[i];

            other_tax_total += product.other_tax_percent == null ? 0 : parseFloat(product.other_tax_percent);
        }
        return other_tax_total;
    }

    $scope.round_off_finalOrderTotal = function(total) {
        //$scope.given_amount = Math.round($scope.finalOrderTotal());
        return Math.round($scope.finalOrderTotal());
    }

    $scope.finalOrderTotal = function() {
        // console.log($scope.discount_type);
        var discount_type;
        if ($scope.discount_type.key == 3) {
            discount_type = 2;
        } else {
            discount_type = $scope.discount_type.key;
        }


        var finalOrderTotal = 0;

        if ((discount_type == "") || (typeof discount_type == 'undefined') || (discount_type == 'undefined') || (discount_type == null)) {
            discount_type = 0;
        }

        if (discount_type == 0) {
            //none
            finalOrderTotal = $scope.finalTotal() + $scope.branchSpecificTotal();
        } else if (discount_type == 1) {
            // complementary
            // total would be without all taxes
            //finalOrderTotal = $scope.totalAmount();
            finalOrderTotal = $scope.totalAmount() - $scope.totalAmount();
        } else {
            if (($scope.discount_amount == "") || (typeof $scope.discount_amount == 'undefined') || ($scope.discount_amount == 'undefined') || ($scope.discount_amount == null)) {
                $scope.discount_amount = 0;
            }
            var discount_amount_dummy = $scope.discount_amount_dummy;
            var temp_total = $scope.finalTotal();
            var discount_amount = $scope.discount_amount;


            if ($scope.discount_type.key == 3) {
                if ((discount_amount_dummy == "") || (typeof discount_amount_dummy == 'undefined') || (discount_amount_dummy == 'undefined') || (discount_amount_dummy == null)) {
                    discount_amount_dummy = 0;
                }
                if ((discount_amount == "") || (typeof discount_amount == 'undefined') || (discount_amount == 'undefined') || (discount_amount == null)) {
                    discount_amount = 0;
                }

                discount_amount = (discount_amount_dummy * 100) / temp_total;


                temp_total = temp_total - discount_amount_dummy;
                temp_total
                $scope.discount_amount = discount_amount;

            } else {
                discount_amount = discount_amount_dummy;
                $scope.discount_amount = discount_amount;

            }
            //finalOrderTotal =  ($scope.finalTotal()+($scope.finalTotal()*$scope.discount_amount/100))+$scope.branchSpecificTotal();
            finalOrderTotal = ($scope.finalTotal() - ($scope.finalTotal() * $scope.discount_amount / 100)) + $scope.branchSpecificTotal();

            // finalOrderTotal = $scope.totalAmount() + 1;
        }

        return finalOrderTotal;

        // console.log(discount_type);
    }

    $scope.discountAmtEle = false;

    $scope.requestDiscounttype = function() {
        //console.log($scope.order_id+':'+$scope.discount_type.key+':'+$scope.discount_amount);
        var discount_type;
        var note = "";
        if ($scope.discount_type.key == 3) {
            discount_type = 2;
        } else {
            discount_type = $scope.discount_type.key;
        }
        if (discount_type == 3 || discount_type == 2) {
            //angular.element("#discount_amount_ele").show();
            $scope.discountAmtEle = true;
            if (($scope.note == "") || (typeof $scope.note == 'undefined') || ($scope.note == 'undefined') || ($scope.note == null)) {
                angular.element("#printButtonTbl").attr('disabled', 'disabled');
                angular.element("#note_error").show();
            } else {
                angular.element("#printButtonTbl").removeAttr('disabled', 'disabled');
                angular.element("#note_error").hide();
                note = $scope.note;
            }
        } else {
            angular.element("#printButtonTbl").removeAttr('disabled', 'disabled');
            angular.element("#note_error").hide();
            $scope.discountAmtEle = false;
            
        }

        if ($rootScope.online == true) {
            var param_final_amount = $scope.finalOrderTotal();
            var param_return_amount = Math.round(($scope.given_amount == null ? 0 : $scope.given_amount) - $scope.finalOrderTotal());

            var request = $http({
                method: "post",
                url: "index.php/order/update_order_discount",
                data: 'order_id=' + $scope.order_id + '&discount_type=' + discount_type + '&discount_amount=' + $scope.discount_amount + '&total_amount=' + param_final_amount + '&return_amount=' + param_return_amount + '&note=' + note,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {

                $scope.selected_table_id = angular.element('#table_id').val();
                // change - table list details; total_amount
                angular.forEach($scope.data, function(value, key) {

                    // console.log(value);

                    if ($scope.selected_table_id == value.table_detail_id) {
                        value.order_id = $scope.order_id;
                        value.order_code = $scope.order_code;
                        value.total_amount = $scope.finalOrderTotal();
                    }

                    $scope.is_bill_saved = true;

                });


            });
        }
    }

    $scope.updatePaymentType = function() {
        //console.log('pay'+$scope.payment_type.key);
        if ($rootScope.online == true) {
            var request = $http({
                method: "post",
                url: "index.php/order/update_order_field",
                data: 'order_id=' + $scope.order_id + '&payment_type=' + $scope.payment_type.key + '&payment_card_number=' + $scope.payment_card_number,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {});
        }
    }

    $scope.payment_card_number_typingTimer; //timer identifier
    $scope.payment_card_number_doneTypingInterval = 1000; //time in ms, 5 second for example    

    $scope.changePayment_card_numberOnKeyup = function(payment_card_number) {
        if ($rootScope.online == true) {
            clearTimeout($scope.payment_card_number_typingTimer);
            $scope.payment_card_number_typingTimer = setTimeout(function() {

                // console.log(given_amount);

                if ((payment_card_number != "") && (typeof payment_card_number != 'undefined') && (payment_card_number != 'undefined') && (payment_card_number != null)) {

                    var request = $http({
                        method: "post",
                        url: "index.php/order/update_order_field",
                        data: 'order_id=' + $scope.parcel_order_id + '&payment_card_number=' + payment_card_number,
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    });

                    request.success(function(response) {
                        //  console.log(response);
                    });
                }


            }, $scope.payment_card_number_doneTypingInterval);
        }
    }


    $scope.changePayment_card_numberOnKeydown = function(payment_card_number) {
        if ($rootScope.online == true) {
            clearTimeout($scope.payment_card_number_typingTimer);
        }
    }


    // $scope.paymentType = function()
    // {
    //     var request = $http({
    //                 method: "post",
    //                 url:  "index.php/order/update_order_discount",
    //                 data: 'order_id='+$scope.order_id+'&discount_type='+$scope.discount_type.key+'&discount_amount='+$scope.discount_amount+'&total_amount='+param_final_amount+'&return_amount='+param_return_amount,
    //                 headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    //           });
    // }


    $scope.placeOrder = function() {
        $scope.is_bill_saved = false;
        // make save btn disable once it is clicked to avoid reentry of order
        $scope.saveBtnDisabled = true;

        // get products count
        // if >= 1 then allow save

        //console.log($scope.products.length);

        if ($scope.products.length > 0) {

            //console.log('placeOrder');

            // insert into main table

            var request = $http({
                method: "post",
                url: "index.php/order/save_order_to_main_table",
                data: 'order_id=' + $scope.order_id,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {

                $scope.selected_table_id = angular.element('#table_id').val();
                $scope.selected_table_number = angular.element('#table_number').val();

                angular.forEach($scope.data, function(value, key) {

                    // console.log(value);

                    if ($scope.selected_table_id == value.table_detail_id) {
                        value.order_id = $scope.order_id;
                        value.order_code = $scope.order_code;
                        //value.total_amount = $scope.finalTotal();   
                        //value.total_amount = finalOrderTotal();   
                        value.total_amount = $scope.finalOrderTotal();
                    }

                    $scope.is_bill_saved = true;

                });



            });

        }
    }

    $scope.countTotalProducts = function() {
        if ($scope.products.length > 0) {
            if ($scope.saveBtnDisabled == true) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    $scope.countTotalProductsLive = function() {
        if ($scope.products.length > 0) {
            return false;
        } else {
            return true;
        }
    }

    $scope.countTotalProducts_printDiv = function() {
        if ($scope.products.length > 0) {
            if ($scope.saveBtnDisabled == true) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }




    $scope.saveOrder = function() {

        if ($scope.products.length > 0) {
            var request = $http({
                method: "post",
                url: "index.php/order/print_order",
                data: 'order_id=' + $scope.order_id,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {});
        }

    }



    // print the bill
    $scope.printInvoice = function() {

        //console.log('out:print');

        // $scope.$watch('online', function(){

        //console.log('in:print');

        if ($rootScope.online == true) {
            if ($scope.is_bill_saved == true) {
                $scope.saveBtnDisabled = true;
            }

            var order_id = $scope.order_id;
            var order_code = $scope.order_code;

            //console.log("Order ID: " + order_id);
            //console.log("order_code : " + order_code);

            $scope.printOrderCode = $scope.order_code;



            var request = $http({
                method: "post",
                url: "index.php/order/get_details_to_print_invoive",
                data: 'order_id=' + $scope.order_id,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {

                $scope.branch_specific_taxes = '';
                $scope.dicount_percent = 0;
                var service_tax_number_html = '';
                var other_number_html = '';
                var branch_contact = '';
                var note = '';
                var city_name = '';
                var pincode = '';

                // if(response.latest_printed_bill==response.data.order_code)
                // {



                if (response.data) {
                    $scope.captain = response.data.waiter_name;
                    // $scope.covers = response.data.max_capacity;
                    $scope.covers = response.data.number_of_person;
                    $scope.order_date = response.data.order_date;
                    $scope.order_time = response.data.order_time;
                    $scope.table_number = response.data.table_number;
                    $scope.note = response.data.notes;
                    if($scope.note != '' && $scope.note != null){
                        note = '<tr> <td colspan="5"><b>NOTE : </b>' + $scope.note + '</td> </tr> <tr><td>&nbsp;</td></tr>';
                    }
                    $scope.invoice_items = response.invoice_items;
                    $scope.invoice_total = response.invoice_total;
                    $scope.dicount_percent = response.data.discount_amount;
                   
                   

                    $scope.printOrderCode = response.data.order_code;
                    $scope.brand_name = response.data.brand_name;
                    $scope.sub_brand_id = response.data.sub_brand_id;

                    if (response.branch_specific_taxes) {
                        $scope.branch_specific_taxes = response.branch_specific_taxes;
                    }

                    if (response.branch_details) {
                        $scope.service_tax_number = response.branch_details.service_tax_number;
                        city_name = response.branch_details.city_name;
                        pincode = response.branch_details.pincode;

                        $scope.branch_contact = response.branch_details.contact;
                        if($scope.branch_contact != '' && $scope.branch_contact != null){
                            branch_contact = '<br> CONTACT: ' + $scope.branch_contact;
                        }
                        if ($scope.service_tax_number != '' && $scope.service_tax_number != null) {
                            service_tax_number_html = 'Service Code – 996331 <br>  CIN No: ' + $scope.service_tax_number;
                        } 
                        $scope.other_number = response.branch_details.other_number;
                        if ($scope.other_number != '' && $scope.other_number != null) {
                            other_number_html = 'GSTIN: ' + $scope.other_number;
                        }
                    }

                    $scope.grand_total = response.grand_total;
                }

                var total_order_items = '';

                if (response.total_order_items) {
                    total_order_items = response.total_order_items;
                }

                


                var branch_name = $scope.branch_name;
                var brand_name = $scope.brand_name;
                var sub_brand_id = $scope.sub_brand_id;
                var branch_address = $scope.branch_address;
                
                var captain = $scope.captain;
                var covers = $scope.covers;
                var order_date = $scope.order_date;
                var order_time = $scope.order_time;
                var table_number = $scope.table_number;
                var invoice_items = $scope.invoice_items;
                var invoice_total = $scope.invoice_total;
                var branch_specific_taxes = '';
                if (($scope.branch_specific_taxes != "") && (typeof $scope.branch_specific_taxes != 'undefined') && ($scope.branch_specific_taxes != 'undefined') && ($scope.branch_specific_taxes != null)) {
                    branch_specific_taxes = $scope.branch_specific_taxes;
                }

                var dicount_percent = 0;
                var discount_column = '';
                if ($scope.dicount_percent != 0) {
                    dicount_percent = $scope.dicount_percent;
                    discount_column = '<tr> <td colspan="4">DISCOUNT @ ' + dicount_percent + '%</td> <td align="right">' + ((invoice_total * dicount_percent) / 100).toFixed(2) + '</td> </tr>';
                }

                var grand_total = $scope.grand_total;

                var request = $http({
                    method: "post",
                    url: "index.php/order/check_for_previous_order",
                    data: 'order_code=' + $scope.printOrderCode + '&brand_id=' + sub_brand_id,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                });

                request.success(function(response) {

                    if (response.status) {
                        if (response.status == "1") {
                            //success


                            var popupWin = window.open('', '_blank', 'width=300,height=600');
                            popupWin.document.open();

                            popupWin.document.write('<style type="text/css"> table.tableizer-table { font-size: 12px; font-family: Verdana, Geneva, Arial, sans-serif; text-transform: uppercase;width:255px; } table.inner-table{border-collapse:collapse;} .tableizer-table td { padding: 2px 0px; margin: 0px; vertical-align: initial; }.bottom-border {border-width:1px;border-bottom : 1px dashed #000 !important;}.top-border {border-width:1px;border-top : 1px dashed #000 !important;} .border { border-width:1px;border-top : 1px dashed #000 !important;border-bottom : 1px dashed #000 !important; }.center{text-align:center;} .title {font-size : 16px;}</style><table class="tableizer-table"> <thead> <tr class="tableizer-firstrow"> <th colspan="5" class="center title">' + branch_name + '</th> </tr> </thead> <tbody> <tr> <td colspan="5" class="center">' + branch_address +'<br>'+ city_name + ' - ' + pincode + branch_contact + '</td> </tr> <tr> <td class="bottom-border center" colspan="5" > RETAIL INVOICE</td> </tr> <tr> <td class="center" colspan="5">' + order_date +' '+ order_time+ '</td> </tr> <tr> <td colspan="5" style="padding:0px;"> <table class="tableizer-table inner-table" style="width:100%"> <tr> <td>BILL: ' + $scope.printOrderCode + '</td> <td align="right">TABLE: ' + table_number + '</td> </tr> <tr> <td>CAPTAIN: ' + captain + '</td> <td align="right">COVERS: ' + covers + '</td> </tr> </table> </td> </tr><tr><td colspan="5" style="padding:0px;"> <table class="tableizer-table inner-table" style="width:100%"><tr><td class="border"><b>ITEM NAME</b></td><td class="border" align="right"><b>QTY</b></td><td class="border" align="right"><b>RATE</b></td><td class="border" align="right" style="padding-left:4px;"><b>AMT</b></td></tr> ' + invoice_items + '</table></td></tr> <tr> <td colspan="4" class="top-border">TOTAL QTY</td> <td class="top-border" align="right">' + total_order_items + '</td> </tr> <tr> <td colspan="4"><b>SUB TOTAL</b></td> <td align="right"><b>' + (invoice_total).toFixed(2) + '</b></td> </tr> '+ discount_column + branch_specific_taxes + ' <tr> <td colspan="4">TOTAL</td> <td style="height:auto;" align="right">' + (grand_total).toFixed(2) + '</td> </tr> <tr> <td colspan="4">ROUND OFF</td> <td style="height:auto;" align="right">' + (Math.round(grand_total) - (grand_total)).toFixed(2) + '</td> </tr> <tr> <td colspan="4" class="border title"><b>GRAND TOTAL</b></td> <td class="border title" style="height:auto;" align="right"><b>' + (Math.round(grand_total)).toFixed(2) + '</b></td> </tr> <tr> <td>&nbsp;</td> </tr>' + note +'<tr> <td colspan="5" align="center">' + service_tax_number_html + ' <br>' + other_number_html + ' <br>NO REVERSE CHARGE</td> </tr> <tr></tr> <tr> <td colspan="5" align="center">**** THANK YOU! VISIT AGAIN ****</td> </tr><tr> <td>&nbsp;</td> </tr> </tbody></table>');


                            popupWin.document.close();



                            //delete order from live table and update is_print as 1

                            var request = $http({
                                method: "post",
                                url: "index.php/order/print_order",
                                data: 'order_id=' + $scope.order_id,
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                }
                            });

                            request.success(function(response) {


                                // reset everything
                                $scope.waiter_id = '';
                                $scope.waiter_list.selected = '';
                                $scope.product_list.selected = '';
                                $scope.products = [];

                                $scope.selected_table_id = angular.element('#table_id').val();

                                angular.forEach($scope.data, function(value, key) {

                                    // console.log(value);

                                    if ($scope.selected_table_id == value.table_detail_id) {
                                        value.order_id = '';
                                        value.order_code = '';
                                        value.total_amount = '';
                                    }

                                });
                                //angular.element("#bill_number").html("");
                                angular.element("#table_number").html("<b> -- </b>");
                                angular.element('#table_id').val("");
                                angular.element("#no_of_person").val("");

                                angular.element("#table_list tr").css('background-color', '#FFF');
                                angular.element("#note").val("");


                                // chnage color of selected table

                                angular.element("#bill_div").css({
                                    'pointer-events': 'none',
                                    'opacity': 0.6
                                });

                                angular.element("#printButtonTbl").attr('disabled', 'disabled');
                                angular.element("#discount_amount").val("0");
                                $scope.discount_type = 0;
                                $scope.discount_amount_dummy = 0;
                                $scope.discount_amount = 0;

                            });
                        } else {
                            alert("Internet connection error : Please try again");
                            angular.element("#printButtonTbl").removeAttr('disabled');
                        }
                    }
                });

                // }
                // else
                // {
                //   $scope.saveBtnDisabled = false;
                // }

            });


        }
        // else
        // {

        //   angular.element("#printButtonTbl").attr('disabled','disabled');
        //   $window.alert('Please Connect to Internet print');
        //   return false;
        // }

        //$rootScope.$emit("CallParcelGetNextBill");
        //$rootScope.$emit("CallDeliveryGetNextBill");
        //});


        //angular.element("#printButtonTbl").attr('disabled','disabled');

    }

    $scope.printBill = function() {

        $scope.saveOrder();
        //$scope.placeOrder();

        if ($scope.is_bill_saved == true) {
            $scope.saveBtnDisabled = true;
        }

        var printContents = angular.element("#product_table_div").html();


        var branch_div = angular.element("#branch_name").val();
        var waiter_div = angular.element("#waiter_div :selected").text();
        var bill_number_div = angular.element("#bill_number").text();
        var table_number_div = angular.element("#table_number").text();

        var total_tax_1_div = angular.element("#total_tax_1_div").text();
        var total_tax_2_div = angular.element("#total_tax_2_div").text();
        var final_amount_div = angular.element("#final_amount_div").text();
        var given_amount_div = angular.element("#given_amount_div").val();
        var return_amount_div = angular.element("#return_amount_div").text();


        var popupWin = window.open('', '_blank', 'width=300,height=300');
        popupWin.document.open();
        popupWin.document.write('<html><head><link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" /></head><body style="margin:20px" onload="window.print()"><br><p><b>Branch : </b> ' + branch_div + '</p><p><b>Bill Number : </b> ' + bill_number_div + '</p><p><b>Table Number : </b> ' + table_number_div + '</p> <p><b> Waiter : </b> ' + waiter_div + '<br><br>' + printContents + '<br><div style="float:right"><p>' + total_tax_1_div + '</p><p>' + total_tax_2_div + '</p><p>' + final_amount_div + '</p><p> Given amount : ' + given_amount_div + '</p><p>' + return_amount_div + '</p></div></body></html>');
        popupWin.document.close();

        // reset everything
        $scope.waiter_id = '';
        $scope.waiter_list.selected = '';
        $scope.product_list.selected = '';
        $scope.products = [];

        $scope.selected_table_id = angular.element('#table_id').val();

        angular.forEach($scope.data, function(value, key) {

            // console.log(value);

            if ($scope.selected_table_id == value.table_detail_id) {
                value.order_id = '';
                value.order_code = '';
                value.total_amount = '';
            }

        });
        angular.element("#bill_number").html("");
        angular.element("#table_number").html("<b> -- </b>");
        angular.element('#table_id').val("");
        angular.element("#no_of_person").val("");

        angular.element("#table_list tr").css('background-color', '#FFF');

        // chnage color of selected table

        angular.element("#bill_div").css({
            'pointer-events': 'none',
            'opacity': 0.6
        });

    }


    $scope.resetOrder = function() {
        // delete order on reset
        if ($rootScope.online == true) {

            var request = $http({
                method: "post",
                url: "index.php/order/reset_order",
                data: 'order_id=' + $scope.order_id,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {

                // reset everything
                $scope.waiter_id = '';
                $scope.waiter_list.selected = '';
                $scope.product_list.selected = '';
                $scope.products = [];

                $scope.selected_table_id = angular.element('#table_id').val();

                angular.forEach($scope.data, function(value, key) {

                    // console.log(value);

                    if ($scope.selected_table_id == value.table_detail_id) {
                        value.order_id = '';
                        value.order_code = '';
                        value.total_amount = '';
                    }

                });
                //angular.element("#bill_number").html("");
                angular.element("#table_number").html("<b> -- </b>");
                angular.element('#table_id').val("");
                angular.element("#no_of_person").val("");

                $scope.table_number = '';

                angular.element("#table_list tr").css('background-color', '#FFF');

                // chnage color of selected table
                // $(event.target).parent().css('background-color','#E7E7E9');
                angular.element("#bill_div").css({
                    'pointer-events': 'none',
                    'opacity': 0.6
                });

                // $timeout(function() {
                //     angular.element('#myselector').triggerHandler('click');
                //   });

            });
        }


    }

    $scope.make_table_row_active_by_table_number = function(table_number) {

        //angular.element("#table_row_"+table_number).css('background-color','red');

        angular.forEach($scope.data, function(value) {

            //console.log(value);

            if (value.table_number == table_number) {
                // console.log('true'+value.max_capacity);
                // $scope.chnageTable(value.table_detail_id,value.table_number,value.max_capacity,value);
                $scope.chnageTable(value.table_detail_id, value.table_number, value);
            }

        });

    }

    $scope.get_recent_order_details = function(table_detail) {
        //console.log(table_detail);
        angular.element("#bill_div").css({
            'pointer-events': 'none',
            'opacity': 0.7
        });

        $scope.order_id = table_detail.order_id;

        // find out waiter by bill 
        var request = $http({
            method: "post",
            url: "index.php/order/get_order_details_by_id",
            data: 'order_id=' + $scope.order_id,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });

        request.success(function(response) {

            //console.log(response);
            //$scope.no_of_person = response.order.max_capacity;
            $scope.no_of_person = response.order.number_of_person;
            $scope.table_number = response.order.table_number;

            //angular.element('#bill_number').html(response.order.order_id);
            //angular.element('#bill_number').html(response.order.order_code);
            angular.element('#table_number').val(response.order.table_number);
            angular.element('#table_id').val(response.order.table_detail_id);


            // make waiter selected

            if (response.order) {

                angular.forEach($scope.waiter_list, function(value, key) {

                    if (value.waiter_id == response.order.waiter_id) {
                        $scope.waiter_list.selected = value;
                    }

                });

                $scope.products = response.order_items;

                $scope.discount_type = $scope.discount_type_list[response.order.discount_type];

                $scope.discount_amount = response.order.discount_amount;

                $scope.payment_type = $scope.payment_type_list[parseInt(response.order.payment_type) - 1];

                $scope.payment_card_number = response.order.payment_card_number;

            }




        });

    }

    $scope.update_order = function() {
        // get number_of_person and waiter by order_id

        var waiter_id = '';
        if ((($scope.waiter_list.selected.waiter_id) != "") && ((typeof $scope.waiter_list.selected.waiter_id) != 'undefined') && (($scope.waiter_list.selected.waiter_id) != 'undefined') && (($scope.waiter_list.selected.waiter_id) != null)) {
            waiter_id = $scope.waiter_list.selected.waiter_id;
        }

        var no_of_person = '';
        if (($scope.no_of_person != "") && (typeof $scope.no_of_person != 'undefined') && ($scope.no_of_person != 'undefined') && ($scope.no_of_person != null)) {
            no_of_person = $scope.no_of_person;
        }

        var request = $http({
            method: "post",
            url: "index.php/order/update_order_field",
            data: 'order_id=' + $scope.order_id + '&waiter_id=' + waiter_id + '&number_of_person=' + $scope.no_of_person,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });
        request.success(function(response) {
            //$scope.dailyIncome = response.dailyIncome;
        });
    }

    //   hotkeys.add({
    //   combo: 'alt+p',
    //   description: 'This one goes to 11',
    //   callback: function() {
    //    // $scope.volume += 1;
    //    console.log('add1');
    //   }
    // });
    if ($rootScope.online == true) {
        hotkeys.bindTo($scope)
            .add({
                combo: 'alt+o',
                description: 'Print Order',
                callback: function() {

                    //console.log('tableorder');

                    $scope.printInvoice();

                }

            });
    }
    //hotkeys.del('alt+p');

    $scope.paymentMethod = function(order_id)
    {
        if ($rootScope.online == true) 
        {
            
            //console.log('order_id'+order_id);
           // var order_id = $scope.order_id;

                var modalInstance = $modal.open({
                        templateUrl: 'myModalContent1.html',
                        controller: 'PaymentModalInstanceCtrl',
                        size: 'sm',
                        resolve: {
                                    order_id: function() 
                                    {
                                        return order_id;
                                    }
                                    // payment_card_number: function()
                                    // {
                                    //     return payment_card_number;
                                    // }
                                   
                                }
                    });

                modalInstance.result.then(function() {

                         
                    //updateOrderField();
                    
                });
        }
    }




  
    


}]);

app.controller('PaymentModalInstanceCtrl', ["$scope", "$http", "$state", "$modalInstance", "$rootScope", "order_id",  function($scope, $http, $state, $modalInstance, $rootScope, order_id) {

    
    /*var recent_payment_type_list = [{
            key: "1",
            value: "Cash"
        }, {
            key: "2",
            value: "Credit Card"
        }, {
            key: "3",
            value: "Debit Card"
        }, {
            key: "4",
            value: "Voucher"
        }];*/
        //console.log(recent_payment_type_list);

    var request = $http({
                    method: "post",
                    url: "index.php/payment/getPaymentTypeForOrder",
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                });
                request.success(function(response) {
                    //console.log(response.payment_list);
                    $scope.recent_payment_type_list = response.payment_list;
                });

    
    //$scope.recent_payment_type_list = recent_payment_type_list;
    ///$scope.recent_payment_type = recent_payment_type_list[0];

    // for use of parseFloat in expression
    $scope.parseFloat = parseFloat;



    $scope.payment_card_number_typingTimer; //timer identifier
    $scope.payment_card_number_doneTypingInterval = 1000; //time in ms, 5 second for example


    $scope.ok = function() {

       
        if ($scope.payment_order_form.$valid) 
        {
            console.log('pay'+$scope.recent_payment_type.key);
            console.log('payment_card_number'+$scope.payment_card_number);
            if ($rootScope.online == true) 
            {
                var request = $http({
                    method: "post",
                    url: "index.php/order/update_order_field",
                    data: 'order_id=' + order_id + '&payment_type=' + $scope.recent_payment_type.key + '&payment_card_number=' + $scope.payment_card_number,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                });
                request.success(function(response) {
                    //$scope.dailyIncome = response.dailyIncome;
                    console.log('success');
                });
            }
        }

        $modalInstance.close();

    };

    $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
    };
}]);

app.controller('ModalInstanceCtrl', ["$scope", "$http", "$state", "product_id", "selected_products", "quantity", "$modalInstance", function($scope, $http, $state, product_id, selected_products, quantity, $modalInstance) {

    $scope.ok = function() {

        // delete product       
        // console.log(selected_products);
        // console.log(product_id);
        // console.log(quantity);

        selected_products.quantity = parseFloat(selected_products.quantity) + parseFloat(quantity);

        $modalInstance.close();

    };

    $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
    };
}]);

app.controller('ParcelModalInstanceCtrl', ["$scope", "$http", "$state", "product_id", "selected_products", "quantity", "$modalInstance", function($scope, $http, $state, product_id, selected_products, quantity, $modalInstance) {

    $scope.ok = function() {

        // delete product       
        // console.log(selected_products);
        // console.log(product_id);
        // console.log(quantity);

        selected_products.quantity = parseFloat(selected_products.quantity) + parseFloat(quantity);

        $modalInstance.close();

    };

    $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
    };
}]);

app.controller('DeliveryModalInstanceCtrl', ["$scope", "$http", "$state", "product_id", "selected_products", "quantity", "$modalInstance", function($scope, $http, $state, product_id, selected_products, quantity, $modalInstance) {

    $scope.ok = function() {

        // delete product       
        // console.log(selected_products);
        // console.log(product_id);
        // console.log(quantity);

        selected_products.quantity = parseFloat(selected_products.quantity) + parseFloat(quantity);

        $modalInstance.close();

    };

    $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
    };
}]);

app.controller('parcelOrderCtrl', ["$scope", "$http", "$state", "$modal", "$window", "$q", "$rootScope", "hotkeys", function($scope, $http, $state, $modal, $window, $q, $rootScope, hotkeys) {


    //   $scope.waiter_id = '';
    // $scope.waiter_list.selected = '';

    // get waiter list by logged in branch
    var request = $http({
        method: "post",
        url: "index.php/waiter/waiter_list_by_branch",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });
    request.success(function(response) {

        $scope.waiter_list = response.data;
        //console.log($scope.waiter_list);                         
    });

    //get brand_list by logged in branch
    var request = $http({
        method: "post",
        url: "index.php/report/branchwise_brand_item_wise_sales",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });
    request.success(function(response) {

        $scope.brand_list_by_branch = response.brand_list_by_branch;
    });


    // $scope.getnextbillParcel = function()
    // {
    //  // console.log('comm');
    //     var request = $http({
    //                   method: "post",
    //                   url: "index.php/order/get_next_bill_id",
    //                   data: {},
    //                   headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    //               });
    //           request.success(function (response) {                        
    //                       angular.element('#parcel_bill_number').html(response.next_bill_id);

    //                       $scope.parcel_order_id = '';

    //                       $scope.parcel_order_code = response.next_bill_id;
    //               });
    // }




    $scope.update_order_parcel = function() {
        // get number_of_person and waiter by order_id

        var waiter_id = '';
        if ((($scope.waiter_list.selected.waiter_id) != "") && ((typeof $scope.waiter_list.selected.waiter_id) != 'undefined') && (($scope.waiter_list.selected.waiter_id) != 'undefined') && (($scope.waiter_list.selected.waiter_id) != null)) {
            waiter_id = $scope.waiter_list.selected.waiter_id;
        }

        /*var brand_id = '';
        if ((($scope.brand_list_by_branch.selected.brand_id) != "") && ((typeof $scope.brand_list_by_branch.selected.brand_id) != 'undefined') && (($scope.brand_list_by_branch.selected.brand_id) != 'undefined') && (($scope.brand_list_by_branch.selected.brand_id) != null)) {
            brand_id = $scope.brand_list_by_branch.selected.brand_id;
        }*/


        var request = $http({
            method: "post",
            url: "index.php/order/update_order_field",
            data: 'order_id=' + $scope.parcel_order_id + '&waiter_id=' + waiter_id,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });
        request.success(function(response) {
            //$scope.dailyIncome = response.dailyIncome;
        });
    }

    // $rootScope.$on("CallParcelGetNextBill", function(){
    //          $scope.getnextbillParcel();
    //       });

    $scope.parcel_show_tax_1 = false;
    $scope.parcel_show_tax_2 = false;
    $scope.parcel_product_list = [];
    $scope.parcel_products = [];
    $scope.parcel_order_id = '';

    $scope.parcel_dailyIncome = 0;
    var request = $http({
        method: "post",
        url: "index.php/order/get_daily_income_of_branch",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });
    request.success(function(response) {
        $scope.parcel_dailyIncome = response.dailyIncome;
    });

    var parcel_discount_type_list = [{
        key: "0",
        value: "None"
    }, {
        key: "1",
        value: "Complementary"
    }, {
        key: "2",
        value: "Discount Percentage"
    }];
    $scope.parcel_discount_type_list = parcel_discount_type_list;

    $scope.parcel_discount_type = $scope.parcel_discount_type_list[0];

    /*var parcel_payment_type_list = [{
        key: "1",
        value: "Cash"
    }, {
        key: "2",
        value: "Credit Card"
    }, {
        key: "3",
        value: "Debit Card"
    }];*/

    var request = $http({
                    method: "post",
                    url: "index.php/payment/getPaymentTypeForOrder",
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                });
                request.success(function(response) {
                    //console.log(response.payment_list);
                    $scope.parcel_payment_type_list = response.payment_list;
                    $scope.parcel_payment_type = response.payment_list[0];
                });


    //$scope.parcel_payment_type_list = parcel_payment_type_list;
    //$scope.parcel_payment_type = parcel_payment_type_list[0];

    // for use of parseFloat in expression
    $scope.parseFloat = parseFloat;

    // get bill number
    // var request = $http({
    //               method: "post",
    //               url: "index.php/order/get_next_bill_id",
    //               data: {},
    //               headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    //           });
    //       request.success(function (response) {                        
    //                   angular.element('#parcel_bill_number').html(response.next_bill_id);

    //                   $scope.parcel_order_id = '';

    //                   $scope.parcel_order_code = response.next_bill_id;
    //           });


    // get products
    var request = $http({
        method: "post",
        url: "index.php/product/parcel_product_list_by_branch",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });
    request.success(function(response) {
        if (response.status == "1") {
            if (response.data) {
                $scope.parcel_product_list = response.data;
            }
        }
    });

    // get tax_names
    var request = $http({
        method: "post",
        url: "index.php/tax/parcel_tax_by_branch",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });
    request.success(function(response) {

        //console.log(response.data);

        if (response.data[0]) {
            $scope.parcel_tax_1 = response.data[0].tax_name;
            $scope.parcel_show_tax_1 = true;
            $scope.parcel_tax_1_tax_id = response.data[0].tax_id;

        }
        if (response.data[1]) {
            $scope.parcel_tax_2 = response.data[1].tax_name;
            $scope.parcel_show_tax_2 = true;
            $scope.parcel_tax_2_tax_id = response.data[1].tax_id;
        }


    });


    $scope.parcel_branchSpecificTax_list = [];
    var request = $http({
        method: "post",
        url: "index.php/tax/parcel_branchSpecificTax_list",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });

    request.success(function(response) {
        //console.log('tax'+response.data);
        $scope.parcel_branchSpecificTax_list = response.data;
    });


    // get live parcel order
    var request = $http({
        method: "post",
        url: "index.php/order/get_live_parcel_order",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });

    request.success(function(response) {

        //  console.log(response);


        // make waiter selected

        if (response.order) {



            $scope.parcel_order_id = response.order.order_id;

            //console.log('here'+$scope.parcel_order_id);


            angular.forEach($scope.waiter_list, function(value, key) {

                if (value.waiter_id == response.order.waiter_id) {
                    $scope.waiter_list.selected = value;
                }

            });

            $scope.parcel_products = response.order_items;

            $scope.parcel_discount_type = $scope.parcel_discount_type_list[response.order.discount_type];

            $scope.parcel_discount_amount = response.order.discount_amount;

            // // console.log(response.order.payment_type);

            $scope.parcel_payment_type = $scope.parcel_payment_type_list[parseInt(response.order.payment_type) - 1];

            $scope.parcel_payment_card_number = response.order.payment_card_number;

        }
    });
    // ------------------------------------------------------------------- //

    $scope.addParcelProduct = function() {
        // console.log($scope.parcel_product_list);

        // $scope.$watch('online', function(){

        // if($rootScope.online == false)
        //  {
        //    $window.alert('Please Connect to Internet');
        //    return false;
        //  }
        //  else
        //{

        if ($rootScope.online == true) {
            if ($scope.parcel_order_form.$valid) {
                // get selected product from drop down

                var selected_products = $scope.parcel_product_list.selected;

                var selected_product_id = '';
                selected_product_id = selected_products.product_id;

                //   console.log($scope.parcel_products);
                // console.log(selected_products);

                var product_id_arr = [];
                angular.forEach($scope.parcel_products, function(value, key) {

                    if (product_id_arr.indexOf(value.product_id) === -1) {
                        product_id_arr.push(value.product_id);
                    }
                    if (value.product_id == selected_products.product_id) {
                        selected_products = value;
                    }
                });


                //console.log(product_id_arr);

                if (product_id_arr.indexOf(selected_product_id) !== -1) {
                    // a is NOT in array1
                    //array1.push(a);
                    // console.log('YO');

                    var modalInstance = $modal.open({
                        templateUrl: 'myModalContent.html',
                        controller: 'ParcelModalInstanceCtrl',
                        size: 'sm',
                        resolve: {
                            product_id: function() {
                                return selected_product_id;
                            },
                            selected_products: function() {
                                return selected_products;
                            },
                            quantity: function() {
                                return $scope.parcel_product_qty;
                            }
                        }
                    });

                    modalInstance.result.then(function() {

                        //$scope.bool_for_order_update = bool_for_order_update;                      
                        parcel_insertIntoDb();
                    });


                } else {

                    selected_products.quantity = $scope.parcel_product_qty;

                    parcel_insertIntoDb();

                }

                function parcel_insertIntoDb() {

                    // add the quantity inputted by user in the selected product
                    selected_products.total = $scope.parcel_getTotal(selected_products);

                    if ($scope.parcel_products.indexOf(selected_products) === -1) {
                        $scope.parcel_products.push(selected_products);
                    }

                    $scope.parcel_product_qty = '';

                    // insert into live table        

                    var param = $("[name='parcel_order_form']").serialize();

                    //console.log(JSON.stringify(param));

                    var param_tax = $scope.parcel_service_tax_total() + $scope.parcel_other_tax_total();

                    var param_final_amount = $scope.parcel_finalOrderTotal();

                    var param_return_amount = Math.round(($scope.parcel_given_amount == null ? 0 : $scope.parcel_given_amount) - $scope.parcel_finalTotal() - $scope.parcel_branchSpecificTotal());


                    $scope.parcel_tax_arr = [];

                    angular.forEach($scope.parcel_branchSpecificTax_list, function(value, key) {

                        var obj = {};
                        obj.tax_id = value.tax_id;
                        obj.tax_percent = value.tax_percent;
                        $scope.parcel_tax_arr.push(obj);

                    });

                    if (($scope.parcel_tax_1_tax_id != "") && (typeof $scope.parcel_tax_1_tax_id != 'undefined') && ($scope.parcel_tax_1_tax_id != 'undefined') && ($scope.parcel_tax_1_tax_id != null)) {
                        $scope.parcel_tax_arr.push({
                            "tax_id": $scope.parcel_tax_1_tax_id,
                            "tax_percent": $scope.parcel_service_tax_total()
                        });
                    }

                    if (($scope.parcel_tax_2_tax_id != "") && (typeof $scope.parcel_tax_2_tax_id != 'undefined') && ($scope.parcel_tax_2_tax_id != 'undefined') && ($scope.parcel_tax_2_tax_id != null)) {
                        $scope.parcel_tax_arr.push({
                            "tax_id": $scope.parcel_tax_2_tax_id,
                            "tax_percent": $scope.parcel_other_tax_total()
                        });
                    }

                    var sub_total = $scope.parcel_finalTotal();

                    var waiter_id = $scope.waiter_list.selected.waiter_id;
                    //var brand_id = $scope.brand_list_by_branch.selected.brand_id;

                    var request_parcel_add_order = $http({
                        method: "post",
                        url: "index.php/order/add_order",
                        data: param + '&order_type=3&' + '&waiter_id=' + waiter_id + '&total_items=' + $scope.parcel_products.length + '&order_id=' + $scope.parcel_order_id + '&total_amount=' + param_final_amount + '&return_amount=' + param_return_amount + '&sub_total=' + sub_total + '&order_code=' + $scope.parcel_order_code,
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    });
                    request_parcel_add_order.success(function(response) {

                        angular.element("#printButtonParcel").removeAttr('disabled');
                        $scope.$broadcast('parcel_order_focus');

                        $scope.parcel_order_id = response.order_id;
                        //console.log($scope.parcel_order_id +' : '+ response.order_id);

                        // order items
                        var request = $http({
                            method: "post",
                            url: "index.php/order/add_order_items",
                            data: 'order_id=' + $scope.parcel_order_id + '&products=' + JSON.stringify(selected_products),
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        });

                        // request.success(function (response) {
                        // });

                        // order tax
                        var request = $http({
                            method: "post",
                            url: "index.php/order/add_order_tax",
                            data: 'order_id=' + $scope.parcel_order_id + '&tax_arr=' + JSON.stringify($scope.parcel_tax_arr),
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        });

                        // request.success(function (response) { 
                        // });




                    });
                    request_parcel_add_order.error(function(errorReason) {
                        console.log('Error : parcel_add_order. Cause : ' + errorReason);
                    });

                    //focus back on product list
                    var result = document.getElementById("parcel_product_list");
                    var uiSelect = angular.element(result).controller('uiSelect');
                    uiSelect.focusser[0].focus();
                    uiSelect.focus = true;
                    uiSelect.setFocus();
                    uiSelect.open = false;

                }
            }
        }
        //}

        // add product iff the form is valid

        // })

    }

    $scope.parcel_totalAmount = function() {

        var total = 0;
        for (var i = 0; i < $scope.parcel_products.length; i++) {
            var product = $scope.parcel_products[i];
            total += (product.quantity * product.price)
        }
        return total;
    }

    $scope.parcel_getTotal = function(product) {

        //console.log(product);return false;

        //for(var i = 0; i < $scope.parcel_products.length; i++){
        var total = 0;
        //  var product = $scope.parcel_products;
        total += (product.quantity * product.price) + ((product.quantity * product.price) * (parseFloat((product.service_tax_percent == null) ? 0 : product.service_tax_percent) + parseFloat((product.other_tax_percent == null) ? 0 : product.other_tax_percent))) / 100
        //}
        return total;
    }

    $scope.parcel_finalTotal = function() {
        var final_total = 0;
        for (var i = 0; i < $scope.parcel_products.length; i++) {

            var product = $scope.parcel_products[i];

            final_total += $scope.parcel_getTotal(product);
        }
        return final_total;
    }

    // total of service_tax
    $scope.parcel_service_tax_total = function() {
        var service_tax_total = 0;
        for (var i = 0; i < $scope.parcel_products.length; i++) {

            var product = $scope.parcel_products[i];

            service_tax_total += product.service_tax_percent == null ? 0 : parseFloat(product.service_tax_percent);
        }
        return service_tax_total;
    }

    $scope.parcel_other_tax_total = function() {
        var other_tax_total = 0;
        for (var i = 0; i < $scope.parcel_products.length; i++) {

            var product = $scope.parcel_products[i];

            other_tax_total += product.other_tax_percent == null ? 0 : parseFloat(product.other_tax_percent);
        }
        return other_tax_total;
    }

    $scope.round_off_parcel_finalOrderTotal = function() {
        //$scope.given_amount = Math.round($scope.parcel_finalOrderTotal());
        return Math.round($scope.parcel_finalOrderTotal());
    }

    $scope.parcel_finalOrderTotal = function() {
        // console.log($scope.discount_type);
        var parcel_discount_type = $scope.parcel_discount_type.key;

        var parcel_finalOrderTotal = 0;

        if ((parcel_discount_type == "") || (typeof parcel_discount_type == 'undefined') || (parcel_discount_type == 'undefined') || (parcel_discount_type == null)) {
            parcel_discount_type = 0;
        }

        if (parcel_discount_type == 0) {
            //none
            parcel_finalOrderTotal = $scope.parcel_finalTotal() + $scope.parcel_branchSpecificTotal();
        } else if (parcel_discount_type == 1) {
            // complementary
            // total would be without all taxes
            //parcel_finalOrderTotal = $scope.parcel_totalAmount();

            parcel_finalOrderTotal = $scope.parcel_totalAmount() - $scope.parcel_totalAmount();
        } else {
            if (($scope.parcel_discount_amount == "") || (typeof $scope.parcel_discount_amount == 'undefined') || ($scope.parcel_discount_amount == 'undefined') || ($scope.parcel_discount_amount == null)) {
                $scope.parcel_discount_amount = 0;
            }
            //finalOrderTotal =  ($scope.finalTotal()+($scope.finalTotal()*$scope.discount_amount/100))+$scope.branchSpecificTotal();
            parcel_finalOrderTotal = ($scope.parcel_finalTotal() - ($scope.parcel_finalTotal() * $scope.parcel_discount_amount / 100)) + $scope.parcel_branchSpecificTotal();

            // finalOrderTotal = $scope.totalAmount() + 1;
        }

        return parcel_finalOrderTotal;

        // console.log(discount_type);
    }

    $scope.parcel_requestDiscounttype = function() {
        //console.log($scope.order_id+':'+$scope.discount_type.key+':'+$scope.discount_amount);
        if ($rootScope.online == true) {
            var param_final_amount = $scope.parcel_finalOrderTotal();
            var param_return_amount = Math.round(($scope.parcel_given_amount == null ? 0 : $scope.parcel_given_amount) - $scope.parcel_finalOrderTotal());

            var request = $http({
                method: "post",
                url: "index.php/order/update_order_discount",
                data: 'order_id=' + $scope.parcel_order_id + '&discount_type=' + $scope.parcel_discount_type.key + '&discount_amount=' + $scope.parcel_discount_amount + '&total_amount=' + param_final_amount + '&return_amount=' + param_return_amount,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {
                $scope.parcel_is_bill_saved = true;
            });
        }
    }

    // final amount after branch specific tax
    $scope.parcel_branchSpecificTotal = function() {
        var parcel_total = 0;
        for (var i = 0; i < $scope.parcel_branchSpecificTax_list.length; i++) {
            var parcel_tax = $scope.parcel_branchSpecificTax_list[i];

            var parcel_discount_type = $scope.parcel_discount_type.key;

            if ((parcel_discount_type == "") || (typeof parcel_discount_type == 'undefined') || (parcel_discount_type == 'undefined') || (parcel_discount_type == null)) {
                parcel_discount_type = 0;
            }

            if (parcel_discount_type == 0) {
                //none
                var parcel_finTotal = $scope.parcel_finalTotal();
            } else if (parcel_discount_type == 1) {
                // complementary
                // total would be without all taxes
                //var parcel_finTotal = $scope.parcel_totalAmount();
                var parcel_finTotal = $scope.parcel_totalAmount() - $scope.parcel_totalAmount();
            } else {
                if (($scope.parcel_discount_amount == "") || (typeof $scope.parcel_discount_amount == 'undefined') || ($scope.parcel_discount_amount == 'undefined') || ($scope.parcel_discount_amount == null)) {
                    $scope.parcel_discount_amount = 0;
                }
                var parcel_discount_amount = $scope.parcel_discount_amount;
                // var finTotal = $scope.finalTotal()+(($scope.finalTotal()*discount_amount)/100);
                var parcel_finTotal = $scope.parcel_finalTotal() - (($scope.parcel_finalTotal() * parcel_discount_amount) / 100);
            }

            //total += parseFloat((parseFloat($scope.finalTotal())*parseFloat(tax.tax_percent))/100);  
            parcel_total += parseFloat((parseFloat(parcel_finTotal) * parseFloat(parcel_tax.tax_percent)) / 100);

        }
        //return Math.round(parcel_total);
        return parcel_total;
    }

    $scope.parcel_removeProduct = function(product_id, service_tax_percent, other_tax_percent, productRows) {

        //console.log('parcel_order_id:'+$scope.parcel_order_id);return false;

        // get the total which should be removed
        //             $scope.$watch('online', function(){

        // if($rootScope.online == false)
        //         {
        //           $window.alert('Please Connect to Internet');
        //           return false;
        //         }
        //         else
        //         {

        if ($rootScope.online == true) {
            var order_item_id = productRows.productRows;
            var tax = parseFloat((service_tax_percent == null) ? 0 : service_tax_percent) + parseFloat((other_tax_percent == null) ? 0 : other_tax_percent);

            var given_amount = ($scope.parcel_given_amount == null ? 0 : $scope.parcel_given_amount);

            // remove order items from db
            var request = $http({
                method: "post",
                url: "index.php/order/delete_order_items",
                data: 'tax=' + tax + '&given_amount=' + given_amount + '&product_id=' + product_id + '&order_id=' + $scope.parcel_order_id+'&order_item_id='+order_item_id,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {

                // $scope.products.splice( index, 1 );
                // remove row from table
                var index = -1;
                var comArr = eval($scope.parcel_products);
                for (var i = 0; i < comArr.length; i++) {
                    if (comArr[i].product_id === product_id) {
                        index = i;
                        break;
                    }
                }
                if (index === -1) {
                    alert("Something gone wrong");
                }
                $scope.parcel_products.splice(index, 1);

            });
        }



        //}) 
    }

    $scope.parcel_quantityChange = function(product_id, quantity) {
        //console.log('product_id:'+product_id+'quantity:'+quantity+'order_id:'+$scope.order_id);

        var param_tax = $scope.parcel_service_tax_total() + $scope.parcel_other_tax_total();
        //var param_final_amount = $scope.finalTotal();
        var param_final_amount = $scope.parcel_finalOrderTotal();
        var param_return_amount = Math.round(($scope.parcel_given_amount == null ? 0 : $scope.parcel_given_amount) - $scope.parcel_finalOrderTotal());


        // update order items
        var request = $http({
            method: "post",
            url: "index.php/order/update_order_items",
            data: 'tax=' + param_tax + '&total_amount=' + param_final_amount + '&return_amount=' + param_return_amount + '&product_id=' + product_id + '&quantity=' + quantity + '&order_id=' + $scope.parcel_order_id,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });

        request.success(function(response) {
            //  console.log(response);

            $scope.parcel_is_bill_saved = true;

        });

    }

    //Run javascript function when user finishes typing instead of on key up

    $scope.parcel_typingTimer; //timer identifier
    $scope.parcel_doneTypingInterval = 1000; //time in ms, 5 second for example      

    $scope.parcel_changeGivenAmountOnKeyup = function(parcel_given_amount) {
        clearTimeout($scope.parcel_typingTimer);
        $scope.parcel_typingTimer = setTimeout(function() {

            // console.log(given_amount);

            if ((parcel_given_amount != "") && (typeof parcel_given_amount != 'undefined') && (parcel_given_amount != 'undefined') && (parcel_given_amount != null)) {
                var param_return_amount = Math.round((parcel_given_amount == null ? 0 : parcel_given_amount) - $scope.parcel_finalOrderTotal());

                var request = $http({
                    method: "post",
                    url: "index.php/order/change_given_amount",
                    data: 'return_amount=' + param_return_amount + '&order_id=' + $scope.parcel_order_id + '&given_amount=' + $scope.parcel_given_amount,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                });

                request.success(function(response) {
                    //  console.log(response);
                });
            }


        }, $scope.parcel_doneTypingInterval);
    }


    $scope.parcel_changeGivenAmountOnKeydown = function(given_amount) {
        clearTimeout($scope.parcel_typingTimer);
    }

    $scope.parcel_resetOrder = function() {
        // delete order on reset
        if ($rootScope.online == true) {
            var request = $http({
                method: "post",
                url: "index.php/order/reset_order",
                data: 'order_id=' + $scope.parcel_order_id,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {
                // reset everything

                $scope.parcel_product_list.selected = '';
                $scope.parcel_products = [];

                // get bill number
                // var request = $http({
                //               method: "post",
                //               url: "index.php/order/get_next_bill_id",
                //               data: {},
                //               headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                //           });
                //       request.success(function (response) {                        
                //                   angular.element('#parcel_bill_number').html(response.next_bill_id);

                //                   $scope.parcel_order_id = '';                                                        
                //                   $scope.parcel_order_code = response.next_bill_id;
                //           });

                $scope.parcel_order_id = '';
                $scope.parcel_order_code = '';

            });

        }

    }

    //  $scope.parcel_saveOrder = function()
    //  {

    //     if($scope.parcel_products.length>0) 
    //     {
    //           var request = $http({
    //                             method: "post",
    //                             url: "index.php/order/print_order",
    //                             data: 'order_id='+$scope.parcel_order_id,
    //                             headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    //                         });

    //                     request.success(function (response) { 
    //                     });
    //     }

    // }

        $scope.calculateBranchSpecificTaxParcel = function(tax_percent)
    {
        var discount_type;
        if ($scope.parcel_discount_type.key == 3) {
            discount_type = 2;
        } else {
            discount_type = $scope.parcel_discount_type.key;
        }

        var amt = 0;

        if ((discount_type == "") || (typeof discount_type == 'undefined') || (discount_type == 'undefined') || (discount_type == null)) {
            discount_type = 0;
        }

        if (discount_type == 0) {
            //none
            amt = $scope.parcel_finalTotal();
        } else if (discount_type == 1) {
            // complementary
            // total would be without all taxes
            //amt = $scope.totalAmount();
            amt = $scope.totalAmount() - $scope.totalAmount();
        } else {
            if (($scope.parcel_discount_amount != "") && (typeof $scope.parcel_discount_amount != 'undefined') && ($scope.parcel_discount_amount != 'undefined') && ($scope.parcel_discount_amount != null)) {
                //amt = $scope.finalTotal()+(($scope.finalTotal()*$scope.parcel_discount_amount)/100);
                amt = $scope.parcel_finalTotal() - (($scope.parcel_finalTotal() * $scope.parcel_discount_amount) / 100);
            } else {
                amt = $scope.parcel_finalTotal();
            }
        }

        // var amt = $scope.finalTotal()+(($scope.finalTotal()*$scope.parcel_discount_amount)/100);
        //console.log('Parcel');
        //console.log(((amt * tax_percent) / 100).toFixed(2));
        return ((amt * tax_percent) / 100).toFixed(2);
    }

    var request = $http({
        method: "post",
        url: "index.php/branch/getLoggedInBranchDetails",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });
    request.success(function(response) {
        // console.log(response.data.name);
        $scope.parcel_branch_name = response.data.name;
        $scope.parcel_branch_address = response.data.address;
        //angular.element("#branch_name").val(response.data.name);
    });

    $scope.parcel_saveBtnDisabled = false;


    $scope.parcel_saveOrder = function() {

        if ($scope.parcel_products.length > 0) {
            var request = $http({
                method: "post",
                url: "index.php/order/print_order",
                data: 'order_id=' + $scope.parcel_order_id,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {});
        }

    }


    $scope.countTotalProducts_printDiv_parcel = function() {
        if ($scope.parcel_products.length > 0) {
            return false;
        } else {
            return true;
        }
    }


    // print the bill
    $scope.parcel_printInvoice = function() {

        //console.log('parcel_printInvoice' + $scope.parcel_order_id);

        if ($scope.parcel_order_id != "" && $scope.parcel_order_id != null && typeof $scope.parcel_order_id !== 'undefined') {
            //$scope.$watch('online', function(){

            if ($rootScope.online == true) {

                //$scope.parcel_saveOrder();

                if ($scope.parcel_is_bill_saved == true) {
                    $scope.parcel_saveBtnDisabled = true;
                }

                var order_id = $scope.parcel_order_id;
                var order_code = $scope.parcel_order_code;
                $scope.parcel_printOrderCode = $scope.parcel_order_code;



                var request = $http({
                    method: "post",
                    url: "index.php/order/get_details_to_print_invoive_parcel",
                    data: 'order_id=' + $scope.parcel_order_id,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                });

                request.success(function(response) {
                    
                    $scope.parcel_branch_specific_taxes = '';
                    var service_tax_number_html = '';
                    var other_number_html = '';
                    var other_number_html = '';
                    var branch_contact = '';
                    var note = '';
                    var city_name = '';
                    var pincode = '';
                    


                    // if(response.latest_printed_bill==response.data.order_code)
                    // {

                    if (response.data) {
                        $scope.captain = response.data.waiter_name;
                        $scope.parcel_order_date = response.data.order_date;
                        $scope.parcel_brand_name = response.data.brand_name;
                        $scope.parcel_order_time = response.data.order_time;
                        $scope.dicount_percent = response.data.discount_amount;
                        

                        $scope.parcel_printOrderCode = response.data.order_code;
                        $scope.note = response.data.notes;
                        if($scope.note != '' && $scope.note != null){
                            note = '<tr> <td colspan="5"><b>NOTE: </b>' + $scope.note + '</td> </tr> <tr><td>&nbsp;</td></tr>';
                        }

                        $scope.parcel_invoice_items = response.invoice_items;
                        $scope.parcel_invoice_total = response.invoice_total;
                        if (response.branch_specific_taxes) {
                            $scope.parcel_branch_specific_taxes = response.branch_specific_taxes;
                        }
                        if (response.branch_details) {
                            $scope.service_tax_number = response.branch_details.service_tax_number;
                            pincode = response.branch_details.pincode;
                            city_name = response.branch_details.city_name;
                            $scope.branch_contact = response.branch_details.contact;
                            if($scope.branch_contact != '' && $scope.branch_contact != null){
                                branch_contact = '<br> CONTACT: ' + $scope.branch_contact;
                            }
                            if ($scope.service_tax_number != '' && $scope.service_tax_number != null) {
                                service_tax_number_html = 'Service Code – 996331 <br> CIN No: ' + $scope.service_tax_number;
                            }

                            $scope.other_number = response.branch_details.other_number;
                            if ($scope.other_number != '' && $scope.other_number != null) {
                                other_number_html = 'GSTIN: ' + $scope.other_number;
                            }
                        }


                        $scope.parcel_grand_total = response.grand_total;
                    }

                    var total_order_items = '';
                    if (response.total_order_items) {
                        total_order_items = response.total_order_items;
                    }


                    var parcel_branch_name = $scope.parcel_branch_name;
                    var parcel_branch_address = $scope.parcel_branch_address;

                    var parcel_brand_name = $scope.parcel_brand_name;
                    var captain =  $scope.captain;
                    var parcel_order_date = $scope.parcel_order_date;
                    var parcel_order_time = $scope.parcel_order_time;

                    var parcel_invoice_items = $scope.parcel_invoice_items;
                    var parcel_invoice_total = $scope.parcel_invoice_total;
                    var parcel_branch_specific_taxes = '';
                    if (($scope.parcel_branch_specific_taxes != "") && (typeof $scope.parcel_branch_specific_taxes != 'undefined') && ($scope.parcel_branch_specific_taxes != 'undefined') && ($scope.parcel_branch_specific_taxes != null)) {
                        parcel_branch_specific_taxes = $scope.parcel_branch_specific_taxes;
                    }

                    var parcel_grand_total = $scope.parcel_grand_total;

                    var dicount_percent = 0;
                    var discount_column = '';
                    if ($scope.dicount_percent != 0) {
                        dicount_percent = $scope.dicount_percent;
                        discount_column = '<tr> <td colspan="4">DISCOUNT @ ' + dicount_percent + '%</td> <td align="right">' + ((parcel_invoice_total * dicount_percent) / 100).toFixed(2) + '</td> </tr> ';
                    }


                    //var brand_id = $scope.brand_list_by_branch.selected.brand_id;

                    var request = $http({
                        method: "post",
                        url: "index.php/order/check_for_previous_order",
                        data: 'order_code=' + $scope.parcel_printOrderCode,
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    });

                    request.success(function(response) {
                        console.log(response);
                        if (response.status) {
                            if (response.status == "1") {
                                //success

                                var popupWin = window.open('', '_blank', 'width=300,height=600');
                                popupWin.document.open();


                                popupWin.document.write('<style type="text/css"> table.tableizer-table { font-size: 12px; font-family: Verdana, Geneva, Arial, sans-serif; text-transform: uppercase;width:255px; }table.inner-table{border-collapse:collapse;} .tableizer-table td { padding: 2px 0px; margin: 0px; vertical-align: initial; }.bottom-border {border-width:1px;border-bottom : 1px dashed #000 !important;}.top-border {border-width:1px;border-top : 1px dashed #000 !important;} .border { border-width:1px;border-top : 1px dashed #000 !important;border-bottom : 1px dashed #000 !important; }.center{text-align:center;} .title {font-size : 16px;}</style><table class="tableizer-table"> <thead> <tr class="tableizer-firstrow"> <th colspan="5" class="center title">' + parcel_branch_name + '</th> </tr> </thead> <tbody> <tr> <td colspan="5" class="center">' + parcel_branch_address +'<br>'+ city_name + ' - ' + pincode + branch_contact + '</td> </tr> <tr > <td class="bottom-border center" colspan="5" > RETAIL INVOICE </td> </tr><tr> <td colspan="5" class="center" > PARCEL ORDER </td> </tr> <tr> <td class="center" colspan="5">' + parcel_order_date +' '+ parcel_order_time + '</td> </tr> <tr><td colspan="5" style="padding:0px;"> <table class="tableizer-table inner-table" style="width:100%"> <tr> <td>BILL: ' + $scope.parcel_printOrderCode + '</td> <td align="right">CAPTAIN: ' + captain + '</td> </tr> </table> </td></tr> <tr> <td colspan="5" style="padding:0px;"> <table class="tableizer-table inner-table" style="width:100%"><tr><td class="border"><b>ITEM NAME</b></td><td class="border" align="right"><b>QTY</b></td><td class="border" align="right"><b>RATE</b></td><td class="border" align="right" style="padding-left:4px;"><b>AMT</b></td></tr> ' + parcel_invoice_items + '</table></td> <tr> <td colspan="4" class="top-border">TOTAL QTY</td> <td class="top-border" align="right">' + total_order_items + '</td> </tr> <tr> <td colspan="4"><b>SUB TOTAL</b></td> <td align="right"><b>' + (parcel_invoice_total).toFixed(2) + '</b></td> </tr> '+ discount_column + parcel_branch_specific_taxes + ' <tr> <td colspan="4">TOTAL</td> <td style="height:auto;" align="right">' + (parcel_grand_total).toFixed(2) + '</td> </tr> <tr> <td colspan="4">ROUND OFF</td> <td style="height:auto;" align="right">' + (Math.round(parcel_grand_total) - (parcel_grand_total)).toFixed(2) + '</td> </tr><tr> <td colspan="4" class="border title"><b>GRAND TOTAL</b></td> <td class="border title" style="height:auto;" align="right"><b>' + (Math.round(parcel_grand_total)).toFixed(2) + '</b></td> </tr> <tr> <td>&nbsp;</td> </tr>' + note +'<tr> <td colspan="5" align="center">' + service_tax_number_html + ' <br>' + other_number_html + ' <br>NO REVERSE CHARGE</td> </tr> <tr></tr> <tr> <td colspan="5" align="center">**** THANK YOU! VISIT AGAIN ****</td> </tr><tr> <td>&nbsp;</td> </tr> </tbody></table>');

                                popupWin.document.close();


                                var request = $http({
                                    method: "post",
                                    url: "index.php/order/print_order",
                                    data: 'order_id=' + $scope.parcel_order_id,
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    }
                                });

                                request.success(function(response) {

                                    // reset everything

                                    $scope.parcel_product_list.selected = '';
                                    $scope.parcel_products = [];

                                    // get bill number
                                    // var request = $http({
                                    //           method: "post",
                                    //           url: "index.php/order/get_next_bill_id",
                                    //           data: {},
                                    //           headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                                    //       });
                                    //   request.success(function (response) {                        
                                    //               angular.element('#parcel_bill_number').html(response.next_bill_id);

                                    //               $scope.parcel_order_id = '';

                                    //               $scope.parcel_order_code = response.next_bill_id;
                                    //       });

                                    $scope.parcel_order_id = '';
                                    $scope.parcel_order_code = '';
                                    angular.element("#parcel_discount_amount").val("0");
                                    $scope.parcel_discount_type = 0;
                                    //$scope.discount_amount_dummy = 0;
                                    $scope.parcel_discount_amount = 0;

                                    //angular.element("#printButtonParcel").attr('disabled','disabled');

                                    // }
                                    // else
                                    // {
                                    //   angular.element("#printButtonParcel").removeAttr('disabled');
                                    // }

                                });
                            } else {
                                alert("Internet connection error : Please try again");
                                angular.element("#printButtonParcel").removeAttr('disabled');
                            }
                        }
                    });

                });
            }

            //});// $rootScope.$emit("CallDeliveryGetNextBill");

            // angular.element("#printButtonParcel").attr('disabled','disabled');
            angular.element("#printButtonParcel").attr('disabled', 'disabled');
        }

    }

    $scope.parcel_printInvoiceAllBrands = function() {

        // console.log('here');
        // return false;
        if ($rootScope.online == true) {

            var request = $http({
                method: "post",
                url: "index.php/order/get_details_to_print_invoive_all_brand",
                data: 'order_id=' + $scope.parcel_order_id,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {



                var invoice_items = response.invoice_items;
                var branch_name = response.data.branch_name;


                var popupWin = window.open('', '_blank', 'width=300,height=300');

                popupWin.document.open();

                popupWin.document.write('<style type="text/css">table.tableizer-table{min-width:250px;font-size: 12px; font-family: Arial, Helvetica, sans-serif;text-transform: uppercase;}.tableizer-table td{padding: 2px 0px;margin: 0px;vertical-align: initial;}.dotted{border-bottom-style: dashed !important; border-width:1px;}.dotted-top{border-bottom-style: dashed !important;border-top-style: dashed !important;border-width:1px;}.dotted-top-only{border-top-style: dashed !important;border-width:1px;}.title{ margin-right:15px;}</style><table class="tableizer-table"> <thead> <tr class="tableizer-firstrow"> <th colspan="5" style="text-align:center"><b>' + branch_name + '</b></th> </tr> </thead> <tbody> ' + invoice_items + ' </tbody></table>');


                popupWin.document.close();



            });


        }
    }


    $scope.parcel_updatePaymentType = function() {
        //console.log('pay'+$scope.payment_type.key);
        if ($rootScope.online == true) {
            var request = $http({
                method: "post",
                url: "index.php/order/update_order_field",
                data: 'order_id=' + $scope.parcel_order_id + '&payment_type=' + $scope.parcel_payment_type.key + '&payment_card_number=' + $scope.parcel_payment_card_number,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {});
        }
    }

    $scope.parcel_payment_card_number_typingTimer; //timer identifier
    $scope.parcel_payment_card_number_doneTypingInterval = 1000; //time in ms, 5 second for example    

    $scope.parcel_changePayment_card_numberOnKeyup = function(parcel_payment_card_number) {
        if ($rootScope.online == true) {
            clearTimeout($scope.parcel_payment_card_number_typingTimer);
            $scope.parcel_payment_card_number_typingTimer = setTimeout(function() {

                // console.log(given_amount);

                if ((parcel_payment_card_number != "") && (typeof parcel_payment_card_number != 'undefined') && (parcel_payment_card_number != 'undefined') && (parcel_payment_card_number != null)) {

                    var request = $http({
                        method: "post",
                        url: "index.php/order/update_order_field",
                        data: 'order_id=' + $scope.parcel_order_id + '&payment_card_number=' + parcel_payment_card_number,
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    });

                    request.success(function(response) {
                        //  console.log(response);
                    });
                }


            }, $scope.parcel_payment_card_number_doneTypingInterval);
        }
    }


    $scope.changePayment_card_numberOnKeydown = function(parcel_payment_card_number) {
        if ($rootScope.online == true) {
            clearTimeout($scope.parcel_payment_card_number_typingTimer);
        }
    }

    if ($rootScope.online == true) {
        hotkeys.bindTo($scope)
            .add({
                combo: 'alt+p',
                description: 'Print Order',
                callback: function() {

                    //console.log('parcel');
                    $scope.parcel_printInvoice();

                }

            });
    }
    // /hotkeys.del('alt+p');



}]);




app.controller('deliveryOrderCtrl', ["$scope", "$http", "$state", "$modal", "$window", "$q", "$rootScope", "hotkeys", function($scope, $http, $state, $modal, $window, $q, $rootScope, hotkeys) {

    $scope.openViewBillModal = function()
    {
        if ($rootScope.online == true) 
        {
            
            //console.log('order_id'+order_id);
           // var order_id = $scope.order_id;

                var modalInstance = $modal.open({
                        templateUrl: 'viewBillModal.html',
                        controller: 'deliveryOrderCtrl',
                        //size: 'sm',
                        resolve: {
                                    // order_id: function() 
                                    // {
                                    //     return order_id;
                                    // }
                                    // // payment_card_number: function()
                                    // // {
                                    // //     return payment_card_number;
                                    // // }
                                   
                                }
                    });

                modalInstance.result.then(function() {

                         
                    //updateOrderField();
                    
                });
        }
    }

    // $scope.getnextbillDelivery = function()
    // {
    //  // console.log('comm');
    //    // get bill number
    //     var request = $http({
    //                   method: "post",
    //                   url: "index.php/order/get_next_bill_id",
    //                   data: {},
    //                   headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    //               });
    //           request.success(function (response) {                        
    //                       angular.element('#delivery_bill_number').html(response.next_bill_id);

    //                       $scope.delivery_order_id = '';

    //                       $scope.delivery_order_code = response.next_bill_id;
    //               });
    // }
    // $rootScope.$on("CallDeliveryGetNextBill", function(){
    //          $scope.getnextbillDelivery();
    //       });

    $scope.delivery_show_tax_1 = false;
    $scope.delivery_show_tax_2 = false;
    $scope.delivery_product_list = [];
    $scope.delivery_products = [];
    $scope.customer_contact = 0;

    $scope.delivery_dailyIncome = 0;
    var request = $http({
        method: "post",
        url: "index.php/order/get_daily_income_of_branch",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });
    request.success(function(response) {
        $scope.delivery_dailyIncome = response.dailyIncome;
    });

    var delivery_discount_type_list = [{
        key: "0",
        value: "None"
    }, {
        key: "1",
        value: "Complementary"
    }, {
        key: "2",
        value: "Discount Percentage"
    }];
    $scope.delivery_discount_type_list = delivery_discount_type_list;

    $scope.delivery_discount_type = $scope.delivery_discount_type_list[0];

    /*var delivery_payment_type_list = [{
        key: "1",
        value: "Cash"
    }, {
        key: "2",
        value: "Credit Card"
    }, {
        key: "3",
        value: "Debit Card"
    }];

    $scope.delivery_payment_type_list = delivery_payment_type_list;*/

    var request = $http({
                    method: "post",
                    url: "index.php/payment/getPaymentTypeForOrder",
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                });
                request.success(function(response) {
                    //console.log(response.payment_list);
                    $scope.delivery_payment_type_list = response.payment_list;
                    $scope.delivery_payment_type = response.payment_list[0];
                });
    //$scope.delivery_payment_type = delivery_payment_type_list[0];

    // for use of parseFloat in expression
    $scope.parseFloat = parseFloat;


    // get bill number
    // var request = $http({
    //               method: "post",
    //               url: "index.php/order/get_next_bill_id",
    //               data: {},
    //               headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    //           });
    //       request.success(function (response) {                        
    //                   angular.element('#delivery_bill_number').html(response.next_bill_id);

    //                   $scope.delivery_order_id = '';

    //                   $scope.delivery_order_code = response.next_bill_id;
    //           });

    $scope.delivery_order_id = '';
    $scope.delivery_order_code = '';


    // get products
    var request = $http({
        method: "post",
        url: "index.php/product/delivery_product_list_by_branch",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });
    request.success(function(response) {
        if (response.status == "1") {
            if (response.data) {
                $scope.delivery_product_list = response.data;
            }
        }
    });

    // get tax_names
    var request = $http({
        method: "post",
        url: "index.php/tax/delivery_tax_by_branch",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });
    request.success(function(response) {

        //console.log(response.data);

        if (response.data[0]) {
            $scope.delivery_tax_1 = response.data[0].tax_name;
            $scope.delivery_show_tax_1 = true;
            $scope.delivery_tax_1_tax_id = response.data[0].tax_id;

        }
        if (response.data[1]) {
            $scope.delivery_tax_2 = response.data[1].tax_name;
            $scope.delivery_show_tax_2 = true;
            $scope.delivery_tax_2_tax_id = response.data[1].tax_id;
        }


    });


    $scope.delivery_branchSpecificTax_list = [];
    var request = $http({
        method: "post",
        url: "index.php/tax/delivery_branchSpecificTax_list",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });

    request.success(function(response) {
        //console.log('tax'+response.data);
        $scope.delivery_branchSpecificTax_list = response.data;
    });


    // get live delivery order
    var request = $http({
        method: "post",
        url: "index.php/order/get_live_delivery_order",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });

    request.success(function(response) {

        // console.log(response);



        // make waiter selected

        if (response.order) {

            $scope.delivery_order_id = response.order.order_id;

            $scope.customer_name = response.order.customer_name;
            $scope.customer_contact = response.order.contact;
            $scope.customer_address = response.order.address;

            angular.forEach($scope.waiter_list, function(value, key) {

                if (value.waiter_id == response.order.waiter_id) {
                    $scope.waiter_list.selected = value;
                }

            });

            $scope.delivery_products = response.order_items;

            $scope.delivery_discount_type = $scope.delivery_discount_type_list[response.order.discount_type];

            $scope.delivery_discount_amount = response.order.discount_amount;

            // // console.log(response.order.payment_type);

            $scope.delivery_payment_type = $scope.delivery_payment_type_list[parseInt(response.order.payment_type) - 1];

            $scope.delivery_payment_card_number = response.order.payment_card_number;

        }
    });
    // ------------------------------------------------------------------- //


    $scope.addDeliveryProduct = function() {
        // console.log($scope.delivery_product_list);


        // $scope.$watch('online', function()
        // {
        //   //console.log($rootScope.online);

        //   if($rootScope.online == false)
        //   {

        //     $window.alert('Please Connect to Internet');
        //     return false;
        //   }
        if ($rootScope.online == true) {
            if ($scope.delivery_order_form.$valid) {
                // get selected product from drop down

                var selected_products = $scope.delivery_product_list.selected;
                var selected_product_id = '';
                selected_product_id = selected_products.product_id;

                //   console.log($scope.delivery_products);
                // console.log(selected_products);

                var product_id_arr = [];
                angular.forEach($scope.delivery_products, function(value, key) {

                    if (product_id_arr.indexOf(value.product_id) === -1) {
                        product_id_arr.push(value.product_id);
                    }
                    if (value.product_id == selected_products.product_id) {
                        selected_products = value;
                    }
                });


                //console.log(product_id_arr);

                if (product_id_arr.indexOf(selected_product_id) !== -1) {
                    // a is NOT in array1
                    //array1.push(a);
                    // console.log('YO');

                    var modalInstance = $modal.open({
                        templateUrl: 'myModalContent.html',
                        controller: 'DeliveryModalInstanceCtrl',
                        size: 'sm',
                        resolve: {
                            product_id: function() {
                                return selected_product_id;
                            },
                            selected_products: function() {
                                return selected_products;
                            },
                            quantity: function() {
                                return $scope.delivery_product_qty;
                            }
                        }
                    });

                    modalInstance.result.then(function() {

                        //$scope.bool_for_order_update = bool_for_order_update;                      
                        delivery_insertIntoDb();
                    });


                } else {

                    selected_products.quantity = $scope.delivery_product_qty;

                    delivery_insertIntoDb();

                }

                function delivery_insertIntoDb() {

                    // add the quantity inputted by user in the selected product
                    selected_products.total = $scope.delivery_getTotal(selected_products);

                    if ($scope.delivery_products.indexOf(selected_products) === -1) {
                        $scope.delivery_products.push(selected_products);
                    }

                    //selected_products.total = selected_products.getTotal;         

                    $scope.delivery_product_qty = '';


                    // insert into live table        

                    var param = $("[name='delivery_order_form']").serialize();

                    var param_tax = $scope.delivery_service_tax_total() + $scope.delivery_other_tax_total();
                    //var param_final_amount = $scope.finalTotal();
                    var param_final_amount = $scope.delivery_finalOrderTotal();

                    var param_return_amount = Math.round(($scope.delivery_given_amount == null ? 0 : $scope.delivery_given_amount) - $scope.delivery_finalTotal() - $scope.delivery_branchSpecificTotal());

                    // var discount_type = $scope.discount_type.key;
                    // var discount_amount = $scope.discount_amount;

                    $scope.delivery_tax_arr = [];

                    angular.forEach($scope.delivery_branchSpecificTax_list, function(value, key) {

                        var obj = {};
                        obj.tax_id = value.tax_id;
                        obj.tax_percent = value.tax_percent;
                        $scope.delivery_tax_arr.push(obj);

                    });

                    if (($scope.delivery_tax_1_tax_id != "") && (typeof $scope.delivery_tax_1_tax_id != 'undefined') && ($scope.delivery_tax_1_tax_id != 'undefined') && ($scope.delivery_tax_1_tax_id != null)) {
                        $scope.delivery_tax_arr.push({
                            "tax_id": $scope.delivery_tax_1_tax_id,
                            "tax_percent": $scope.delivery_service_tax_total()
                        });
                    }

                    if (($scope.delivery_tax_2_tax_id != "") && (typeof $scope.delivery_tax_2_tax_id != 'undefined') && ($scope.delivery_tax_2_tax_id != 'undefined') && ($scope.delivery_tax_2_tax_id != null)) {
                        $scope.delivery_tax_arr.push({
                            "tax_id": $scope.delivery_tax_2_tax_id,
                            "tax_percent": $scope.delivery_other_tax_total()
                        });
                    }

                    console.log($scope.delivery_tax_arr);

                    // console.log('param : '+param+'param_tax:'+param_tax+' - finalamount:'+param_final_amount);
                    // console.log('products :'+JSON.stringify(selected_products));
                    // console.log('total_items :'+$scope.delivery_products.length);
                    // console.log('delivery_order_id :'+$scope.delivery_order_id);
                    // console.log('param_return_amount :'+param_return_amount);

                    var sub_total = $scope.delivery_finalTotal();

                    var request_delivery_add_order = $http({
                        method: "post",
                        url: "index.php/order/add_order",
                        data: param + '&order_type=2&' + '&total_items=' + $scope.delivery_products.length + '&order_id=' + $scope.delivery_order_id + '&total_amount=' + param_final_amount + '&return_amount=' + param_return_amount + '&sub_total=' + sub_total + '&order_code=' + $scope.delivery_order_code,
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    });
                    request_delivery_add_order.success(function(response) {

                        angular.element("#printButtonDelivery").removeAttr('disabled');

                        $scope.delivery_order_id = response.order_id;

                        // order items
                        var request = $http({
                            method: "post",
                            url: "index.php/order/add_order_items",
                            data: 'order_id=' + $scope.delivery_order_id + '&products=' + JSON.stringify(selected_products),
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        });

                        // request.success(function (response) {
                        // });

                        // order tax
                        var request = $http({
                            method: "post",
                            url: "index.php/order/add_order_tax",
                            data: 'order_id=' + $scope.delivery_order_id + '&tax_arr=' + JSON.stringify($scope.delivery_tax_arr),
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        });



                        // request.success(function (response) { 
                        // });

                    });
                    request_delivery_add_order.error(function(errorReason) {
                        console.log('Error : delivery_add_order. Cause : ' + errorReason);
                    });

                }
            }
        }
        // add product iff the form is valid


        // })

    }

    $scope.calculateBranchSpecificTaxDelivery = function(tax_percent)
    {
        var discount_type;
        if ($scope.delivery_discount_type.key == 3) {
            discount_type = 2;
        } else {
            discount_type = $scope.delivery_discount_type.key;
        }

        var amt = 0;

        if ((discount_type == "") || (typeof discount_type == 'undefined') || (discount_type == 'undefined') || (discount_type == null)) {
            discount_type = 0;
        }

        if (discount_type == 0) {
            //none
            amt = $scope.delivery_finalTotal();
        } else if (discount_type == 1) {
            // complementary
            // total would be without all taxes
            //amt = $scope.totalAmount();
            amt = $scope.delivery_totalAmount() - $scope.delivery_totalAmount();
        } else {
            if (($scope.delivery_discount_amount != "") && (typeof $scope.delivery_discount_amount != 'undefined') && ($scope.delivery_discount_amount != 'undefined') && ($scope.delivery_discount_amount != null)) {
                //amt = $scope.finalTotal()+(($scope.finalTotal()*$scope.parcel_discount_amount)/100);
                amt = $scope.delivery_finalTotal() - (($scope.delivery_finalTotal() * $scope.delivery_discount_amount) / 100);
            } else {
                amt = $scope.delivery_finalTotal();
            }
        }

        // var amt = $scope.finalTotal()+(($scope.finalTotal()*$scope.parcel_discount_amount)/100);
        //console.log('Parcel');
        //console.log(((amt * tax_percent) / 100).toFixed(2));
        return ((amt * tax_percent) / 100).toFixed(2);
    }

    $scope.delivery_totalAmount = function() {

        var total = 0;
        for (var i = 0; i < $scope.delivery_products.length; i++) {
            var product = $scope.delivery_products[i];
            total += (product.quantity * product.price)
        }
        return total;
    }

    $scope.delivery_getTotal = function(product) {

        //console.log(product);return false;

        //for(var i = 0; i < $scope.delivery_products.length; i++){
        var total = 0;
        //  var product = $scope.delivery_products;
        total += (product.quantity * product.price) + ((product.quantity * product.price) * (parseFloat((product.service_tax_percent == null) ? 0 : product.service_tax_percent) + parseFloat((product.other_tax_percent == null) ? 0 : product.other_tax_percent))) / 100
        //}
        return total;
    }

    $scope.delivery_finalTotal = function() {
        var final_total = 0;
        for (var i = 0; i < $scope.delivery_products.length; i++) {

            var product = $scope.delivery_products[i];

            final_total += $scope.delivery_getTotal(product);
        }
        return final_total;
    }

    // total of service_tax
    $scope.delivery_service_tax_total = function() {
        var service_tax_total = 0;
        for (var i = 0; i < $scope.delivery_products.length; i++) {

            var product = $scope.delivery_products[i];

            service_tax_total += product.service_tax_percent == null ? 0 : parseFloat(product.service_tax_percent);
        }
        return service_tax_total;
    }

    $scope.delivery_other_tax_total = function() {
        var other_tax_total = 0;
        for (var i = 0; i < $scope.delivery_products.length; i++) {

            var product = $scope.delivery_products[i];

            other_tax_total += product.other_tax_percent == null ? 0 : parseFloat(product.other_tax_percent);
        }
        return other_tax_total;
    }

    $scope.round_off_delivery_finalOrderTotal = function() {
        //$scope.given_amount = Math.round($scope.delivery_finalOrderTotal());
        return Math.round($scope.delivery_finalOrderTotal());
    }

    $scope.delivery_finalOrderTotal = function() {
        // console.log($scope.discount_type);
        var delivery_discount_type = $scope.delivery_discount_type.key;

        var delivery_finalOrderTotal = 0;

        if ((delivery_discount_type == "") || (typeof delivery_discount_type == 'undefined') || (delivery_discount_type == 'undefined') || (delivery_discount_type == null)) {
            delivery_discount_type = 0;
        }

        if (delivery_discount_type == 0) {
            //none
            delivery_finalOrderTotal = $scope.delivery_finalTotal() + $scope.delivery_branchSpecificTotal();
        } else if (delivery_discount_type == 1) {
            // complementary
            // total would be without all taxes
            //delivery_finalOrderTotal = $scope.delivery_totalAmount();
            delivery_finalOrderTotal = $scope.delivery_totalAmount() - $scope.delivery_totalAmount();
        } else {
            if (($scope.delivery_discount_amount == "") || (typeof $scope.delivery_discount_amount == 'undefined') || ($scope.delivery_discount_amount == 'undefined') || ($scope.delivery_discount_amount == null)) {
                $scope.delivery_discount_amount = 0;
            }
            //finalOrderTotal =  ($scope.finalTotal()+($scope.finalTotal()*$scope.discount_amount/100))+$scope.branchSpecificTotal();
            delivery_finalOrderTotal = ($scope.delivery_finalTotal() - ($scope.delivery_finalTotal() * $scope.delivery_discount_amount / 100)) + $scope.delivery_branchSpecificTotal();

            // finalOrderTotal = $scope.totalAmount() + 1;
        }

        return delivery_finalOrderTotal;

        // console.log(discount_type);
    }

    $scope.delivery_requestDiscounttype = function() {
        //console.log($scope.order_id+':'+$scope.discount_type.key+':'+$scope.discount_amount);
        if ($rootScope.online == true) {
            var param_final_amount = $scope.delivery_finalOrderTotal();

            var param_return_amount = Math.round(($scope.delivery_given_amount == null ? 0 : $scope.delivery_given_amount) - $scope.delivery_finalOrderTotal());

            var request = $http({
                method: "post",
                url: "index.php/order/update_order_discount",
                data: 'order_id=' + $scope.delivery_order_id + '&discount_type=' + $scope.delivery_discount_type.key + '&discount_amount=' + $scope.delivery_discount_amount + '&total_amount=' + param_final_amount + '&return_amount=' + param_return_amount,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {
                $scope.delivery_is_bill_saved = true;
            });
        }
    }

    // final amount after branch specific tax
    $scope.delivery_branchSpecificTotal = function() {
        var delivery_total = 0;
        for (var i = 0; i < $scope.delivery_branchSpecificTax_list.length; i++) {
            var delivery_tax = $scope.delivery_branchSpecificTax_list[i];

            var delivery_discount_type = $scope.delivery_discount_type.key;

            if ((delivery_discount_type == "") || (typeof delivery_discount_type == 'undefined') || (delivery_discount_type == 'undefined') || (delivery_discount_type == null)) {
                delivery_discount_type = 0;
            }

            if (delivery_discount_type == 0) {
                //none
                var delivery_finTotal = $scope.delivery_finalTotal();
            } else if (delivery_discount_type == 1) {
                // complementary
                // total would be without all taxes
                //var delivery_finTotal = $scope.delivery_totalAmount();
                var delivery_finTotal = $scope.delivery_totalAmount() - $scope.delivery_totalAmount();
            } else {
                if (($scope.delivery_discount_amount == "") || (typeof $scope.delivery_discount_amount == 'undefined') || ($scope.delivery_discount_amount == 'undefined') || ($scope.delivery_discount_amount == null)) {
                    $scope.delivery_discount_amount = 0;
                }
                var delivery_discount_amount = $scope.delivery_discount_amount;
                // var finTotal = $scope.finalTotal()+(($scope.finalTotal()*discount_amount)/100);
                var delivery_finTotal = $scope.delivery_finalTotal() - (($scope.delivery_finalTotal() * delivery_discount_amount) / 100);
            }

            //total += parseFloat((parseFloat($scope.finalTotal())*parseFloat(tax.tax_percent))/100);  
            delivery_total += parseFloat((parseFloat(delivery_finTotal) * parseFloat(delivery_tax.tax_percent)) / 100);

        }
        //return Math.round(delivery_total);
        return delivery_total;
    }

    $scope.delivery_removeProduct = function(product_id, service_tax_percent, other_tax_percent) {

        //console.log('delivery_order_id:'+$scope.delivery_order_id);return false;

        // get the total which should be removed
        //     $scope.$watch('online', function(){

        //       if($rootScope.online == false)
        // {
        //   $window.alert('Please Connect to Internet');
        //   return false;
        // }
        if ($rootScope.online == true) {
            var tax = parseFloat((service_tax_percent == null) ? 0 : service_tax_percent) + parseFloat((other_tax_percent == null) ? 0 : other_tax_percent);

            var given_amount = ($scope.delivery_given_amount == null ? 0 : $scope.delivery_given_amount);

            // remove order items from db
            var request = $http({
                method: "post",
                url: "index.php/order/delete_order_items",
                data: 'tax=' + tax + '&given_amount=' + given_amount + '&product_id=' + product_id + '&order_id=' + $scope.delivery_order_id,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }

            });

            request.success(function(response) {

                // $scope.products.splice( index, 1 );
                // remove row from table
                var index = -1;
                var comArr = eval($scope.delivery_products);
                for (var i = 0; i < comArr.length; i++) {
                    if (comArr[i].product_id === product_id) {
                        index = i;
                        break;
                    }
                }
                if (index === -1) {
                    alert("Something gone wrong");
                }
                $scope.delivery_products.splice(index, 1);

            });
        }



        //}) 
    }

    $scope.delivery_quantityChange = function(product_id, quantity) {
        //console.log('product_id:'+product_id+'quantity:'+quantity+'order_id:'+$scope.order_id);

        var param_tax = $scope.delivery_service_tax_total() + $scope.delivery_other_tax_total();
        //var param_final_amount = $scope.finalTotal();
        var param_final_amount = $scope.delivery_finalOrderTotal();
        var param_return_amount = Math.round(($scope.delivery_given_amount == null ? 0 : $scope.delivery_given_amount) - $scope.delivery_finalOrderTotal());


        // update order items
        var request = $http({
            method: "post",
            url: "index.php/order/update_order_items",
            data: 'tax=' + param_tax + '&total_amount=' + param_final_amount + '&return_amount=' + param_return_amount + '&product_id=' + product_id + '&quantity=' + quantity + '&order_id=' + $scope.delivery_order_id,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });

        request.success(function(response) {
            //  console.log(response);

            $scope.delivery_is_bill_saved = true;

        });

    }

    //Run javascript function when user finishes typing instead of on key up

    $scope.delivery_typingTimer; //timer identifier
    $scope.delivery_doneTypingInterval = 1000; //time in ms, 5 second for example      

    $scope.delivery_changeGivenAmountOnKeyup = function(delivery_given_amount) {
        clearTimeout($scope.delivery_typingTimer);
        $scope.delivery_typingTimer = setTimeout(function() {

            // console.log(given_amount);

            if ((delivery_given_amount != "") && (typeof delivery_given_amount != 'undefined') && (delivery_given_amount != 'undefined') && (delivery_given_amount != null)) {
                var param_return_amount = Math.round((delivery_given_amount == null ? 0 : delivery_given_amount) - $scope.delivery_finalOrderTotal());

                var request = $http({
                    method: "post",
                    url: "index.php/order/change_given_amount",
                    data: 'return_amount=' + param_return_amount + '&order_id=' + $scope.delivery_order_id + '&given_amount=' + $scope.delivery_given_amount,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                });

                request.success(function(response) {
                    //  console.log(response);
                });
            }


        }, $scope.delivery_doneTypingInterval);
    }


    $scope.delivery_changeGivenAmountOnKeydown = function(given_amount) {
        clearTimeout($scope.delivery_typingTimer);
    }

    $scope.delivery_resetOrder = function() {
        // delete order on reset
        if ($rootScope.online == true) {
            var request = $http({
                method: "post",
                url: "index.php/order/reset_order",
                data: 'order_id=' + $scope.delivery_order_id,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {
                // reset everything

                $scope.delivery_product_list.selected = '';
                $scope.delivery_products = [];
                $scope.customer_name = '';
                $scope.customer_contact = '';
                $scope.customer_address = '';

                // angular.element('#delivery_bill_number').html($scope.delivery_order_code);

                // get bill number
                // var request = $http({
                //               method: "post",
                //               url: "index.php/order/get_next_bill_id",
                //               data: {},
                //               headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                //           });
                //       request.success(function (response) {                        
                //                   angular.element('#delivery_bill_number').html(response.next_bill_id);

                //                   $scope.delivery_order_id = '';

                //                   $scope.delivery_order_code = response.next_bill_id;
                //           });

                $scope.delivery_order_id = '';
                $scope.delivery_order_code = '';

            });
        }

    }

    $scope.delivery_saveOrder = function() {

        if ($scope.delivery_products.length > 0) {
            var request = $http({
                method: "post",
                url: "index.php/order/print_order",
                data: 'order_id=' + $scope.delivery_order_id,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {});
        }

    }

    var request = $http({
        method: "post",
        url: "index.php/branch/getLoggedInBranchDetails",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });
    request.success(function(response) {
        // console.log(response.data.name);
        $scope.delivery_branch_name = response.data.name;
        $scope.delivery_branch_address = response.data.address;
        //angular.element("#branch_name").val(response.data.name);
    });

    $scope.delivery_saveBtnDisabled = false;


    $scope.countTotalProducts_printDiv_delivery = function() {
        if ($scope.delivery_products.length > 0) {
            return false;
        } else {
            return true;
        }
    }

    // print the bill
    $scope.delivery_printInvoice = function() {

        //console.log('delivery_printInvoice');

        //$scope.$watch('online', function(){

        if ($rootScope.online == true) {

            //$scope.delivery_saveOrder();

            if ($scope.delivery_is_bill_saved == true) {
                $scope.delivery_saveBtnDisabled = true;
            }

            var order_id = $scope.delivery_order_id;
            var order_code = $scope.delivery_order_code;

            $scope.delivery_printOrderCode = $scope.delivery_order_code;



            var request = $http({
                method: "post",
                url: "index.php/order/get_details_to_print_invoive_delivery",
                data: 'order_id=' + $scope.delivery_order_id,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {

                $scope.delivery_branch_specific_taxes = '';
                var service_tax_number_html = '';
                var other_number_html = '';

                // if(response.latest_printed_bill==response.data.order_code)
                // {

                if (response.data) {

                    $scope.delivery_order_date = response.data.order_date;
                    $scope.delivery_order_time = response.data.order_time;
                    $scope.dicount_percent = response.data.discount_amount;

                    $scope.delivery_printOrderCode = response.data.order_code;

                    $scope.delivery_invoice_items = response.invoice_items;
                    $scope.delivery_invoice_total = response.invoice_total;
                    if (response.branch_specific_taxes) {
                        $scope.delivery_branch_specific_taxes = response.branch_specific_taxes;
                    }
                    if (response.branch_details) {
                        $scope.service_tax_number = response.branch_details.service_tax_number;
                        if ($scope.service_tax_number != '' && $scope.service_tax_number != null) {
                            service_tax_number_html = '<br>Service Code – 996331  CIN No: ' + $scope.service_tax_number;
                        }

                        $scope.other_number = response.branch_details.other_number;
                        if ($scope.other_number != '' && $scope.other_number != null) {
                            other_number_html = '<br>No Reverse Charge GSTIN: ' + $scope.other_number;
                        }
                    }

                    $scope.delivery_grand_total = response.grand_total;
                }

                var total_order_items = '';
                if (response.total_order_items) {
                    total_order_items = response.total_order_items;
                }

                var delivery_branch_name = $scope.delivery_branch_name;
                var delivery_branch_address = $scope.delivery_branch_address;

                var delivery_order_date = $scope.delivery_order_date;
                var delivery_order_time = $scope.delivery_order_time;

                var delivery_invoice_items = $scope.delivery_invoice_items;
                var delivery_invoice_total = $scope.delivery_invoice_total;
                var delivery_branch_specific_taxes = '';
                if (($scope.delivery_branch_specific_taxes != "") && (typeof $scope.delivery_branch_specific_taxes != 'undefined') && ($scope.delivery_branch_specific_taxes != 'undefined') && ($scope.delivery_branch_specific_taxes != null)) {
                    delivery_branch_specific_taxes = $scope.delivery_branch_specific_taxes;
                }

                var delivery_grand_total = $scope.delivery_grand_total;

                // var dicount_percent = 0;
                // if ($scope.dicount_percent != 0) {
                //     dicount_percent = $scope.dicount_percent;
                // }

                var city_name = '';
                var pincode = '';
                var branch_contact='';

                if (response.branch_details) {
                            $scope.service_tax_number = response.branch_details.service_tax_number;
                            pincode = response.branch_details.pincode;
                            city_name = response.branch_details.city_name;
                            $scope.branch_contact = response.branch_details.contact;
                            if($scope.branch_contact != '' && $scope.branch_contact != null){
                                branch_contact = '<br> CONTACT: ' + $scope.branch_contact;
                            }
                            if ($scope.service_tax_number != '' && $scope.service_tax_number != null) {
                                service_tax_number_html = 'Service Code – 996331 <br> CIN No: ' + $scope.service_tax_number;
                            }

                            $scope.other_number = response.branch_details.other_number;
                            if ($scope.other_number != '' && $scope.other_number != null) {
                                other_number_html = 'GSTIN: ' + $scope.other_number;
                            }
                        }

                    

                    // var total_order_items = '';
                    // if (response.total_order_items) {
                    //     total_order_items = response.total_order_items;
                    // }
                    //var captain =  $scope.captain;
                    
                    var dicount_percent = 0;
                    var discount_column = '';
                    if ($scope.dicount_percent != 0) {
                        dicount_percent = $scope.dicount_percent;
                        discount_column = '<tr> <td colspan="4">DISCOUNT @ ' + dicount_percent + '%</td> <td align="right">' + ((delivery_invoice_total * dicount_percent) / 100).toFixed(2) + '</td> </tr> ';
                    }

                    var customer ='' ;

                    if(response.data.customer.firstname){
                        customer += '<tr> <td colspan="5">NAME: '+response.data.customer.firstname+'</td> </tr> ';
                    }
                    if(response.data.customer.contact){
                        if(response.data.customer.contact==0 || response.data.customer.contact=="0"){
                            response.data.customer.contact = '--';
                        }
                        customer += '<tr> <td colspan="5">CONTACT: '+response.data.customer.contact+'</td> </tr> ';
                    }
                    if(response.data.customer.address){
                        customer += '<tr> <td colspan="5">ADDRESS: '+response.data.customer.address+'</td> </tr> ';
                    }
                    var customer_details ='';
                    if(customer){
                        customer_details = '<tr> <td colspan="5" class="top-border center"> CUSTOMER DETAILS</td> </tr>';
                        customer_details += customer;
                    }



                // popupWin.document.write('<style type="text/css">table.tableizer-table{font-size: 12px;border: none; font-family: Arial, Helvetica, sans-serif;}.tableizer-table td{padding: 4px;margin: 3px;border: none;}.tableizer-table th{font-weight: bold;}</style><table class="tableizer-table"><thead><tr class="tableizer-firstrow"><th></th><th>&nbsp;</th><th>'+delivery_branch_name+'</th><th>&nbsp;</th><th>&nbsp;</th></tr></thead><tbody> <tr><td>&nbsp;</td><td>&nbsp;</td><td>'+delivery_branch_address+'</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td>TAX Invoice</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td>Bill:</td><td>'+order_id+'</td><td>'+delivery_order_date+'</td></tr><tr><td>'+delivery_order_time+'</td></tr><tr><td colspan="2"><b>Description</b></td><td><b>Qty</b></td><td><b>Rate</b></td><td><b>Value</b></td></tr>'+delivery_invoice_items+'<tr><td colspan="2"></td><td><b>'+total_order_items+'</b></td><td></td><td></td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>Sub Total:</td><td>&nbsp;</td><td>'+delivery_invoice_total+'</td></tr>'+delivery_branch_specific_taxes+'<!--<tr><td>cashier</td><td>Mataji</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>--><tr><td>&nbsp;</td><td>&nbsp;</td><td><b>Total:</b></td><td>&nbsp;</td><td><b>'+delivery_grand_total+'</b></td></tr></tbody></table>');

                var request = $http({
                    method: "post",
                    url: "index.php/order/check_for_previous_order",
                    data: 'order_code=' + $scope.delivery_printOrderCode,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                });

                request.success(function(response) {

                    if (response.status) {
                        if (response.status == "1") {
                            //success

                            var popupWin = window.open('', '_blank', 'width=300,height=300');
                            popupWin.document.open();


                        

                           popupWin.document.write('<style type="text/css"> table.tableizer-table { font-size: 12px; font-family: Verdana, Geneva, Arial, sans-serif; text-transform: uppercase;width:255px; }table.inner-table{border-collapse:collapse;} .tableizer-table td { padding: 2px 0px; margin: 0px; vertical-align: initial; }.bottom-border {border-width:1px;border-bottom : 1px dashed #000 !important;}.top-border {border-width:1px;border-top : 1px dashed #000 !important;} .border { border-width:1px;border-top : 1px dashed #000 !important;border-bottom : 1px dashed #000 !important; }.center{text-align:center;} .title {font-size : 16px;}</style><table class="tableizer-table"> <thead> <tr class="tableizer-firstrow"> <th colspan="5" class="center title">' + delivery_branch_name + '</th> </tr> </thead> <tbody> <tr> <td colspan="5" class="center">' + delivery_branch_address +'<br>'+ city_name + ' - ' + pincode + branch_contact + '</td> </tr> <tr > <td class="bottom-border center" colspan="5" > RETAIL INVOICE </td> </tr><tr> <td colspan="5" class="center" > DELIVERY ORDER </td> </tr> <tr> <td class="center" colspan="5">' + delivery_order_date +' '+ delivery_order_time + '</td> </tr> <tr> <td class="center" colspan="5"> <b>BILL:' + $scope.delivery_printOrderCode + '</b></td> </tr>'+customer_details+'<tr> <td colspan="5" style="padding:0px;"> <table class="tableizer-table inner-table" style="width:100%"><tr><td class="border"><b>ITEM NAME</b></td><td class="border" align="right"><b>QTY</b></td><td class="border" align="right"><b>RATE</b></td><td class="border" align="right" style="padding-left:4px;"><b>AMT</b></td></tr> ' + delivery_invoice_items + '</table></td> <tr> <td colspan="4" class="top-border">TOTAL QTY</td> <td class="top-border" align="right">' + total_order_items + '</td> </tr> <tr> <td colspan="4"><b>SUB TOTAL</b></td> <td align="right"><b>' + (delivery_invoice_total).toFixed(2) + '</b></td> </tr> '+ discount_column + delivery_branch_specific_taxes + ' <tr> <td colspan="4">TOTAL</td> <td style="height:auto;" align="right">' + (delivery_grand_total).toFixed(2) + '</td> </tr> <tr> <td colspan="4">ROUND OFF</td> <td style="height:auto;" align="right">' + (Math.round(delivery_grand_total) - (delivery_grand_total)).toFixed(2) + '</td> </tr><tr> <td colspan="4" class="border title"><b>GRAND TOTAL</b></td> <td class="border title" style="height:auto;" align="right"><b>' + (Math.round(delivery_grand_total)).toFixed(2) + '</b></td> </tr> <tr> <td>&nbsp;</td> </tr>' + note +'<tr> <td colspan="5" align="center">' + service_tax_number_html + ' <br>' + other_number_html + ' <br>NO REVERSE CHARGE</td> </tr> <tr></tr> <tr> <td colspan="5" align="center">**** THANK YOU! VISIT AGAIN ****</td> </tr><tr> <td>&nbsp;</td> </tr> </tbody></table>');

                            popupWin.document.close();


                            var request = $http({
                                method: "post",
                                url: "index.php/order/print_order",
                                data: 'order_id=' + $scope.delivery_order_id,
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                }
                            });

                            request.success(function(response) {



                                // reset everything

                                $scope.delivery_product_list.selected = '';
                                $scope.delivery_products = [];
                                $scope.customer_name = '';
                                $scope.customer_contact = '';
                                $scope.customer_address = '';
                                $scope.delivery_order_id = '';
                                $scope.delivery_order_code = '';


                                // get bill number
                                // var request = $http({
                                //           method: "post",
                                //           url: "index.php/order/get_next_bill_id",
                                //           data: {},
                                //           headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                                //       });
                                //   request.success(function (response) {                        
                                //               angular.element('#delivery_bill_number').html(response.next_bill_id);

                                //               $scope.delivery_order_id = '';

                                //               $scope.delivery_order_code = response.next_bill_id;
                                //       });



                                // angular.element("#printButtonDelivery").attr('disabled','disabled');

                                // }
                                // else
                                // {
                                //   angular.element("#printButtonDelivery").removeAttr('disabled');
                                // }

                            });


                        } else {
                            alert("Internet connection error : Please try again");
                            angular.element("#printButtonDelivery").removeAttr('disabled');
                        }
                    }
                });

            });
        }

        // $rootScope.$emit("CallParcelGetNextBill");
        // })

        angular.element("#printButtonDelivery").attr('disabled', 'disabled');

    }

    $scope.delivery_printInvoiceAllBrands = function() {

        // console.log('here');
        // return false;
        if ($rootScope.online == true) {

            var request = $http({
                method: "post",
                url: "index.php/order/get_details_to_print_invoive_all_brand",
                data: 'order_id=' + $scope.delivery_order_id,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {



                var invoice_items = response.invoice_items;
                var branch_name = response.data.branch_name;


                var popupWin = window.open('', '_blank', 'width=300,height=300');

                popupWin.document.open();

                popupWin.document.write('<style type="text/css"> table.tableizer-table{font-size: 14px; border: none; font-family: Arial, Helvetica, sans-serif;}.tableizer-table td{padding: 4px; margin: 3px; border: none; font-weight: 600;}.tableizer-table th{font-weight: bold;}.dotted{border-bottom-style: dotted !important; border-width: 2px;}.dotted-top{border-bottom-style: dotted !important; border-top-style: dotted !important; border-width: 2px;}</style><table class="tableizer-table"> <thead> <tr class="tableizer-firstrow"> <th></th> <th>&nbsp;</th> <th colspan="2">' + branch_name + '</th> <th>&nbsp;</th> </tr></thead> <tbody> ' + invoice_items + ' </tbody></table>');


                popupWin.document.close();



            });


        }
    }

    $scope.delivery_updatePaymentType = function() {
        //console.log('pay'+$scope.payment_type.key);
        if ($rootScope.online == true) {
            var request = $http({
                method: "post",
                url: "index.php/order/update_order_field",
                data: 'order_id=' + $scope.delivery_order_id + '&payment_type=' + $scope.delivery_payment_type.key + '&payment_card_number=' + $scope.delivery_payment_card_number,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            });

            request.success(function(response) {});
        }
    }

    $scope.delivery_payment_card_number_typingTimer; //timer identifier
    $scope.delivery_payment_card_number_doneTypingInterval = 1000; //time in ms, 5 second for example    

    $scope.delivery_changePayment_card_numberOnKeyup = function(delivery_payment_card_number) {
        if ($rootScope.online == true) {
            clearTimeout($scope.delivery_payment_card_number_typingTimer);
            $scope.delivery_payment_card_number_typingTimer = setTimeout(function() {

                // console.log(given_amount);

                if ((delivery_payment_card_number != "") && (typeof delivery_payment_card_number != 'undefined') && (delivery_payment_card_number != 'undefined') && (delivery_payment_card_number != null)) {

                    var request = $http({
                        method: "post",
                        url: "index.php/order/update_order_field",
                        data: 'order_id=' + $scope.delivery_order_id + '&payment_card_number=' + delivery_payment_card_number,
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    });

                    request.success(function(response) {
                        //  console.log(response);
                    });
                }


            }, $scope.delivery_payment_card_number_doneTypingInterval);
        }
    }


    $scope.changePayment_card_numberOnKeydown = function(delivery_payment_card_number) {
        if ($rootScope.online == true) {
            clearTimeout($scope.delivery_payment_card_number_typingTimer);
        }
    }
    if ($rootScope.online == true) {

        hotkeys.bindTo($scope)
            .add({
                combo: 'alt+d',
                description: 'Print Order',
                callback: function() {

                    //console.log('homedel');

                    $scope.delivery_printInvoice();

                }

            });
    }
    //hotkeys.del('alt+p');



}]);