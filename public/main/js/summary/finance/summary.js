// CAKUNG
function getSummaryCakung(startDate = null, endDate = null) {
    $.ajax({
        url: "/summary/get",
        headers: {
            "X-CSRF-TOKEN": csrf,
        },
        data: {
            distributorID: "D-2004-000001",
            startDate,
            endDate,
        },
        type: "post",
        success: function (data) {
            let purchaseOrderCakungExcludeBatal = "";
            let purchaseOrderCakung = "";
            let purchasingCakung = "";
            let voucherCakung = "";
            let deliveryOrderCakung = "";
            let billRealCakung = "";
            let billTargetCakung = "";
            let endingInventoryCakung = "";
            for (const item of data) {
                purchaseOrderCakungExcludeBatal += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.PurchaseOrderExcludeBatal
                )}</td>`;
                purchaseOrderCakung += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.PurchaseOrder
                )}</td>`;
                purchasingCakung += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.Purchasing
                )}</td>`;
                voucherCakung += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.Voucher
                )}</td>`;
                deliveryOrderCakung += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.DeliveryOrder
                )}</td>`;
                billRealCakung += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.BillReal
                )}</td>`;
                billTargetCakung += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.BillTarget
                )}</td>`;
                endingInventoryCakung += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.EndingInventory
                )}</td>`;
            }
            $("#purchase-order-cakung-exclude-batal").append(
                purchaseOrderCakungExcludeBatal
            );
            $("#purchase-order-cakung").append(purchaseOrderCakung);
            $("#purchasing-cakung").append(purchasingCakung);
            $("#voucher-cakung").append(voucherCakung);
            $("#delivery-order-cakung").append(deliveryOrderCakung);
            $("#bill-real-cakung").append(billRealCakung);
            $("#bill-target-cakung").append(billTargetCakung);
            $("#ending-inventory-cakung").append(endingInventoryCakung);
            $("#purchase-order-cakung-exclude-batal .loader-cakung").remove();
        },
    });
}

// BANDUNG
function getSummaryBandung(startDate = null, endDate = null) {
    $.ajax({
        url: "/summary/get",
        headers: {
            "X-CSRF-TOKEN": csrf,
        },
        data: {
            distributorID: "D-2004-000005",
            startDate,
            endDate,
        },
        type: "post",
        success: function (data) {
            let purchaseOrderBandungExcludeBatal = "";
            let purchaseOrderBandung = "";
            let purchasingBandung = "";
            let voucherBandung = "";
            let deliveryOrderBandung = "";
            let billRealBandung = "";
            let billTargetBandung = "";
            let endingInventoryBandung = "";
            for (const item of data) {
                purchaseOrderBandungExcludeBatal += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.PurchaseOrderExcludeBatal
                )}</td>`;
                purchaseOrderBandung += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.PurchaseOrder
                )}</td>`;
                purchasingBandung += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.Purchasing
                )}</td>`;
                voucherBandung += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.Voucher
                )}</td>`;
                deliveryOrderBandung += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.DeliveryOrder
                )}</td>`;
                billRealBandung += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.BillReal
                )}</td>`;
                billTargetBandung += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.BillTarget
                )}</td>`;
                endingInventoryBandung += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.EndingInventory
                )}</td>`;
            }
            $("#purchase-order-bandung-exclude-batal").append(
                purchaseOrderBandungExcludeBatal
            );
            $("#purchase-order-bandung").append(purchaseOrderBandung);
            $("#purchasing-bandung").append(purchasingBandung);
            $("#voucher-bandung").append(voucherBandung);
            $("#delivery-order-bandung").append(deliveryOrderBandung);
            $("#bill-real-bandung").append(billRealBandung);
            $("#bill-target-bandung").append(billTargetBandung);
            $("#ending-inventory-bandung").append(endingInventoryBandung);
            $("#purchase-order-bandung-exclude-batal .loader-bandung").remove();
        },
    });
}

// CIRACAS
function getSummaryCiracas(startDate = null, endDate = null) {
    $.ajax({
        url: "/summary/get",
        headers: {
            "X-CSRF-TOKEN": csrf,
        },
        data: {
            distributorID: "D-2004-000006",
            startDate,
            endDate,
        },
        type: "post",
        success: function (data) {
            let purchaseOrderCiracasExcludeBatal = "";
            let purchaseOrderCiracas = "";
            let purchasingCiracas = "";
            let voucherCiracas = "";
            let deliveryOrderCiracas = "";
            let billRealCiracas = "";
            let billTargetCiracas = "";
            let endingInventoryCiracas = "";
            for (const item of data) {
                purchaseOrderCiracasExcludeBatal += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.PurchaseOrderExcludeBatal
                )}</td>`;
                purchaseOrderCiracas += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.PurchaseOrder
                )}</td>`;
                purchasingCiracas += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.Purchasing
                )}</td>`;
                voucherCiracas += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.Voucher
                )}</td>`;
                deliveryOrderCiracas += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.DeliveryOrder
                )}</td>`;
                billRealCiracas += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.BillReal
                )}</td>`;
                billTargetCiracas += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.BillTarget
                )}</td>`;
                endingInventoryCiracas += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.EndingInventory
                )}</td>`;
            }
            $("#purchase-order-ciracas-exclude-batal").append(
                purchaseOrderCiracasExcludeBatal
            );
            $("#purchase-order-ciracas").append(purchaseOrderCiracas);
            $("#purchasing-ciracas").append(purchasingCiracas);
            $("#voucher-ciracas").append(voucherCiracas);
            $("#delivery-order-ciracas").append(deliveryOrderCiracas);
            $("#bill-real-ciracas").append(billRealCiracas);
            $("#bill-target-ciracas").append(billTargetCiracas);
            $("#ending-inventory-ciracas").append(endingInventoryCiracas);
            $("#purchase-order-ciracas-exclude-batal .loader-ciracas").remove();
        },
    });
}

// Semarang
function getSummarySemarang(startDate = null, endDate = null) {
    $.ajax({
        url: "/summary/get",
        headers: {
            "X-CSRF-TOKEN": csrf,
        },
        data: {
            distributorID: "D-2004-000002",
            startDate,
            endDate,
        },
        type: "POST",
        success: function (data) {
            let purchaseOrderSemarangExcludeBatal = "";
            let purchaseOrderSemarang = "";
            let purchasingSemarang = "";
            let voucherSemarang = "";
            let deliveryOrderSemarang = "";
            let billRealSemarang = "";
            let billTargetSemarang = "";
            let endingInventorySemarang = "";
            for (const item of data) {
                purchaseOrderSemarangExcludeBatal += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.PurchaseOrderExcludeBatal
                )}</td>`;
                purchaseOrderSemarang += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.PurchaseOrder
                )}</td>`;
                purchasingSemarang += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.Purchasing
                )}</td>`;
                voucherSemarang += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.Voucher
                )}</td>`;
                deliveryOrderSemarang += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.DeliveryOrder
                )}</td>`;
                billRealSemarang += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.BillReal
                )}</td>`;
                billTargetSemarang += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.BillTarget
                )}</td>`;
                endingInventorySemarang += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.EndingInventory
                )}</td>`;
            }
            $("#purchase-order-semarang-exclude-batal").append(
                purchaseOrderSemarangExcludeBatal
            );
            $("#purchase-order-semarang").append(purchaseOrderSemarang);
            $("#purchasing-semarang").append(purchasingSemarang);
            $("#voucher-semarang").append(voucherSemarang);
            $("#delivery-order-semarang").append(deliveryOrderSemarang);
            $("#bill-real-semarang").append(billRealSemarang);
            $("#bill-target-semarang").append(billTargetSemarang);
            $("#ending-inventory-semarang").append(endingInventorySemarang);
            $("#purchase-order-semarang-exclude-batal .loader-semarang").remove();
        },
    });
}

// Yogyakarta
function getSummaryYogyakarta(startDate = null, endDate = null) {
    $.ajax({
        url: "/summary/get",
        headers: {
            "X-CSRF-TOKEN": csrf,
        },
        data: {
            distributorID: "D-2212-000001",
            startDate,
            endDate,
        },
        type: "POST",
        success: function (data) {
            let purchaseOrderYogyakartaExcludeBatal = "";
            let purchaseOrderYogyakarta = "";
            let purchasingYogyakarta = "";
            let voucherYogyakarta = "";
            let deliveryOrderYogyakarta = "";
            let billRealYogyakarta = "";
            let billTargetYogyakarta = "";
            let endingInventoryYogyakarta = "";
            for (const item of data) {
                purchaseOrderYogyakartaExcludeBatal += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.PurchaseOrderExcludeBatal
                )}</td>`;
                purchaseOrderYogyakarta += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.PurchaseOrder
                )}</td>`;
                purchasingYogyakarta += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.Purchasing
                )}</td>`;
                voucherYogyakarta += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.Voucher
                )}</td>`;
                deliveryOrderYogyakarta += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.DeliveryOrder
                )}</td>`;
                billRealYogyakarta += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.BillReal
                )}</td>`;
                billTargetYogyakarta += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.BillTarget
                )}</td>`;
                endingInventoryYogyakarta += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.EndingInventory
                )}</td>`;
            }
            $("#purchase-order-yogyakarta-exclude-batal").append(
                purchaseOrderYogyakartaExcludeBatal
            );
            $("#purchase-order-yogyakarta").append(purchaseOrderYogyakarta);
            $("#purchasing-yogyakarta").append(purchasingYogyakarta);
            $("#voucher-yogyakarta").append(voucherYogyakarta);
            $("#delivery-order-yogyakarta").append(deliveryOrderYogyakarta);
            $("#bill-real-yogyakarta").append(billRealYogyakarta);
            $("#bill-target-yogyakarta").append(billTargetYogyakarta);
            $("#ending-inventory-yogyakarta").append(endingInventoryYogyakarta);
            $("#purchase-order-yogyakarta-exclude-batal .loader-yogyakarta").remove();
        },
    });
}

// GRAND TOTAL
function getSummaryGrandTotal(startDate = null, endDate = null) {
    $.ajax({
        url: "/summary/get",
        headers: {
            "X-CSRF-TOKEN": csrf,
        },
        data: {
            distributorID: "grandTotal",
            startDate,
            endDate,
        },
        type: "post",
        success: function (data) {
            let purchaseOrderGrandTotalExcludeBatal = "";
            let purchaseOrderGrandTotal = "";
            let purchasingGrandTotal = "";
            let voucherGrandTotal = "";
            let deliveryOrderGrandTotal = "";
            let billRealGrandTotal = "";
            let billTargetGrandTotal = "";
            let endingInventoryGrandTotal = "";
            for (const item of data) {
                purchaseOrderGrandTotalExcludeBatal += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.PurchaseOrderExcludeBatal
                )}</td>`;
                purchaseOrderGrandTotal += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.PurchaseOrder
                )}</td>`;
                purchasingGrandTotal += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.Purchasing
                )}</td>`;
                voucherGrandTotal += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.Voucher
                )}</td>`;
                deliveryOrderGrandTotal += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.DeliveryOrder
                )}</td>`;
                billRealGrandTotal += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.BillReal
                )}</td>`;
                billTargetGrandTotal += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.BillTarget
                )}</td>`;
                endingInventoryGrandTotal += `<td class="text-right align-middle p-2 data">${thousands_separators(
                    item.EndingInventory
                )}</td>`;
            }
            $("#purchase-order-grand-total-exclude-batal").append(
                purchaseOrderGrandTotalExcludeBatal
            );
            $("#purchase-order-grand-total").append(purchaseOrderGrandTotal);
            $("#purchasing-grand-total").append(purchasingGrandTotal);
            $("#voucher-grand-total").append(voucherGrandTotal);
            $("#delivery-order-grand-total").append(deliveryOrderGrandTotal);
            $("#bill-real-grand-total").append(billRealGrandTotal);
            $("#bill-target-grand-total").append(billTargetGrandTotal);
            $("#ending-inventory-grand-total").append(
                endingInventoryGrandTotal
            );
            $(
                "#purchase-order-grand-total-exclude-batal .loader-grand-total"
            ).remove();
        },
    });
}

function getSummary(startDate = null, endDate = null) {
    $.ajax({
        url: "/summary/get",
        headers: {
            "X-CSRF-TOKEN": csrf,
        },
        data: {
            distributorID: "tanggal",
            startDate,
            endDate,
        },
        type: "post",
        success: function (data) {
            let tanggal = "";
            for (const item of data) {
                tanggal += `<th class="data">${item.DateSummary}</th>`;
            }
            $(".loader-tanggal").remove();
            $("#tanggal").append(tanggal);

            const loaderCakung = `<td class="w-50 text-center align-middle loader-cakung" colspan="${data.length}" rowspan="8">harap tunggu <i class="fas fa-spinner fa-spin"></i></td>`;
            $("#purchase-order-cakung-exclude-batal").append(loaderCakung);

            const loaderBandung = `<td class="w-50 text-center align-middle loader-bandung" colspan="${data.length}" rowspan="8">harap tunggu <i class="fas fa-spinner fa-spin"></i></td>`;
            $("#purchase-order-bandung-exclude-batal").append(loaderBandung);

            const loaderCiracas = `<td class="w-50 text-center align-middle loader-ciracas" colspan="${data.length}" rowspan="8">harap tunggu <i class="fas fa-spinner fa-spin"></i></td>`;
            $("#purchase-order-ciracas-exclude-batal").append(loaderCiracas);

            const loaderGrandTotal = `<td class="w-50 text-center align-middle loader-grand-total" colspan="${data.length}" rowspan="8">harap tunggu <i class="fas fa-spinner fa-spin"></i></td>`;
            $("#purchase-order-grand-total-exclude-batal").append(
                loaderGrandTotal
            );

            if (depo == "ALL" && !regional) {
                getSummarySemarang(startDate, endDate);
                getSummaryYogyakarta(startDate, endDate);
                getSummaryCakung(startDate, endDate);
                getSummaryBandung(startDate, endDate);
                getSummaryCiracas(startDate, endDate);
                getSummaryGrandTotal(startDate, endDate);
            }
            if (depo == "ALL" && regional == "REGIONAL1") {
                getSummarySemarang(startDate, endDate);
                getSummaryYogyakarta(startDate, endDate);
                getSummaryGrandTotal(startDate, endDate);
            }
            if (depo == "ALL" && regional == "REGIONAL2") {
                getSummaryCakung(startDate, endDate);
                getSummaryBandung(startDate, endDate);
                getSummaryCiracas(startDate, endDate);
                getSummaryGrandTotal(startDate, endDate);
            }
        },
    });
}
