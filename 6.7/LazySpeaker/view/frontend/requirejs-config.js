var config = {
    deps: [
        'js/category-slider'
    ],
    paths: {
        'datatables.net': 'Mageplaza_LazySpeaker/js/jquery.dataTables.min',
        "datatables": "Mageplaza_LazySpeaker/js/dataTables.bootstrap4.min",
        "datatables_select": "Mageplaza_LazySpeaker/js/dataTables.select.min",
        "datatables_rowreorder": "Mageplaza_LazySpeaker/js/dataTables.rowReorder.min",
        "sweetalert2": "Mageplaza_LazySpeaker/js/sweetalert2.all.min",
        "responsivevoice": "Mageplaza_LazySpeaker/js/responsivevoice",
        "owlcarousel": "Mageplaza_LazySpeaker/js/owl.carousel.min"
    },
    shim: {
        "dataTableJs": ["jquery"],
        'owlcarousel': {
            deps: ['jquery']
        }
    }
};
