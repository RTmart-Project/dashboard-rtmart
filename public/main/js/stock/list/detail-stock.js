$(document).ready(function () {
    // DataTables
    dataTablesDetailStock();

    function dataTablesDetailStock() {
        let roleID = $('meta[name="role-id"]').attr("content");

        $("#detail-stock .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-detail-stock'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            ajax: {
                url: window.location.href,
            },
            columns: [
                {
                    data: "PurchaseID",
                    name: "ms_stock_product.PurchaseID",
                    orderable: false,
                },
                {
                    data: "QtyBefore",
                    name: "ms_stock_product_log.QtyBefore",
                    orderable: false,
                },
                {
                    data: "QtyAction",
                    name: "ms_stock_product_log.QtyAction",
                    orderable: false,
                },
                {
                    data: "QtyAfter",
                    name: "ms_stock_product_log.QtyAfter",
                    orderable: false,
                },
                {
                    data: "PurchasePrice",
                    name: "ms_stock_product_log.PurchasePrice",
                    orderable: false,
                },
                {
                    data: "CreatedDate",
                    name: "ms_stock_product_log.CreatedDate",
                    type: "date",
                    orderable: false,
                },
                {
                    data: "ConditionStock",
                    name: "ms_stock_product.ConditionStock",
                    orderable: false,
                },
                {
                    data: "ActionType",
                    name: "ms_stock_product_log.ActionType",
                    orderable: false,
                },
                {
                    data: "ActionBy",
                    name: "ms_stock_product_log.ActionBy",
                    orderable: false,
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "DetailListStock"
                        );
                    },
                    text: "Export",
                    className: "btn-sm",
                    titleAttr: "Excel",
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                        orthogonal: "export",
                    },
                },
            ],
            aoColumnDefs: [
                {
                    aTargets: [4],
                    visible: roleID == "AD" ? false : true,
                },
            ],
            ordering: false,
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }
});
