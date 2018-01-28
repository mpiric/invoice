'use strict';

/**
 * Config constant
 */
app.constant('APP_MEDIAQUERY', {
    'desktopXL': 1200,
    'desktop': 992,
    'tablet': 768,
    'mobile': 480
});
app.constant('JS_REQUIRES', {
    //*** Scripts
    scripts: {
        //*** Javascript Plugins
        'modernizr': ['bower_components/components-modernizr/modernizr.js'],
        'moment': ['bower_components/moment/min/moment.min.js'],
        'spin': 'bower_components/spin.js/spin.js',

        //*** jQuery Plugins
        'perfect-scrollbar-plugin': ['bower_components/perfect-scrollbar/js/min/perfect-scrollbar.jquery.min.js', 'bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css'],
        'ladda': ['bower_components/ladda/dist/ladda.min.js', 'bower_components/ladda/dist/ladda-themeless.min.css'],
        'sweet-alert': ['bower_components/sweetalert/lib/sweet-alert.min.js', 'bower_components/sweetalert/lib/sweet-alert.css'],
        'chartjs': 'bower_components/chartjs/Chart.min.js',
        'jquery-sparkline': 'bower_components/jquery.sparkline.build/dist/jquery.sparkline.min.js',
        'ckeditor-plugin': 'bower_components/ckeditor/ckeditor.js',
        'jquery-nestable-plugin': ['bower_components/jquery-nestable/jquery.nestable.js'],
        'touchspin-plugin': ['bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js', 'bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css'],

        //*** Controllers
        'dashboardCtrl': 'assets/js/controllers/dashboardCtrl.js',
        'dashboardNewCtrl': 'assets/js/controllers/dashboardNewCtrl.js',
        'iconsCtrl': 'assets/js/controllers/iconsCtrl.js',
        'vAccordionCtrl': 'assets/js/controllers/vAccordionCtrl.js',
        'ckeditorCtrl': 'assets/js/controllers/ckeditorCtrl.js',
        'laddaCtrl': 'assets/js/controllers/laddaCtrl.js',
        'ngTableCtrl': 'assets/js/controllers/ngTableCtrl.js',
        'branchCtrl': 'assets/js/controllers/branchCtrl.js',
        'waiterCtrl': 'assets/js/controllers/waiterCtrl.js',
        'taxmainCtrl': 'assets/js/controllers/taxmainCtrl.js',
        'categoryCtrl': 'assets/js/controllers/categoryCtrl.js',
        'productCtrl': 'assets/js/controllers/productCtrl.js',
        'taxCtrl': 'assets/js/controllers/taxCtrl.js',
        'brandCtrl': 'assets/js/controllers/brandCtrl.js',
        'dashboardAdminCtrl': 'assets/js/controllers/dashboardAdminCtrl.js',
        'orderCtrl': 'assets/js/controllers/orderCtrl.js',
        'productcategoryCtrl': 'assets/js/controllers/productcategoryCtrl.js',
        'waitercommissionCtrl': 'assets/js/controllers/waitercommissionCtrl.js',
        'branchproductsCtrl': 'assets/js/controllers/branchproductsCtrl.js',
        'customerCtrl': 'assets/js/controllers/customerCtrl.js',
        //'cropCtrl': 'assets/js/controllers/cropCtrl.js',
        'asideCtrl': 'assets/js/controllers/asideCtrl.js',
        //'toasterCtrl': 'assets/js/controllers/toasterCtrl.js',
        'sweetAlertCtrl': 'assets/js/controllers/sweetAlertCtrl.js',
        'mapsCtrl': 'assets/js/controllers/mapsCtrl.js',
        'chartsCtrl': 'assets/js/controllers/chartsCtrl.js',
        'reportCtrl': 'assets/js/controllers/reportCtrl.js',
        //'calendarCtrl': 'assets/js/controllers/calendarCtrl.js',
        'navCtrl': 'assets/js/controllers/navCtrl.js',
        //'nestableCtrl': 'assets/js/controllers/nestableCtrl.js',
        'validationCtrl': ['assets/js/controllers/validationCtrl.js'],
        'userCtrl': ['assets/js/controllers/userCtrl.js'],
        'selectCtrl': 'assets/js/controllers/selectCtrl.js',
        'wizardCtrl': 'assets/js/controllers/wizardCtrl.js',
        'uploadCtrl': 'assets/js/controllers/uploadCtrl.js',
        //'treeCtrl': 'assets/js/controllers/treeCtrl.js',
        //'inboxCtrl': 'assets/js/controllers/inboxCtrl.js',
        'xeditableCtrl': 'assets/js/controllers/xeditableCtrl.js',
        'chatCtrl': 'assets/js/controllers/chatCtrl.js',
        'salesCtrl': 'assets/js/controllers/salesCtrl.js',
        'waiterSalesCtrl': 'assets/js/controllers/waiterSalesCtrl.js',
        'branchSalesCtrl': 'assets/js/controllers/branchSalesCtrl.js',
        'generalReportCtrl': 'assets/js/controllers/generalReportCtrl.js',
        'pdfmake': 'assets/js/pdfmake.js',
        'vfs_fonts': 'assets/js/vfs_fonts.js',
        'dailySalesReportCtrl': 'assets/js/controllers/dailySalesReportCtrl.js',
        //'dailySalesReportCtrl': 'assets/js/controllers/dailySalesReportCtrl.js',
        'pdfmake': 'assets/js/pdfmake.js',
        'vfs_fonts': 'assets/js/vfs_fonts.js',
        'storeproductCtrl': 'assets/js/controllers/storeproductCtrl.js',
        'storeinwardCtrl': 'assets/js/controllers/storeinwardCtrl.js',
        'kitcheninwardCtrl': 'assets/js/controllers/kitcheninwardCtrl.js',
        'productrecipeCtrl': 'assets/js/controllers/productrecipeCtrl.js',
        'tableCtrl': 'assets/js/controllers/tableCtrl.js',
        'brandWiseDailySalesReportCtrl': 'assets/js/controllers/brandWiseDailySalesReportCtrl.js',
        'paymentCtrl': 'assets/js/controllers/paymentCtrl.js',
        




        //*** Filters
        // 'htmlToPlaintext': 'assets/js/filters/htmlToPlaintext.js'
    },
    //*** angularJS Modules
    modules: [{
        name: 'angularMoment',
        files: ['bower_components/angular-moment/angular-moment.min.js']
    }, {
        name: 'angular-ladda',
        files: ['bower_components/angular-ladda/dist/angular-ladda.min.js']
    }, {
        name: 'ngTable',
        files: ['bower_components/ng-table/dist/ng-table.min.js', 'bower_components/ng-table/dist/ng-table.min.css']
    }, {
        name: 'ui.select',
        files: ['bower_components/angular-ui-select/dist/select.min.js', 'bower_components/angular-ui-select/dist/select.min.css', 'bower_components/select2/dist/css/select2.min.css', 'bower_components/select2-bootstrap-css/select2-bootstrap.min.css', 'bower_components/selectize/dist/css/selectize.bootstrap3.css']
    }, {
        name: 'angularFileUpload',
        files: ['bower_components/angular-file-upload/angular-file-upload.min.js']
    }, {
        name: 'ngAside',
        files: ['bower_components/angular-aside/dist/js/angular-aside.min.js', 'bower_components/angular-aside/dist/css/angular-aside.min.css']
    }, {
        name: 'truncate',
        files: ['bower_components/angular-truncate/src/truncate.js']
    }, {
        name: 'flow',
        files: ['bower_components/ng-flow/dist/ng-flow-standalone.min.js']
    }, {
        name: 'vAccordion',
        files: ['bower_components/v-accordion/dist/v-accordion.min.js', 'bower_components/v-accordion/dist/v-accordion.min.css']
    }, {
        name: 'xeditable',
        files: ['bower_components/angular-xeditable/dist/js/xeditable.min.js', 'bower_components/angular-xeditable/dist/css/xeditable.css', 'assets/js/config/config-xeditable.js']
    }, {
        name: 'checklist-model',
        files: ['bower_components/checklist-model/checklist-model.js']
    }, {
        name: 'tc.chartjs',
        files: ['bower_components/tc-angular-chartjs/dist/tc-angular-chartjs.min.js']
    }, {
        name: 'cfp.hotkeys',
        files: ['bower_components/hotkeys.js']
    }]
});