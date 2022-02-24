$(document).ready(function () {
    // DataTables
    dataTablesSpecialPrice();

    function dataTablesSpecialPrice() {
        const urlDistributorProductDetails = window.location.pathname;
        const segmentUrl = urlDistributorProductDetails.split("/");
        const merchantID = segmentUrl.pop();

        $("#special-price .table-datatables").DataTable({
            processing: true,
            serverSide: true,
            stateServe: true,
            dom:
                "<'row'<'col-sm-12 col-md-7'<'reset-special-price'>tl><'col-sm-12 col-md-1'l><'col-sm-12 col-md-4'f><'col-sm-12'>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            ajax: {
                url:
                    "/distribution/merchant/specialprice/" +
                    merchantID +
                    "/get",
            },
            columns: [
                {
                    data: "Grade",
                    name: "ms_distributor_grade.Grade",
                    orderable: false,
                },
                {
                    data: "ProductName",
                    name: "ms_product.ProductName",
                },
                {
                    data: "Price",
                    name: "ms_distributor_product_price.Price",
                },
                {
                    data: "SpecialPrice",
                    name: "ms_product_special_price.SpecialPrice",
                },
                {
                    data: "Action",
                    name: "Action",
                    orderable: false,
                    searchable: false,
                },
            ],
            order: [1, "asc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
            drawCallback: function (settings, json) {
                const currencyJumlahTopup = new AutoNumeric.multiple(
                    ".special-price",
                    {
                        allowDecimalPadding: false,
                        decimalCharacter: ",",
                        digitGroupSeparator: ".",
                        unformatOnSubmit: true,
                    }
                );
            },
            aoColumnDefs: [
                {
                    aTargets: [2],
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
        });
    }

    $("div.reset-special-price").html(
        `<button class="btn btn-sm btn-danger btn-reset">Reset Special Price</button>`
    );

    let csrf = $('meta[name="csrf_token"]').attr("content");

    // Event listener saat button simpan di klik
    $("table").on("click", ".btn-simpan", function (e) {
        e.preventDefault();
        let specialPrice = $(this)
            .closest("tr")
            .find(".special-price")
            .val()
            .replaceAll(".", "");

        let merchantID = $(this).data("merchant-id");
        let productID = $(this).data("product-id");
        let gradeID = $(this).data("grade-id");

        $.ajax({
            url: `/distribution/merchant/specialprice/insertOrUpdate`,
            headers: {
                "X-CSRF-TOKEN": csrf,
            },
            data: {
                specialPrice: specialPrice,
                merchantID: merchantID,
                productID: productID,
                gradeID: gradeID,
            },
            type: "post",
            success: function (result) {
                if (result.status == "success") {
                    iziToast.success({
                        title: "Berhasil",
                        message: result.message,
                        position: "topRight",
                    });
                }

                if (result.status == "failed") {
                    iziToast.error({
                        title: "Gagal",
                        message: result.message,
                        position: "topRight",
                    });
                }
                $("#special-price .table-datatables").DataTable().ajax.reload();
            },
        });
    });

    // Event listener saat button hapus di klik
    $("table").on("click", ".btn-hapus", function (e) {
        e.preventDefault();

        let merchantID = $(this).data("merchant-id");
        let productID = $(this).data("product-id");
        let gradeID = $(this).data("grade-id");

        $.ajax({
            url: `/distribution/merchant/specialprice/delete`,
            headers: {
                "X-CSRF-TOKEN": csrf,
            },
            data: {
                merchantID: merchantID,
                productID: productID,
                gradeID: gradeID,
            },
            type: "post",
            success: function (result) {
                if (result.status == "success") {
                    iziToast.success({
                        title: "Berhasil",
                        message: result.message,
                        position: "topRight",
                    });
                }

                if (result.status == "failed") {
                    iziToast.error({
                        title: "Gagal",
                        message: result.message,
                        position: "topRight",
                    });
                }
                $("#special-price .table-datatables").DataTable().ajax.reload();
            },
        });
    });

    // Event listener saat button reset di klik
    $(".reset-special-price").on("click", ".btn-reset", function (e) {
        e.preventDefault();

        $.confirm({
            title: "Reset Special Price",
            content: `Apakah yakin ingin reset harga spesial dari <b>${storeName}</b>?`,
            closeIcon: true,
            type: "red",
            typeAnimated: true,
            buttons: {
                ya: {
                    btnClass: "btn-danger",
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        $.ajax({
                            url: `/distribution/merchant/specialprice/reset`,
                            headers: {
                                "X-CSRF-TOKEN": csrf,
                            },
                            data: {
                                merchantID: merchantID,
                                gradeID: gradeID,
                            },
                            type: "post",
                            success: function (result) {
                                if (result.status == "success") {
                                    iziToast.success({
                                        title: "Berhasil",
                                        message: result.message,
                                        position: "topRight",
                                    });
                                }

                                if (result.status == "failed") {
                                    iziToast.error({
                                        title: "Gagal",
                                        message: result.message,
                                        position: "topRight",
                                    });
                                }
                                $("#special-price .table-datatables")
                                    .DataTable()
                                    .ajax.reload();
                            },
                        });
                    },
                },
                tidak: function () {},
            },
        });
    });
});
