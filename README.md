#Install
###Ubuntu 16.04


    sudo apt update
    sudo apt install apache2 php mysql-server redis-server libapache2-mod-php php-xml php-mbstring php-zip php-curl 
    
    cd /var/www
    sudo apt install git
    git clone https://github.com/luozhenyu/buaa_helper.git
    sudo a2enmod rewrite
    configure apache2' vhosts and make sure DocumentRoot is '/var/www/buaa_helper/public'

    sudo apt install composer
    sudo apt install npm
    sudo ln -s /usr/bin/nodejs /usr/bin/node
    npm install -g bower

    cd /var/www/buaa_helper
    sudo chmod -R 777 ./
    composer install
    bower install

    cp .env.example .env
    php artisan key:generate
    vim .env and config as you wish

    php artisan migrate --seed