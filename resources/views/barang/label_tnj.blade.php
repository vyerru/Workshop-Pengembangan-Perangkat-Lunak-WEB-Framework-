<!DOCTYPE html>
<html>
<head>
    <style>
        @page { margin: 12mm; }
        body { font-family: sans-serif; margin: 0; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        
        td {
            width: 20%;
            height: 34.125mm;
            text-align: center;
            vertical-align: middle;
            padding: 1px 2px;
            overflow: hidden;
            border: 0.5px dashed #999;
        }
        .barcode-container { margin: 0 auto; }
        .id-barang { font-size: 8px; color: #555; display: block; margin-top: 2px; }
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
                    <div class="barcode-container">
                        <img src="data:image/png;base64,{{ base64_encode($generator->getBarcode($item->id_barang, $generator::TYPE_CODE_128, 1, 30)) }}" alt="Barcode {{ $item->id_barang }}">
                    </div>
                    
                    <span class="id-barang">{{ $item->id_barang }}</span>
                @endif
            </td>
        @endforeach
        
        @php
            $sisa = 5 - (count($dataCetak) % 5);
            if($sisa > 0 && $sisa < 5) {
                for($i=0; $i<$sisa; $i++){ echo "<td></td>"; }
            }
        @endphp
        </tr>
    </table>
</body>
</html>
