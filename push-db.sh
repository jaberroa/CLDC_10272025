#!/bin/bash

# --------------------------------------------------------------
# 🚀 push-db.sh — Envía tu base local (Docker) hacia RDS via S3
# --------------------------------------------------------------

# Configura variables
DB_PASS="2192Daa6251981*.*"
EC2_INSTANCE_ID="i-0123456789abcdef0"  # 🔁 reemplaza con tu ID real de instancia
S3_BUCKET="elasticbeanstalk-us-east-1-634018648496/backups"
RDS_HOST="cldcistaging.c4rie2uost3w.us-east-1.rds.amazonaws.com"
RDS_USER="cldciUser"
RDS_DB="cldciStaging"

# Exportar base de datos local desde Docker
echo "🚀 Exportando base local..."
docker exec cldc_mysql mysqldump -u root -proot_password \
  --single-transaction --routines --triggers --events \
  --set-gtid-purged=OFF cldc_database > cldci_staging_dump.sql

# Comprimir el archivo
gzip -f cldci_staging_dump.sql

# Subir el dump a S3
echo "📤 Subiendo dump a S3..."
aws s3 cp cldci_staging_dump.sql.gz s3://$S3_BUCKET/

# Restaurar en RDS (comando remoto en EC2)
echo "📥 Restaurando en RDS desde EC2..."
aws ssm send-command \
  --instance-ids "$EC2_INSTANCE_ID" \
  --document-name "AWS-RunShellScript" \
  --comment "Import DB to RDS" \
  --parameters '{"commands":["cd /tmp && aws s3 cp s3://'"$S3_BUCKET"'/cldci_staging_dump.sql.gz ./ && gunzip -f cldci_staging_dump.sql.gz && mysql -h '"$RDS_HOST"' -u '"$RDS_USER"' -p'"$DB_PASS"' '"$RDS_DB"' < /tmp/cldci_staging_dump.sql"]}'

echo "✅ Base local subida correctamente al RDS (staging)"
