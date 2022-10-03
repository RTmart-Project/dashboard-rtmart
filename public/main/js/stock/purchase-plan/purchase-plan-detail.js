$(document).ready(function () {
    // DataTables

    dataTablesPurchasePlanDetail();

    function dataTablesPurchasePlanDetail() {
        $("#purchase-plan-detail .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            ajax: {
                url: window.location.pathname,
            },
            columns: [
                {
                    data: "PlanDate",
                    name: "PlanDate",
                },
                {
                    data: "DistributorName",
                    name: "DistributorName",
                },
                {
                    data: "SupplierName",
                    name: "SupplierName",
                },
                {
                    data: "Note",
                    name: "Note",
                },
                {
                    data: "ProductID",
                    name: "ProductID",
                },
                {
                    data: "ProductName",
                    name: "ProductName",
                },
                {
                    data: "ProductLabel",
                    name: "ProductLabel",
                },
                {
                    data: "Qty",
                    name: "Qty",
                },
                {
                    data: "QtyPO",
                    name: "QtyPO",
                },
                {
                    data: "PercentagePO",
                    name: "PercentagePO",
                },
                {
                    data: "PurchasePrice",
                    name: "PurchasePrice",
                },
                {
                    data: "PurchaseValue",
                    name: "PurchaseValue",
                },
                {
                    data: "PercentInterest",
                    name: "PercentInterest",
                },
                {
                    data: "InterestValue",
                    name: "InterestValue",
                },
                {
                    data: "SellingPrice",
                    name: "SellingPrice",
                },
                {
                    data: "SellingValue",
                    name: "SellingValue",
                },
                {
                    data: "PercentVoucher",
                    name: "PercentVoucher",
                },
                {
                    data: "VoucherValue",
                    name: "VoucherValue",
                },
                {
                    data: "GrossMargin",
                    name: "GrossMargin",
                },
                {
                    data: "MarginCtn",
                    name: "MarginCtn",
                },
                {
                    data: "NettMargin",
                    name: "NettMargin",
                },
                {
                    data: "PercentageMargin",
                    name: "PercentageMargin",
                },
                {
                    data: "LastStock",
                    name: "LastStock",
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "PurchasePlan"
                        );
                    },
                    className: "btn-sm",
                    text: "Export",
                    titleAttr: "Excel",
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [
                            0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14,
                            15, 16, 17, 18, 19, 20, 21, 22,
                        ],
                        orthogonal: "export",
                    },
                },
            ],
            aoColumnDefs: [
                {
                    aTargets: [10, 11, 13, 14, 15, 17, 18, 19, 20],
                    mRender: function (data, type, full) {
                        if (type === "export") {
                            return data;
                        } else {
                            if (data == null || data == "") {
                                return data;
                            } else {
                                const currencySeperatorFormat =
                                    thousands_separators(data);
                                return currencySeperatorFormat;
                            }
                        }
                    },
                },
                {
                    aTargets: [0],
                    visible: false,
                },
            ],
            ordering: false,
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }
});
