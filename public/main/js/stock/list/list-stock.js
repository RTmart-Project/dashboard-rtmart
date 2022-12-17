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
                    d.distributorId = $("#list-stock .select-filter-custom select").val();
                    d.enterDate = $("#list-stock #enter_date").val();
                },
            },
            columns: [
                {
                    data: "DistributorName",
                    name: "ms_distributor.DistributorName",
                },
                {
                    data: "InvestorName",
                    name: "ms_investor.InvestorName",
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
                    data: "ProductLabel",
                    name: "ms_stock_product.ProductLabel",
                },
                {
                    data: "GoodStock",
                    name: "GoodStock",
                },
                {
                    data: "NominalGoodStock",
                    name: "NominalGoodStock",
                },
                {
                    data: "BadStock",
                    name: "BadStock",
                },
                {
                    data: "NominalBadStock",
                    name: "NominalBadStock",
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
                            "ListStock"
                        );
                    },
                    text: "Export",
                    className: "btn-sm",
                    titleAttr: "Excel",
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [0, 1, 2, 4, 5, 6, 7, 8, 9],
                        orthogonal: "export",
                    },
                },
            ],
            aoColumnDefs: [
                {
                    aTargets: [7, 9],
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
            order: [
                [0, "asc"],
                [2, "asc"],
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
                        <input type="text" name="enter_date" id="enter_date" class="ml-2 form-control form-control-sm" readonly>
                        <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                        <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-2">Refresh</button>
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

    // Setting Awal Daterangepicker
    $("#list-stock #enter_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Menyisipkan Placeholder Date
    $("#list-stock #enter_date").val("");
    $("#list-stock #enter_date").attr("placeholder", "Enter Date");

    // Event Listener saat tombol refresh diklik
    $("#list-stock #refresh").click(function () {
        $("#list-stock #enter_date").val("");
        $("#list-stock .table-datatables").DataTable().search("");
        $("#list-stock .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#list-stock #filter").click(function () {
        $("#list-stock .table-datatables").DataTable().ajax.reload();
    });
});
