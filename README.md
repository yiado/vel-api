## APACHE
    Ubuntu
    1) sudo apt update
    2_ sudo apache2ctl configtest
    3) sudo apache2ctl configtest
    4) service apache2 restart
     

## PHP
    Ubuntu
    sudo add-apt-repository ppa:ondrej/php5-5.6
    sudo apt-get install python-software-properties
    sudo add-apt-repository ppa:ondrej/php
    sudo apt update
    sudo apt install -y php5.6
    php -v

    sudo apt-get install php5.6-gd php5.6-mysql php5.6-dom php5.6-cli php5.6-json php5.6-common php5.6-mbstring php5.6-opcache php5.6-readline
    sudo apt install php5.6-xml php5.6-zip

    ## --> MAS INFO --> https://tecadmin.net/install-php5-on-ubuntu/

    Centos 7
    1) yum install https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
    2) yum install http://rpms.remirepo.net/enterprise/remi-release-7.rpm
    3) yum install yum-utils
    4) yum-config-manager --enable remi-php56
    5) yum install php php-mcrypt php-cli php-gd php-curl php-mysql php-ldap php-zip php-fileinfo
    6) php -v

    CONFIGURACION
    ## HABILITAR EXTENSION:
        MYSQL
    ## HABILITAR 
        short_open_tag = On

    

## MYSQL
    sudo apt-get update
    sudo apt-get install mysql-server.
    mysql_secure_installation

## CREA USUARIO MYSQL
    https://www.digitalocean.com/community/tutorials/crear-un-nuevo-usuario-y-otorgarle-permisos-en-mysql-es

## COMPOSER
    https://www.ionos.com/community/hosting/php/install-and-use-php-composer-on-ubuntu-1604/

## CLONAR REPOSITORIO
    git clone git@bitbucket.org:adminpok/igeo-backend.git

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

    sudo composer install

## CREAR CARPETAS
    mkdir docs asset_doc /temp/qrcode

## Permitir que los scripts y módulos HTTPD se conecten a la red
    sudo setsebool httpd_can_network_connect=1


## PERMISOS EN AMBIENTE PRODUCTIVO
    sudo find . -type f -exec chmod 755 {} \;
    sudo find . -type d -exec chmod 664 {} \;
 
## CARPETAS ESPECIALES CON PERMISOS 777
    chmod -R 777 plans/ docs/ assets_doc/ style/ temp/ system/application/libraries/tcpdf/cache/