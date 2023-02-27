<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ "Disclaimer - ". $merchant->MerchantID. " - ". $merchant->UsernameIDCard }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 50px 80px;
        }
    </style>
</head>

<body>
    <div>
        <p align="center">
            <strong>SYARAT DAN KETENTUAN</strong>
        </p>
        <p align="center">
            <strong>PINJAMAN</strong>
        </p>
        <p>
            <strong>A. </strong>
            <strong>Definisi</strong>
        </p>
        <p>
            1. RT Tempo adalah suatu layanan dalam Platform yang merupakan pemberian
            fasilitas pinjaman modal kerja yang disalurkan berupa barang perdagangan
            dengan berbasis teknologi informasi yang disediakan oleh (Para) Pemberi
            Pinjaman kepada Penerima Pinjaman melalui PT RTmart Grup Indonesia sebagai
            Platform.
        </p>
        <p>
            2. Peminjam adalah Mitra PT RTmart yang merupakan Calon Peminjam yang telah
            disetujui permohonannya oleh Pemberi Pinjaman dan telah menerima Fasilitas
            Pinjaman.
        </p>
        <p>
            3. Pemberi Pinjaman adalah Koperasi Simpan Pinjam Sekartama (KOSPIN
            SEKARTAMA) yang dalam melakukan pengikatan perjanjian ini di wakili oleh
            IBNU DODY PRAYITNO, S.H., Jabatan Manager. KOSPIN SEKARTAMA adalah suatu
            badan usaha layanan keuangan yang meruapakan partner kerja RTmart yang
            ditunjuk untuk memberikan fasilitas pinjaman kepada mitra RTmart.
        </p>
        <p>
            4. Informasi adalah data, dokumen atau keterangan tentang Pengguna atau
            Calon Peminjam atau Peminjam yang telah diberikan oleh Pengguna, Calon
            Peminjam atau Peminjam berdasarkan persetujuan dari Penguna, Calon Peminjam
            tersebut dengan tujuan untuk mendapatkan pinjaman yang dibutuhkan.
        </p>
        <p>
            <strong>B. </strong>
            <strong>Pernyataan Kebenaran Data</strong>
        </p>
        <p>
            Saya yang bertanda tangan di bawah ini :
        </p>
        <p>
            Nama : {{ $merchant->UsernameIDCard }}
        </p>
        <p>
            Alamat : {{ $merchant->StoreAddress }}
        </p>
        <p>
            Pekerjaan : WIRAUSAHA
        </p>
        <p>
            No. KTP : {{ $merchant->NumberIDCard }}
        </p>
        <p>
            Saat ini saya akan membuat Perjanjian Membuka Pinjaman dengan Pemberi
            Pinjaman. Untuk itu saya menyatakan dengan Sebenar-benarnya bahwa :
        </p>
        <p>
            1. Data pribadi KTP (Kartu Tanda Penduduk) yang saya serahkan kepada
            Pemberi Pinjaman adalah Benar dan ASLI MILIK SAYA.
        </p>
        <p>
            2. Saya sanggup untuk melakukan pembayaran kewajiban TEPAT WAKTU tiap
            bulannya sesuai nilai setoran yang sudah disepakati.
        </p>
        <p>
            3. Saya mengajukan pinjaman kepada Pemberi Pinjaman adalah untuk keperluan
            modal usaha sebagai Mitra RTmart, sesuai permohonan pinjaman yang saya
            sampaikan.
        </p>
        <p>
            Pernyataan ini saya buat dengan sebenar-benarnya tanpa ada paksaan dari
            pihak manapun. Apabila saya melanggar atau pernyataan saya ini tidak sesuai
            dengan DATA dan FAKTA yang sebenarnya, maka saya bersedia menerima segala
            KONSEKUENSI yang timbul termasuk TINDAKAN HUKUM yang akan diambil oleh
            pihak Pemberi Pinjaman.
        </p>
        <p>
            <strong>C. </strong>
            <strong>Pernyataan Pembukaan Pinjaman</strong>
        </p>
        <p>
            <strong></strong>
        </p>
        <p align="center">
            PERJANJIAN MEMBUKA PINJAMAN
        </p>
        <p align="center">
            No : {{ $merchant->SeriesNumber }}
        </p>
        <p>
            Pada hari ini {{ date('Y-m-d', strtotime($merchant->MembershipCoupleSubmitDate)) }} telah disepakati Perjanjian Membuka Pinjaman
            oleh dan antara :
        </p>
        <p>
            I. Nama : {{ $merchant->UsernameIDCard }}
        </p>
        <p>
            Pekerjaan : Wirausaha
        </p>
        <p>
            NIK : {{ $merchant->NumberIDCard }}
        </p>
        <p>
            Alamat : {{ $merchant->StoreAddress }}
        </p>
        <p>
            Untuk selanjutnya disebut PIHAK I atau PEMINJAM.
        </p>
        <p>
            II. IBNU DODY PRAYITNO, S.H., Jabatan Manager di Kospin SEKARTAMA bertindak
            untuk dan atas nama serta mewakili Kospin SEKARTAMA. Yang selanjutnya
            disebut sebagai PIHAK II atau Pemberi Pinjaman.
        </p>
        <p>
            PEMINJAM dan KOSPIN SEKARTAMA secara bersama-sama selanjutnya disebut PARA
            PIHAK.
        </p>
        <p>
            Bahwa PEMINJAM telah mengajukan permohonan pinjaman melalui RTmart kepada
            Kospin SEKARTAMA tanggal {{ date('Y-m-d', strtotime($merchant->MembershipCoupleSubmitDate)) }} dengan ketentuan pokok yang telah
            disetujui PEMINJAM. Ketentuan pokok tersebut akan diuraikan lebih Ianjut
            dalam ketentuan dan syarat-syarat perjanjian pinjaman dalam pasal-pasal
            dalam perjanjian ini;
        </p>
        <p>
            <strong>D. </strong>
            <strong>Faslitas Pinjaman</strong>
        </p>
        <p>
            Pemberi Pinjaman setuju untuk memberikan fasilitas pinjaman kepada Peminjam
            berupa pinjaman uang senilai yang diajukan yang akan dipindah bukukan
            kedalam virtual account atas nama peminjam melalui sistem RTmart, untuk
            selanjutnya dipakai sebagai pembayaran atas transaksi pembelian barang dari
            RTmart.
        </p>
        <p>
            <strong>E. </strong>
            <strong>Jasa dan Biaya Administrasi</strong>
        </p>
        <p>
            Atas pinjaman tersebut diatas, Peminjam wajib membayar kepada Pemberi
            Pinjaman:
        </p>
        <p>
            1. Jasa sebesar 1,5 % per bulan ditanggung oleh RTmart
        </p>
        <p>
            2. Biaya Administrasi sebesar 3 % dari plafon pinjaman dipotong dimuka
            ditanggung oleh RTmart
        </p>
        <p>
            <strong>F. </strong>
            <strong>Jangka Waktu dan Angsuran</strong>
        </p>
        <p>
            1. Pembayaran setoran jasa dibayarkan setiap bulan selama 6 (enam) bulan,
            dan pada saat jatuh tempo pinjaman, peminjam wajib melakukan pelunasan atas
            jumlah pinjaman yang terhutang oleh Peminjam kepada Pemberi Pinjaman.
        </p>
        <p>
            2. Angsuran pinjaman berupa setoran jasa setiap bulan sampai dengan
            pelunasan pada saat jatuh tempo akan diterima oleh Pemberi Pinjaman melalui
            RTmart.
        </p>
        <p>
            3. Tanggal angsuran dan tanggal pelunasan pokok pinjaman akan di
            informasikan melalui RTmart
        </p>
        <p>
            <strong>G. </strong>
            <strong>Angunan</strong>
        </p>
        <p>
            Untuk menjamin kepastian pembayaran kembali seluruh pinjaman baik pokok,
            jasa dan biaya-biaya lainnya yang timbul dari perjanjian ini, akan di jamin
            sepenuhnya oleh RTmart sebagai penanggungjawab sesuai dengan Perjanjian
            Kerjasama yang sudah disepakati.
        </p>
        <p>
            <strong>H. </strong>
            <strong>Denda</strong>
        </p>
        <p>
            Dalam hal Peminjam tidak membayar angsuran dan setoran bunga tepat pada
            waktunya sebagaimana telah ditentukan dalam pasal (2) perjanjian pinjaman
            ini, Oleh sebab itu Peminjam dikenakan Denda sebesar 0,2 (nol koma dua)
            persen per hari untuk setiap keterlambatan, dihitung dari besarnya
            kewajiban yang masih terlambat dibayar, sudah harus dibayar pada bulan
            angsuran berikutnya. Denda ini akan dibayarkan terlebih dahulu oleh RTmart
            untuk selanjutnya di tagihkan kepada peminjam.
        </p>
        <p>
            <strong>I. </strong>
            <strong>Pelunasan Sebelum Jatuh Tempo</strong>
        </p>
        <p>
            Peminjam berhak untuk melunasi pinjaman sewaktu-waktu sebelum jatuh tempo
            pada hari dan jam kerja, melunasi baik sisa pokok, jasa, denda dan
            biaya-biaya yang sudah terjadi yang timbul karena adanya perjanjian ini
            tidak dikenakan biaya penalty (sesuai nilai Pokok dan jasa berjalan).
        </p>
        <p>
            <strong>J. </strong>
            <strong>Pemantauan Pinjaman</strong>
        </p>
        <p>
            Selama PEMINJAM karena sebab apapun juga masih mempunyai pinjaman kepada
            Kospin SEKARTAMA, maka :
        </p>
        <p>
            1. Peminjam bersedia untuk melakukan pembayaran angsuran sebagaimana
            dimaksud pada Pasal 3 Ayat 1 melalui Pihak RTmart Indoneisa.
        </p>
        <p>
            2. Peminjam mengizinkan wakil-wakil Pemberi Pinjamanyang sah untuk
            memeriksa keadaan usaha Peminjam.
        </p>
        <p>
            3. Peminjam akan menyampalkan kepada Pemberi Pinjaman. informasi yang
            sebenar-benarnya dan selengkap-lengkapnya tentang kondisi usaha dan atau
            perubahan-perubahan yang ada.
        </p>
        <p>
            <strong>K. </strong>
            <strong>Keadaan Ingkar Janji</strong>
        </p>
        <p>
            Peminjam menyatakan semua data dan informasi yang diberikannya pada Pemberi
            Pinjaman adalah benar dan Peminjam berjanji untuk melaksanakan semua
            kewajibannya terkait pinjamannya ini dengan baik, namun apabila ternyata :
        </p>
        <p>
            1. Peminjam tidak membayar baik pokok dan atau jasa tepat pada waktunya,
            atau
        </p>
        <p>
            2. Peminjam melanggar dan atau tidak melaksanakan kewajiban yang
            disyaratkan dalam perjanjian ini.
        </p>
        <p>
            3. Maka Para Pihak sepakat menyatakan Peminjam dalam keadaan ingkar janji.
        </p>
        <p>
            <strong>L. </strong>
            <strong>Domisili dan Pemberitahuan</strong>
        </p>
        <p>
            Para PIHAK dengan ini menyatakan bahwa :
        </p>
        <p>
            1. Alamat Pemberi Pinjaman dan peminjam sebagaimana tercantum pada awal
            Perjanjian Pinjaman ini merupakan alamat tetap bagi masing-masing pihak,
            dan secara sah dipergunakan untuk segala surat menyurat atau komunikasi
            diantara para pihak.
        </p>
        <p>
            2. Apabila ada perubahan alamat maka para pihak wajib memberitahukan secara
            tertulis alamat barunya kepada pihak lainnya paling lambat 7 (tujuh) hari
            sejak terjadinya perubahan alamat.
        </p>
        <p>
            3. Selama tidak terdapat pemberitahuan tentang perubahan alamat sebagaimana
            dimaksud pada ayat 2 pasal ini, maka untuk surat menyurat atau komunikasi
            yang dilakukan ke alamat yang tercantum pada awal Perjanjian Pinjaman
            dianggap sah menurut hukum.
        </p>
        <p>
            <strong>M. </strong>
            <strong>Domisili dan Hukum Yang Berlaku</strong>
        </p>
        <p>
            1. Dalam hal terjadi perbedaan pendapat dalam memahami atau menafsirkan
            bagian-bagian dari isi perjanjian atau terjadi perselisihan dalam
            melaksanakan perjanjian ini, maka para PIHAK sepakat untuk menyelesaikan
            secara musyawarah dan mufakat.
        </p>
        <p>
            2. Apabila penyelesaian secara musyawarah dan mufakat tidak dapat
            menyelesaikan permasalahan maka para PIHAK sepakat untuk memilih domisili
            hukum di kantor Panitera Pengadilan Negeri Kendal.
        </p>
        <p>
            <strong>N. </strong>
            <strong>Lain-lain</strong>
        </p>
        <p>
            1. Perjanjian pinjaman ini merupakan perjanjian yang tidak dapat dipisahkan
            dengan perjanjian yang dibuat dikemudian hari oleh Para pihak baik berupa
            penambahan, perpanjangan maupun perubahan-perubahan lainnya dan yang dibuat
            tersendiri dari perjanjian pinjaman ini adalah merupakan bagian terpenting
            dan tidak dapat dipisah-pisahkan dari perjanjian ini yang tidak akan dibuat
            tanpa adanya kuasa-kuasa tersebut. Dan karenanya kuasa-kuasa tersebut dan
            kuasa yang ada dalam surat perjanjian ini tidak dapat dicabut kembali dan
            tidak akan berakhir karena sebab-sebab apapun juga, selama perjanjian ini
            berlangsung dan selama Peminjam belum melunasi seluruh pinjamanya kepada
            Pemberi Pinjaman.
        </p>
        <p>
            2. Peminjam telah membaca dan memahami seluruh ketentuan yang ada dalam
            Perjanjlan Pinjaman dan Syarat dan Ketentuan Umum Pemberian Fasilitas
            Pinjaman serta Peminjam memperoleh informasi yang jelas dan benar tentang
            Fasilitas pinjaman yang diberikan oleh Pemberi Pinjaman kepada Peminjam.
        </p>
        <p>
            3. Perjanjian pinjaman ini telah disesuaikan dengan Ketentuan
            Perundangan-undangan.
        </p>
        <p>
            4. RTmart berhak mengatur dan mengubah bentuk skema pendistribusian
            pinjaman dengan terlebih dahulu menginformasikan aturan dan perubahannya ke
            penerima pinjaman
        </p><br><br><br>
        <p>
            <strong>Peminjam, </strong>
        </p><br>
        <p>
            {{ $merchant->UsernameIDCard }}<strong></strong>
        </p>
    </div>
</body>

<script>
    window.addEventListener("load", window.print());
</script>

</html>