$(document).ready(function () {
    // DataTables
    dataTablesMerchantFairbanc();

    function dataTablesMerchantFairbanc() {
        $("#merchant-fairbanc .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-7'tl><'col-sm-12 col-md-1'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/setting/module/fairbanc/get",
            },
            columns: [
                {
                    data: "MerchantID",
                    name: "ms_merchant_account.MerchantID",
                },
                {
                    data: "StoreName",
                    name: "ms_merchant_account.StoreName",
                },
                {
                    data: "Partner",
                    name: "ms_merchant_account.Partner",
                },
                {
                    data: "OwnerFullName",
                    name: "ms_merchant_account.OwnerFullName",
                },
                {
                    data: "PhoneNumber",
                    name: "ms_merchant_account.PhoneNumber",
                },
                {
                    data: "Grade",
                    name: "ms_distributor_grade.Grade",
                },
                {
                    data: "StoreAddress",
                    name: "ms_merchant_account.StoreAddress",
                },
                {
                    data: "DistributorName",
                    name: "ms_distributor.DistributorName",
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
                            "MerchantFairbanc"
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
                        columns: [0, 1, 2, 3, 4, 5, 6, 7],
                        orthogonal: "export",
                    },
                },
            ],
            // order: [5, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }

    $("#merchant-fairbanc table").on(
        "click",
        ".delete-merchant-fairbanc",
        function (e) {
            e.preventDefault();
            const merchantId = $(this).data("merchant-id");
            const merchantName = $(this).data("merchant-name");
            $.confirm({
                title: "Hapus Merchant Fairbanc!",
                content: `Yakin ingin mengapus <b>${merchantName}</b> dari daftar Merchant Fairbanc?`,
                closeIcon: true,
                buttons: {
                    Yakin: {
                        btnClass: "btn-red",
                        draggable: true,
                        dragWindowGap: 0,
                        action: function () {
                            window.location =
                                "/setting/module/fairbanc/delete/" + merchantId;
                        },
                    },
                    batal: function () {},
                },
            });
        }
    );
});
