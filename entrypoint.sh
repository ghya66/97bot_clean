#!/usr/bin/env bash
set -e
update_env(){ k="$1"; v="${!1}"; [ -n "$v" ] && sed -i "s|^\s*${k}\s*=.*|${k} = ${v}|" .env || true; }
update_env DB_HOST
update_env DB_PORT
update_env DB_NAME
update_env DB_USER
update_env DB_PASSWORD
update_env api_id
update_env api_hash
update_env TRONSCAN_APIKEY
update_env tronGrid_APIkey
update_env okip

redis-server --save "" --appendonly no &
exec /app/97bot start
