#!/bin/bash

FLASK_ENDPOINT="https://udara.unis.ac.id/fiver/initiate_monitoring"

while true; do
    curl -X POST -H "Content-Type: application/json" -d "{}" --silent -o /dev/null "$FLASK_ENDPOINT"
    echo "Monitoring triggered at $(date)" >> /var/log/monitor_trigger_per_second.log
    sleep 1 # Tunggu 1 detik
done