$(document).ready(function () {
    // DataTables
    dataTablesProductLists();

    function dataTablesProductLists() {
        $("#product-list .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/master/product/list/get/",
            },
            columns: [
                {
                    data: "ProductOwner",
                    name: "ms_product.ProductOwner",
                },
                {
                    data: "ProductID",
                    name: "ms_product.ProductID",
                },
                {
                    data: "ProductName",
                    name: "ms_product.ProductName",
                },
                {
                    data: "ProductImage",
                    name: "ms_product.ProductImage",
                },
                {
                    data: "ProductCategoryName",
                    name: "ms_product_category.ProductCategoryName",
                },
                {
                    data: "ProductTypeName",
                    name: "ms_product_type.ProductTypeName",
                },
                {
                    data: "Brand",
                    name: "ms_brand_type.Brand",
                },
                {
                    data: "ProductUOMName",
                    name: "ms_product_uom.ProductUOMName",
                },
                {
                    data: "ProductUOMDesc",
                    name: "ms_product.ProductUOMDesc",
                },
                {
                    data: "Price",
                    name: "ms_product.Price",
                },
                {
                    data: "Action",
                    name: "Action",
                    orderable: false,
                    searchable: false,
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "ProductLists"
                        );
                    },
                    action: exportDatatableHelper.newExportAction,
                    text: "Export",
                    titleAttr: "Excel",
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [0, 1, 3, 4, 5, 6, 7, 8, 9],
                        orthogonal: "export",
                    },
                },
            ],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
            aoColumnDefs: [
                {
                    aTargets: [9],
                    mRender: function (data, type, full) {
                        if (type === "export") {
                            return data;
                        } else {
                            if (data == null || data == "") {
                                return data;
                            } else {
                                var currencySeperatorFormat =
                                    thousands_separators(data);
                                return currencySeperatorFormat;
                            }
                        }
                    },
                },
            ],
        });
    }
});
