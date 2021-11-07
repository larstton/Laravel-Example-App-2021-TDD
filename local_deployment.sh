#!/bin/bash
set -e

#
# This script is executed on the destination host after the build is finished
# The Bitbucket Pipeline executes the script over SSH using the default www-data user
#
test -e /tmp/$BUILD
test -e /var/www/.core_v3 && rm -rf /var/www/.core_v3
test -e /var/www/core_v3 || mkdir /var/www/core_v3
mkdir /var/www/.core_v3
tar xzf /tmp/$BUILD -C /var/www/.core_v3/

cd /var/www/.core_v3

echo "Saving previous commit SHA from the current version .commit_hash dotfile, if it exists..."
FILE=/var/www/core_v3/.commit_hash
if [ -f "$FILE" ]; then
  yes | cp -f $FILE .commit_hash_previous
  echo "...saved."
fi

# Activate the new build
mv /var/www/core_v3 /var/www/_core_v3
mv /var/www/.core_v3 /var/www/core_v3

# Clean up
rm -rf /var/www/_core_v3
rm -f /tmp/$BUILD

cd /var/www/core_v3

# Clear Complied/Routes/Cache/Config/Event/View
php artisan cache:clear
php artisan clear-compiled --no-interaction
php artisan optimize:clear --no-interaction

# Migrate DB
php artisan migrate --no-interaction --force

# Cache Routes/Events/Config
php artisan route:cache --no-interaction
php artisan event:cache --no-interaction
php artisan config:cache --no-interaction

# Will reload via the systemd config.
sudo -n cp corev3-queue-worker@.service /etc/systemd/system/
sudo -n systemctl daemon-reload
# Start service if not running
sudo -n systemctl status corev3-queue-worker@1.service||sudo -n systemctl start corev3-queue-worker@1.service
sudo -n systemctl status corev3-queue-worker@2.service||sudo -n systemctl start corev3-queue-worker@2.service
# Enable the autostart of services on boot
sudo -n systemctl enable corev3-queue-worker@1.service
sudo -n systemctl enable corev3-queue-worker@2.service

php artisan queue:restart
php artisan storage:link
php artisan cloudradar:sentry:create-release

# Create an empty log file.
# The monitoring throws errors if the file doesn't exist
source .env
test -e storage/logs/${LOG_FILE}||touch storage/logs/${LOG_FILE}
