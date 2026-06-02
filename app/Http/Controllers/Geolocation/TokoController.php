<?php

namespace App\Http\Controllers\Geolocation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Geolocation\TokoStoreRequest;
use App\Models\Toko;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Picqer\Barcode\BarcodeGeneratorPNG;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TokoController extends Controller
{
    public function index(): View
    {
        $tokos = Toko::with('createdBy')->orderByDesc('created_at')->get();

        return view('geolocation.toko.index', compact('tokos'));
    }

    public function create(): View
    {
        return view('geolocation.toko.create');
    }

    public function store(TokoStoreRequest $request): RedirectResponse
    {
        Toko::create(array_merge($request->validated(), [
            'created_by' => auth()->id(),
        ]));

        return redirect()->route('geolocation.toko.index')
            ->with('success', 'Toko berhasil ditambahkan.');
    }

    public function edit(Toko $toko): View
    {
        return view('geolocation.toko.edit', compact('toko'));
    }

    public function update(TokoStoreRequest $request, Toko $toko): RedirectResponse
    {
        $toko->update($request->validated());

        return redirect()->route('geolocation.toko.index')
            ->with('success', 'Toko berhasil diperbarui.');
    }

    public function destroy(Toko $toko): RedirectResponse
    {
        $toko->delete();

        return redirect()->route('geolocation.toko.index')
            ->with('success', 'Toko berhasil dihapus.');
    }

    public function cetakBarcode(Toko $toko)
    {
        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($toko->barcode_token, $generator::TYPE_CODE_128);

        return response($barcode, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'inline; filename="barcode-toko-' . $toko->id . '.png"',
        ]);
    }

    public function cetakQrCode(Toko $toko)
    {
        $svg = QrCode::size(300)->generate($toko->barcode_token);
        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
        ]);
    }
}
