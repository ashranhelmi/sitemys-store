#!/bin/sh
# Builds database/demo-template.sqlite (gitignored) from the normal seeders: fictional products,
# orders and the demo admin. Every demo visitor starts from a private copy of this file.
# Never build it from real customer data.
set -e
cd "$(dirname "$0")/.."
T="$PWD/database/demo-template.sqlite"
rm -f "$T.tmp"
touch "$T.tmp"
DB_CONNECTION=sqlite DB_DATABASE="$T.tmp" DEMO_MODE=false APP_ENV=local php artisan migrate:fresh --seed --force --no-interaction
chmod 0644 "$T.tmp"
mv "$T.tmp" "$T"
echo "Template ready: $T"
