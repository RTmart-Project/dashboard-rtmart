$(document).ready(function () {
    // DataTables
    dataTablesStoreList();

    function dataTablesStoreList() {
        $("#store-list .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-9'<'filter-store-list'>tl><l><'col-sm-12 col-md-2'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/rtsales/store/get",
                data: function (d) {
                    d.fromDate = $("#store-list #from_date").val();
                    d.toDate = $("#store-list #to_date").val();
                    d.distributorID = $(
                        "#store-list .filter-distributor select"
                    ).val();
                },
            },
            columns: [
                {
                    data: "CreatedDate",
                    name: "ms_store.CreatedDate",
                },
                {
                    data: "StoreID",
                    name: "ms_store.StoreID",
                },
                {
                    data: "StoreName",
                    name: "ms_store.StoreName",
                },
                {
                    data: "OwnerName",
                    name: "ms_store.OwnerName",
                },
                {
                    data: "PhoneNumber",
                    name: "ms_store.PhoneNumber",
                },
                {
                    data: "StoreAddress",
                    name: "ms_store.StoreAddress",
                },
                {
                    data: "Districts",
                    name: "ms_store.Districts",
                },
                {
                    data: "SubDistricts",
                    name: "ms_store.SubDistricts",
                },
                {
                    data: "MerchantID",
                    name: "ms_store.MerchantID",
                },
                {
                    data: "DistributorName",
                    name: "ms_distributor.DistributorName",
                },
                {
                    data: "Grade",
                    name: "ms_store.Grade",
                },
                {
                    data: "StoreType",
                    name: "ms_store.StoreType",
                },
                {
                    data: "SalesCode",
                    name: "ms_store.SalesCode",
                },
                {
                    data: "SalesName",
                    name: "ms_sales.SalesName",
                },
                {
                    data: "Action",
                    name: "Action",
                    searhable: false,
                    orderable: false,
                },
            ],
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "StoreList"
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
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13],
                        orthogonal: "export",
                    },
                },
            ],
            order: [0, "desc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
        });
    }

    // Create element for DateRange Filter
    $("div.filter-store-list").html(`
                    <div class="row">
                        <div class="col-12 col-md-8">
                            <div class="input-group">
                                <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
                                <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm" readonly>
                                <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
                                <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-2">Refresh</button>
                                <div class="filter-distributor ml-2">
                                    <select class="form-control form-control-sm">
                                    <option selected disabled hidden>Filter Distributor</option>
                                    <option value="">Semua</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>`);

    // Setting Awal Daterangepicker
    $("#store-list #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#store-list #to_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    var bCodeChange = false;

    function dateStartChange() {
        if (bCodeChange == true) return;
        else bCodeChange = true;

        $("#store-list #to_date").daterangepicker({
            minDate: $("#store-list #from_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    function dateEndChange() {
        if (bCodeChange == true) return;
        else bCodeChange = true;

        $("#store-list #from_date").daterangepicker({
            maxDate: $("#store-list #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#store-list .filter-store-list").on("change", "#from_date", function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $("#store-list .filter-store-list").on("change", "#to_date", function () {
        dateEndChange();
    });

    // Menyisipkan Placeholder Date
    $("#store-list #from_date").val("");
    $("#store-list #to_date").val("");
    $("#store-list #from_date").attr("placeholder", "From Date");
    $("#store-list #to_date").attr("placeholder", "To Date");

    // Event Listener saat tombol refresh diklik
    $("#store-list #refresh").click(function () {
        $("#store-list #from_date").val("");
        $("#store-list #to_date").val("");
        $("#store-list .table-datatables").DataTable().search("");
        // $('#store-list .select-filter-custom select').val('').change();
        // $('#store-list .select-filter-custom select option[value=]').attr('selected', 'selected');
        $("#store-list .table-datatables").DataTable().ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#store-list #filter").click(function () {
        $("#store-list .table-datatables").DataTable().ajax.reload();
    });

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
            $("#store-list .filter-distributor select").append(option);
        },
    });

    // Event listener saat tombol select option diklik
    $("#store-list .filter-distributor select").change(function () {
        $("#store-list .table-datatables").DataTable().ajax.reload();
    });

    // Event listener saat tombol delete diklik
    $("table").on("click", ".btn-delete", function (e) {
        e.preventDefault();
        const storeID = $(this).data("store-id");
        const storeName = $(this).data("store-name");
        $.confirm({
            title: "Hapus Store!",
            content: `Yakin ingin menghapus <b>${storeID} - ${storeName}</b> ?`,
            closeIcon: true,
            buttons: {
                hapus: {
                    btnClass: "btn-red",
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        window.location = "/rtsales/store/delete/" + storeID;
                    },
                },
                tidak: function () {},
            },
        });
    });
});
