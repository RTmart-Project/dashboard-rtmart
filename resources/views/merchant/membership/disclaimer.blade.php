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
            padding: 20px 80px;
        }

        #top5 {
            padding:5px 10px 10px 0px; !important;
        }

        #pasal {
            margin-top: 50px
        }
    </style>
</head>

<body>
    <p align="center">
        <strong>PERJANJIAN</strong>
        <strong> </strong>
        <strong>MEMBUKA PINJAMAN</strong>
        <strong></strong>
    </p>
    <p align="center">
        No : {{ $merchant->SeriesNumber }}
    </p>
    <p>
        Pada hari ini {{ $date }} telah disepakati Perjanjian Membuka Pinjaman
        oleh dan antara:
    </p>
    <table border="0" cellspacing="0" cellpadding="0">
        <tbody>
            <tr>
                <td id="top5" width="22" valign="top">
                    I.
                </td>
                <td id="top5" width="104" valign="top">
                    Nama
                </td>
                <td id="top5" width="18" valign="top">
                    :
                </td>
                <td id="top5" width="444" valign="top">
                    {{ $merchant->UsernameIDCard }}
                </td>
            </tr>
            <tr>
                <td id="top5" width="22" valign="top">
                </td>
                <td id="top5" width="104" valign="top">
                    Pekerjaan
                </td>
                <td id="top5" width="18" valign="top">
                    :
                </td>
                <td id="top5" width="444" valign="top">
                    Wirausaha
                </td>
            </tr>
            <tr>
                <td id="top5" width="22" valign="top">
                </td>
                <td id="top5" width="104" valign="top">
                    NIK
                </td>
                <td id="top5" width="18" valign="top">
                    :
                </td>
                <td id="top5" width="444" valign="top">
                    {{ $merchant->NumberIDCard }}
                </td>
            </tr>
            <tr>
                <td width="22" valign="top">
                </td>
                <td id="top5" width="104">
                    Alamat
                </td>
                <td id="top5" width="18">
                    :
                </td>
                <td id="top5" width="444">
                    {{ $merchant->StoreAddress }}
                </td>
            </tr>
        </tbody>
    </table>
    <p>
        Untuk selanjutnya disebut PIHAK I atau Peminjam.
    </p>
    <p>
        II. IBNU DODY PRAYITNO, S.H., Jabatan Manager di Kospin SEKARTAMA bertindak
        untuk dan atas nama serta mewakili Kospin SEKARTAMA, yang selanjutnya
        disebut sebagai PIHAK II atau Pemberi Pinjaman.
    </p>
    <p>
        III. Momog Irnawan selaku Direktur PT RT Mart Grup Indonesia bertindak
        mewakili RT Mart, yang selanjut disebut sebagai PIHAK III atau Penjamin
    </p>
    <p>
        Peminjam, Pemberi Pinjaman atau Kospin SEKARTAMA dan Penjamin secara
        bersama-sama selanjutnya disebut para pihak. Para Pihak dalam kedudukan
        masing-masing seperti tersebut diatas, dengan ini terlebih dahulu
        menerangkan hal-hal sebagai berikut :
    </p>
    <p>
        1. Bahwa RT Tempo adalah suatu layanan dalam Platform yang merupakan
        pemberian fasilitas pinjaman modal kerja yang disalurkan berupa barang
        perdagangan dengan berbasis teknologi informasi yang disediakan oleh (Para)
        Pemberi Pinjaman kepada Penerima Pinjaman melalui PT RTmart sebagai
        Platform;
    </p>
    <p>
        2. Bahwa Peminjam adalah Mitra RTmart yang merupakan Peminjam yang telah
        disetujui permohonannya oleh Pemberi Pinjaman dan telah menerima Fasilitas
        Pinjaman.
    </p>
    <p>
        3. Bahwa Pemberi Pinjaman adalah Koperasi Simpan Pinjam Sekartama (KOSPIN
        SEKARTAMA) yang dalam melakukan pengikatan perjanjian ini di wakili oleh
        IBNU DODY PRAYITNO, S.H., Jabatan Manager. KOSPIN SEKARTAMA adalah suatu
        badan usaha layanan keuangan yang merupakan partner kerja RTmart yang
        ditunjuk untuk memberikan fasilitas pinjaman kepada mitra RTmart;
    </p><br><br><br><br>
    <p>
        4. Bahwa Informasi adalah data, dokumen atau keterangan tentang Pengguna
        atau Calon Peminjam atau Peminjam yang telah diberikan oleh Pengguna, Calon
        Peminjam atau Peminjam berdasarkan persetujuan dari Penguna, Calon Peminjam
        tersebut dengan tujuan untuk mendapatkan pinjaman yang dibutuhkan;
    </p>
    <p>
        5. Bahwa Peminjam mengajukan pinjaman kepada Pemberi Pinjaman melalui
        RTmart adalah untuk keperluan modal usaha sebagai Mitra RTmart, sesuai
        permohonan pinjaman yang telah disampaikan;
    </p>
    <p>
        6. Bahwa Kospin SEKARTAMA telah memberikan persetujuan atas permohonan
        tersebut dengan ketentua dan syarat yang berlaku;
    </p>
    <p>
        7. Bahwa PENJAMIN bertanggung jawab serta membayar lunas tiap-tiap dan
        seluruh jumlah uang yang terhutang oleh Pihak Peminjam baik saat ini maupun
        dikemudian hari yang terhutang kepada Pihak Kospin SEKARTAMA.
    </p>
    <p>
        Berdasarkan hal-hal tersebut diatas, dengan ini Para Pihak telah sepakat
        untuk membuat Perjanjian ini dengan ketentuan dan syarat-syarat sebagai
        berikut :
    </p>
    <p align="center" id="pasal">
        Pasal 1
    </p>
    <p align="center">
        <strong>Faslitas Pinjaman</strong>
    </p>
    <p>
        Pemberi Pinjaman setuju untuk memberikan fasilitas pinjaman kepada Peminjam
        berupa pinjaman uang senilai Rp {{ $merchant->Nominal }} ({{ $merchant->Penyebut }}) yang diajukan
        yang akan dipindah bukukan kedalam virtual account atas nama peminjam
        melalui sistem RTmart, untuk selanjutnya dipakai sebagai pembayaran atas
        transaksi pembelian barang dari RTmart.
    </p>
    <p align="center" id="pasal">
        Pasal 2
    </p>
    <p align="center">
        <strong>Jasa dan Biaya Provisi Administrasi</strong>
    </p>
    <p>
        Atas pinjaman tersebut diatas, Peminjam wajib membayar kepada Pemberi
        Pinjaman:<strong></strong>
    </p>
    <p>
        1. Jasa sebesar 1,5 % per bulan ditanggung oleh RTmart<strong></strong>
    </p>
    <p>
        2. Biaya Provisi dan Administrasi sebesar 3 % dari plafon pinjaman dipotong
        dimuka ditanggung oleh RTmart<strong></strong>
    </p>
    <p align="center" id="pasal" style="margin-top: 200px">
        Pasal 3<strong></strong>
    </p>
    <p align="center">
        <strong>Jangka Waktu dan Angsuran</strong>
    </p>
    <p>
        1. Pembayaran setoran jasa dibayarkan setiap bulan selama 6 (enam) bulan
        dan pada saat jatuh tempo pinjaman, peminjam wajib melakukan pelunasan atas
        jumlah pinjaman yang terhutang oleh Peminjam kepada Pemberi Pinjaman.
    </p>
    <p>
        2. Angsuran pinjaman berupa setoran jasa setiap bulan sampai dengan
        pelunasan pada saat jatuh tempo akan diterima oleh Pemberi Pinjaman melalui
        RTmart.
    </p>
    <p>
        3. Tanggal angsuran dan tanggal pelunasan pokok pinjaman akan
        diinformasikan melalui RTmart.
    </p>
    <p align="center" id="pasal">
        Pasal 4
    </p>
    <p align="center">
        <strong>Penjamin</strong>
    </p>
    <p>
        Pihak Penjamin setuju dan mengikatkan diri terhadap Kospin SEKARTAMA untuk
        bertanggung jawab serta membayar lunas tiap-tiap dan seluruh jumlah uang
        yang terhutang oleh Pihak PEMINJAM baik saat ini maupun dikemudian hari
        yang terhutang kepada Pihak Kospin SEKARTAMA yaitu hutang pokok, bunga,
        denda serta biaya-biaya lainnya yang terhutang kepada Pihak Kospin
        SEKARTAMA berdasarkan Perjanjian Membuka Pinjaman, yang demikian pada waktu
        permintaan pertama dari Pihak Kospin SEKARTAMA terhadap Pihak Penjamin
        untuk itu tanpa diperlukan lagi sesuatu pembuktian tentang kelalaian
        PEMINJAM dalam memenuhi kewajibannya.
    </p>
    <p>
        <strong></strong>
    </p>
    <p align="center" id="pasal">
        Pasal 5
    </p>
    <p align="center">
        <strong>Denda</strong>
    </p>
    <p>
        Dalam hal Peminjam tidak membayar angsuran dan setoran bunga tepat pada
        waktunya sebagaimana telah ditentukan dalam pasal (2) perjanjian pinjaman <strong> </strong>ini. Oleh sebab itu
        Peminjam dikenakan Denda sebesar 0,2%
        (nol koma dua) persen per hari untuk setiap keterlambatan, dihitung dari
        besarnya kewajiban yang masih terlambat dibayar, sudah harus dibayar pada
        bulan angsuran berikutnya.
    </p>
    <p align="center" id="pasal">
        Pasal 6
    </p>
    <p align="center">
        <strong>Pelunasan Sebelum Jatuh Tempo</strong>
    </p>
    <p>
        Peminjam berhak untuk melunasi pinjaman sewaktu-waktu sebelum jatuh tempo
        pada hari dan jam kerja, melunasi baik sisa pokok, jasa, denda dan
        biaya-biaya yang sudah terjadi yang timbul karena adanya perjanjian ini
        tidak dikenakan biaya penalty (sesuai nilai Pokok dan jasa berjalan).
    </p>
    <p align="center" id="pasal">
        Pasal 7
    </p>
    <p align="center">
        <strong>Pernyataan</strong>
    </p>
    <p>
        PEMINJAM dengan ini menyatakan dan menjamin kepada Kospin SEKARTAMA
        mengenai kebenaran hal-hal sebagai berikut untuk selama berlakunya
        perjanjian Ini:
    </p>
    <p>
        1. Data pribadi KTP (Kartu Tanda Penduduk) yang saya serahkan kepada
        Pemberi Pinjaman adalah benar
    </p>
    <p>
        2. Saya sanggup untuk melakukan pembayaran kewajiban TEPAT WAKTU tiap
        bulannya sesuai nilai setoran yang sudah disepakati;
    </p>
    <p>
        Apabila saya melanggar atau pernyataan saya ini tidak sesuai dengan DATA
        dan FAKTA yang sebenarnya, maka saya bersedia menerima segala KONSEKUENSI
        yang timbul termasuk TINDAKAN HUKUM yang akan diambil oleh pihak Pemberi
        Pinjaman.
    </p>
    <p align="center" id="pasal">
        Pasal 8<strong></strong>
    </p>
    <p align="center">
        <strong>Pemantauan Pinjaman</strong>
    </p>
    <p>
        Selama PEMINJAM karena sebab apapun juga masih mempunyai pinjaman kepada
        Kospin SEKARTAMA, maka :<strong></strong>
    </p>
    <p>
        1. Peminjam bersedia untuk melakukan pembayaran angsuran sebagaimana
        dimaksud pada Pasal 3 Ayat 1 melalui Pihak RTmart.<strong></strong>
    </p>
    <p>
        2. Peminjam mengizinkan wakil-wakil Pemberi Pinjaman yang sah untuk
        memeriksa keadaan usaha Peminjam.<strong></strong>
    </p>
    <p>
        3. Peminjam akan menyampaikan kepada Pemberi Pinjaman informasi yang
        sebenar-benarnya dan selengkap-lengkapnya tentang kondisi usaha dan atau
        perubahan - perubahan yang ada.<strong></strong>
    </p>
    <p>
        <strong></strong>
    </p>
    <p align="center" id="pasal">
        Pasal 9<strong></strong>
    </p>
    <p align="center">
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
        2. Peminjam melanggar dan/atau tidak melaksanakan kewajiban yang
        disyaratkan dalam perjanjian ini.
    </p>
    <p>
        Maka Para Pihak sepakat menyatakan Peminjam dalam keadaan ingkar janji.
    </p>
    <p align="center" id="pasal">
        Pasal 10
    </p>
    <p align="center">
        <strong>Domisili dan Pemberitahuan</strong>
    </p>
    <p>
        Para pihak dengan ini menyatakan bahwa :<strong></strong>
    </p>
    <p>
        1. Alamat Pemberi Pinjaman dan peminjam sebagaimana tercantum pada awal
        Perjanjian Pinjaman ini merupakan alamat tetap bagi masing-masing pihak,
        dan secara sah dipergunakan untuk segala surat menyurat atau komunikasi
        diantara para pihak.
    </p>
    <p>
        2. Apabila ada perubahan alamat, maka para pihak wajib memberitahukan
        secara tertulis alamat barunya kepada pihak lainnya paling lambat 7 (tujuh)
        hari sejak terjadinya perubahan alamat.
    </p>
    <p>
        3. Selama tidak terdapat pemberitahuan tentang perubahan alamat sebagaimana
        dimaksud pada ayat 2 pasal ini, maka untuk surat menyurat atau komunikasi
        yang dilakukan ke alamat yang tercantum pada awal Perjanjian Pinjaman
        dianggap sah menurut hukum.
    </p><br>
    <p align="center" id="pasal">
        Pasal 11
    </p>
    <p align="center">
        <strong>Domisili dan Hukum Yang Berlaku</strong>
    </p>
    <p>
        Dalam hal terjadi perbedaan pendapat dalam memahami atau menafsirkan
        bagian-bagian dari isi perjanjian atau terjadi perselisihan dalam
        melaksanakan perjanjian ini, maka para PIHAK sepakat untuk menyelesaikan
        secara musyawarah dan mufakat.
    </p>
    <p>
        Apabila penyelesaian secara musyawarah dan mufakat tidak dapat
        menyelesaikan permasalahan, maka para PIHAK sepakat untuk memilih domisili
        hukum di kantor Panitera Pengadilan Negeri Kendal.
    </p>
    <p align="center" id="pasal">
        Pasal 12
    </p>
    <p align="center">
        <strong>Lain-lain</strong>
    </p>
    <p>
        1. Perjanjian pinjaman ini merupakan perjanjian yang tidak dapat dipisahkan
        dengan perjanjian yang dibuat dikemudian hari oleh Para Pihak baik berupa
        penambahan, perpanjangan maupun perubahan-perubahan lainnya dan yang dibuat
        tersendiri dari perjanjian pinjaman ini adalah merupakan bagian terpenting
        dan tidak dapat dipisah-pisahkan dari perjanjian ini yang tidak akan dibuat
        tanpa adanya kuasa-kuasa tersebut. Dan karenanya kuasa-kuasa tersebut dan
        kuasa yang ada dalam surat perjanjian ini tidak dapat dicabut kembali dan
        tidak akan berakhir karena sebab-sebab apapun juga, selama perjanjian ini
        berlangsung dan selama Peminjam belum melunasi seluruh pinjamannya kepada
        Pemberi Pinjaman;
    </p>
    <p>
        2. Peminjam telah membaca dan memahami seluruh ketentuan yang ada dalam
        Perjanjian Pinjaman dan Syarat dan Ketentuan Umum Pemberian Fasilitas
        Pinjaman serta Peminjam memperoleh informasi yang jelas dan benar tentang
        Fasilitas pinjaman yang diberikan oleh Pemberi Pinjaman kepada Peminjam;
    </p>
    <p>
        3. Perjanjian pinjaman ini telah disesuaikan dengan Ketentuan Perundangan-
        undangan;
    </p>
    <p>
        4. RTmart berhak mengatur dan mengubah bentuk skema pendistribusian
        pinjaman dengan terlebih dahulu menginformasikan aturan dan perubahannya ke
        penerima pinjaman.
    </p>
    <p>
        Demikian akte perjanjian membuka pinjaman dibuat dan ditandatangani di
        WELERI pada hari {{ $dayName }} tanggal {{ $date }}, yang bermateraikan cukup
        serta mempunyai kekuatan hukum yang sama untuk masing-masing pihak.
    </p>
    <table border="0" cellspacing="0" cellpadding="0">
        <tbody>
            <tr>
                <td width="301" valign="top">
                    <p align="center">
                        Kospin SEKARTAMA,
                    </p>
                </td>
                <td width="301" valign="top">
                    <p align="center">
                        Peminjam,
                    </p>
                </td>
            </tr>
            <tr>
                <td width="301" valign="top">
                    <p align="center">
                        <strong>IBNU DODY PRAYITNO, SH</strong>
                    </p>
                </td>
                <td width="301" valign="top">
                    <p align="center">
                        <strong>{{ $merchant->UsernameIDCard }}</strong>
                    </p>
                </td>
            </tr>
            <tr>
                <td width="301" valign="top">
                    <p align="center">
                        <strong></strong>
                    </p>
                </td>
                <td width="301" valign="top">
                </td>
            </tr>
            <tr>
                <td width="301" valign="top">
                    <p align="center">
                        <strong></strong>
                    </p>
                </td>
                <td width="301" valign="top">
                    <p align="center">
                        Penjamin,
                    </p>
                </td>
            </tr>
            <tr>
                <td width="301" valign="top">
                    <p align="center">
                        <strong></strong>
                    </p>
                </td>
                <td width="301" valign="top">
                    <p align="center">
                        <strong>MOMOG IRNAWAN</strong>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
    <p>
        <strong> </strong>
    </p>
    <p>
        <strong> </strong>
    </p>
    <p>
        Dokumen ini telah disetujui secara sah oleh peminjam melalui aplikasi
        RTmart Merchant
    </p>
</body>

<script>
    window.addEventListener("load", window.print());
</script>

</html>