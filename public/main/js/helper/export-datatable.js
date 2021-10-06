const exportDatatableHelper = {
    generateFilename: function (customFilename) {
        const today = new Date()
        const month = today.toLocaleString('id-ID', {
            month: 'long'
        });
        const date = today.getDate();
        const year = today.getFullYear();
        const hours = today.getHours();
        const minute = today.getMinutes();

        const filename = customFilename + "_" + date + month + year + "_" + hours + minute;
        return filename;
    },
}