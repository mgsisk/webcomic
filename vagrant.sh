#!/bin/bash

start=$(date +%s)
host=$1
port=$2
gip4=$3
# shellcheck disable=SC2001
hip4=$(echo "$3" | sed 's/\.[0-9]*$/.1/')
svtz=$4
phpv=$5
wpvn=$6
wptz=$7

export DEBIAN_FRONTEND=noninteractive
printf 'set grub-pc/install_devices /dev/sda' | debconf-communicate &>/dev/null

# ----- System -----------------------------------------------------------------

packages=(
  'nginx'
  'mariadb-server'
  'nodejs'
  'postfix'
  'graphviz'
  'memcached'
  "php$phpv-curl"
  "php$phpv-dom"
  "php$phpv-gd"
  "php$phpv-fpm"
  "php$phpv-mbstring"
  "php$phpv-memcached"
  "php$phpv-mysql"
  "php$phpv-pgsql"
  "php$phpv-sqlite3"
  "php$phpv-xdebug"
  "php$phpv-xml"
  "php$phpv-zip"
)

if ! date | grep -q 'EDT' && ! date | grep -q 'EST'; then
  printf 'Updating timezone to %s' "$svtz"
  rm -f /etc/localtime
  cp "/usr/share/zoneinfo/$svtz" /etc/localtime
fi

if [ ! -e /usr/lib/apt/methods/https ]; then
  printf 'Installing apt-transport-https'
  aptitude -y update &>/dev/null
  aptitude -y install apt-transport-https &>/dev/null
fi

if ! grep -q 'nginx.org' /etc/apt/sources.list; then
  printf 'Updating apt sources with nginx.org'
  wget -qO- http://nginx.org/keys/nginx_signing.key | apt-key add - &>/dev/null
  tee -a /etc/apt/sources.list &>/dev/null <<SOURCE
deb http://nginx.org/packages/mainline/debian/ jessie nginx
deb-src http://nginx.org/packages/mainline/debian/ jessie nginx
SOURCE
fi

if ! grep -q '/mariadb/' /etc/apt/sources.list; then
  printf 'Updating apt sources with mirror.jaleco.com/mariadb'
  apt-key adv --recv-keys --keyserver keyserver.ubuntu.com 0xcbcb082a1bb943db &>/dev/null
  tee -a /etc/apt/sources.list &>/dev/null <<SOURCE
deb http://mirror.jaleco.com/mariadb/repo/10.2/debian jessie main
deb-src http://mirror.jaleco.com/mariadb/repo/10.2/debian jessie main
SOURCE
fi

if ! grep -q 'nodesource.com' /etc/apt/sources.list; then
  printf 'Updating apt sources with deb.nodesource.com'
  wget -qO- --no-check-certificate https://deb.nodesource.com/gpgkey/nodesource.gpg.key | apt-key add - &>/dev/null
  tee -a /etc/apt/sources.list &>/dev/null <<SOURCE
deb https://deb.nodesource.com/node_7.x jessie main
deb-src https://deb.nodesource.com/node_7.x jessie main
SOURCE
fi

if ! grep -q 'ondrej/php' /etc/apt/sources.list; then
  printf 'Updating apt sources with packages.sury.org'
  wget -qO- --no-check-certificate https://packages.sury.org/php/apt.gpg | apt-key add - &>/dev/null
  tee -a /etc/apt/sources.list &>/dev/null <<SOURCE
deb https://packages.sury.org/php/ jessie main
deb-src https://packages.sury.org/php/ jessie main
SOURCE
fi

printf 'Updating list of available packages'
aptitude -y update &>/dev/null

printf 'Updating installed packages'
aptitude -y upgrade &>/dev/null

for package in "${packages[@]}"; do
  if apt-cache policy "$package" | grep -q 'Installed: (none)'; then
    printf 'Installing %s' "$package"
    aptitude -y install "$package" &>/dev/null
  fi
done

# ----- Nginx ------------------------------------------------------------------

if [ ! -e /etc/nginx/server.key ] || [ ! -e /etc/nginx/server.crt ]; then
  printf 'Creating self-signed certificate'
  openssl req -new -newkey rsa:4096 -days 365 -nodes -x509 -subj /CN="*.$host" -keyout /etc/nginx/server.key -out /etc/nginx/server.crt &>/dev/null
fi

if ! grep -q ':443' /etc/nginx/conf.d/default.conf; then
  printf 'Updating Nginx configuration'
  tee /etc/nginx/conf.d/default.conf &>/dev/null <<NGINX
ssl_certificate /etc/nginx/server.crt;
ssl_certificate_key /etc/nginx/server.key;

server {
  server_name $host;

  listen 80 default_server;
  listen [::]:80 default_server ipv6only=on;
  listen 443 ssl default_server;
  listen [::]:443 ssl default_server ipv6only=on;

  root /usr/share/nginx/html;

  index index.php;

  location / {
    try_files \$uri \$uri/ /index.php?\$args;
  }

  location ~ \\.php$ {
    fastcgi_index index.php;
    fastcgi_pass unix:/run/php/php$phpv-fpm.sock;
    include fastcgi_params;
    fastcgi_param SCRIPT_NAME \$fastcgi_script_name;
    fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
  }

  #sed
}

server {
  server_name admin.$host;

  listen 80;
  listen [::]:80;
  listen 443 ssl;
  listen [::]:443 ssl;

  root /usr/share/nginx/admin;

  index index.php;

  location / {
    try_files \$uri \$uri/ =404;
  }

  location ~ \\.php$ {
    fastcgi_index index.php;
    fastcgi_pass unix:/run/php/php$phpv-fpm.sock;
    include fastcgi_params;
    fastcgi_param SCRIPT_NAME \$fastcgi_script_name;
    fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
  }
}
NGINX
fi

if ! grep -q 'user www-data' /etc/nginx/nginx.conf; then
  printf 'Updating Nginx user'
  sed -ir 's/user  nginx;/user www-data;/' /etc/nginx/nginx.conf
  rm -f /etc/nginx/nginx.confr
fi

if ! grep -q 'sendfile off;' /etc/nginx/nginx.conf; then
  printf 'Updating Nginx sendfile'
  sed -ir 's/sendfile *on;/sendfile off;/' /etc/nginx/nginx.conf
  rm -f /etc/nginx/nginx.confr
fi

if ! grep -q 'client_max_body_size 999m;' /etc/nginx/nginx.conf; then
  printf 'Updating Nginx client_max_body_size'
  sed -ir 's/sendfile *off;/sendfile off;\n    client_max_body_size 999m;/' /etc/nginx/nginx.conf
  rm -f /etc/nginx/nginx.confr
fi

if ! grep -q 'fastcgi_read_timeout 999;' /etc/nginx/nginx.conf; then
  printf 'Updating Nginx fastcgi_read_timeout'
  sed -ir 's/client_max_body_size 999m;/client_max_body_size 999m;\n    fastcgi_read_timeout 999;/' /etc/nginx/nginx.conf
  rm -f /etc/nginx/nginx.confr
fi

if [ ! -e /usr/share/nginx/admin/index.php ]; then
  printf 'Creating Nginx index page'
  mkdir /usr/share/nginx/admin
  tee /usr/share/nginx/admin/index.php &>/dev/null <<INDEX
<!DOCTYPE html>
<title>$host</title>
<style>
  body {font: 100%/1 sans-serif; text-align: center;}
  a {color: inherit; display: block; padding: 1em; text-decoration: none;}
  a:focus, a:hover {background: aliceblue; outline: 0;}
  a:active {background: whitesmoke;}
  h1 {margin: 0;}
</style>
<h1><a href='http://local.test/'>$host</a></h1>
<?php
foreach (glob('*', GLOB_ONLYDIR) as \$directory) {
  if (!glob("{\$directory}/index.{php,html}", GLOB_BRACE)) continue;

  echo "<a href='".\$directory."/'>".\$directory."</a>\\n";
}

INDEX
fi

# ----- MariaDB ----------------------------------------------------------------

if mysql -u root -e 'select user from mysql.user where password = ""' | grep -q 'root'; then
  printf 'Updating MariaDB root user'
  mysql -u root -e 'update mysql.user set password = password("root") where user = "root"'
  mysql -u root -e "create user 'root'@'$hip4' identified by 'root'"
  mysql -u root -e "grant all privileges on *.* to 'root'@'$hip4' with grant option;"
  mysql -u root -e "delete from mysql.user where user = '' or (user = 'root' and host not in ('localhost', '127.0.0.1', '::1', '$hip4'))"
  mysql -u root -e 'flush privileges'
fi

if ! grep -q '^default-character-set = utf8' /etc/mysql/conf.d/mariadb.cnf; then
  printf 'Updating MariaDB configuration'
  sed -ir 's/#default-character-set = utf8/user = root\npassword = root\ndefault-character-set = utf8/' /etc/mysql/conf.d/mariadb.cnf
  sed -ir 's/#character-set-server  = utf8/character-set-server = utf8\ncollation-server = utf8_general_ci\ncharacter_set_server = utf8\ncollation_server = utf8_general_ci/' /etc/mysql/conf.d/mariadb.cnf
  rm -f /etc/mysql/conf.d/mariadb.cnfr
fi

if grep -q '#bind-address=0.0.0.0' /etc/mysql/my.cnf; then
  printf 'Updating MariaDB binding'
  sed -ir 's/#bind-address=0.0.0.0/bind-address=0.0.0.0/' /etc/mysql/my.cnf
  rm -f  /etc/mysql/my.cnfr
fi

if [ ! -e /usr/share/nginx/admin/db/index.php ]; then
  printf 'Installing Adminer'
  mkdir /usr/share/nginx/admin/db
  wget -qO /usr/share/nginx/admin/db/index.php https://github.com/vrana/adminer/releases/download/v4.3.1/adminer-4.3.1-en.php
fi

# ----- MailDev ----------------------------------------------------------------

if ! which maildev &>/dev/null; then
  printf 'Installing MailDev'
  npm install -g --silent forever maildev &>/dev/null
  forever start "$(which maildev)" &>/dev/null
fi

if [ ! -e /etc/init.d/maildev ]; then
  printf 'Creating MailDev service'
  tee /etc/init.d/maildev &>/dev/null <<MAILDEV
#!/bin/bash

### BEGIN INIT INFO
# Provides: maildev
# Required-Start: \$remote_fs \$syslog
# Required-Stop: \$remote_fs \$syslog
# Default-Start: 2 3 4 5
# Default-Stop: 0 1 6
# Short-Description: Start maildev at boot
# Description: Enable maildev service at boot
### END INIT INFO

forever start "$(which maildev)"
MAILDEV

  chmod +x /etc/init.d/maildev
  update-rc.d maildev defaults
fi

if ! grep -q 'relayhost = 127.0.0.1:1025' /etc/postfix/main.cf; then
  printf 'Updating postfix relayhost'
  sed -ir 's/relayhost =/relayhost = 127.0.0.1:1025/' /etc/postfix/main.cf
  rm -f /etc/postfix/main.cfr
fi

if [ ! -e /usr/share/nginx/admin/mail/index.php ]; then
  printf 'Creating MailDev redirect'
  mkdir /usr/share/nginx/admin/mail
  tee /usr/share/nginx/admin/mail/index.php &>/dev/null <<MAILDEV
<?php header('Location: http://$host:1080'); exit;
MAILDEV
fi

# ----- PHP --------------------------------------------------------------------

if ! grep -q 'user = vagrant' "/etc/php/$phpv/fpm/pool.d/www.conf"; then
  printf 'Updating PHP user'
  sed -ir 's/user = www-data/user = vagrant/' "/etc/php/$phpv/fpm/pool.d/www.conf"
  rm -f "/etc/php/$phpv/fpm/pool.d/www.confr"
fi

if ! grep -q 'group = vagrant' "/etc/php/$phpv/fpm/pool.d/www.conf"; then
  printf 'Updating PHP group'
  sed -ir 's/group = www-data/group = vagrant/' "/etc/php/$phpv/fpm/pool.d/www.conf"
  rm -f "/etc/php/$phpv/fpm/pool.d/www.confr"
fi

if ! grep -q 'display_errors = On' "/etc/php/$phpv/fpm/php.ini"; then
  printf 'Updating PHP error display'
  sed -ir 's/display_errors = Off/display_errors = On/' "/etc/php/$phpv/fpm/php.ini"
  rm -f "/etc/php/$phpv/fpm/php.inir"
fi

if ! grep -q 'error_reporting = E_ALL$' "/etc/php/$phpv/fpm/php.ini"; then
  printf 'Updating PHP error reporting'
  sed -ir 's/error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT/error_reporting = E_ALL/' "/etc/php/$phpv/fpm/php.ini"
  rm -f "/etc/php/$phpv/fpm/php.inir"
fi

if ! grep -q 'upload_max_filesize = 999M' "/etc/php/$phpv/fpm/php.ini"; then
  printf 'Updating PHP max upload filesize'
  sed -ir 's/upload_max_filesize = 2M/upload_max_filesize = 999M/' "/etc/php/$phpv/fpm/php.ini"
  rm -f "/etc/php/$phpv/fpm/php.inir"
fi

if ! grep -q 'post_max_size = 999M' "/etc/php/$phpv/fpm/php.ini"; then
  printf 'Updating PHP max post size'
  sed -ir 's/post_max_size = 8M/post_max_size = 999M/' "/etc/php/$phpv/fpm/php.ini"
  rm -f "/etc/php/$phpv/fpm/php.inir"
fi

if [ ! -e /usr/share/nginx/admin/php ]; then
  printf 'Creating PHP info page'
  mkdir /usr/share/nginx/admin/php
  tee /usr/share/nginx/admin/php/index.php &>/dev/null <<PHPINFO
<?php phpinfo();
PHPINFO
fi

if [ ! -e /usr/share/nginx/admin/opcache/index.php ]; then
  printf 'Installing OPcache Status'
  mkdir /usr/share/nginx/admin/opcache
  wget -qO /usr/share/nginx/admin/opcache/index.php https://raw.githubusercontent.com/rlerdorf/opcache-status/master/opcache.php
fi

if [ ! -e /usr/share/nginx/admin/memcache/index.php ]; then
  printf 'Installing phpMemcachedAdmin'
  wget -q https://github.com/wp-cloud/phpmemcacheadmin/archive/master.tar.gz
  tar fx master.tar.gz
  mv phpmemcacheadmin* /usr/share/nginx/admin/memcache
  rm -f master.tar.gz
fi

if [ ! -e /usr/share/nginx/admin/webgrind/index.php ]; then
  printf 'Installing Webgrind'
  wget -q https://github.com/jokkedk/webgrind/archive/master.tar.gz
  tar fx master.tar.gz
  mv webgrind* /usr/share/nginx/admin/webgrind
  rm -f master.tar.gz
fi

if [ ! -e /usr/local/bin/dot ]; then
  printf 'Updating Graphviz link'
  ln -fs /usr/bin/dot /usr/local/bin/dot
fi

if ! grep -q 'xdebug.profiler_enable_trigger = 1' "/etc/php/$phpv/mods-available/xdebug.ini"; then
  printf 'Updating Xdebug configuration'
  tee "/etc/php/$phpv/mods-available/xdebug.ini" &>/dev/null <<XDEBUG
zend_extension=xdebug.so

xdebug.collect_params = 1
xdebug.idekey = '$host'
xdebug.profiler_enable_trigger = 1
xdebug.remote_autostart = 1
xdebug.remote_enable = 1
xdebug.remote_host = "$gip4"
xdebug.var_display_max_children = -1
xdebug.var_display_max_data = -1
xdebug.var_display_max_depth = -1
XDEBUG
fi

# ----- Project ----------------------------------------------------------------

plugins=(
  debug-bar
  debug-bar-console
  debug-bar-cron
  debug-bar-extender
  developer
  log-deprecated-notices
  log-viewer
  monster-widget
  piglatin
  rewrite-rules-inspector
  rtl-tester
  regenerate-thumbnails
  simply-show-hooks
  simply-show-ids
  theme-check
  theme-test-drive
  user-switching
  wordpress-beta-tester
  wordpress-importer
)

if ! which wp &>/dev/null; then
  printf 'Installing wp-cli'
  wget -qO /usr/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && chmod +x /usr/bin/wp
fi

if [ ! -e /usr/share/nginx/html/wp-cron.php ]; then
  printf 'Downloading WordPress'
  rm -rf /usr/share/nginx/html
  mkdir /usr/share/nginx/html
  wp --allow-root --quiet core download --path=/usr/share/nginx/html --version="$wpvn"
fi

if ! mysql -e 'SHOW SCHEMAS' | grep -q 'wp'; then
  printf 'Creating WordPress database'
  mysql -e 'CREATE DATABASE wp'
fi

cd /usr/share/nginx/html || exit

if [ ! -e wp-config.php ]; then
  printf 'Creating WordPress configuration'
  wp --allow-root --quiet core config --dbname=wp --dbuser=root --dbpass=root --extra-php <<WPCONFIG
define('WP_DEBUG', true);
define('SAVEQUERIES', true);
define('SCRIPT_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);
WPCONFIG
fi

if ! wp --allow-root core is-installed &>/dev/null; then
  printf 'Installing WordPress'
  wp --allow-root --quiet core multisite-install --skip-email --url="$host" --title="$host" --admin_user=root --admin_password=root --admin_email="root@$host"
fi

if ! wp --allow-root option get timezone_string | grep -q "$wptz"; then
  printf 'Updating WordPress timezone to %s' "$wptz"
  wp --allow-root --quiet --skip-plugins option update timezone_string "$wptz"
fi

if ! wp --allow-root option get permalink_structure | grep -q '/%postname%/'; then
  printf 'Updating WordPress permalinks to /%%postname%%/'
  wp --allow-root --quiet --skip-plugins rewrite structure '/%postname%/'
fi

for plugin in "${plugins[@]}"; do
  if ! wp --allow-root --quiet plugin is-installed "$plugin"; then
    printf 'Installing %s' "$plugin"
    wp --allow-root --quiet plugin install "$plugin" &>/dev/null
  fi
done

for plugin in "${plugins[@]}"; do
  if [ "$plugin" = 'akismet' ]; then continue; fi
  if [ "$plugin" = 'hello' ]; then continue; fi
  if [ "$plugin" = 'piglatin' ]; then continue; fi

  if ! wp --allow-root plugin status "$plugin" | grep -q 'Status: Network Active'; then
    printf 'Activating %s' "$plugin"
    wp --allow-root --quiet plugin activate "$plugin" --network &>/dev/null
  fi
done

if wp --allow-root db query 'select count(*) from wp_users' | grep -q '1'; then
  printf 'Importing WordPress Theme Unit Test Data'
  wget -q https://raw.githubusercontent.com/WPTRT/theme-unit-test/master/themeunittestdata.wordpress.xml
  wp --allow-root --quiet import themeunittestdata.wordpress.xml --authors=create &>/dev/null
  rm -f themeunittestdata.wordpress.xml
fi

if [ -e wp-content/debug.log ]; then
  printf 'Cleaning up WordPress debug log'
  unlink wp-content/debug.log
fi

if ! stat -c '%U' wp-config.php | grep -q 'vagrant'; then
  printf 'Updating WordPress file and directory owner'
  find . -exec chown vagrant {} \; &>/dev/null
fi

if [ ! -e wp-content/plugins/webcomic ]; then
  printf 'Updating WordPress project directory link'
  ln -fs /vagrant/src wp-content/plugins/webcomic-dev
fi

cd || exit

if grep -q '#sed' /etc/nginx/conf.d/default.conf; then
  printf 'Updating Nginx configuration'
  cat > /tmp/nginxloc <<NGINXLOC
  if (!-e \$request_filename) {
    rewrite /wp-admin$ \$scheme://\$host\$uri/ permanent;
    rewrite ^/([^/]+)?(/wp-.*) /\$2 last;
    rewrite ^/([^/]+)?(/.*\\.php)$ /\$2 last;
  }
NGINXLOC
  sed -ir '/#sed/{r /tmp/nginxloc
    d}' /etc/nginx/conf.d/default.conf
  rm -f /etc/nginx/conf.d/default.confr
fi

# ----- System -----------------------------------------------------------------

printf 'Restarting services'
service nginx restart
service mysql restart
service postfix restart
forever restart "$(which maildev)" &>/dev/null
service memcached restart
service "php$phpv-fpm" restart

printf 'Cleaning up'
aptitude clean &>/dev/null

# ----- End --------------------------------------------------------------------

end=$(date +%s)

printf 'Provisioning completed in %s seconds\n' $((end - start))
printf 'Now serving at %s (%s || localhost:%s)' "$host" "$gip4" "$port"
