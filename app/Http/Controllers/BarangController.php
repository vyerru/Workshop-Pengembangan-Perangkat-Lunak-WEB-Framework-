<?php

namespace App\Http\Controllers;

use App\Models\barang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangController extends Controller
{
    public function index(): View
    {
        $model = new barang();
        $primaryKey = $model->getKeyName();
        $inputColumns = $this->resolveInputColumns($model);
        $columns = array_values(array_unique(array_merge([$primaryKey], $model->getFillable())));

        return view('barang.index', [
            'barang' => barang::query()->orderByDesc($primaryKey)->get(),
            'columns' => $columns,
            'inputColumns' => $inputColumns,
            'primaryKey' => $primaryKey,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $model = new barang();
        $payload = $this->validatedPayload($request, $model);

        if (in_array('timestamp', $model->getFillable(), true)) {
            $payload['timestamp'] = now()->format('Y-m-d H:i:s');
        }

        barang::create($payload);

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil ditambahkan.');
    }

    public function update(Request $request, barang $barang): RedirectResponse
    {
        $payload = $this->validatedPayload($request, $barang);
        $barang->update($payload);

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(barang $barang): RedirectResponse
    {
        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil dihapus.');
    }

    private function resolveInputColumns(barang $model): array
    {
        $fillable = $model->getFillable();

        return array_values(array_filter(
            $fillable,
            fn (string $column) => ! in_array($column, ['timestamp', 'created_at', 'updated_at'], true)
        ));
    }

    private function validatedPayload(Request $request, barang $model): array
    {
        $inputColumns = $this->resolveInputColumns($model);
        $rules = [];

        foreach ($inputColumns as $column) {
            if (str_contains($column, 'harga')) {
                $rules[$column] = ['required', 'numeric'];
                continue;
            }

            if (in_array($column, ['timestamp', 'tanggal', 'waktu'], true)) {
                $rules[$column] = ['nullable', 'date'];
                continue;
            }

            $rules[$column] = ['required', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        return collect($validated)
            ->map(fn ($value) => is_string($value) ? trim($value) : $value)
            ->all();
    }

    public function cetakLabel(Request $request)
    {
        $request->validate([
            'barang_ids' => 'required|array',
            'x' => 'required|integer|min:1|max:5',
            'y' => 'required|integer|min:1|max:8',
        ]);

        $barangs = barang::whereIn('id_barang', $request->barang_ids)->get();

        $skip = (($request->y - 1) * 5) + ($request->x - 1);

        $dataCetak = [];
        
        for ($i = 0; $i < $skip; $i++) {
            $dataCetak[] = null;
        }
        
        foreach ($barangs as $b) {
            $dataCetak[] = $b;
        }

        $pdf = Pdf::loadView('barang.label_tnj', compact('dataCetak'))->setPaper('a4', 'portrait');
        
        return $pdf->stream('label_harga_tnj108.pdf');
    }
}
