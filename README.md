# Introduction
学生事务服务中心
# Install

### Ubuntu 16.04


* Install LAMP and git
```Bash
sudo apt update
sudo apt install apache2 php mysql-server redis-server libapache2-mod-php php-mysql php-xml php-mbstring php-zip php-curl php-imagick -y
sudo a2enmod rewrite
    
cd /var/www
sudo apt install git -y
git clone https://github.com/luozhenyu/buaa_helper.git
```

* Configure vhosts of apache2
* An example
```apacheconfig
<VirtualHost *:80>
    ServerName www.ourbuaa.com

    DocumentRoot /var/www/buaa_helper/public

    <Directory /var/www/buaa_helper/public>
        Options FollowSymLinks
        AllowOverride All
        Order Allow,Deny
        Allow From All
    </Directory>
</VirtualHost>
```

* Install Composer and NPM
```Bash
sudo apt install composer npm -y
sudo ln -s /usr/bin/nodejs /usr/bin/node
sudo npm install -g bower

cd /var/www/buaa_helper
sudo chmod -R 777 ./
composer install
bower install
```

* Laravel config
```Bash
cp .env.example .env
php artisan key:generate
vim .env                    # and config as you wish

php artisan migrate --seed
```

* Config Supervisor
```Bash
sudo apt install supervisor
cd /etc/supervisor/conf.d
sudo su
vim buaa_helper.conf

supervisorctl reload
supervisorctl start all
```

```
# An Example
[program:buaa_helper]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/buaa_helper/artisan queue:work --sleep=2 --tries=3
autostart=true
autorestart=true
user=ubuntu
numprocs=8
redirect_stderr=true
stdout_logfile=/var/www/buaa_helper/storage/logs/worker.log
```