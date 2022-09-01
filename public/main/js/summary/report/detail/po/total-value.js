$(document).ready(function () {
    // DataTables
    dataTablesTotalValuePO();

    function dataTablesTotalValuePO() {
        const params = location.search;

        $("#summary-value-po .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            ajax: {
                url: "/summary/reportDetail/totalValuePO" + params,
            },
            columns: [
                {
                    data: "StockOrderID",
                    name: "tmo.StockOrderID",
                },
                {
                    data: "CreatedDate",
                    name: "tmo.CreatedDate",
                    type: "date",
                },
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
                    data: "PaymentMethodName",
                    name: "ms_payment_method.PaymentMethodName",
                },
                {
                    data: "StatusOrder",
                    name: "ms_status_order.StatusOrder",
                },
                {
                    data: "TotalPrice",
                    name: "tmo.TotalPrice",
                    searchable: false,
                },
                {
                    data: "DiscountPrice",
                    name: "tmo.DiscountPrice",
                    searchable: false,
                },
                {
                    data: "DiscountVoucher",
                    name: "tmo.DiscountVoucher",
                    searchable: false,
                },
                {
                    data: "ServiceChargeNett",
                    name: "tmo.ServiceChargeNett",
                    searchable: false,
                },
                {
                    data: "DeliveryFee",
                    name: "tmo.DeliveryFee",
                    searchable: false,
                },
                {
                    data: "GrandTotal",
                    name: "GrandTotal",
                    searchable: false,
                },
                {
                    data: "Sales",
                    name: "Sales",
                },
                {
                    data: "ProductID",
                    name: "tmod.ProductID",
                },
                {
                    data: "ProductName",
                    name: "ms_product.ProductName",
                },
                {
                    data: "PromisedQuantity",
                    name: "tmod.PromisedQuantity",
                },
                {
                    data: "Nett",
                    name: "tmod.Nett",
                },
                {
                    data: "PurchasePrice",
                    name: "PurchasePrice",
                    searchable: false,
                },
                {
                    data: "SubTotalProduct",
                    name: "SubTotalProduct",
                    searchable: false,
                },
                {
                    data: "ValuePurchase",
                    name: "ValuePurchase",
                    searchable: false,
                },
                {
                    data: "ValueMargin",
                    name: "ValueMargin",
                    searchable: false,
                },
                {
                    data: "Margin",
                    name: "Margin",
                    searchable: false,
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "SummaryTotalValuePO"
                        );
                    },
                    text: "Export",
                    titleAttr: "Excel",
                    className: "btn-sm",
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [
                            0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14,
                            15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26,
                        ],
                        orthogonal: "export",
                    },
                },
            ],
            aoColumnDefs: [
                {
                    aTargets: [11, 12, 13, 14, 15, 16, 21, 22, 23, 24, 25],
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
            ],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }
});
