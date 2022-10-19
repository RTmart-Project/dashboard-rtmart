$(document).ready(function () {
    const csrf = $('meta[name="csrf_token"]').attr("content");

    // Setting Awal Daterangepicker
    $("#banner-slider #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#banner-slider #to_date").daterangepicker({
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

        $("#banner-slider #to_date").daterangepicker({
            minDate: $("#banner-slider #from_date").val(),

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

        $("#banner-slider #from_date").daterangepicker({
            maxDate: $("#banner-slider #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Menyisipkan Placeholder Date
    $("#banner-slider #from_date").val("");
    $("#banner-slider #to_date").val("");
    $("#banner-slider #from_date").attr("placeholder", "From Date");
    $("#banner-slider #to_date").attr("placeholder", "To Date");

    // Disabled input to date ketika from date berubah
    $("#banner-slider").on("change", "#from_date", function () {
        dateStartChange();
    });
    // Disabled input from date ketika to date berubah
    $("#banner-slider").on("change", "#to_date", function () {
        dateEndChange();
    });

    bannerSliderData();

    $("#filter-tanggal-mulai").on("click", function () {
        const startDate = $("#from_date").val();
        const endDate = $("#to_date").val();
        const filterStatus = $("#status").val();
        const filterBy = "tanggal-mulai";
        $("#banner-slider-table .table-datatables").DataTable().destroy();
        bannerSliderData(startDate, endDate, filterStatus, filterBy);
    });

    $("#filter-tanggal-berakhir").on("click", function () {
        const startDate = $("#from_date").val();
        const endDate = $("#to_date").val();
        const filterStatus = $("#status").val();
        const filterBy = "tanggal-berakhir";
        $("#banner-slider-table .table-datatables").DataTable().destroy();
        bannerSliderData(startDate, endDate, filterStatus, filterBy);
    });

    $("#refresh").on("click", function () {
        $("#from_date").val("");
        $("#to_date").val("");
        $("#status").val("");
        $("#status").selectpicker("refresh");
        $("#banner-slider-table .table-datatables").DataTable().destroy();
        bannerSliderData();
    });

    function bannerSliderData(
        startDate = null,
        endDate = null,
        filterStatus = null,
        filterBy = null
    ) {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": csrf,
            },
        });
        $("#banner-slider-table .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-7'<'filter-banner-slider-table'>tl><'col-sm-12 col-md-4'f><'col-12 col-md-1 text-center'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/banner/slider/data",
                type: "POST",
                data: {
                    startDate: startDate,
                    endDate: endDate,
                    filterStatus: filterStatus,
                    filterBy: filterBy,
                },
            },
            columns: [
                {
                    data: "PromoID",
                    name: "PromoID",
                },
                {
                    data: "PromoTitle",
                    name: "PromoTitle",
                },
                {
                    data: "PromoImage",
                    name: "PromoImage",
                },
                {
                    data: "PromoStartDate",
                    name: "PromoStartDate",
                    type: "date",
                },
                {
                    data: "PromoExpiryDate",
                    name: "PromoExpiryDate",
                    type: "date",
                },
                {
                    data: "PromoStatus",
                    name: "PromoStatus",
                },
                {
                    data: "PromoTarget",
                    name: "PromoTarget",
                },
                {
                    data: "TargetID",
                    name: "TargetID",
                },
                {
                    data: "ClassActivityPage",
                    name: "ClassActivityPage",
                },
                {
                    data: "ActivityButtonText",
                    name: "ActivityButtonText",
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
                            "BannerSlider"
                        );
                    },
                    action: exportDatatableHelper.newExportAction,
                    text: "Export",
                    titleAttr: "Excel",
                    className: "btn-sm rounded",
                    exportOptions: {
                        modifier: {
                            page: "all",
                        },
                        columns: [0, 1, 3, 4, 5, 6, 7, 8],
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

    $("#banner-slider-table").on("click", ".delete-promo", function (e) {
        e.preventDefault();
        const id = $(this).data("id");
        $.confirm({
            title: "Delete Data!",
            content: "Are you sure want to <b>delete</b> this data?",
            closeIcon: true,
            buttons: {
                delete: {
                    btnClass: "btn-red",
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        window.location = "/banner/slider/delete/" + id;
                    },
                },
                cancel: function () {},
            },
        });
    });
});
