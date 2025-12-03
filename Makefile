
build-stack:
	docker build -t db-prod:latest ./database
	docker build -t db-test:latest ./database
	docker build -t db-prod-backup:latest ./database/backup
	docker build -t nginx-prod:latest ./nginx
	docker build -t php-prod:latest ./php
	docker build -t php-test:latest ./php

deploy-stack:
	docker stack deploy -c docker-compose.yml website

remove-stack:
	docker stack rm website

# build-site:
# 	docker compose -f docker/docker-compose.yml build

# up-site: \
# 	stop-site \
# 	docker-up-site

# stop-site:
# 	docker compose -f docker/docker-compose.yml stop

# docker-up-site:
# 	docker compose -f docker/docker-compose.yml up -d --build --remove-orphans

# down-site:
# 	docker compose down -f docker/docker-compose.yml --volumes --remove-orphans --rmi local

# create-setting-site:
# 	docker compose -f docker/docker-compose.yml cp -a docker/environment/dev/php/settings.php php:/var/www/html/settings.php
# 	docker compose -f docker/docker-compose.yml run --rm php mkdir logs

# mysql-import-site:
# 	docker compose -f docker/docker-compose.yml exec mysql mysql -uroot -proot -e "DROP DATABASE IF EXISTS mmb;"
# 	docker compose -f docker/docker-compose.yml exec mysql mysql -uroot -proot -e "CREATE DATABASE mmb;"
# 	docker compose -f docker/docker-compose.yml exec -e MYSQL_PWD=mmb mysql sh -c 'exec pv /sql/mmb_structure.sql | mysql -u mmb mmb'
# 	docker compose -f docker/docker-compose.yml exec -e MYSQL_PWD=mmb mysql sh -c 'exec pv /tmp/Fixture.sql | mysql -u mmb mmb'

# init-site: \
# 	build-site \
# 	docker-up-site \
# 	create-setting-site \
# 	mysql-import-site