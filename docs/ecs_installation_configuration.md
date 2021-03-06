## ECS Configuration
Download http://freeit.de/pub/ecs4_1.2.0-20180301120015_amd64.deb

### Installation of needed packages
    apt-get install mysql-common libpq5 libsqlite3-0 openssl libxml2 libxslt1.1 libssl1.0.2 libmariadbclient18 libevent-2.0-5 libevent-core-2.0-5 libevent-extra-2.0-5 libreadline7 postgresql

### Installation ECS4
    dpkg -i ecs4_1.2.0-20180301120015_amd64.deb

### Database
    su - postgres
    createuser -d ecs4 
    exit

## ECS4 Configuration
    su ecs4
    ecs4 run rake db:setup
    ecs4 run rake cc:ressources
    ecs4 config:set PORT=8080
    ecs4 run web 
    exit
    ecs4 scale web=1
Ensure the port is correct!

### View logs
    ecs4 logs

### Update
    dpkg -i install neuesecs.deb
    ecs4 restart

### nginx proxy and user password authentication
    apt-get install nginx apache2-utils
    cd /etc/nginx
    mkdir conf
    htpasswd -c /etc/nginx/conf.ecs ecsuser

### Example vhost nginx
    upstream ecs { 
            server localhost:8080; 
    }
    
    server {
            server_name DUMMY_SERVER
            listen 80;
             
            # Logfiles
            access_log /var/log/nginx/ecs_access.log;
            access_log on;
            access_log upstreamlog;
            error_log /var/log/nginx/ecs_error.log;
            error_log on;
    
                location / {
                    return 301 https://DUMMY_SERVER;
            }
    
    }
    
    server {
            server_name DUMMY_SERVER;
            listen 443 ssl http2;
    
            error_page 404 /local/404.html;
            error_page 403 502 504 =503 /local/503.html;
    
            location / {
                  proxy_pass http://ecs;
                  auth_basic           "closed site";
                  auth_basic_user_file conf/.ecs;
                  allow all;
            }
    
            #SSL
            ssl on;
            ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
            ssl_prefer_server_ciphers on;
            ssl_ciphers "EECDH+AESGCM EDH+AESGCM EECDH EDH -CAMELLIA -SEED !aNULL !eNULL !LOW !3DES !MD5 !EXP !PSK !SRP !DSS !RC4";
            ssl_certificate /etc/ssl/certs/ssl-cert-snakeoil.pem;
            ssl_certificate_key /etc/ssl/private/ssl-cert-snakeoil.key;
    
            # Logfiles
            access_log /var/log/nginx/ecs_access.log;
            access_log on;
            access_log upstreamlog;
            error_log /var/log/nginx/ecs_error.log;
            error_log on;
    
            # post size / request handling settings
            client_max_body_size 20m;
    
            # Proxy settings
            proxy_set_header Host $http_host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    
            proxy_set_header X-EcsAuthId $remote_user;
            proxy_connect_timeout 300s;
            proxy_read_timeout 3600s;
            proxy_send_timeout 3600s;
            proxy_buffer_size 32k;
            proxy_buffers 32 16k;
    
    }

## Configuring the  ECS server
- create a community with a name XYZ
- create a participant with a name and the ILIAS-URL and add it to the XYZ community, the user id has to be equal with the username in the nginx passwd file.

## in ILIAS
- Create a new import category in your repository and add the ref-id to the configuration
- In the administration => ECS => Add a new ECS and configure as following:
  - ECS activate: yes
  - Name the configuration ecs
  - server URL: "DUMMY_SERVER" server_name from nginx vhost configuration
  - protocol: HTTPS
  - port: 443
  - username: ecsuser
  - password: %password%
  - Polling Time: 30 seconds
  - Import-ID: ref_id of category
