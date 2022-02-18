$(document).ready(function () {
       
    // DataTables
    dataTablesRoles();

    function dataTablesRoles() {

        $('#setting-role .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            "ajax": {
                url: "/setting/role/get"
            },
            columns: [
                {
                    data: 'RoleID',
                    name: 'RoleID'
                },
                {
                    data: 'RoleName',
                    name: 'RoleName'
                },
                {
                    data: 'Action',
                    name: 'Action'
                }
            ],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('Roles');
                },
                text: 'Export',
                titleAttr: 'Excel',
                exportOptions: {
                    modifier: {
                        page: 'all'
                    },
                    columns: [0, 1],
                    orthogonal: 'export'
                },
            }],
            "aoColumnDefs": [
                {
                    "aTargets": [2],
                    "orderable": false
                }
            ],
            "lengthChange": false,
            "responsive": true,
            "autoWidth": false
        });
    }
});