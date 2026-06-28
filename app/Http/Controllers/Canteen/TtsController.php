<?php

namespace App\Http\Controllers\Canteen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TtsController extends Controller
{
    public function proxy(Request $request)
    {
        $text = $request->query('text');
        if (!$text) {
            return response('text parameter required', 400);
        }

        $hash = md5($text);
        $cachePath = 'tts/' . $hash . '.mp3';

        if (!Storage::exists($cachePath)) {
            $mp3 = $this->fetchFromGoogle($text);

            if (!$mp3) {
                $mp3 = $this->fetchFromGttsCli($text);
            }

            if (!$mp3) {
                Log::warning('TTS unavailable for text: ' . $text);
                return response('', 204);
            }

            Storage::put($cachePath, $mp3);
        }

        $mp3 = Storage::get($cachePath);

        return response($mp3, 200, [
            'Content-Type' => 'audio/mpeg',
            'Cache-Control' => 'public, max-age=31536000',
            'Content-Length' => strlen($mp3),
        ]);
    }

    private function fetchFromGttsCli(string $text): ?string
    {
        $escaped = escapeshellarg($text);
        $output = @shell_exec("/home/vier/.local/bin/gtts-cli {$escaped} --lang id --output - 2>/dev/null");

        if ($output === null || strlen($output) < 100) {
            return null;
        }

        return $output;
    }

    private function fetchFromGoogle(string $text): ?string
    {
        $url = 'https://translate.google.com/translate_tts?ie=UTF-8&q='
            . urlencode($text)
            . '&tl=id&client=tw-ob';

        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n",
                'timeout' => 5,
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $mp3 = @file_get_contents($url, false, $context);

        if ($mp3 === false || strlen($mp3) < 100) {
            return null;
        }

        return $mp3;
    }
}
