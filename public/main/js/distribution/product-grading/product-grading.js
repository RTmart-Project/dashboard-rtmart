$(document).ready(function () {
    // DataTables
    dataTablesProductGrading();

    function dataTablesProductGrading() {
        $("#product-grading .table-datatables").DataTable({
            dom:
                "<'row'<'col-sm-12 col-md-5'<'filter-product-grading'>tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            stateServe: true,
            ajax: {
                url: "/distribution/product/get",
                data: function (d) {
                    d.distributorId = $(
                        "#product-grading .select-filter-custom select"
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
                    name: "ms_distributor_product_price.ProductID",
                },
                {
                    data: "ProductName",
                    name: "ms_product.ProductName",
                },
                {
                    data: "ProductImage",
                    name: "ms_product.ProductImage",
                },
                {
                    data: "ProductCategoryName",
                    name: "ms_product_category.ProductCategoryName",
                },
                {
                    data: "ProductTypeName",
                    name: "ms_product_type.ProductTypeName",
                },
                {
                    data: "ProductUOMName",
                    name: "ms_product_uom.ProductUOMName",
                },
                {
                    data: "ProductUOMDesc",
                    name: "ms_product.ProductUOMDesc",
                },
                {
                    data: "Price",
                    name: "ms_distributor_product_price.Price",
                },
                {
                    data: "Grade",
                    name: "ms_distributor_grade.Grade",
                },
                {
                    data: "IsPreOrder",
                    name: "ms_distributor_product_price.IsPreOrder",
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
                            "ProductGrading"
                        );
                    },
                    action: exportDatatableHelper.newExportAction,
                    text: "Export",
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
            lengthChange: false,
            responsive: true,
            autoWidth: false,
            aoColumnDefs: [
                {
                    aTargets: [8],
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

    // Create element for DateRange Filter
    let depo = $('meta[name="depo"]').attr("content");
    if (depo == "ALL") {
        $("div.filter-product-grading").html(`<div class="input-group">
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
            $("#product-grading .select-filter-custom select").append(option);
            customDropdownFilter.createCustomDropdowns();
        },
    });

    // Event listener saat tombol select option diklik
    $("#product-grading .select-filter-custom select").change(function () {
        $("#product-grading .table-datatables").DataTable().ajax.reload();
    });

    let csrf = $('meta[name="csrf_token"]').attr("content");
    // Event listener saat tombol edit diklik
    $("table").on("click", ".btn-edit", function (e) {
        e.preventDefault();
        const productName = $(this).data("product-name");
        const gradeName = $(this).data("grade-name");
        const distributorId = $(this).data("distributor-id");
        const productId = $(this).data("product-id");
        const gradeId = $(this).data("grade-id");
        const priceProduct = $(this).data("price");
        const isPreOrder = $(this).data("pre-order");
        $.confirm({
            title: "Edit Produk",
            content: `Ubah produk <b>${productName}</b> grade <b>${gradeName}</b><br>
                <form action="/distribution/product/update/${distributorId}/${productId}/${gradeId}" method="post">
                    <label class="mt-2 mb-0">Harga:</label>
                    <input type="hidden" name="_token" value="${csrf}">
                    <input type="number" class="form-control price" value="${priceProduct}" name="price" autocomplete="off">
                    <label class="mt-2 mb-0">Pre Order:</label>
                    <select class="form-control" name="is_pre_order">
                        <option value="1" ${
                            isPreOrder == 1 ? "selected" : ""
                        }>Ya</option>
                        <option value="0" ${
                            isPreOrder == 0 ? "selected" : ""
                        }>Tidak</option>
                    </select>
                </form>`,
            closeIcon: true,
            buttons: {
                simpan: {
                    btnClass: "btn-success",
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        let price = this.$content.find(".price").val();
                        if (!price) {
                            $.alert(
                                "Harga produk tidak boleh kosong",
                                "Edit Harga"
                            );
                            return false;
                        }
                        this.$content.find("form").submit();
                    },
                },
                tidak: function () {},
            },
        });
    });

    // Event listener saat tombol delete diklik
    $("table").on("click", ".btn-delete", function (e) {
        e.preventDefault();
        const productName = $(this).data("product-name");
        const gradeName = $(this).data("grade-name");
        const distributorId = $(this).data("distributor-id");
        const productId = $(this).data("product-id");
        const gradeId = $(this).data("grade-id");
        $.confirm({
            title: "Hapus Produk!",
            content: `Yakin ingin menghapus produk <b>${productName}</b> grade <b>${gradeName}</b> ?`,
            closeIcon: true,
            buttons: {
                hapus: {
                    btnClass: "btn-red",
                    draggable: true,
                    dragWindowGap: 0,
                    action: function () {
                        window.location =
                            "/distribution/product/delete/" +
                            distributorId +
                            "/" +
                            productId +
                            "/" +
                            gradeId;
                    },
                },
                tidak: function () {},
            },
        });
    });
});
