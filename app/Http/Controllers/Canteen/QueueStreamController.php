<?php

namespace App\Http\Controllers\Canteen;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QueueStreamController extends Controller
{
    public function stream($vendorId)
    {
        $response = new StreamedResponse(function () use ($vendorId) {
            ini_set('output_buffering', 'off');
            ini_set('zlib.output_compression', false);
            ob_implicit_flush(true);

            set_time_limit(config('sse.max_execution'));

            while (ob_get_level()) {
                ob_end_clean();
            }

            $lastHash = null;
            $tick = 0;

            while (true) {
                if (connection_aborted()) {
                    break;
                }

                $antrian = Pesanan::where('vendor_id', $vendorId)
                    ->whereDate('created_at', today())
                    ->where('status_bayar', 1)
                    ->where('status_antrian', Pesanan::ANTRIAN_SIAP_DIPANGGIL)
                    ->orderBy('nomor_antrian', 'asc')
                    ->get(['id', 'nomor_antrian', 'nama', 'kode_pesanan', 'status_antrian', 'updated_at']);

                $antrian->transform(function ($item) {
                    $hash1 = md5('Nomor antrian ' . $item->nomor_antrian . ', ' . $item->nama);
                    $hash2 = md5('Nomor antrian ' . $item->nomor_antrian);
                    $path1 = 'tts/' . $hash1 . '.mp3';
                    $path2 = 'tts/' . $hash2 . '.mp3';
                    $item->tts_call_data = Storage::disk('public')->exists($path1) ? base64_encode(Storage::disk('public')->get($path1)) : null;
                    $item->tts_alone_data = Storage::disk('public')->exists($path2) ? base64_encode(Storage::disk('public')->get($path2)) : null;
                    return $item;
                });

                $currentHash = md5($antrian->toJson());

                if ($currentHash !== $lastHash) {
                    $lastHash = $currentHash;

                    echo "event: queue-update\n";
                    echo "data: " . $antrian->toJson() . "\n\n";
                } elseif ($tick % config('sse.heartbeat_ticks') === 0) {
                    echo ": keep-alive ping\n\n";
                }

                if (ob_get_level()) {
                    ob_flush();
                }
                flush();

                $tick++;
                usleep(config('sse.query_interval'));
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }
}
