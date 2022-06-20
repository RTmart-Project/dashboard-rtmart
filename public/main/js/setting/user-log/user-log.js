$(document).ready(function () {
    // DataTables
    dataTablesUserLog();

    function dataTablesUserLog() {
        $("#user-log .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-user-log'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/setting/user-log/get",
            },
            columns: [
                {
                    data: "UserID",
                    name: "ms_user_activity_log.UserID",
                },
                {
                    data: "Email",
                    name: "ms_user.Email",
                },
                {
                    data: "Name",
                    name: "ms_user.Name",
                },
                {
                    data: "PhoneNumber",
                    name: "ms_user.PhoneNumber",
                },
                {
                    data: "RoleName",
                    name: "ms_role.RoleName",
                },
                {
                    data: "Depo",
                    name: "ms_user.Depo",
                },
                {
                    data: "Detail",
                    name: "Detail",
                    orderable: false,
                    searchable: false,
                },
            ],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }
});
