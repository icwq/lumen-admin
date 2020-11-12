# Lumen Framework Template
 
#### 项目介绍
lumen-admin 是一个lumen 后台开发模板，使用 lumen7.0 编写而成。项目只有后台基本功能。

#### 软件架构
软件架构说明

#### 环境要求
- PHP 7.2.5+
- Mysql 5.7+
- Redis 3.0+

#### 功能模块
- 用户认证 —— 注册、登录、退出；
- RBAC权限管理；
- 管理员操作日志；

#### 安装流程
克隆源代码
```bash
> git clone git@github.com:icwq/lumen-admin.git
```

安装 Composer 依赖包
```bash
> cd lumen-admin/lumen-admin
> composer install
```

赋予storage目录权限
```bash
> chmod -R 755 storage
```

拷贝 .env 文件
```bash
> cp .env.example .env
```

数据库迁移
```bash
> php artisan migrate 
```

添加数据
```bash
> php artisan db:seed
```
