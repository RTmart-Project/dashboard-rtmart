$(document).ready(function () {
    // DataTables
    dataTablesTotalValueDO();

    function dataTablesTotalValueDO() {
        const params = location.search;

        $("#summary-value-do .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            ajax: {
                url: "/summary/reportDetail/totalValueDO" + params,
            },
            columns: [
                {
                    data: "DeliveryOrderID",
                    name: "tmdo.DeliveryOrderID",
                },
                {
                    data: "StatusOrder",
                    name: "ms_status_order.StatusOrder",
                },
                {
                    data: "StockOrderID",
                    name: "tmo.StockOrderID",
                },
                {
                    data: "DatePO",
                    name: "tmo.CreatedDate",
                    type: "date",
                },
                {
                    data: "MerchantExpeditionID",
                    name: "tmed.MerchantExpeditionID",
                },
                {
                    data: "CreatedDate",
                    name: "tmdo.CreatedDate",
                    type: "date",
                },
                {
                    data: "MerchantID",
                    name: "mma.MerchantID",
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
                    data: "ProductID",
                    name: "tmdod.ProductID",
                },
                {
                    data: "ProductName",
                    name: "ms_product.ProductName",
                },
                {
                    data: "Qty",
                    name: "tmdod.Qty",
                },
                {
                    data: "Price",
                    name: "tmdod.Price",
                },
                {
                    data: "PurchasePrice",
                    name: "PurchasePrice",
                    searchable: false,
                },
                {
                    data: "ValueProduct",
                    name: "ValueProduct",
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
                {
                    data: "SubTotal",
                    name: "SubTotal",
                    searchable: false,
                },
                {
                    data: "Discount",
                    name: "tmdo.Discount",
                    searchable: false,
                },
                {
                    data: "ServiceCharge",
                    name: "tmdo.ServiceCharge",
                    searchable: false,
                },
                {
                    data: "DeliveryFee",
                    name: "tmdo.DeliveryFee",
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
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "SummaryTotalValueDO"
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
                            15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27,
                            28,
                        ],
                        orthogonal: "export",
                    },
                },
            ],
            aoColumnDefs: [
                {
                    aTargets: [17, 18, 19, 20, 21, 23, 24, 25, 26, 27],
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
