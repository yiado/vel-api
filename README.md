## APACHE
    Ubuntu
    1) sudo apt update
    2) sudo apache2ctl configtest
    3) sudo apache2ctl configtest
    4) service apache2 restart
    
    Centos
    1) yum install httpd

## PHP
    Ubuntu
    1) sudo add-apt-repository ppa:ondrej/php5-5.6
    2) sudo apt-get install python-software-properties
    3) sudo add-apt-repository ppa:ondrej/php
    4) sudo apt update
    5) sudo apt install -y php5.6

    ## --> MAS INFO --> https://tecadmin.net/install-php5-on-ubuntu/
    *** Evaluar comandos ***
    sudo apt-get install php5.6-gd php5.6-mysql php5.6-dom php5.6-cli php5.6-json php5.6-common php5.6-mbstring php5.6-opcache php5.6-readline
    sudo apt install php5.6-xml php5.6-zip

    Centos 7
    1) yum install https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
    2) yum install http://rpms.remirepo.net/enterprise/remi-release-7.rpm
    3) yum install yum-utils
    4) yum-config-manager --enable remi-php56
    5) yum install php php-mcrypt php-cli php-gd php-curl php-mysql php-ldap php-zip php-fileinfo
    
    CONFIGURACION
    Verificar version 6) php -v
    ## HABILITAR EXTENSION:
        MYSQL
    ## HABILITAR 
        short_open_tag = On
        date.timezone = America/Santiago

## MYSQL
    Ubuntu
    sudo apt-get install mysql-server.
    mysql_secure_installation

    Centos
    1) yum install mariadb-server
    2) systemctl start mariadb
    3) systemctl status mariadb
    4) sudo mysql_secure_installation

## CREA USUARIO MYSQL
    https://www.digitalocean.com/community/tutorials/crear-un-nuevo-usuario-y-otorgarle-permisos-en-mysql-es

## COMPOSER
    https://www.ionos.com/community/hosting/php/install-and-use-php-composer-on-ubuntu-1604/
    Centos
    1) yum install composer

## CLONAR REPOSITORIO
    git clone https://bitbucket.org/adminpok/igeo-backend.git
    composer install

## CAMBIAR CREDENCIALES BD
    cp application/config/bkp_database.php application/config/database.php
    vi application/config/database.php
        $db['default']['hostname'] = "HOST";
        $db['default']['port'] = "3306";
        $db['default']['username'] = "USER";
        $db['default']['password'] = "PASSWD";
        $db['default']['database'] = "DB_NAME";

## CAMBIAR URL
    cp system/application/config/bkp_config.php system/application/config/config.php
    vi system/application/config/config.php
        $config['base_url']	= "http://localhost/igeov4/";

## CREAR CARPETAS
    mkdir docs docs/data docs/thumb asset_doc temp/qrcode
    chmod -R 777 plans/ docs/ asset_doc/ style/ temp/ system/application/libraries/tcpdf/cache/
    chmod -R 755 docs/data

## PERMISOS SELinux (centos)
    1) vi /etc/selinux/config
    2) SELINUX=permissive
    3) shutdown -r now
    4) getenforce

## Permitir que los scripts y módulos HTTPD se conecten a la red
    sudo setsebool httpd_can_network_connect=1


## PERMISOS EN AMBIENTE PRODUCTIVO
    sudo find . -type f -exec chmod 755 {} \;
    sudo find . -type d -exec chmod 664 {} \;