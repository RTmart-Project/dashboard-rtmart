$(document).ready(function () {
    // DataTables
    dataTablesCountMerchantDO();

    function dataTablesCountMerchantDO() {
        const params = location.search;

        $("#summary-count-merchant-do .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            ajax: {
                url: "/summary/reportDetail/countMerchantDO" + params,
            },
            columns: [
                {
                    data: "MerchantID",
                    name: "tmo.MerchantID",
                },
                {
                    data: "StoreName",
                    name: "mma.StoreName",
                },
                {
                    data: "OwnerFullName",
                    name: "mma.OwnerFullName",
                },
                {
                    data: "PhoneNumber",
                    name: "mma.PhoneNumber",
                },
                {
                    data: "StoreAddress",
                    name: "mma.StoreAddress",
                },
                {
                    data: "Partner",
                    name: "mma.Partner",
                },
                {
                    data: "DistributorName",
                    name: "ms_distributor.DistributorName",
                },
                {
                    data: "Sales",
                    name: "Sales",
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "SummaryCountMerchantDO"
                        );
                    },
                    text: "Export",
                    titleAttr: "Excel",
                    className: "btn-sm",
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [0, 1, 2, 3, 4, 5, 6, 7],
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
