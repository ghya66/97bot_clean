# syntax=docker/dockerfile:1

FROM debian:12-slim

ENV DEBIAN_FRONTEND=noninteractive

# 安装 97bot 需要的系统依赖 + PHP + Redis
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        tzdata \
        curl \
        bash \
        procps \
        redis-server \
        php-cli \
        php-mbstring \
        php-xml \
        php-gd \
        php-curl \
        php-zip \
        php-bcmath \
        php-mysql \
        php-redis \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY . /app

RUN chmod +x /app/entrypoint.sh \
    && if [ -f /app/97bot ]; then chmod +x /app/97bot; fi

# 时区随便，你之前是 Asia/Shanghai 就保留
ENV TZ=Asia/Shanghai

# 对外声明一下：HTTP 实际监听 8686（虽然 Zeabur 看的是你在面板填的）
EXPOSE 8686

CMD ["/app/entrypoint.sh"]
