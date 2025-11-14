# Zeabur 部署指南

本文档说明如何把原先依赖宝塔面板的 97bot 项目迁移至 Zeabur。部署目标是在不改动业务逻辑的前提下，使用仓库根目录提供的 `Dockerfile` 完成构建，并通过环境变量配置数据库、Redis 以及 Telegram/Tron 等凭证。

## 一、准备工作

1. **基础账号**：注册并登录 [Zeabur](https://zeabur.com/) 账号，确保拥有可用的项目空间。
2. **代码仓库**：将当前 GitHub 仓库关联到自己的账号，方便在 Zeabur 中导入。
3. **了解项目结构**：
   1. 主进程是仓库根目录的 `97bot` 可执行程序，通过 `entrypoint.sh` 启动，并在启动前按需覆盖 `.env` 中的数据库、Redis 与外部服务配置。
   2. 机器人业务逻辑采用 Webman/ThinkORM 风格的 PHP 组件，位于 `bot/` 目录。例如 Web 接口控制器存放在 `bot/web/`，Redis 队列消费者在 `bot/queue/`。
   3. 项目依赖 MySQL（在 `update.data` 中提供了示例建表语句）以及 Redis（代码大量通过 `support\Redis` 访问；容器启动脚本会自动拉起一个内置 Redis 实例）。

## 二、在 Zeabur 创建服务

1. 登录 Zeabur 控制台，新建一个 Project（项目），进入后点击 **Create Service**。
2. 选择 **Git Repository**，授权访问含有 97bot 代码的 GitHub 仓库，勾选对应分支。
3. Zeabur 会检测到仓库根目录存在 `Dockerfile`，自动使用 Docker 模式构建。无需额外填写 Build Command / Start Command。
4. 首次构建时，Docker 将：
   - 基于 `debian:12-slim` 安装 PHP CLI、Redis 等运行时依赖；
   - 把代码复制到容器 `/app`，并确保 `entrypoint.sh` 与 `97bot` 具有执行权限；
   - 在容器启动时执行 `entrypoint.sh`，先根据环境变量覆盖 `.env`，再启动内置 Redis 与主程序。

## 三、环境变量与服务依赖

部署前请先在 Zeabur 控制台的 **Environment Variables** 面板逐项配置。可参考仓库根目录的 `.env.zeabur.example` 文件，该文件列出了全部关键变量及示例说明。同时注意 Zeabur 会自动注入 `PORT`，容器将通过 `Dockerfile` 中的入口脚本读取该端口，无需额外配置。

### 1. 数据库（MySQL/MariaDB）

- `DB_HOST`、`DB_PORT`、`DB_NAME`、`DB_USER`、`DB_PASSWORD`：创建 Zeabur 提供的 MySQL 附加服务后，从其详情页复制内网主机、端口、数据库名、用户名与密码，分别填入对应变量。
- 首次部署前，请在数据库中执行仓库自带的 `update.data` SQL，创建必要的数据结构。

### 2. Redis（可选）

- 默认 `entrypoint.sh` 会启动容器内 Redis。如果改用 Zeabur 托管 Redis，请在环境变量面板中填写 `REDIS_HOST`、`REDIS_PORT`、`REDIS_PASSWORD`、`REDIS_DB`，并保证 `.env` 中存在对应键值。

### 3. Telegram 凭证

- `api_id`、`api_hash`：到 [my.telegram.org](https://my.telegram.org) 申请后填入，用于让机器人接入 Telegram API。

### 4. Tron / 其他第三方服务

- `TRONSCAN_APIKEY`、`tronGrid_APIkey`、`okip`：分别对应 Tronscan、TronGrid、OKLink 的 API Key，用于启用相关链上功能。

### 5. 运行时可选项

- `TZ`：容器时区设置，如需切换到其它时区可以调整该变量。

## 四、首发部署与常见问题

1. **数据库未初始化**：构建成功后，若后台报错找不到数据表，请将 `update.data` 中的 SQL 在数据库执行一次，再重启服务。
2. **Telegram 凭证缺失**：若未设置 `api_id`/`api_hash`，机器人无法连接 Telegram API，日志会出现认证失败提示。
3. **区块链 API 限额**：Tron 相关接口需正确设置 API Key，否则对应功能会报 401/429 错误。
4. **查看日志与进入容器**：Zeabur 控制台提供实时日志、Shell 入口，可用于排查构建失败或运行异常。

## 五、生产环境建议

1. **日志与监控**：利用 Zeabur 日志界面或自定义日志转储，监控机器人运行状态。
2. **计划任务/队列**：项目依赖 Redis 队列（`bot/queue/` 模块）。`entrypoint.sh` 会在主进程中统一启动消费者，无需额外 Worker；若业务需要新增后台任务，可在 Zeabur 使用额外的 Worker 服务运行同一镜像，并通过环境变量区分模式。
3. **多环境管理**：建议在 Zeabur 中使用不同的环境变量组分别管理测试与生产配置。
4. **备份**：定期备份数据库、`.env` 配置与关键凭证，避免单点故障。

按照以上步骤完成配置后，即可在 Zeabur 上以 Docker 模式稳定运行 97bot，并保持与原宝塔部署方案一致的业务行为。
