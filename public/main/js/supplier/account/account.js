$(document).ready(function () {
    // DataTables
    dataTablesSupplierAccount();

    function dataTablesSupplierAccount() {
        $("#supplier-account .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-supplier-account'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            ajax: {
                url: "/supplier/account/getAllSupplier",
            },
            columns: [
                {
                    data: "SupplierID",
                    name: "SupplierID",
                },
                {
                    data: "SupplierName",
                    name: "SupplierName",
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "DistributorAccounts"
                        );
                    },
                    text: "Export",
                    titleAttr: "Excel",
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [0, 1],
                        orthogonal: "export",
                    },
                },
            ],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }
});
