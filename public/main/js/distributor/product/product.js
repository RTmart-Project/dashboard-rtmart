$(document).ready(function () {
    // DataTables
    dataTablesDistributorProductDetails();

    function dataTablesDistributorProductDetails() {
        const urlDistributorProductDetails = window.location.pathname; // return segment1/segment2/segment3/segment4
        const segmentUrl = urlDistributorProductDetails.split( '/' );
        const distributorId = segmentUrl.pop();

        $('#distributor-product-details .table-datatables').DataTable({
            dom: "<'row'<'col-sm-12 col-md-5'tl><'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-1'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: false,
            stateServe: true,
            "ajax": {
                url: "/distributor/account/product/get/" + distributorId
            },
            columns: [
                {
                    data: 'ProductID',
                    name: 'ProductID'
                },
                {
                    data: 'ProductName',
                    name: 'ProductName'
                },
                {
                    data: 'ProductImage',
                    name: 'ProductImage'
                },
                {
                    data: 'ProductCategoryName',
                    name: 'ProductCategoryName'
                },
                {
                    data: 'ProductTypeName',
                    name: 'ProductTypeName'
                },
                {
                    data: 'ProductUOMName',
                    name: 'ProductUOMName'
                },
                {
                    data: 'ProductUOMDesc',
                    name: 'ProductUOMDesc'
                },
                {
                    data: 'Price',
                    name: 'Price'
                },
                {
                    data: 'Grade',
                    name: 'Grade'
                },
                {
                    data: 'Action',
                    name: 'Action'
                }
            ],
            buttons: [{
                extend: 'excelHtml5',
                filename: function () {
                    return exportDatatableHelper.generateFilename('DistributorProductDetails');
                },
                text: 'Export',
                titleAttr: 'Excel',
                exportOptions: {
                    modifier: {
                        page: 'all'
                    },
                    columns: [0, 1, 3, 4, 5, 6, 7, 8],
                    orthogonal: 'export'
                },
            }],
            "lengthChange": false,
            "responsive": true,
            "autoWidth": false,
            "aoColumnDefs": [
                {
                    "aTargets": [9],
                    "orderable": false
                },
                {
                    "aTargets": [7],
                    "mRender": function (data, type, full) {
                        if (type === 'export') {
                            return data;
                        } else {
                            if (data == null || data == "") {
                                return data;
                            } else {
                                const currencySeperatorFormat = thousands_separators(data)
                                return currencySeperatorFormat;
                            }
                        }
                    }
                }
            ]
        });

        // Event listener saat tombol delete diklik
        $('table').on('click', '.btn-delete', function (e) {
            e.preventDefault();
            const productName = $(this).data("product-name");
            const gradeName = $(this).data("grade-name");
            const distributorId = $(this).data("distributor-id");
            const productId = $(this).data("product-id");
            const gradeId = $(this).data("grade-id");
            $.confirm({
                title: 'Hapus Produk!',
                content: `Yakin ingin menghapus produk <b>${productName}</b> grade <b>${gradeName}</b> ?`,
                closeIcon: true,
                buttons: {
                    hapus: {
                        btnClass: 'btn-red',
                        draggable: true,
                        dragWindowGap: 0,
                        action: function () {
                            window.location = '/distributor/account/product/delete/' + distributorId + '/' + productId + '/' + gradeId
                        }
                    },
                    tidak: function () {
                    }
                }
            });
        });
    }
});