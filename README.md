# Сайт

## Требования

Должен быть установлен `make`, `docker` и `docker-compose`. Примеры успешной установки:

```bash
$ docker-compose --version
Docker Compose version v2.39.2-desktop.1

$ docker --version
Docker version 28.4.0, build d8eb465

$ website % make -v
GNU Make 3.81
```

## Запуск

```bash
make init-site
```

### После запуска

- Сайт - http://localhost:9900/ (admin@example.com : 12345 и ivan@example.com : 12345)
- Почта - http://localhost:9901/