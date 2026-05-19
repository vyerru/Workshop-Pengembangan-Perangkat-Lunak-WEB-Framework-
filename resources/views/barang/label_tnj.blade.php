<!DOCTYPE html>
<html>
<head>
    <style>
        @page { margin: 0.5cm; } 
        body { font-family: sans-serif; margin: 0; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        
        td {
            width: 20%; 
            height: 3.6cm; 
            text-align: center;
            vertical-align: middle;
            padding: 2px;
            overflow: hidden;
        }
        .nama-barang { font-weight: bold; font-size: 11px; display: block; margin-bottom: 3px; }
        .harga-barang { font-size: 14px; font-weight: bold; color: #000; margin-bottom: 5px; display: block; }
        .barcode-container { margin: 0 auto 3px auto; }
        .id-barang { font-size: 8px; color: #555; display: block; }
    </style>
</head>
<body>
    @php
        $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
    @endphp

    <table>
        <tr>
        @foreach($dataCetak as $index => $item)
            @if($index > 0 && $index % 5 == 0)
                </tr><tr>
            @endif
            
            <td>
                @if($item)
                    <span class="nama-barang">{{ substr($item->nama, 0, 20) }}</span>
                    <span class="harga-barang">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                    
                    {{-- Render Barcode Tipe 128 (paling direkomendasikan untuk ID alfanumerik) --}}
                    <div class="barcode-container">
                        <img src="data:image/png;base64,{{ base64_encode($generator->getBarcode($item->id_barang, $generator::TYPE_CODE_128, 1, 30)) }}" alt="Barcode {{ $item->id_barang }}">
                    </div>
                    
                    <span class="id-barang">{{ $item->id_barang }}</span>
                @endif
            </td>
        @endforeach
        
        @php
            // Menyempurnakan sisa grid tabel
            $sisa = 5 - (count($dataCetak) % 5);
            if($sisa > 0 && $sisa < 5) {
                for($i=0; $i<$sisa; $i++){ echo "<td></td>"; }
            }
        @endphp
        </tr>
    </table>
</body>
</html>