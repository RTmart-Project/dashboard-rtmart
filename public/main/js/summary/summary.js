function getSummaryDate(startDate = null, endDate = null) {
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
            $(".loader-tanggal").addClass("d-none");
            $("#tanggal").append(tanggal);

            const loaderCakung = `<td class="w-50 text-center align-middle loader-cakung" colspan="${data.length}" rowspan="8">harap tunggu <i class="fas fa-spinner fa-spin"></i></td>`;
            $("#purchase-order-cakung").append(loaderCakung);

            const loaderBandung = `<td class="w-50 text-center align-middle loader-bandung" colspan="${data.length}" rowspan="8">harap tunggu <i class="fas fa-spinner fa-spin"></i></td>`;
            $("#purchase-order-bandung").append(loaderBandung);

            const loaderCiracas = `<td class="w-50 text-center align-middle loader-ciracas" colspan="${data.length}" rowspan="8">harap tunggu <i class="fas fa-spinner fa-spin"></i></td>`;
            $("#purchase-order-ciracas").append(loaderCiracas);

            const loaderGrandTotal = `<td class="w-50 text-center align-middle loader-grand-total" colspan="${data.length}" rowspan="8">harap tunggu <i class="fas fa-spinner fa-spin"></i></td>`;
            $("#purchase-order-grand-total").append(loaderGrandTotal);
        },
    });
}

function getSummaryCakung(startDate = null, endDate = null) {
    // CAKUNG
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
            let purchaseOrderCakung = "";
            let purchasingCakung = "";
            let voucherCakung = "";
            let deliveryOrderCakung = "";
            let billRealCakung = "";
            let billTargetCakung = "";
            let endingInventoryCakung = "";
            for (const item of data) {
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
            $("#purchase-order-cakung").append(purchaseOrderCakung);
            $("#purchasing-cakung").append(purchasingCakung);
            $("#voucher-cakung").append(voucherCakung);
            $("#delivery-order-cakung").append(deliveryOrderCakung);
            $("#bill-real-cakung").append(billRealCakung);
            $("#bill-target-cakung").append(billTargetCakung);
            $("#ending-inventory-cakung").append(endingInventoryCakung);
            $(".loader-cakung").addClass("d-none");
        },
    });
}

function getSummaryBandung(startDate = null, endDate = null) {
    // BANDUNG
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
            let purchaseOrderBandung = "";
            let purchasingBandung = "";
            let voucherBandung = "";
            let deliveryOrderBandung = "";
            let billRealBandung = "";
            let billTargetBandung = "";
            let endingInventoryBandung = "";
            for (const item of data) {
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
            $("#purchase-order-bandung").append(purchaseOrderBandung);
            $("#purchasing-bandung").append(purchasingBandung);
            $("#voucher-bandung").append(voucherBandung);
            $("#delivery-order-bandung").append(deliveryOrderBandung);
            $("#bill-real-bandung").append(billRealBandung);
            $("#bill-target-bandung").append(billTargetBandung);
            $("#ending-inventory-bandung").append(endingInventoryBandung);
            $(".loader-bandung").addClass("d-none");
        },
    });
}

function getSummaryCiracas(startDate = null, endDate = null) {
    // CIRACAS
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
            let purchaseOrderCiracas = "";
            let purchasingCiracas = "";
            let voucherCiracas = "";
            let deliveryOrderCiracas = "";
            let billRealCiracas = "";
            let billTargetCiracas = "";
            let endingInventoryCiracas = "";
            for (const item of data) {
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
            $("#purchase-order-ciracas").append(purchaseOrderCiracas);
            $("#purchasing-ciracas").append(purchasingCiracas);
            $("#voucher-ciracas").append(voucherCiracas);
            $("#delivery-order-ciracas").append(deliveryOrderCiracas);
            $("#bill-real-ciracas").append(billRealCiracas);
            $("#bill-target-ciracas").append(billTargetCiracas);
            $("#ending-inventory-ciracas").append(endingInventoryCiracas);
            $(".loader-ciracas").addClass("d-none");
        },
    });
}

function getSummaryGrandTotal(startDate = null, endDate = null) {
    // GRAND TOTAL
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
            let purchaseOrderGrandTotal = "";
            let purchasingGrandTotal = "";
            let voucherGrandTotal = "";
            let deliveryOrderGrandTotal = "";
            let billRealGrandTotal = "";
            let billTargetGrandTotal = "";
            let endingInventoryGrandTotal = "";
            for (const item of data) {
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
            $("#purchase-order-grand-total").append(purchaseOrderGrandTotal);
            $("#purchasing-grand-total").append(purchasingGrandTotal);
            $("#voucher-grand-total").append(voucherGrandTotal);
            $("#delivery-order-grand-total").append(deliveryOrderGrandTotal);
            $("#bill-real-grand-total").append(billRealGrandTotal);
            $("#bill-target-grand-total").append(billTargetGrandTotal);
            $("#ending-inventory-grand-total").append(
                endingInventoryGrandTotal
            );
            $(".loader-grand-total").addClass("d-none");
        },
    });
}

function getSummary(startDate = null, endDate = null) {
    getSummaryDate(startDate, endDate);
    getSummaryCakung(startDate, endDate);
    getSummaryBandung(startDate, endDate);
    getSummaryCiracas(startDate, endDate);
    getSummaryGrandTotal(startDate, endDate);
}
