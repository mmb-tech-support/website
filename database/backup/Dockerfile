FROM mariadb:12.1

RUN apt-get update && \
    apt-get install -y mariadb-backup && \
    rm -rf /var/lib/apt/lists/*

COPY backup.sh /usr/local/bin/backup.sh

ENTRYPOINT [ "/usr/local/bin/backup.sh" ]