'use strict';
/**
 * controllers for ng-table
 * Simple table with sorting and filtering on AngularJS
 */
app.factory('Excel', function($window) {
    var uri = 'data:application/vnd.ms-excel;base64,',
        template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
        base64 = function(s) {
            return $window.btoa(unescape(encodeURIComponent(s)));
        },
        format = function(s, c) {
            return s.replace(/{(\w+)}/g, function(m, p) {
                return c[p];
            })
        };
    return {
        tableToExcel: function(tableId, worksheetName) {
            var table = $(tableId),
                ctx = {
                    worksheet: worksheetName,
                    table: table.html()
                },
                href = uri + base64(format(template, ctx));
            return href;
        }
    };
});

app.controller('dailySalesReportCtrl', ["$scope", "$http", "$state", "$log", "Excel", "$timeout", function($scope, $http, $state, $log, Excel, $timeout) {

    $scope.saleslist = [];
    $scope.adminBranch = false;
    $scope.today = function() {
        $scope.dt = new Date();
    };
    $scope.today();

    $scope.clear = function() {
        $scope.dt = null;
    };

    // Disable weekend selection
    $scope.disabled = function(date, mode) {
        return (mode === 'day' && (date.getDay() === 0 || date.getDay() === 6));
    };

    $scope.toggleMin = function() {
        $scope.minDate = $scope.minDate ? null : new Date();
    };
    $scope.toggleMin();

    $scope.open = function($event) {
        $event.preventDefault();
        $event.stopPropagation();

        $scope.opened = !$scope.opened;
    };
    $scope.endOpen = function($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.startOpened = false;
        $scope.endOpened = !$scope.endOpened;
    };
    $scope.startOpen = function($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.endOpened = false;
        $scope.startOpened = !$scope.startOpened;
    };

    $scope.dateOptions = {
        formatYear: 'yy',
        startingDay: 1
    };

    $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
    $scope.format = $scope.formats[0];

    $scope.hstep = 1;
    $scope.mstep = 15;

    // Time Picker
    $scope.options = {
        hstep: [1, 2, 3],
        mstep: [1, 5, 10, 15, 25, 30]
    };

    $scope.ismeridian = true;
    $scope.toggleMode = function() {
        $scope.ismeridian = !$scope.ismeridian;
    };

    $scope.update = function() {
        var d = new Date();
        d.setHours(14);
        d.setMinutes(0);
        $scope.dt = d;
    };

    $scope.changed = function() {
        $log.log('Time changed to: ' + $scope.dt);
    };

    $scope.clear = function() {
        $scope.dt = null;
    };

    $scope.exportToExcel = function(tableId) { // ex: '#my-table'
        var exportHref = Excel.tableToExcel(tableId, 'WireWorkbenchDataExport');
        $timeout(function() {
            location.href = exportHref;
        }, 100); // trigger download
    };
    //  var brand_id = $state.params.brand_id;
    // console.log(brand_id);
    var request = $http({

        method: "post",
        url: "index.php/taxmain/gettaxList",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }

    });
    request.success(function(response) {

        $scope.tax_list_all = response.tax_list_all;
        //console.log($scope.tax_list_all);

        for (var i = 0; i < $scope.tax_list_all.length; i++) {
            angular.element('#subTotalTd').after('<td></td>');
        }
    });

    // var request = $http({
    //                             method: "post",
    //                             url: "index.php/brand/getbrandList",
    //                             data: {},
    //                             headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    //                         });
    //                     request.success(function (response) {

    //                         $scope.brand_list = response.brand_list;
    //                       });

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
            $scope.adminBranch = true;
        }

        $scope.branch_list = response.branch_list;

    });
    //url: "index.php/report/daily_sales_rpt",
    var request = $http({
        method: "post",
        url: "index.php/report/daily_sales_report_default",
        data: {},
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });

    request.success(function(response) {

        $scope.saleslist = response.data;
        //console.log($scope.waiter_list);  

    });

    $scope.viewReport = function(brand_id) {
        var param = $("[name='report_form']").serialize();

        angular.element("#pdf_loader").show();

        //console.log(param);

        var request = $http({
            method: "post",
            url: "index.php/report/daily_sales_report",
            data: param,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });

        request.success(function(response) {

            angular.element("#pdf_loader").hide();

            $scope.saleslist = response.data;
            console.log($scope.saleslist);  
        });

    }
    $scope.viewReportPDF = function(brand_id) {
        var param = $("[name='report_form']").serialize();

        angular.element("#pdf_loader").show();

        var request = $http({
            method: "post",
            url: "index.php/report/get_daily_sales_by_branch_pdf",
            data: param,
            responseType: 'arraybuffer',
            headers: {
                accept: 'application/pdf',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            cache: true,
        });

        request.success(function(response, status, headers) {

            var file = new Blob([response], {
                type: 'application/pdf'
            });
            var fileURL = URL.createObjectURL(file);
            window.open(fileURL);

            angular.element("#pdf_loader").hide();
        });

    }

    $scope.gettotalTaxTotal = function() {
        var final_total = 0;
        for (var i = 0; i < $scope.saleslist.length; i++) {

            var order = $scope.saleslist[i];

            final_total += parseFloat(order.totalTax != null ? order.totalTax : 0);
        }

        return final_total;
    }

    $scope.getbill_amountTotal = function() {
        var final_total = 0;
        for (var i = 0; i < $scope.saleslist.length; i++) {

            var order = $scope.saleslist[i];

            final_total += parseFloat(order.bill_amount != null ? order.bill_amount : 0);
        }

        return final_total;
    }

    $scope.getroundoffTotal = function() {
        var final_total = 0;
        for (var i = 0; i < $scope.saleslist.length; i++) {

            var order = $scope.saleslist[i];

            final_total += parseFloat(order.round_off != null ? order.round_off  : 0);
        }

        return final_total;
    }
    $scope.getRound = function() {
        var final_total = 0;
        for (var i = 0; i < $scope.saleslist.length; i++) {

            var order = $scope.saleslist[i];

            final_total += parseFloat(order.total != null ? order.total : 0);
        }

        return final_total;
    }

    $scope.getTotalSGST = function() {
        var final_total = 0;
        for (var i = 0; i < $scope.saleslist.length; i++) {

            var order = $scope.saleslist[i];

            final_total += parseFloat(order.SGST != null ? order.SGST : 0);
        }
        //console.log('final='+final_total);
        return final_total;
    }



    $scope.getTotalCGST = function() {
        var final_total = 0;
        for (var i = 0; i < $scope.saleslist.length; i++) {

            var order = $scope.saleslist[i];

            final_total += parseFloat(order.CGST != null ? order.CGST : 0);
        }

        return final_total;
    }

   

    $scope.getNettotal = function() {
        var final_total = 0;
        for (var i = 0; i < $scope.saleslist.length; i++) {

            var order = $scope.saleslist[i];

            final_total += parseFloat(order.net_amount != null ? order.net_amount : 0);

        }
        //console.log(final_total);
        return final_total;
    }

    $scope.getTaxfree = function() {
        var tax_free_final_total = 0;
        for (var i = 0; i < $scope.saleslist.length; i++) {

            var order = $scope.saleslist[i];

            tax_free_final_total += parseFloat(order.tax_free != null ? order.tax_free : 0);

        }
        //console.log(final_total);
        return tax_free_final_total;
    }

    $scope.getTotaldiscount = function() {
        var final_total = 0;
        for (var i = 0; i < $scope.saleslist.length; i++) {

            var order = $scope.saleslist[i];

            final_total += parseFloat(order.discount != null ? order.discount : 0);

        }
        //console.log(final_total);
        return final_total;
    }

    $scope.gettotalTaxAmount = function(tax_id) {
        var final_total = 0;

        //console.log($scope.saleslist.length);

        for (var i = 0; i < $scope.saleslist.length; i++) {

            var order = $scope.saleslist[i];

            angular.forEach(order.order_tax, function(value, key) {

                //console.log(key);

                if (key == tax_id) {

                    final_total += parseFloat(value.tax_amount != null ? value.tax_amount : 0);

                    //final_total += ((order.sub_total-order.discount)*value.tax_percent)/100;

                }

            });
        }

        return final_total.toFixed(2);
    }

}]);