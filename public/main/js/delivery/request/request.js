$(document).ready(function () {
    let checkboxFilter = [];
    let csrf = $('meta[name="csrf_token"]').attr("content");

    $(".check-subdistrict").change(function () {
        let checked = $(this).val();
        if ($(this).is(":checked")) {
            checkboxFilter.push(checked);
        } else {
            checkboxFilter.splice($.inArray(checked, checkboxFilter), 1);
        }
        $("#delivery-request .table-datatables").DataTable().ajax.reload();
    });
    // DataTables
    dataTablesDeliveryRequest();

    let selected = [];

    function dataTablesDeliveryRequest() {
        $("#delivery-request .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-delivery-request'>tl><'col-sm-12 col-md-1'l><'col-sm-12 col-md-4'f><''>>" +
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
                    d.checkFilter = checkboxFilter;
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
                    searchable: false,
                },
                {
                    data: "DeliveryOrderID",
                    name: "tx_merchant_delivery_order.DeliveryOrderID",
                },
                {
                    data: "StockOrderID",
                    name: "tx_merchant_delivery_order.StockOrderID",
                },
                {
                    data: "Area",
                    name: "Area",
                },
                {
                    data: "CreatedDate",
                    name: "tx_merchant_delivery_order.CreatedDate",
                    type: "date",
                },
                {
                    data: "DueDate",
                    name: "DueDate",
                    searchable: false,
                },
                {
                    data: "StoreName",
                    name: "StoreName",
                },
                {
                    data: "Products",
                    name: "Products",
                    searchable: false,
                },
                {
                    data: "DistributorName",
                    name: "DistributorName",
                },
                {
                    data: "Sales",
                    name: "Sales",
                },
                {
                    data: "PhoneNumber",
                    name: "PhoneNumber",
                },
            ],
            order: [5, "asc"],
            lengthChange: false,
            responsive: true,
            autoWidth: false,
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
    $("#delivery-request #from_date").attr("placeholder", "From Date");
    $("#delivery-request #to_date").attr("placeholder", "To Date");

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
        let overlay = `<div class="overlay mt-4">
                    <i class="fas fa-3x fa-spinner fa-spin"></i>
                  </div>
                  <p class="text-center mt-3">Loading ...</p>`;
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
                    div += `<div class="callout callout-danger p-2 my-2">
                      <h5 class="text-center">Anda belum memilih Delivery Order ID. Harap pilih terlebih dahulu</h5>
                    </div>`;
                } else {
                    div += result;
                }
                $("#delivery-order-result").html(div);
            },
        });
        stepper.next();
    });

    let Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 4000,
    });

    // Second Next Step
    $("#second-next-step").click(function () {
        let cloneProduct = $("#delivery-order-result").clone();

        $("#preview-product").html(cloneProduct);
        $("#preview-product .warning-choose-product").addClass("d-none");
        $("#preview-product input[type=checkbox]").each(function () {
            if (!$(this).is(":checked")) {
                $(this).parent().parent().addClass("d-none");
            } else {
                let qty = $(this)
                    .parent()
                    .siblings()
                    .siblings()
                    .find("#qty-request-do");
                let qtyVal = Number(qty.val());
                let maxQty = Number(qty.next().next().next().children().text());
                if (qtyVal > maxQty) {
                    Toast.fire({
                        icon: "error",
                        title: "Terdapat quantity yang melebihi maksimum!",
                    });
                } else {
                    stepper.next();
                }
                let newQtyElement = `<span id='qty-expedition'>${qty.val()}</span>`;
                qty.replaceWith(newQtyElement);
            }
        });
        $("#preview-product .card").each(function () {
            let checked = $(this)
                .find("input[type=checkbox]")
                .filter(":checked").length;

            if (checked == 0) {
                $(this).addClass("d-none");
            }
        });
        $("#preview-product input[type=checkbox]").parent().addClass("d-none");
        $("#preview-product .label-product").removeClass("d-flex");
        $("#preview-product .label-product").addClass("d-none");
    });

    let dataExpedition;
    let dataDeliveryOrderDetail = [];

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
        let distributor = $(this).parent().parent().find("#distributor").val();
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
            $("#modalKirimBarang").modal("show");
        }

        // Expedition Data
        dataDeliveryOrderDetail = [];
        $("#preview-product .request-do:not(.d-none)").each(function () {
            let deliveryOrderDetailID = $(this)
                .find("input[type=checkbox]")
                .val();
            let qtyExpedition = $(this).find("#qty-expedition").text();

            dataDeliveryOrderDetail.push({
                deliveryOrderDetailID: deliveryOrderDetailID,
                qtyExpedition: qtyExpedition,
            });
        });

        dataExpedition = JSON.stringify({
            createdDate: createdDate,
            vehicleID: vehicle,
            driverID: driver,
            helperID: helper,
            licensePlate: licensePlate,
            distributor: distributor,
            dataDetail: dataDeliveryOrderDetail,
        });
    });

    $("#create-expedition-btn").click(function (e) {
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
                }, 3000);
            },
        });
    });
});
