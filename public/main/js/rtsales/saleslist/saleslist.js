$(document).ready(function () {
    // DataTables
    dataTablesSalesList();

    function dataTablesSalesList() {
        let roleID = $('meta[name="role-id"]').attr("content");

        $("#sales-list .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-sales-list'>tl><'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/rtsales/saleslist/get",
                data: function (d) {
                    d.team = $("#sales-list .filter-team select").val();
                },
            },
            columns: [
                {
                    data: "SalesName",
                    name: "ms_sales.SalesName",
                },
                {
                    data: "SalesCode",
                    name: "ms_sales.SalesCode",
                },
                {
                    data: "SalesLevel",
                    name: "ms_sales.SalesLevel",
                },
                {
                    data: "Team",
                    name: "Team",
                },
                {
                    data: "PhoneNumber",
                    name: "ms_sales.PhoneNumber",
                },
                {
                    data: "SalesWorkStatusName",
                    name: "ms_sales_work_status.SalesWorkStatusName",
                },
                {
                    data: "ProductGroupName",
                    name: "ms_product_group.ProductGroupName",
                    orderable: false,
                },
                {
                    data: "IsActive",
                    name: "ms_sales.IsActive",
                },
                {
                    data: "JoinDate",
                    name: "ms_sales.JoinDate",
                    orderable: true,
                },
                {
                    data: "ResignDate",
                    name: "ms_sales.ResignDate",
                    orderable: true,
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
                            "SalesList"
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
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                        orthogonal: "export",
                    },
                },
            ],
            aoColumnDefs: [
                {
                    aTargets: [10],
                    visible: roleID == "IT" ? true : false
                }
            ],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }

    // Create element for DateRange Filter
    $("div.filter-sales-list").html(`
                    <div class="row">
                        <div class="col-12 col-md-8">
                            <div class="input-group">
                                <div class="filter-team ml-2">
                                    <select class="form-control form-control-sm">
                                    <option selected disabled hidden>Filter Team</option>
                                    <option value="">Semua</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>`);

    // Load Team Code for filter
    $.ajax({
        type: "get",
        url: "/rtsales/team/get",
        success: function (data) {
            let option;
            const salesTeam = data.data;
            for (const item of salesTeam) {
                option += `<option value="${item.TeamCode}">${item.TeamName}</option>`;
            }
            if (salesTeam.length > 1) {
                $("#sales-list .filter-team select").append(option);
            } else {
                $("#sales-list .filter-team select").remove();
            }
        },
    });

    // Event listener saat tombol select option diklik
    $("#sales-list .filter-team select").change(function () {
        $("#sales-list .table-datatables").DataTable().ajax.reload();
    });

    $("#sales-list table").on("click", ".delete-sales", function (e) {
        e.preventDefault();
        const salesName = $(this).data("sales-name");
        const salesCode = $(this).data("sales-code");
        $.confirm({
            title: "Delete Sales Data!",
            content: `Are you sure want to delete <b>${salesCode} - ${salesName}</b> ?`,
            closeIcon: true,
            buttons: {
                delete: {
                    btnClass: "btn-red",
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        window.location =
                            "/rtsales/saleslist/delete/" + salesCode;
                    },
                },
                cancel: function () { },
            },
        });
    });
});
