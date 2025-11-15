#!/usr/bin/env bash
set -e

cd /app

# === 用环境变量覆盖 .env 中的关键配置（有就覆盖，没有就跳过） ===
update_env() {
  k="$1"
  v="${!1}"
  # 只有在环境变量非空时才覆盖 .env 里对应的行
  [ -n "$v" ] && sed -i "s|^\s*${k}\s*=.*|${k} = ${v}|" .env || true
}

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

# === 1）启动内置 Redis（无持久化） ===
redis-server --save "" --appendonly no &

# === 2）启动 PHP 内置 Web 服务，作为后台入口 ===
# Zeabur 会注入 PORT；没有的话默认 8080
: "${PORT:=8080}"

mkdir -p /var/log
php -S 0.0.0.0:${PORT} -t /app/bot/web > /var/log/php-server.log 2>&1 &

# === 3）最后启动 97bot 主进程（前台运行，保持容器存活） ===
exec /app/97bot start
