# AutoDproc - Backend

---

#### Required installations

-   PHP 8.0+
-   MySQL 8+
-   Node.JS 14+

---

#### Tested on

-   macOS 11+
-   Linux 5.6+

---

---

### Install FFMPEG

```bash
sudo apt -y install ffmpeg
```

#### Install PHP8.1

```bash
PHP_VERSION=8.1

sudo apt install -y software-properties-common
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update -y
sudo apt install -y php$PHP_VERSION
sudo apt install -y php$PHP_VERSION-cli
sudo apt install -y php$PHP_VERSION-curl
sudo apt install -y php$PHP_VERSION-dev
sudo apt install -y php$PHP_VERSION-fpm
sudo apt install -y php$PHP_VERSION-gd
sudo apt install -y php$PHP_VERSION-gmagick
sudo apt install -y php$PHP_VERSION-raphf
sudo apt install -y php$PHP_VERSION-http
sudo apt install -y php$PHP_VERSION-imagick
sudo apt install -y php$PHP_VERSION-intl
sudo apt install -y php$PHP_VERSION-mbstring
sudo apt install -y php$PHP_VERSION-mysql
sudo apt install -y php$PHP_VERSION-opcache
sudo apt install -y php$PHP_VERSION-redis
sudo apt install -y php$PHP_VERSION-soap
sudo apt install -y php$PHP_VERSION-ssh2
sudo apt install -y php$PHP_VERSION-uuid
sudo apt install -y php$PHP_VERSION-xml
sudo apt install -y php$PHP_VERSION-xmlrpc
sudo apt install -y php$PHP_VERSION-yaml
sudo apt install -y php$PHP_VERSION-zip
```

---

#### Nginx config file:

```nginx
server {
  server_name api.autodproc.com;
  listen 80;

  client_max_body_size 200m;
  root /var/www/autodproc.com/api/public;

  add_header X-Frame-Options "SAMEORIGIN";
  add_header X-Content-Type-Options "nosniff";
  charset utf-8;

  index index.php;
  location / {
    try_files $uri $uri/ /index.php?$query_string;
  }

  location = /favicon.ico {
    access_log off;
    log_not_found off;
  }
  location = /robots.txt {
    access_log off;
    log_not_found off;
  }

  error_page 404 /index.php;

  location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
  }
}

```
