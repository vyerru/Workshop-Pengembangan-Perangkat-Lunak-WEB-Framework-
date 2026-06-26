<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class GenerateTtsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $textCall,
        public string $textAlone,
    ) {}

    public function handle(): void
    {
        foreach ([$this->textCall, $this->textAlone] as $text) {
            $hash = md5($text);
            $path = 'tts/' . $hash . '.mp3';

            if (Storage::disk('public')->exists($path)) {
                continue;
            }

            $escaped = escapeshellarg($text);
            $output = @shell_exec("gtts-cli {$escaped} --lang id --output - 2>/dev/null");

            if ($output !== null && strlen($output) >= 100) {
                Storage::disk('public')->put($path, $output);
            }
        }
    }
}
