$(document).ready(function () {
    // DataTables
    dataTablesSalesList();

    function dataTablesSalesList() {
        $("#sales-list .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'tl><'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/rtsales/saleslist/get",
            },
            columns: [
                {
                    data: "SalesName",
                    name: "ms_sales.SalesName",
                },
                {
                    data: "SalesCode",
                    name: "ms_sales.SalesCode",
                },
                {
                    data: "SalesLevel",
                    name: "ms_sales.SalesLevel",
                },
                {
                    data: "Team",
                    name: "Team",
                },
                {
                    data: "PhoneNumber",
                    name: "ms_sales.PhoneNumber",
                },
                {
                    data: "SalesWorkStatusName",
                    name: "ms_sales_work_status.SalesWorkStatusName",
                },
                {
                    data: "ProductGroupName",
                    name: "ms_product_group.ProductGroupName",
                    orderable: false,
                },
                {
                    data: "IsActive",
                    name: "ms_sales.IsActive",
                },
                {
                    data: "JoinDate",
                    name: "ms_sales.JoinDate",
                },
                {
                    data: "ResignDate",
                    name: "ms_sales.ResignDate",
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
                            "SalesList"
                        );
                    },
                    action: exportDatatableHelper.newExportAction,
                    text: "Export",
                    titleAttr: "Excel",
                    className: "btn-sm",
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                        orthogonal: "export",
                    },
                },
            ],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }

    $("#sales-list table").on("click", ".delete-sales", function (e) {
        e.preventDefault();
        const salesName = $(this).data("sales-name");
        const salesCode = $(this).data("sales-code");
        $.confirm({
            title: "Delete Sales Data!",
            content: `Are you sure want to delete <b>${salesCode} - ${salesName}</b> ?`,
            closeIcon: true,
            buttons: {
                delete: {
                    btnClass: "btn-red",
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        window.location =
                            "/rtsales/saleslist/delete/" + salesCode;
                    },
                },
                cancel: function () {},
            },
        });
    });
});
