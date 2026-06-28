<?php

namespace App\Http\Controllers\Canteen;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QueueStreamController extends Controller
{
    public function stream($vendorId)
    {
        $maxConnections = 3;
        $slotTtl = 15;
        $slotKey = 'sse:slot:' . $vendorId;

        for ($i = 1; $i <= $maxConnections; $i++) {
            $tryKey = $slotKey . ':' . $i;
            if (Cache::add($tryKey, time(), $slotTtl)) {
                $slotKey = $tryKey;
                break;
            }
            $slotKey = null;
        }

        if (!$slotKey) {
            return response()->json([
                'error' => 'Terlalu banyak koneksi aktif. Maksimal ' . $maxConnections . ' koneksi per vendor.',
            ], 429);
        }

        $response = new StreamedResponse(function () use ($vendorId, $slotKey, $slotTtl) {
            ini_set('output_buffering', 'off');
            ini_set('zlib.output_compression', false);
            ob_implicit_flush(true);

            set_time_limit(config('sse.max_execution'));

            while (ob_get_level()) {
                ob_end_clean();
            }

            $lastHash = null;
            $lastItems = [];
            $tick = 0;

            try {
                while (true) {
                    if (connection_aborted()) {
                        break;
                    }

                    Cache::set($slotKey, time(), $slotTtl);

                    $antrianJson = $this->fetchQueue($vendorId);
                    $currentHash = md5($antrianJson);

                    if ($currentHash !== $lastHash) {
                        $lastHash = $currentHash;
                        $currentItems = json_decode($antrianJson, true) ?: [];

                        if (empty($lastItems)) {
                            echo "event: queue-full\n";
                            echo "data: " . $antrianJson . "\n\n";
                        } else {
                            $currentIds = array_column($currentItems, 'id');
                            $lastIds = array_column($lastItems, 'id');

                            $removedIds = array_values(array_diff($lastIds, $currentIds));
                            $added = array_values(array_udiff($currentItems, $lastItems, function ($a, $b) {
                                return $a['id'] - $b['id'];
                            }));

                            if (!empty($removedIds)) {
                                echo "event: queue-remove\n";
                                echo "data: " . json_encode($removedIds) . "\n\n";
                            }
                            if (!empty($added)) {
                                echo "event: queue-add\n";
                                echo "data: " . json_encode($added) . "\n\n";
                            }
                        }

                        $lastItems = $currentItems;
                    } elseif ($tick % config('sse.heartbeat_ticks') === 0) {
                        echo ": keep-alive ping\n\n";
                    }

                    if (ob_get_level()) {
                        ob_flush();
                    }
                    flush();

                    if (connection_aborted()) {
                        break;
                    }

                    $tick++;
                    sleep(1);
                }
            } finally {
                Cache::forget($slotKey);
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }

    private function fetchQueue($vendorId)
    {
        $cacheKey = 'sse:queue:' . $vendorId . ':data';

        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $antrian = Pesanan::where('vendor_id', $vendorId)
            ->where('status_bayar', 1)
            ->where('status_antrian', Pesanan::ANTRIAN_SIAP_DIPANGGIL)
            ->whereBetween('created_at', [today()->startOfDay(), today()->endOfDay()])
            ->orderBy('nomor_antrian', 'asc')
            ->get(['id', 'nomor_antrian', 'nama', 'kode_pesanan', 'status_antrian', 'updated_at']);

        $antrian->transform(function ($item) {
            $item->tts_call_url = '/tts?text=' . urlencode('Nomor antrian ' . $item->nomor_antrian . ', ' . $item->nama);
            $item->tts_alone_url = '/tts?text=' . urlencode('Nomor antrian ' . $item->nomor_antrian);
            return $item;
        });

        $json = $antrian->toJson();

        Cache::set($cacheKey, $json, 2);

        return $json;
    }
}
