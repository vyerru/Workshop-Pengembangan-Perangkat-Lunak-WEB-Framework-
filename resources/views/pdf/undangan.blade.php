<!DOCTYPE html>
<html>
<head>
    <title>Sertifikat Penghargaan</title>
    <style>
        @page { margin: 0px; }
        body { font-family: 'Times New Roman', Times, serif; text-align: center; margin: 0; padding: 40px; }
        
        /* Bingkai Sertifikat */
        .border-outer { border: 10px solid #2c3e50; padding: 10px; height: 90%; }
        .border-inner { border: 2px solid #2c3e50; padding: 40px; height: 90%; position: relative; }
        
        .header { font-size: 40pt; font-weight: bold; color: #2c3e50; margin-top: 20px; letter-spacing: 5px; text-transform: uppercase; }
        .sub-header { font-size: 16pt; margin-top: 30px; color: #555; }
        .name { font-size: 35pt; font-weight: bold; font-style: italic; margin-top: 20px; border-bottom: 2px solid #000; display: inline-block; padding: 0 50px; }
        .description { font-size: 16pt; margin-top: 30px; line-height: 1.5; }
        
        /* Layout Tanda Tangan */
        .signature-section { width: 100%; margin-top: 80px; }
        .signature-left { width: 40%; float: left; text-align: center; }
        .signature-right { width: 40%; float: right; text-align: center; }
        .sign-name { font-weight: bold; text-decoration: underline; margin-top: 70px; margin-bottom: 0; }
    </style>
</head>
<body>
    <div class="border-outer">
        <div class="border-inner">
            <div class="header">SERTIFIKAT</div>
            <div class="sub-header">Diberikan Sebagai Bukti Penghargaan Kepada:</div>
            
            <div class="name">Nama Peserta Disini</div>
            
            <div class="description">
                Atas partisipasi dan dedikasinya sebagai <b>Peserta Aktif</b> dalam acara <br>
                "Pelatihan dan Uji Kompetensi Pemrograman Backend Terapan" <br>
                yang diselenggarakan secara resmi pada tanggal {{ date('d F Y') }}.
            </div>
            
            <div class="signature-section">
                <div class="signature-left">
                    <p>Ketua Panitia</p>
                    <p class="sign-name">Nama Ketua Panitia</p>
                    <p style="margin-top: 0;">NIDN. 0011223344</p>
                </div>
                <div class="signature-right">
                    <p>Direktur / Dekan</p>
                    <p class="sign-name">Dr. Nama Pimpinan, M.Kom.</p>
                    <p style="margin-top: 0;">NIP. 197001011995121001</p>
                </div>
            </div>
            <div style="clear: both;"></div> 
        </div>
    </div>
</body>
</html>