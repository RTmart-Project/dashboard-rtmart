$(document).ready(function () {
    // DataTables
    dataTablesReadyStock();

    function dataTablesReadyStock() {
        $("#list-stock .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-list-stock'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            ajax: {
                url: "/stock/list/get",
                data: function (d) {
                    d.distributorId = $(
                        "#list-stock .select-filter-custom select"
                    ).val();
                },
            },
            columns: [
                {
                    data: "DistributorName",
                    name: "ms_distributor.DistributorName",
                },
                {
                    data: "ProductID",
                    name: "ms_stock_product.ProductID",
                },
                {
                    data: "ProductImage",
                    name: "ms_product.ProductImage",
                },
                {
                    data: "ProductName",
                    name: "ms_product.ProductName",
                },
                {
                    data: "GoodStock",
                    name: "GoodStock",
                },
                {
                    data: "BadStock",
                    name: "BadStock",
                },
                {
                    data: "Detail",
                    name: "Detail",
                    searchable: false,
                    orderable: false,
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "ReadyStock"
                        );
                    },
                    text: "Export",
                    className: "btn-sm",
                    titleAttr: "Excel",
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [0, 1, 3, 4, 5],
                        orthogonal: "export",
                    },
                },
            ],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }

    // Create element for Filter
    let depo = $('meta[name="depo"]').attr("content");
    if (depo == "ALL") {
        $("div.filter-list-stock").html(`<div class="input-group">
                          <div class="select-filter-custom ml-2">
                              <select>
                                  <option value="">All</option>
                              </select>
                          </div>
                      </div>`);
    }

    // Load Distributor ID and Name for filter
    $.ajax({
        type: "get",
        url: "/distributor/account/get",
        success: function (data) {
            let option;
            const dataDistributor = data.data;
            for (const item of dataDistributor) {
                option += `<option value="${item.DistributorID}">${item.DistributorName}</option>`;
            }
            $("#list-stock .select-filter-custom select").append(option);
            customDropdownFilter.createCustomDropdowns();
        },
    });

    // Event listener saat tombol select option diklik
    $("#list-stock .select-filter-custom select").change(function () {
        $("#list-stock .table-datatables").DataTable().ajax.reload();
    });
});
