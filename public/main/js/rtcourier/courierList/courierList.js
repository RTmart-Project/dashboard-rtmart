$(document).ready(function () {
    // DataTables
    dataTablesCourierList();

    function dataTablesCourierList() {
        $("#courier-list .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'tl><'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/rtcourier/courier/get",
            },
            columns: [
                {
                    data: "CourierName",
                    name: "ms_courier.CourierName",
                },
                {
                    data: "CourierCode",
                    name: "ms_courier.CourierCode",
                },
                {
                    data: "PhoneNumber",
                    name: "ms_courier.PhoneNumber",
                },
                {
                    data: "Email",
                    name: "ms_courier.Email",
                },
                {
                    data: "CreatedDate",
                    name: "ms_courier.CreatedDate",
                    type: "date",
                },
                {
                    data: "IsActive",
                    name: "ms_courier.IsActive",
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
                            "CourierList"
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
                        columns: [0, 1, 2, 3, 4, 5],
                        orthogonal: "export",
                    },
                },
            ],
            order: [4, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }

    $("#courier-list table").on("click", ".nonactive-courier", function (e) {
        e.preventDefault();
        const courierName = $(this).data("courier-name");
        const courierCode = $(this).data("courier-code");
        $.confirm({
            title: "Nonaktifkan Kurir!",
            content: `Apakah yakin ingin menonaktifkan <b>${courierCode} - ${courierName}</b> ?`,
            closeIcon: true,
            buttons: {
                ya: {
                    btnClass: "btn-red",
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        window.location =
                            "/rtcourier/courier/nonactive/" + courierCode;
                    },
                },
                batal: function () {},
            },
        });
    });
});
