<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papan Antrian Digital</title>
    @include('partials.header')
    <style>
        body {
            background: #f8f9fb;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        .header h1 {
            font-size: 2.8rem;
            font-weight: 700;
            color: #b66dff;
        }
        .header p {
            font-size: 1.1rem;
            color: #6c757d;
            margin-top: 8px;
        }
        #current-call {
            border-radius: 24px;
            padding: 50px 80px;
            text-align: center;
            margin-bottom: 40px;
            min-width: 500px;
            transition: all 0.3s ease;
        }
        #current-call .label {
            font-size: 1.2rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: 600;
        }
        #current-call .number {
            font-size: 8rem;
            font-weight: 900;
            line-height: 1;
            margin: 10px 0;
            color: #b66dff;
        }
        #current-call .name {
            font-size: 2rem;
            font-weight: 600;
            color: #212529;
            margin-top: 10px;
        }
        #current-call .empty-text {
            font-size: 1.5rem;
            color: #adb5bd;
            padding: 40px 0;
        }
        #queue-list {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: center;
            max-width: 1000px;
        }
        .queue-item {
            min-width: 120px;
            animation: fadeIn 0.4s ease-out;
        }
        .queue-item .no {
            font-size: 2rem;
            font-weight: 700;
            color: #b66dff;
        }
        .queue-item .nm {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 4px;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        #start-screen {
            position: fixed;
            inset: 0;
            background: rgba(255,255,255,0.95);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 999;
        }
        #start-screen h2 {
            font-size: 2.5rem;
            color: #b66dff;
            margin-bottom: 20px;
        }
        #start-screen p {
            color: #6c757d;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
        #start-screen button {
            padding: 16px 48px;
            font-size: 1.3rem;
            border: none;
            border-radius: 50px;
            background: #b66dff;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s;
        }
        #start-screen button:hover {
            transform: scale(1.05);
            background: #9a4fd8;
        }
        #controls {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }
        #controls button {
            padding: 8px 20px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background: #fff;
            color: #495057;
            cursor: pointer;
            font-size: 0.85rem;
        }
        #controls button:hover {
            background: #f8f9fa;
        }
        #connection-status {
            position: fixed;
            bottom: 20px;
            left: 20px;
            padding: 6px 14px;
            border-radius: 12px;
            font-size: 0.8rem;
            background: #fff;
            border: 1px solid #dee2e6;
            color: #6c757d;
        }
        #connection-status.connected { color: #28a745; border-color: #28a745; }
        #connection-status.disconnected { color: #dc3545; border-color: #dc3545; }
    </style>
</head>
<body>
    <div id="start-screen">
        <h2>Papan Antrian Digital</h2>
        <p>Klik tombol di bawah untuk memulai menampilkan panggilan antrian.</p>
        <button onclick="startDisplay()">Mulai Papan Antrian</button>
    </div>

    <div class="header text-center">
        <h1>Panggilan Antrian</h1>
        <p id="vendor-label">Memuat...</p>
    </div>

    <div id="current-call" class="card shadow-sm">
        <div class="label">Sedang Dipanggil</div>
        <div class="empty-text">Belum ada panggilan</div>
    </div>

    <div id="queue-list"></div>

    <div id="connection-status">Menghubungkan...</div>

    <div id="controls">
        <button onclick="toggleFullscreen()">Layar Penuh</button>
    </div>

    <script>
        let lastCalledId = null;
        let evtSource = null;
        let audioCtx = null;
        let beepBuffer = null;

        function playBeep() {
            if (!audioCtx || !beepBuffer) return;
            var source = audioCtx.createBufferSource();
            source.buffer = beepBuffer;
            source.connect(audioCtx.destination);
            source.start(0);
        }

        function playAudio(data) {
            return new Promise(function (resolve) {
                if (!audioCtx || !data) {
                    resolve();
                    return;
                }
                try {
                    var binary = atob(data);
                    var bytes = new Uint8Array(binary.length);
                    for (var i = 0; i < binary.length; i++) {
                        bytes[i] = binary.charCodeAt(i);
                    }
                    audioCtx.decodeAudioData(bytes.buffer, function (audioBuffer) {
                        var source = audioCtx.createBufferSource();
                        source.buffer = audioBuffer;
                        source.connect(audioCtx.destination);
                        source.onended = resolve;
                        source.start(0);
                    }, function () { resolve(); });
                } catch (e) {
                    console.error('TTS error:', e);
                    resolve();
                }
            });
        }

        function startDisplay() {
            document.getElementById('start-screen').style.display = 'none';
            document.getElementById('vendor-label').textContent = 'Vendor #{{ $vendorId ?? '?' }}';

            audioCtx = new (window.AudioContext || window.webkitAudioContext)();

            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }

            fetch('{{ asset("assets/audio/BEEP_Beep of a cash register (ID 1417)_BigSoundBank.com.mp3") }}')
                .then(function (r) { return r.arrayBuffer(); })
                .then(function (buf) { return audioCtx.decodeAudioData(buf); })
                .then(function (buf) { beepBuffer = buf; })
                .catch(function () {});

            evtSource = new EventSource('/sse/queue/{{ $vendorId }}');

            evtSource.addEventListener('queue-update', function(e) {
                var data = JSON.parse(e.data);

                if (data.length > 0) {
                    var latest = data[data.length - 1];

                    if (latest.id !== lastCalledId) {
                        lastCalledId = latest.id;
                        playCall(latest);
                    }

                    renderQueueList(data);
                } else {
                    var container = document.querySelector('#current-call');
                    container.innerHTML = [
                        '<div class="label">Sedang Dipanggil</div>',
                        '<div class="empty-text">Belum ada panggilan</div>',
                    ].join('');
                    document.getElementById('queue-list').innerHTML = '';
                }
            });

            evtSource.onopen = function() {
                var el = document.getElementById('connection-status');
                el.textContent = 'Terhubung';
                el.className = 'connected';
            };

            evtSource.onerror = function() {
                var el = document.getElementById('connection-status');
                el.textContent = 'Terputus — Mencoba ulang...';
                el.className = 'disconnected';
            };
        }

        function playCall(item) {
            var container = document.querySelector('#current-call');
            container.innerHTML = [
                '<div class="label">Sedang Dipanggil</div>',
                '<div class="number">' + item.nomor_antrian + '</div>',
                '<div class="name">' + item.nama + '</div>',
            ].join('');

            playBeep();

            playAudio(item.tts_call_data).then(function () {
                return playAudio(item.tts_alone_data);
            });
        }

        function renderQueueList(data) {
            var list = document.getElementById('queue-list');
            list.innerHTML = data.map(function (item) {
                return [
                    '<div class="queue-item card shadow-sm py-3 px-4">',
                    '<div class="no">' + item.nomor_antrian + '</div>',
                    '<div class="nm">' + item.nama + '</div>',
                    '</div>',
                ].join('');
            }).join('');
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(function () {});
            } else {
                document.exitFullscreen().catch(function () {});
            }
        }
    </script>
</body>
</html>
