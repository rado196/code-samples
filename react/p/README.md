# Landing website

---
#### Required installations:
- Python 2/3
- Node.JS 14+

---
#### Tested on:
- macOS 11+
- Linux 5.6+

---
#### Installation:
- Install node modules via `npm install`

##### For development server:
- Run `npm start` to start front-end app

##### For production server:
- Run `npm run build` for production build
- Use nginx configuration below

---
#### Nginx config file:
```nginx
server {
  server_name 443.how www.443.how;
  listen 80 default_server;

  return 307 https://$host$request_uri;
}

server {
  server_name 443.how www.443.how;
  listen 443 ssl http2;

  root /var/www/443-landing/public;
  index index.html index.htm;

  proxy_intercept_errors on;
  error_page 404 /404;

  location / {
    try_files $uri $uri/ /index.html =404;
  }

  location ~* \.(?:ico|css|js|gif|jpe?g|png|svg|woff|woff2)$ {
    gzip_static on;
    expires 30d;
    add_header Vary Accept-Encoding;
    add_header Cache-Control max-age=3156000;
    access_log off;
  }
}
```
