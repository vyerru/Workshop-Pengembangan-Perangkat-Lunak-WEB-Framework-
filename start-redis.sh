#!/bin/bash
/tmp/redis-7.2.4/src/redis-server --daemonize yes --port 6379
echo "Redis started on port 6379"
/tmp/redis-7.2.4/src/redis-cli -p 6379 ping
