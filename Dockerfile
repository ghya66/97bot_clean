# Dockerfile
FROM debian:11-slim

RUN apt-get update && apt-get install -y --no-install-recommends \
    redis-server ca-certificates tzdata curl bash procps \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY 97bot_240811/ /app/
RUN chmod +x /app/97bot

# 可选：通过环境变量覆盖 .env（只覆盖存在的键，没传就保持文件里原值）
# 你也可以直接提交一个已经写好的 .env（不推荐把真实密码硬编码进仓库）
RUN printf '%s\n' '#!/usr/bin/env bash' 'set -e' \
'update_env(){ k="$1"; v="${!1}"; [ -n "$v" ] && sed -i "s|^\\s*${k}\\s*=.*|'"'"'${k} = '"'"'"'"'${v}'"'"'"'"'|" .env || true; }' \
'update_env DB_HOST' \
'update_env DB_PORT' \
'update_env DB_NAME' \
'update_env DB_USER' \
'update_env DB_PASSWORD' \
'update_env api_id' \
'update_env api_hash' \
'update_env TRONSCAN_APIKEY' \
'update_env tronGrid_APIkey' \
'update_env okip' \
'' \
'# 启动内置 Redis（无持久化），再以前台模式拉起 97bot' \
'redis-server --save "" --appendonly no &' \
'exec /app/97bot start' > /app/entrypoint.sh \
&& chmod +x /app/entrypoint.sh

# 如果 97bot 会开启 Web（面板/回调），一般会监听 80/8080
# Zeabur 通常注入 PORT；若 97bot 不支持改端口，也能直接映射固定端口
ENV PORT=8080
EXPOSE 80 8080

CMD ["/app/entrypoint.sh"]
