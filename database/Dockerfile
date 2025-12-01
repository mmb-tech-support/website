FROM mariadb:12.1

RUN apt-get update \
    && apt install -y pv \
    && apt-get autoremove -y \
    && apt-get autoclean \
    && rm -r /var/lib/apt/lists/*

COPY db-init/ /docker-entrypoint-initdb.d/
COPY db-run.sh /usr/local/bin/

EXPOSE 3306
ENTRYPOINT ["db-run.sh"]