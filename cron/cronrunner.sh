#!/bin/bash
# Yes, I know this should parse throuh the file to get the right dir,
# but this feature is supposed to have been working for the last month,
# so I'm just hacking this together before anyone notices.
cd /var/www/iodine/
php /var/www/iodine/cron/signup_alert.php
