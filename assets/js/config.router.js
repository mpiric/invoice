'use strict';

/**
 * Config for the router
 */
app.config(['$stateProvider', '$urlRouterProvider', '$controllerProvider', '$compileProvider', '$filterProvider', '$provide', '$ocLazyLoadProvider', 'JS_REQUIRES',
    function($stateProvider, $urlRouterProvider, $controllerProvider, $compileProvider, $filterProvider, $provide, $ocLazyLoadProvider, jsRequires) {

        app.controller = $controllerProvider.register;
        app.directive = $compileProvider.directive;
        app.filter = $filterProvider.register;
        app.factory = $provide.factory;
        app.service = $provide.service;
        app.constant = $provide.constant;
        app.value = $provide.value;

        // LAZY MODULES

        $ocLazyLoadProvider.config({
            debug: false,
            events: true,
            modules: jsRequires.modules
        });

        // APPLICATION ROUTES
        // -----------------------------------
        // For any unmatched url, redirect to /app/dashboard
        // $urlRouterProvider.otherwise("/app/dashboard");
        $urlRouterProvider.otherwise("/login/signin");
        //
        // Set up the states
        $stateProvider.state('app', {
                url: "/app",
                templateUrl: "assets/views/app.html",
                resolve: loadSequence('modernizr', 'moment', 'angularMoment', 'perfect-scrollbar-plugin', 'ngAside', 'vAccordion', 'navCtrl', 'chartjs', 'tc.chartjs', 'chatCtrl'),
                abstract: true
            }).state('app.dashboard', {
                url: "/dashboard",
                templateUrl: "assets/views/dashboard.html",
                resolve: loadSequence('jquery-sparkline', 'dashboardCtrl'),
                title: 'Dashboard',
                ncyBreadcrumb: {
                    label: 'Dashboard'
                }
            }).state('app.dashboard_new', {
                url: "/dashboard_new",
                templateUrl: "assets/views/dashboard_new.html",
                resolve: loadSequence('jquery-sparkline', 'dashboardNewCtrl'),
                title: 'Dashboard',
                ncyBreadcrumb: {
                    label: 'Dashboard'
                }
            }).state('app.report.waiter', {
                url: "/waiter_report",
                templateUrl: "index.php/report/waiter_report",
                resolve: loadSequence('waiterSalesCtrl'),
                title: 'Waiter Report',
                ncyBreadcrumb: {
                    label: 'Waiter Report'
                }
            }).state('app.report.sales', {
                url: "/sales_report",
                templateUrl: "index.php/report/sales_report",
                resolve: loadSequence('jquery-sparkline', 'salesCtrl'),
                title: 'Sales Report',
                ncyBreadcrumb: {
                    label: 'Sales Report'
                }
            }).state('app.report.dailysales', {
                url: "/daily_sales",
                templateUrl: "index.php/report/daily_sales",
                resolve: loadSequence('jquery-sparkline', 'dailySalesReportCtrl'),
                title: 'Daily Sales Report',
                ncyBreadcrumb: {
                    label: 'Daily Sales Report'
                }
            }).state('app.report.branch', {
                url: "/branch_report",
                templateUrl: "index.php/report/branch_report",
                resolve: loadSequence('jquery-sparkline', 'branchSalesCtrl'),
                title: 'Branch Report',
                ncyBreadcrumb: {
                    label: 'Branch Report'
                }
            }).state('app.report.item_wise_sales', {
                url: "/item_wise_sales",
                templateUrl: "index.php/report/item_wise_sales",
                resolve: loadSequence('generalReportCtrl'),
                title: 'Branch Report',
                ncyBreadcrumb: {
                    label: 'Branch Report'
                }
            }).state('app.report.store_item_report', {
                url: "/store_item_report",
                templateUrl: "index.php/report/store_item_report",
                resolve: loadSequence('generalReportCtrl'),
                title: 'Store Items Report',
                ncyBreadcrumb: {
                    label: 'Store Items Report'
                }
            }).state('app.report.daily_purchase', {
                url: "/daily_purchase",
                templateUrl: "index.php/report/daily_purchase",
                resolve: loadSequence('generalReportCtrl'),
                title: 'Daily Purchase Report',
                ncyBreadcrumb: {
                    label: 'Daily Purchase Report'
                }
            }).state('app.report.waiter_rpt', {
                url: "/waiter_rpt",
                templateUrl: "index.php/report/waiter_rpt",
                resolve: loadSequence('generalReportCtrl'),
                title: 'waiter Report',
                ncyBreadcrumb: {
                    label: 'waiter Report'
                }
            }).state('app.report.brandwisedailysales', {
                url: "/brandwisedailysales",
                templateUrl: "index.php/report/brandwisedailysales",
                resolve: loadSequence('brandWiseDailySalesReportCtrl'),
                title: 'Brand Wise Daily Sales Report',
                ncyBreadcrumb: {
                    label: 'Brand Wise Daily Sales Report'
                }
            }).state('app.report.product_recipe', {
                url: "/product_recipe",
                templateUrl: "index.php/report/product_recipe",
                resolve: loadSequence('generalReportCtrl'),
                title: 'Product Recipe Report',
                ncyBreadcrumb: {
                    label: 'Product Recipe Report'
                }
            }).state('app.dashboardAdmin', {
                url: "/dashboardAdmin",
                templateUrl: "assets/views/dashboard_admin.html",
                resolve: loadSequence('jquery-sparkline', 'dashboardAdminCtrl'),
                title: 'Dashboard',
                ncyBreadcrumb: {
                    label: 'Dashboard'
                }
            }).state('app.table', {
                url: '/table',
                template: '<div ui-view class="fade-in-up"></div>',
                title: 'Tables',
                ncyBreadcrumb: {
                    label: 'Tables'
                }
            }).state('app.waiter', {
                url: '/waiter',
                template: '<div ui-view class="fade-in-up"></div>',
                title: 'Waiter',
                ncyBreadcrumb: {
                    label: 'Waiter'
                }
            }).state('app.waiter.create', {
                url: '/create',
                templateUrl: "index.php/waiter/create",
                title: 'Create',
                ncyBreadcrumb: {
                    label: 'Create'
                },
                resolve: loadSequence('waiterCtrl')
            }).state('app.waiter.update', {
                url: '/update?waiter_id',
                templateUrl: "index.php/waiter/create",
                title: 'Update',
                ncyBreadcrumb: {
                    label: 'Update'
                },
                resolve: loadSequence('ngTable', 'waiterCtrl')
            }).state('app.waiter.list', {
                url: '/list',
                templateUrl: "index.php/waiter/index",
                title: 'List',
                ncyBreadcrumb: {
                    label: 'List'
                },
                resolve: loadSequence('ngTable', 'waiterCtrl')
            }).state('app.waiter.info', {
                url: '/info?waiter_id',
                templateUrl: "index.php/waiter/infoWaiter",
                title: 'Info',
                ncyBreadcrumb: {
                    label: 'Info'
                },
                resolve: loadSequence('ngTable', 'waiterCtrl')
            }).state('app.product', {
                url: '/product',
                template: '<div ui-view class="fade-in-up"></div>',
                title: 'Product',
                ncyBreadcrumb: {
                    label: 'Product'
                }
            }).state('app.product.create', {
                url: '/create',
                templateUrl: "index.php/product/create",
                title: 'Create',
                ncyBreadcrumb: {
                    label: 'Create'
                },
                resolve: loadSequence('ui.select', 'productCtrl')
            }).state('app.product.update', {
                url: '/update?product_id',
                templateUrl: "index.php/product/create",
                title: 'Update',
                ncyBreadcrumb: {
                    label: 'Update'
                },
                resolve: loadSequence('ui.select', 'productCtrl')
            }).state('app.product.list', {
                url: '/list',
                templateUrl: "index.php/product/index",
                title: 'List',
                ncyBreadcrumb: {
                    label: 'List'
                },
                resolve: loadSequence('ngTable', 'productCtrl')
            }).state('app.product.info', {
                url: '/info?product_id',
                templateUrl: "index.php/product/infoProduct",
                title: 'Info',
                ncyBreadcrumb: {
                    label: 'Info'
                },
                resolve: loadSequence('ngTable', 'productCtrl')
            }).state('app.productcategory', {
                url: '/productCategory',
                template: '<div ui-view class="fade-in-up"></div>',
                title: 'Productcategory',
                ncyBreadcrumb: {
                    label: 'Product Category'
                }
            }).state('app.productcategory.create', {
                url: '/create',
                templateUrl: "index.php/productCategory/create",
                title: 'Create',
                ncyBreadcrumb: {
                    label: 'Create'
                },
                resolve: loadSequence('ui.select', 'ngTable', 'productcategoryCtrl')
            }).state('app.productcategory.update', {
                url: '/update?product_category_id   ',
                templateUrl: "index.php/productCategory/create",
                title: 'Update',
                ncyBreadcrumb: {
                    label: 'Update'
                },
                resolve: loadSequence('ui.select', 'ngTable', 'productcategoryCtrl')
            }).state('app.productcategory.info', {
                url: '/info?product_category_id',
                templateUrl: "index.php/productCategory/infoProductcategory",
                title: 'Info',
                ncyBreadcrumb: {
                    label: 'Info'
                },
                resolve: loadSequence('ngTable', 'productcategoryCtrl')
            }).state('app.productcategory.list', {
                url: '/list',
                templateUrl: "index.php/productCategory/index",
                title: 'List',
                ncyBreadcrumb: {
                    label: 'List'
                },
                resolve: loadSequence('ngTable', 'productcategoryCtrl')
            }).state('app.taxmain', {
                url: '/taxmain',
                template: '<div ui-view class="fade-in-up"></div>',
                title: 'Taxmain',
                ncyBreadcrumb: {
                    label: 'Taxmain'
                }
            }).state('app.taxmain.create', {
                url: '/create',
                templateUrl: "index.php/taxmain/create",
                title: 'Create',
                ncyBreadcrumb: {
                    label: 'Create'
                },
                resolve: loadSequence('taxmainCtrl')
            }).state('app.taxmain.update', {
                url: '/update?tax_id',
                templateUrl: "index.php/taxmain/create",
                title: 'Update',
                ncyBreadcrumb: {
                    label: 'Update'
                },
                resolve: loadSequence('ngTable', 'taxmainCtrl')
            }).state('app.taxmain.list', {
                url: '/list',
                templateUrl: "index.php/taxmain/index",
                title: 'List',
                ncyBreadcrumb: {
                    label: 'List'
                },
                resolve: loadSequence('ngTable', 'taxmainCtrl')
            }).state('app.taxmain.info', {
                url: '/info?tax_id',
                templateUrl: "index.php/taxmain/infotaxmain",
                title: 'Info',
                ncyBreadcrumb: {
                    label: 'Info'
                },
                resolve: loadSequence('ngTable', 'taxmainCtrl')
            }).state('app.waitercommission', {
                url: '/waiterCommission',
                template: '<div ui-view class="fade-in-up"></div>',
                title: 'Waitercommission',
                ncyBreadcrumb: {
                    label: 'Waiter Commission'
                }
            }).state('app.waitercommission.create', {
                url: '/create',
                templateUrl: "index.php/waiterCommission/create",
                title: 'Create',
                ncyBreadcrumb: {
                    label: 'Create'
                },
                resolve: loadSequence('ui.select', 'waitercommissionCtrl')
            }).state('app.waitercommission.update', {
                url: '/update?waiter_commission_id',
                templateUrl: "index.php/waiterCommission/create",
                title: 'Update',
                ncyBreadcrumb: {
                    label: 'Update'
                },
                resolve: loadSequence('ui.select', 'waitercommissionCtrl')
            }).state('app.waitercommission.list', {
                url: '/list',
                templateUrl: "index.php/waiterCommission/index",
                title: 'List',
                ncyBreadcrumb: {
                    label: 'List'
                },
                resolve: loadSequence('ngTable', 'waitercommissionCtrl')
            }).state('app.waitercommission.info', {
                url: '/info?waiter_commission_id',
                templateUrl: "index.php/waiterCommission/infoWaitercommission",
                title: 'Info',
                ncyBreadcrumb: {
                    label: 'Info'
                },
                resolve: loadSequence('ngTable', 'waitercommissionCtrl')
            }).state('app.branchproducts', {
                url: '/branchProducts',
                template: '<div ui-view class="fade-in-up"></div>',
                title: 'Branchproducts',
                ncyBreadcrumb: {
                    label: 'Branch Products'
                }
            }).state('app.branchproducts.create', {
                url: '/create',
                templateUrl: "index.php/branchProducts/create",
                title: 'Create',
                ncyBreadcrumb: {
                    label: 'Create'
                },
                resolve: loadSequence('ngTable', 'branchproductsCtrl')
            }).state('app.branchproducts.update', {
                url: '/update?branch_products_id   ',
                templateUrl: "index.php/branchProducts/create",
                title: 'Update',
                ncyBreadcrumb: {
                    label: 'Update'
                },
                resolve: loadSequence('ngTable', 'branchproductsCtrl')
            }).state('app.branchproducts.info', {
                url: '/info?product_category_id',
                templateUrl: "index.php/branchProducts/infoBranchproducts",
                title: 'Info',
                ncyBreadcrumb: {
                    label: 'Info'
                },
                resolve: loadSequence('ngTable', 'branchproductsCtrl')
            }).state('app.branchproducts.list', {
                url: '/list',
                templateUrl: "index.php/branchProducts/index",
                title: 'List',
                ncyBreadcrumb: {
                    label: 'List'
                },
                resolve: loadSequence('ngTable', 'branchproductsCtrl')
            }).state('app.customer', {
                url: '/customer',
                template: '<div ui-view class="fade-in-up"></div>',
                title: 'Customer',
                ncyBreadcrumb: {
                    label: 'Customer'
                }
            }).state('app.customer.create', {
                url: '/create',
                templateUrl: "index.php/customer/create",
                title: 'Create',
                ncyBreadcrumb: {
                    label: 'Create'
                },
                resolve: loadSequence('ui.select', 'customerCtrl')
            }).state('app.customer.update', {
                url: '/update?customer_id',
                templateUrl: "index.php/customer/create",
                title: 'Update',
                ncyBreadcrumb: {
                    label: 'Update'
                },
                resolve: loadSequence('ui.select', 'customerCtrl')
            }).state('app.customer.list', {
                url: '/list',
                templateUrl: "index.php/customer/index",
                title: 'List',
                ncyBreadcrumb: {
                    label: 'List'
                },
                resolve: loadSequence('ngTable', 'customerCtrl')
            }).state('app.customer.info', {
                url: '/info?customer_id',
                templateUrl: "index.php/customer/infoCustomer",
                title: 'Info',
                ncyBreadcrumb: {
                    label: 'Info'
                },
                resolve: loadSequence('ngTable', 'customerCtrl')
            }).state('app.tax', {
                url: '/tax',
                template: '<div ui-view class="fade-in-up"></div>',
                title: 'Tax',
                ncyBreadcrumb: {
                    label: 'Tax'
                }
            }).state('app.tax.create', {
                url: '/create',
                templateUrl: "index.php/tax/create",
                title: 'Create',
                ncyBreadcrumb: {
                    label: 'Create'
                },
                resolve: loadSequence('ngTable', 'taxCtrl')
            }).state('app.tax.update', {
                url: '/update?tax_master_id',
                templateUrl: "index.php/tax/create",
                title: 'Update',
                ncyBreadcrumb: {
                    label: 'Update'
                },
                resolve: loadSequence('ngTable', 'taxCtrl')
            }).state('app.tax.info', {
                url: '/info?tax_master_id',
                templateUrl: "index.php/tax/infoTax",
                title: 'Info',
                ncyBreadcrumb: {
                    label: 'Info'
                },
                resolve: loadSequence('ngTable', 'taxCtrl')
            }).state('app.tax.list', {
                url: '/list',
                templateUrl: "index.php/tax/index",
                title: 'List',
                ncyBreadcrumb: {
                    label: 'List'
                },
                resolve: loadSequence('ngTable', 'taxCtrl')
            }).state('app.brand', {
                url: '/brand',
                template: '<div ui-view class="fade-in-up"></div>',
                title: 'Brand',
                ncyBreadcrumb: {
                    label: 'Brand'
                }
            }).state('app.brand.create', {
                url: '/create',
                templateUrl: "index.php/brand/create",
                title: 'Create',
                ncyBreadcrumb: {
                    label: 'Create'
                },
                resolve: loadSequence('ngTable', 'brandCtrl')
            }).state('app.brand.update', {
                url: '/update?brand_id',
                templateUrl: "index.php/brand/create",
                title: 'Update',
                ncyBreadcrumb: {
                    label: 'Update'
                },
                resolve: loadSequence('ngTable', 'brandCtrl')
            }).state('app.brand.info', {
                url: '/info?brand_id',
                templateUrl: "index.php/brand/infoBrand",
                title: 'Info',
                ncyBreadcrumb: {
                    label: 'Info'
                },
                resolve: loadSequence('ngTable', 'brandCtrl')
            }).state('app.brand.list', {
                url: '/list',
                templateUrl: "index.php/brand/index",
                title: 'List',
                ncyBreadcrumb: {
                    label: 'List'
                },
                resolve: loadSequence('ngTable', 'brandCtrl')
            }).state('app.order', {
                url: '/order',
                template: '<div ui-view class="fade-in-up"></div>',
                title: 'Orders',
                ncyBreadcrumb: {
                    label: 'Orders'
                }
            }).state('app.order.create', {
                url: '/create',
                templateUrl: "index.php/order/create",
                title: 'Create',
                ncyBreadcrumb: {
                    label: 'Create'
                },
                resolve: loadSequence('ui.select', 'orderCtrl', 'pdfmake', 'vfs_fonts', 'cfp.hotkeys')
            }).state('app.order.list', {
                url: '/list',
                templateUrl: "index.php/order/index",
                title: 'List',
                ncyBreadcrumb: {
                    label: 'List'
                },
                resolve: loadSequence('ngTable', 'orderCtrl')
            }).state('app.order.orderUpdate', {
                url: '/update?order_id',
                templateUrl: "index.php/order/orderUpdate",
                title: 'Update',
                ncyBreadcrumb: {
                    label: 'Update'
                },
                resolve: loadSequence('ngTable', 'orderCtrl')
            }).state('app.branch', {
                url: '/branch',
                template: '<div ui-view class="fade-in-up"></div>',
                title: 'Branch',
                ncyBreadcrumb: {
                    label: 'Branch'
                }
            }).state('app.branch.create', {
                url: '/create',
                templateUrl: "index.php/branch/create",
                title: 'Create',
                ncyBreadcrumb: {
                    label: 'Create'
                },
                resolve: loadSequence('ui.select', 'ngTable', 'branchCtrl')
            }).state('app.branch.update', {
                url: '/update?branch_id',
                templateUrl: "index.php/branch/create",
                title: 'Update',
                ncyBreadcrumb: {
                    label: 'Update'
                },
                resolve: loadSequence('ui.select', 'ngTable', 'branchCtrl')
            }).state('app.branch.info', {
                url: '/info?branch_id',
                templateUrl: "index.php/branch/infoBranch",
                title: 'Info',
                ncyBreadcrumb: {
                    label: 'Info'
                },
                resolve: loadSequence('ngTable', 'branchCtrl')
            }).state('app.branch.list', {
                url: '/list',
                templateUrl: "index.php/branch/index",
                title: 'List',
                ncyBreadcrumb: {
                    label: 'List'
                },
                resolve: loadSequence('ngTable', 'branchCtrl')
            })
            .state('app.report', {
                url: '/report',
                template: '<div ui-view class="fade-in-up"></div>',
                title: 'Report',
                ncyBreadcrumb: {
                    label: 'Report'
                }
            }).state('app.storeproduct', {
                url: '/storeproduct',
                template: '<div ui-view class="fade-in-up"></div>',
                title: 'Store Product',
                ncyBreadcrumb: {
                    label: 'Store Product'
                }
            }).state('app.storeproduct.create', {
                url: '/create',
                templateUrl: "index.php/storeproduct/create",
                title: 'Create',
                ncyBreadcrumb: {
                    label: 'Create'
                },
                resolve: loadSequence('ui.select', 'ngTable', 'storeproductCtrl')
            }).state('app.storeproduct.update', {
                url: '/update?store_product_id',
                templateUrl: "index.php/storeproduct/create",
                title: 'Update',
                ncyBreadcrumb: {
                    label: 'Update'
                },
                resolve: loadSequence('ui.select', 'ngTable', 'storeproductCtrl')
            }).state('app.storeproduct.list', {
                url: '/list',
                templateUrl: "index.php/storeproduct/index",
                title: 'List',
                ncyBreadcrumb: {
                    label: 'List'
                },
                resolve: loadSequence('ngTable', 'storeproductCtrl')
            }).state('error', {
                url: '/error',
                template: '<div ui-view class="fade-in-up"></div>'
            }).state('error.404', {
                url: '/404',
                templateUrl: "assets/views/utility_404.html",
            }).state('error.500', {
                url: '/500',
                templateUrl: "assets/views/utility_500.html",
            }).state('app.kitcheninward', {
                url: '/kitcheninward',
                template: '<div ui-view class="fade-in-up"></div>',
                title: 'Kitchen Inward',
                ncyBreadcrumb: {
                    label: 'Kitchen Inward'
                }
            }).state('app.kitcheninward.create', {
                url: '/create?kitchen_inward_id',
                templateUrl: "index.php/kitcheninward/create",
                title: 'Manage',
                ncyBreadcrumb: {
                    label: 'Manage'
                },
                resolve: loadSequence('ui.select', 'ngTable', 'kitcheninwardCtrl')
            }).state('app.storeinward', {
                url: '/storeinward',
                template: '<div ui-view class="fade-in-up"></div>',
                title: 'Store Inward',
                ncyBreadcrumb: {
                    label: 'Store Inward'
                }
            }).state('app.storeinward.create', {
                url: '/create',
                templateUrl: "index.php/storeinward/create",
                title: 'Create',
                ncyBreadcrumb: {
                    label: 'Create'
                },
                resolve: loadSequence('ui.select', 'ngTable', 'storeinwardCtrl')
            }).state('app.storeinward.update', {
                url: '/update?store_inward_id',
                templateUrl: "index.php/storeinward/create",
                title: 'Update',
                ncyBreadcrumb: {
                    label: 'Update'
                },
                resolve: loadSequence('ui.select', 'ngTable', 'storeinwardCtrl')
            }).state('app.storeinward.list', {
                url: '/list',
                templateUrl: "index.php/storeinward/index",
                title: 'List',
                ncyBreadcrumb: {
                    label: 'List'
                },
                resolve: loadSequence('ngTable', 'storeinwardCtrl')
            }).state('app.category', {
                url: '/category',
                template: '<div ui-view class="fade-in-up"></div>',
                title: 'Category',
                ncyBreadcrumb: {
                    label: 'Category'
                }
            }).state('app.category.create', {
                url: '/create',
                templateUrl: "index.php/category/create",
                title: 'Create',
                ncyBreadcrumb: {
                    label: 'Create'
                },
                resolve: loadSequence('categoryCtrl')
            }).state('app.category.update', {
                url: '/update?category_id',
                templateUrl: "index.php/category/create",
                title: 'Update',
                ncyBreadcrumb: {
                    label: 'Update'
                },
                resolve: loadSequence('ngTable', 'categoryCtrl')
            }).state('app.category.list', {
                url: '/list',
                templateUrl: "index.php/category/index",
                title: 'List',
                ncyBreadcrumb: {
                    label: 'List'
                },
                resolve: loadSequence('ngTable', 'categoryCtrl')
            }).state('app.productrecipe', {
                url: '/productrecipe',
                template: '<div ui-view class="fade-in-up"></div>',
                title: 'productrecipe',
                ncyBreadcrumb: {
                    label: 'Product Recipe'
                }
            }).state('app.productrecipe.create', {
                url: '/create',
                templateUrl: "index.php/productrecipe/create",
                title: 'Create',
                ncyBreadcrumb: {
                    label: 'Create'
                },
                resolve: loadSequence('ui.select', 'ngTable', 'productrecipeCtrl')
            })

        // Login routes

        .state('login', {
            url: '/login',
            template: '<div ui-view class="fade-in-right-big smooth"></div>',
            abstract: true
        }).state('login.signin', {
            url: '/signin',
            templateUrl: "assets/views/login_login.html",
            controller: function($scope, $http, $state) {

                $scope.submitLogin = function(login_form) {
                    if (login_form.$valid) {

                        var request = $http({
                            method: "post",
                            url: "index.php/branch/login",
                            data: {
                                username: $scope.username,
                                password: $scope.password
                            },
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                        });
                        /* Successful HTTP post request or not */
                        request.success(function(data) {

                            //alert(data.status);
                            //console.log(data);

                            if (data.status != "1") {
                                //error
                                alert(data.message);
                            } else {
                                // success;
                                //console.log(data.data.message.branch_type);
                                $scope.user.name = $scope.username;
                                if (data.data.message.branch_type == "1") {
                                    //console.log(data.branch_type);
                                    $state.go('app.dashboardAdmin');
                                    return false;
                                } else {
                                    //$state.go('app.dashboard');
                                    $state.go('app.dashboard_new');
                                }

                            }
                        });

                    }

                }
            }
        }).state('login.forgot', {
            url: '/forgot',
            templateUrl: "assets/views/login_forgot.html"
        }).state('login.registration', {
            url: '/registration',
            templateUrl: "assets/views/login_registration.html"
        }).state('login.lockscreen', {
            url: '/lock',
            templateUrl: "assets/views/login_lock_screen.html"
        }).state('login.logout', {
            url: '/logout/',
            templateUrl: "assets/views/login_login.html",
            controller: function($scope, $http, $state, $location) {
                var request = $http({
                    method: "post",
                    url: "index.php/branch/logout",
                    data: {},
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                /* Successful HTTP post request or not */
                request.success(function(data) {

                    //alert(data.status);
                    console.log(data);
                    // 
                    // $state.reload();
                    //$state.go('login.signin');   

                    if (data == 1) {
                        $state.go('login.signin');
                        // $state.transitionTo("login.signin");
                        //redirectTo: '/login/signin';  
                    }
                });
            }
        });


        // Generates a resolve object previously configured in constant.JS_REQUIRES (config.constant.js)
        function loadSequence() {
            var _args = arguments;
            return {
                deps: ['$ocLazyLoad', '$q',
                    function($ocLL, $q) {
                        var promise = $q.when(1);
                        for (var i = 0, len = _args.length; i < len; i++) {
                            promise = promiseThen(_args[i]);
                        }
                        return promise;

                        function promiseThen(_arg) {
                            if (typeof _arg == 'function')
                                return promise.then(_arg);
                            else
                                return promise.then(function() {
                                    var nowLoad = requiredData(_arg);
                                    if (!nowLoad)
                                        return $.error('Route resolve: Bad resource name [' + _arg + ']');
                                    return $ocLL.load(nowLoad);
                                });
                        }

                        function requiredData(name) {
                            if (jsRequires.modules)
                                for (var m in jsRequires.modules)
                                    if (jsRequires.modules[m].name && jsRequires.modules[m].name === name)
                                        return jsRequires.modules[m];
                            return jsRequires.scripts && jsRequires.scripts[name];
                        }
                    }
                ]
            };
        }
    }
]);