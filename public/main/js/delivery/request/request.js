$(document).ready(function () {
    // let checkboxFilter = [];
    let csrf = $('meta[name="csrf_token"]').attr("content");
    let roleID = $('meta[name="role-id"]').attr("content");

    // DataTables
    dataTablesDeliveryRequest();

    let selected = [];

    function dataTablesDeliveryRequest() {
        $("#delivery-request .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-6'<'filter-delivery-request'>tl><'col-sm-12 col-md-1'l><'col-sm-12 col-md-4'f><'col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                headers: {
                    "X-CSRF-TOKEN": csrf,
                },
                url: "/delivery/request/get",
                type: "post",
                data: function (d) {
                    d.fromDate = $("#delivery-request #from_date").val();
                    d.toDate = $("#delivery-request #to_date").val();
                    // d.checkFilter = checkboxFilter;
                    d.urutanDO = $(
                        "#delivery-request .select-filter-custom select"
                    ).val();
                },
            },
            columns: [
                {
                    data: "Empty",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "Checkbox",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "DeliveryOrderID",
                    name: "tmdo.DeliveryOrderID",
                },
                {
                    data: "CreatedDate",
                    name: "tmdo.CreatedDate",
                    type: "date",
                },
                {
                    data: "UrutanDO",
                    name: "UrutanDO",
                    searchable: false,
                },
                {
                    data: "StockOrderID",
                    name: "tmdo.StockOrderID",
                },
                {
                    data: "StoreName",
                    name: "StoreName",
                },
                {
                    data: "PhoneNumber",
                    name: "PhoneNumber",
                },
                // {
                //     data: "Area",
                //     name: "Area"
                // },
                {
                    data: "DistributorName",
                    name: "DistributorName",
                },
                {
                    data: "Sales",
                    name: "Sales",
                },
                {
                    data: "Products",
                    name: "Products",
                    searchable: false,
                },
            ],
            order: [3, "asc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
            buttons: [
                {
                    extend: "excelHtml5",
                    filename: function () {
                        return exportDatatableHelper.generateFilename(
                            "DeliveryPlan"
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
                        columns: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                        orthogonal: "export",
                    },
                },
            ],
            aoColumnDefs: [
                {
                    aTargets: [0, 1],
                    visible: roleID != "HL" ? true : false,
                },
            ],
            rowCallback: function (row, data) {
                if ($.inArray(data.DeliveryOrderID, selected) !== -1) {
                    $(row.childNodes[1].childNodes[0]).prop("checked", true);
                }
            },
        });
    }

    $("#delivery-request tbody").on("change", ".check-do-id", function () {
        let that = this.parentElement.parentElement;
        let id = this.parentElement.nextSibling.textContent;
        let index = $.inArray(id, selected);

        if (index === -1) {
            selected.push(id);
        } else {
            selected.splice(index, 1);
        }

        if ($(this).is(":checked")) {
            this.checked = true;
        } else {
            this.checked = false;
        }
    });

    // Create element for DateRange Filter
    $("div.filter-delivery-request").html(`<div class="input-group">
        <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" readonly>
        <input type="text" name="to_date" id="to_date" class="ml-2 form-control form-control-sm" readonly>
        <button type="submit" id="filter" class="ml-2 btn btn-sm btn-primary">Filter</button>
        <button type="button" name="refresh" id="refresh" class="btn btn-sm btn-warning ml-2">Refresh</button>
        <div class="select-filter-custom ml-2">
            <select class="form-control form-control-sm">
                <option value="">All</option>
                <option value="DO ke-1">DO ke-1</option>
                <option value="DO ke-2">DO ke-2</option>
                <option value="DO ke-3">DO ke-3</option>
            </select>
        </div>
    </div>`);

    // Setting Awal Daterangepicker
    $("#delivery-request #from_date").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "YYYY-MM-DD",
        },
    });

    // Setting Awal Daterangepicker
    $("#delivery-request #to_date").daterangepicker({
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

        $("#delivery-request #to_date").daterangepicker({
            minDate: $("#delivery-request #from_date").val(),
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

        $("#delivery-request #from_date").daterangepicker({
            maxDate: $("#delivery-request #to_date").val(),
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "YYYY-MM-DD",
            },
        });
        bCodeChange = false;
    }

    // Disabled input to date ketika from date berubah
    $("#delivery-request .filter-delivery-request").on(
        "change",
        "#from_date",
        function () {
            dateStartChange();
        }
    );

    // Disabled input from date ketika to date berubah
    $("#delivery-request .filter-delivery-request").on(
        "change",
        "#to_date",
        function () {
            dateEndChange();
        }
    );

    // Menyisipkan Placeholder Date
    $("#delivery-request #from_date").val("");
    $("#delivery-request #to_date").val("");
    $("#delivery-request #from_date").attr("placeholder", "From Date Plan");
    $("#delivery-request #to_date").attr("placeholder", "To Date Plan");

    // Event Listener saat tombol refresh diklik
    $("#delivery-request #refresh").click(function () {
        $("#delivery-request #from_date").val("");
        $("#delivery-request #to_date").val("");
        $("#delivery-request .table-datatables").DataTable().search("");
        $("#delivery-request .table-datatables")
            .DataTable()
            .ajax.reload(null, false);
    });

    // Event listener saat tombol filter diklik
    $("#delivery-request #filter").click(function () {
        $("#delivery-request .table-datatables").DataTable().ajax.reload();
    });

    // Event listener saat tombol select option diklik
    $("#delivery-request .select-filter-custom select").change(function () {
        $("#delivery-request .table-datatables").DataTable().ajax.reload();
    });

    let deliveryOrderID = [];

    $("#delivery-request table").on("change", ".check-do-id", function () {
        let checkedDO = $(this).val();

        if ($(this).is(":checked")) {
            deliveryOrderID.push(checkedDO);
        } else {
            deliveryOrderID.splice($.inArray(checkedDO, deliveryOrderID), 1);
        }

        $("#do-selected").html(deliveryOrderID.join(", "));
    });

    // First Next Step
    $("#first-next-step").click(function () {
        let overlay = `
            <div class="overlay mt-4">
                <i class="fas fa-3x fa-spinner fa-spin"></i>
            </div>
            <p class="text-center mt-3">Loading ...</p>
        `;
        $("#delivery-order-result").html(overlay);
        $.ajax({
            url: `/delivery/request/getDeliveryOrderByID`,
            headers: {
                "X-CSRF-TOKEN": csrf,
            },
            data: {
                arrayDeliveryOrderID: deliveryOrderID,
            },
            type: "post",
            success: function (result) {
                let div = "";
                if (result == "400") {
                    div += `
                        <div class="callout callout-danger p-2 my-2">
                            <h5 class="text-center">Anda belum memilih Delivery Order ID. Harap pilih terlebih dahulu</h5>
                        </div>
                    `;
                } else {
                    div += result;
                }
                $("#delivery-order-result").html(div);
            },
        });
        stepper.next();
    });

    $("#delivery-order-result").on("change", ".send-by", function () {
        let existStock = $(this).parent().parent().find("#exist-stock");
        let source = $(this).parent().parent().find(".select-source");
        existStock.removeClass("d-none");
        existStock.addClass("d-block");
        source.removeClass("d-none");
    });

    $("#delivery-order-result").on("change", ".source-product", function () {
        const sourceProduct = $(this).val();

        if (sourceProduct == "PKP") {
            $(this).find('option[value="PKP"]').attr("selected", "selected");
            $(this).find('option[value="NON-PKP"]').removeAttr("selected");
        } else {
            $(this)
                .find('option[value="NON-PKP"]')
                .attr("selected", "selected");
            $(this).find('option[value="PKP"]').removeAttr("selected");
        }
    });

    $("#delivery-order-result").on("change", ".source-investor", function () {
        const sourceProductInvestor = $(this).val();
        $(this).find("option").removeAttr("selected");
        $(this)
            .find(`option[value="${sourceProductInvestor}"]`)
            .attr("selected", "selected");
    });

    $("#delivery-order-result").on("change", ".source-product, .source-investor", function () {
        const requestDO = $(this).closest(".request-do");
        const productID = requestDO.find("#product-id").val();
        const distributorID = requestDO.find("#distributor-id").val();
        const productInvestor = requestDO.find(".source-investor").val();
        const productLabel = requestDO.find(".source-product").val();

        $.ajax({
            type: "get",
            url: `/delivery/request/sumStockProduct/${productID}/${distributorID}/${productInvestor}/${productLabel}`,
            success: function (result) {
                requestDO.find("#exist-qty-perinvestor").html(result);
            },
        });
    }
    );

    let Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 4000,
    });

    // Second Next Step
    $("#second-next-step").click(function () {
        let next = true;

        if ($("#delivery-order-result :checkbox:checked").length < 1) {
            Toast.fire({
                icon: "error",
                title: "Pilih produk terlebih dahulu!",
            });

            return (next = false);
        }

        let cloneProduct = $("#delivery-order-result").clone();

        $("#preview-product").html(cloneProduct);
        $("#preview-product .warning-choose-product").addClass("d-none");

        let dataProduct = [];

        $("#preview-product input[type=checkbox]").each(function () {
            if (!$(this).is(":checked")) {
                $(this).parent().parent().addClass("d-none");
            } else {
                let productName = $(this)
                    .parent()
                    .siblings()
                    .find("#product-name")
                    .text();
                let productID = $(this)
                    .parent()
                    .siblings()
                    .find("#product-id")
                    .val();

                let qty = $(this)
                    .parent()
                    .siblings()
                    .siblings()
                    .find("#qty-request-do");
                let qtyVal = Number(qty.val());
                let maxQty = Number(qty.next().next().next().children().text());
                let existQty = Number(
                    qty.next().next().next().next().children().text()
                );

                dataProduct.push({
                    productName: productName,
                    productID: productID,
                    qty: qtyVal,
                    existQty: existQty,
                });

                if (Number(qtyVal) > Number(existQty)) {
                    Toast.fire({
                        icon: "error",
                        title: productName + " melebihi qty stok tersedia!",
                    });

                    return (next = false);
                }

                if (Number(qtyVal) > Number(maxQty)) {
                    Toast.fire({
                        icon: "error",
                        title: productName + " melebihi maksimum quantity!",
                    });

                    return (next = false);
                }

                if (qtyVal < 1) {
                    Toast.fire({
                        icon: "error",
                        title:
                            "Quantity " + productName + " harus lebih dari 0!",
                    });

                    return (next = false);
                }

                let newQtyElement = `<span id='qty-expedition'>${qty.val()}</span>`;
                qty.replaceWith(newQtyElement);

                const productLabel = $(this)
                    .closest(".request-do")
                    .find(".source-product");
                const productLabelValue = productLabel.val();
                const newProductLabelElemenet = `<span id='source-product' class='d-block'>${productLabelValue}</span>`;
                productLabel.replaceWith(newProductLabelElemenet);

                const productInvestor = $(this)
                    .closest(".request-do")
                    .find(".source-investor");
                const productInvestorValue = productInvestor.val();
                const productInvestorText = $(this)
                    .closest(".request-do")
                    .find(`.source-investor option[value="${productInvestorValue}"]`)
                    .text();
                const newProductInvestorElemenet = `
                    <span id='source-investor' class='d-none'>${productInvestorValue}</span>
                    <span class="d-block">${productInvestorText}</span>
                `;

                productInvestor.replaceWith(newProductInvestorElemenet);
            }
        });

        let resultDataProduct = Object.values(
            dataProduct.reduce(
                (c, { productID, qty, existQty, productName }) => {
                    c[productID] = c[productID] || { productID, qty: 0 };
                    c[productID].qty += qty;
                    c[productID].existQty = existQty;
                    c[productID].productName = productName;
                    return c;
                },
                {}
            )
        );

        $.each(resultDataProduct, function (key, value) {
            if (value.qty > value.existQty) {
                console.log(`qty: ${value.existQty}`);
                Toast.fire({
                    icon: "error",
                    title: value.productName + " melebihi qty stok tersedia",
                });

                return (next = false);
            }
        });

        $("#preview-product .card").each(function () {
            let checked = $(this)
                .find("input[type=checkbox]")
                .filter(":checked").length;

            if (checked == 0) {
                $(this).addClass("d-none");
            }
            let deliveryOrderID = $(this).find(".do-id").text();
            let maxNominal = $(this)
                .find("#max-nominal")
                .text()
                .replaceAll("Rp ", "")
                .replaceAll(".", "");
            let subtotalNominal = $(this)
                .find("#price-subtotal")
                .text()
                .replaceAll("Rp ", "")
                .replaceAll(".", "");
            // if (Number(subtotalNominal) > Number(maxNominal)) {
            //     Toast.fire({
            //         icon: "error",
            //         title:
            //             deliveryOrderID + " melebihi maksimum nominal kirim!",
            //     });
            //     return (next = false);
            // }
        });

        $("#preview-product input[type=checkbox]").parent().addClass("d-none");
        $("#preview-product .label-product").removeClass("d-flex");
        $("#preview-product .label-product").addClass("d-none");

        if (next == true) {
            stepper.next();
        }
    });

    let dataExpedition;
    let dataDeliveryOrderDetail;

    $("#kirim-barang").click(function (e) {
        e.preventDefault();
        let createdDate = $(this)
            .parent()
            .parent()
            .find("#created_date_do")
            .val();
        let vehicle = $(this).parent().parent().find("#vehicle").val();
        let driver = $(this).parent().parent().find("#driver").val();
        let helper = $(this).parent().parent().find("#helper").val();
        let licensePlate = $(this)
            .parent()
            .parent()
            .find("#license_plate")
            .val();

        if (createdDate == "") {
            Toast.fire({
                icon: "error",
                title: "Harap isi waktu pengiriman!",
            });
        } else if (vehicle == "") {
            Toast.fire({
                icon: "error",
                title: "Harap isi jenis kendaraan!",
            });
        } else if (driver == "") {
            Toast.fire({
                icon: "error",
                title: "Harap isi driver!",
            });
        } else if (helper == "") {
            Toast.fire({
                icon: "error",
                title: "Harap isi helper!",
            });
        } else if (licensePlate == "") {
            Toast.fire({
                icon: "error",
                title: "Harap isi Plat Nomor Kendaraan!",
            });
        } else {
            $("#modalValidasi").modal("show");
        }

        // Data per DO
        dataDeliveryOrderID = [];
        $("#preview-product .card-do:not(.d-none)").each(function () {
            let doID = $(this).children().children().find(".do-id").text();
            dataDeliveryOrderID.push({
                deliveryOrderID: doID,
            });
        });

        // Expedition Data
        dataDeliveryOrderDetail = [];
        $("#preview-product .request-do:not(.d-none)").each(function () {
            let deliveryOrderDetailID = $(this)
                .find("input[type=checkbox]")
                .val();
            let deliveryOrderID = $(this).find("#delivery-order-id").val();
            let distributor = $(this).find("#distributor").val();
            let distributorID = $(this).find("#distributor-id").val();
            let productID = $(this).find("#product-id").val();
            let qtyExpedition = $(this).find("#qty-expedition").text();
            let sourceProduct = $(this).find("#source-product").text();
            let sourceProductInvestor = $(this).find("#source-investor").text();

            dataDeliveryOrderDetail.push({
                deliveryOrderID: deliveryOrderID,
                deliveryOrderDetailID: deliveryOrderDetailID,
                distributor: distributor,
                distributorID: distributorID,
                productID: productID,
                qtyExpedition: qtyExpedition,
                sourceProduct: sourceProduct,
                sourceProductInvestor: sourceProductInvestor,
            });
        });

        // DO Detail ID not checked
        dataDeliveryOrderDetailNotChecked = [];
        $("#preview-product .request-do.d-none").each(function () {
            let deliveryOrderDetailID = $(this)
                .find("input[type=checkbox]")
                .val();

            dataDeliveryOrderDetailNotChecked.push({
                deliveryOrderDetailIDNotChecked: deliveryOrderDetailID,
            });
        });

        dataExpedition = JSON.stringify({
            createdDate: createdDate,
            vehicleID: vehicle,
            driverID: driver,
            helperID: helper,
            licensePlate: licensePlate,
            dataDetail: dataDeliveryOrderDetail,
            dataDeliveryOrderID: dataDeliveryOrderID,
            dataDeliveryOrderDetailNotChecked: dataDeliveryOrderDetailNotChecked,
        });
    });

    $("#btn-validasi").click(function (e) {
        const checkPhoneNumber = $("#phone_number_check").prop("checked");
        const checkAddress = $("#address_check").prop("checked");

        if (checkPhoneNumber == true && checkAddress == true) {
            $("#modalValidasi").modal("hide");
            $("#modalKirimBarang").modal("show");
        } else {
            Toast.fire({
                icon: "error",
                title: "Harap checklist data validasi!",
            });
        }
    });

    $("#create-expedition-btn").click(function (e) {
        $("body").append(`
            <div class="card m-0" style="z-index:99999;">
                <div class="overlay position-fixed flex-column">
                    <i class="fas fa-4x fa-spinner fa-spin"></i>
                    <h4 class="mt-4">Please do not refresh the page</h4>
                </div>
            </div>
        `);
        $.ajax({
            url: `/delivery/request/createExpedition`,
            headers: {
                "X-CSRF-TOKEN": csrf,
            },
            data: {
                dataExpedition: dataExpedition,
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
                setTimeout(function () {
                    location.reload(true);
                }, 2500);
            },
        });
    });
});
