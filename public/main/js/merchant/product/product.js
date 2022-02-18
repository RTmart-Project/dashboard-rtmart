$(document).ready(function () {
    // DataTables
    dataTablesMerchantProductDetails();

    function dataTablesMerchantProductDetails() {
        const urlMerchantProductDetails = window.location.pathname; // return segment1/segment2/segment3/segment4
        const segmentUrl = urlMerchantProductDetails.split("/");
        const merchantId = segmentUrl.pop();

        $("#merchant-product-details .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            ajax: {
                url: "/merchant/account/product/get/" + merchantId,
            },
            columns: [
                {
                    data: "ProductID",
                    name: "ProductID",
                },
                {
                    data: "ProductName",
                    name: "ProductName",
                },
                {
                    data: "ProductImage",
                    name: "ProductImage",
                },
                {
                    data: "Quantity",
                    name: "Quantity",
                },
                {
                    data: "ProductCategoryName",
                    name: "ProductCategoryName",
                },
                {
                    data: "ProductTypeName",
                    name: "ProductTypeName",
                },
                {
                    data: "ProductUOMName",
                    name: "ProductUOMName",
                },
                {
                    data: "ProductUOMDesc",
                    name: "ProductUOMDesc",
                },
                {
                    data: "Price",
                    name: "Price",
                },
                {
                    data: "PurchasePrice",
                    name: "PurchasePrice",
                },
                {
                    data: "Action",
                    name: "Action",
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "MerchantProductDetails"
                        );
                    },
                    text: "Export",
                    titleAttr: "Excel",
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [0, 1, 3, 4, 5, 6, 7, 8],
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
                    orderable: false,
                },
                {
                    aTargets: [7, 8],
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
        });

        // Event listener saat tombol delete diklik
        $("table").on("click", ".btn-delete", function (e) {
            e.preventDefault();
            const productName = $(this).data("product-name");
            const merchantId = $(this).data("merchant-id");
            const productId = $(this).data("product-id");
            $.confirm({
                title: "Hapus Produk!",
                content: `Yakin ingin menghapus produk <b>${productName}</b> ?`,
                closeIcon: true,
                buttons: {
                    hapus: {
                        btnClass: "btn-red",
                        draggable: true,
                        dragWindowGap: 0,
                        action: function () {
                            window.location =
                                "/merchant/account/product/delete/" +
                                merchantId +
                                "/" +
                                productId;
                        },
                    },
                    tidak: function () {},
                },
            });
        });
    }
});
