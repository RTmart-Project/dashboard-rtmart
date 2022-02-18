$(document).ready(function () {
    // DataTables
    dataTablesDistributorHaistar();

    function dataTablesDistributorHaistar() {
        $("#distributor-haistar .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-distributor-haistar'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            ajax: {
                url: "/setting/module/haistar/get",
            },
            columns: [
                {
                    data: "DistributorID",
                    name: "DistributorID",
                },
                {
                    data: "DistributorName",
                    name: "DistributorName",
                },
                {
                    data: "IsHaistar",
                    name: "IsHaistar",
                },
                {
                    data: "Email",
                    name: "Email",
                },
                {
                    data: "Address",
                    name: "Address",
                },
                {
                    data: "CreatedDate",
                    name: "CreatedDate",
                    type: "date",
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
                            "DistributorHaistar"
                        );
                    },
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
            // order: [4, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
            columnDefs: [
                {
                    targets: [5, 6],
                    orderable: false,
                },
            ],
        });
    }

    $("#distributor-haistar table").on(
        "click",
        ".delete-distributor-haistar",
        function (e) {
            e.preventDefault();
            const distributorId = $(this).data("distributor-id");
            const distributorName = $(this).data("distributor-name");
            $.confirm({
                title: "Hapus Distributor Haistar!",
                content: `Yakin ingin mengapus <b>${distributorName}</b> dari daftar Distributor Haistar?`,
                closeIcon: true,
                buttons: {
                    Yakin: {
                        btnClass: "btn-red",
                        draggable: true,
                        dragWindowGap: 0,
                        action: function () {
                            window.location =
                                "/setting/module/haistar/delete/" +
                                distributorId;
                        },
                    },
                    batal: function () {},
                },
            });
        }
    );
});
