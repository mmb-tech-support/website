# Настройка хоста

Ниже приведён набор шагов для первоначальной настройки хоста (Debian/Ubuntu). Выполняйте команды с правами `sudo`.

## 0. Обновление системы

```sh
sudo apt update
sudo apt upgrade
```

## 1. Установка имени машины

Отредактируйте файл с именем хоста:

```sh
sudo vi /etc/hostname
```

Запишите в файл нужное имя и перезагрузите систему или примените команду `hostnamectl set-hostname <имя>`.

## 2. Установка часового пояса

```sh
sudo timedatectl set-timezone Europe/Moscow
```

## 3. Настройка SSH

Отредактируйте конфигурацию SSH-сервера:

```sh
sudo vi /etc/ssh/sshd_config
```

После изменений перезапустите службу:

```sh
sudo systemctl restart sshd
```

## 4. Отключение IPv6

Временно отключить IPv6 можно так:

```sh
sudo sysctl net.ipv6.conf.all.disable_ipv6=1
```

Для постоянного отключения создайте/отредактируйте файл конфигурации sysctl:

```sh
sudo vi /etc/sysctl.d/70-ipv6.conf
# Добавьте строку:
net.ipv6.conf.all.disable_ipv6=1

sudo sysctl --system
```

## 5. Настройка firewalld

Установите `firewalld` и выполните базовую конфигурацию зон и сервисов:

```sh
sudo apt-get install -y firewalld
sudo firewall-cmd --set-default-zone=public
sudo firewall-cmd --permanent --add-service=ssh
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --permanent --set-target=DROP
sudo firewall-cmd --permanent --remove-service=mdns
sudo firewall-cmd --permanent --remove-service=dhcpv6-client
sudo firewall-cmd --permanent --add-icmp-block=redirect
sudo firewall-cmd --permanent --add-icmp-block=router-advertisement
sudo firewall-cmd --permanent --add-rich-rule='rule family="ipv4" service name="ssh" limit value="3/m" accept'
sudo systemctl enable --now firewalld
sudo firewall-cmd --reload
```

Примечание: Генерация DH-параметров для `nginx` (4096 бит) — опционально, если вы готовите TLS:

```sh
sudo openssl dhparam -out /etc/ssl/certs/dhparam.pem 4096
```

## 6. Установка и настройка Docker / Docker Swarm

Ниже — примерный набор команд и конфигураций для установки Docker, включения Swarm и создания сетей/volumes для стека.

### Установка Docker

```sh
sudo apt-get install -y docker.io
```

### Настройка `daemon.json`

Отредактируйте `/etc/docker/daemon.json` и добавьте (пример):

```json
{
  "ipv6": false,
  "userns-remap": "default",
  "log-driver": "json-file",
  "log-opts": {
    "max-size": "10m",
    "max-file": "10"
  },
  "debug": false
}
```

Перезапустите Docker:

```sh
sudo systemctl restart docker
```

### Инициализация Docker Swarm и сети

```sh
docker swarm init

docker network create -d overlay --attachable ...
```

### Сборка и деплой стека

```sh
docker stack deploy -c docker-compose.yml myapp

# force update/backup servicedocker service update --force database_prod_backup
```

### Примеры volume и secret для бэкапов

```sh
docker volume create \
  --driver local \
  --opt type=none \
  --opt o=bind \
  --opt device=<Absolute path to backups directory> \
  db_backups

echo "mmb" | docker secret create db_name -
```

## 7. Настройка cron для автоматических backup и обновления конейтейнера.

Для автоматического создания backup базы данных настройте cron-задания: полный backup 1 раз в неделю (например, каждое воскресенье в 2:00), инкрементальный backup 1 раз в час.

Отредактируйте crontab для root (или соответствующего пользователя):

```sh
sudo crontab -e
```

Добавьте следующие строки:

```bash
# Полный backup каждое воскресенье в 2:00
0 2 * * 0 /srv/website/database/backup/backup.sh full

# Инкрементальный backup каждый час
0 * * * * /srv/website/database/backup/backup.sh incremental
```

Проверьте логи cron (`sudo journalctl -u cron`) на ошибки.