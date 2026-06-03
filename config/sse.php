<?php

return [
    'query_interval' => env('SSE_QUERY_INTERVAL', 500000),
    'heartbeat_ticks' => 10,
    'max_execution' => 3600,
];
