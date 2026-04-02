<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class pdfController extends Controller
{
    public function cetakSertifikat() 
    {
        $data = ['nama' => 'AING', 'event' => 'INI KOMPETISI'];
        $pdf = Pdf::loadView('pdf.sertifikat', $data)
                  ->setPaper('a4', 'landscape');

        return $pdf->stream('sertifikat.pdf');
    }

    public function cetakUndangan()
    {
        $pdf = Pdf::loadView('pdf.undangan')
                  ->setPaper('a4', 'portrait'); 
        
        return $pdf->stream('undangan.pdf');
    } 
}
