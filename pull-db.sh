# Este descarla la base de datod de RDS AWS a Local Docker Mysql
# Script de permisos: chmod +x pull-db.sh
# Comando para ejecutarlo ./pull-db.sh
#!/bin/bash
echo "🗄️ Creando backup en EC2..."
aws ssm send-command \
--instance-ids "i-0cc0112a85d1995cd" \
--document-name "AWS-RunShellScript" \
--comment "Dump RDS to S3" \
--parameters commands="cd /tmp && mysqldump -h cldcistaging.c4rie2uost3w.us-east-1.rds.amazonaws.com -u cldciUser -p'2192Daa6251981*.*' --single-transaction --routines --triggers --events --set-gtid-purged=OFF cldciStaging > cldci_staging_backup.sql && gzip -f cldci_staging_backup.sql && aws s3 cp cldci_staging_backup.sql.gz s3://elasticbeanstalk-us-east-1-634018648496/backups/"

echo "📥 Descargando backup desde S3..."
aws s3 cp s3://elasticbeanstalk-us-east-1-634018648496/backups/cldci_staging_backup.sql.gz ./
gunzip -f cldci_staging_backup.sql.gz

echo "💾 Restaurando en MySQL local..."
docker exec -i cldc_mysql mysql -u root -proot_password cldc_database < cldci_staging_backup.sql

echo "✅ Base staging restaurada en local"
