#!/bin/bash

MYSQL_PD_USER='**!**'
MYSQL_PD_PW="**!**"
MYSQL_PD_DBNAME='**!**'

BACKUPDIR=./backups
BACKUP=$BACKUPDIR/db1.txt

backup_database() {
    printf "$(date) : $$   backing up database '$1'\n"
    mysqldump --extended-insert=false -u"$MYSQL_PD_USER" "$MYSQL_PD_DBNAME" -p"$MYSQL_PD_PW" > $BACKUP.tmp
    mv $BACKUP.tmp $BACKUP
    cd $BACKUPDIR
}

backup_database "PD*"