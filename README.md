# Online Betting System 
Online Betting System Web Service
Environment
===============
* Centos v7
* Mongodb v3.0.8

Installation Environment
===============
**CentOS**
* Install Apache
```
sudo yum install httpd
sudo yum install php
sudo chkconfig httpd on
sudo service httpd start

```
* Install MongoDB
```
cd /etc/yum.repos.d
vi mongodb.repo
	[MongoDB]
	name=MongoDB Repository
	baseurl=http://repo.mongodb.org/yum/redhat/$releasever/mongodb-org/3.0/x86_64/
	gpgcheck=0
	enabled=1
sudo yum install -y mongodb-10gen-server
sudo yum install -y mongodb-org
sudo chkconfig mongod on
sudo service mongod start

```
* Configure MongoDb with Apache
```
sudo yum install -y gcc php-pear php-devel
sudo pecl install mongo
sudo vi /etc/php.ini
	extension = mongo.so
sudo service httpd restart
sudo /usr/sbin/setsebool -P httpd_can_network_connect 1
```

Installation Project
===============
```
clone project https://github.com/mapring/OnlineBettingSystemServer
	go to project/application/config
	rename config.php.txt 		-> config-local.php
	rename constants.php.txt 	-> constants-local.php
	rename mongodb.php.txt 		-> mongodb-local.php
