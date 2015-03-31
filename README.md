#Slack - Deploys - Reviews

##SetUp
Laravel will end up being easy to setup if you set up Homestead below first.  Laravel requires composer and unless you have all the needed php librarys locally it will throw an error, so usually I will run all composer commands from the VM

After pulling down the lastest from it you should SSH into the box and install composer

	curl -sS https://getcomposer.org/installer | php

once installed run `composer update` inside the code's root directory that should install the rest of the dependencies for you.

Laravel uses `.env.php` files for configuration I have placed a sample file in the repo but a first step is to create your `.env.local.php` file with the following

    <?php
        return array(
            'DB_USER' => '',
            'DB_PASSWORD' => '',
            'deployChannel' => '',
            'reviewChannel' => '',
            'slackEndPoint' => ''
        );


filling in the data based on your setup
`deployChannel` and `reviewChannel` should be a __PRIVATE__ channel that you create and invite the devcodedad into to test.   For example mine is #testCodeDadPat

This will allow you to test slack group messages without spamming the group.

The slackend point is =`xoxb-4242334240-lrXGW8IW2jKuYCvHVhPJ8EUo` for zCodeDadDev

Once this is all completed from the command in the root directory run the following command

	php artisan migrate

This will rake the migrations files and get your database up to date.

At this point you should be up and running.   A quick way to test is to try the url
`http://homestead.app/reviews/list` and see if anything comes up.

##Code Overview
I followed pretty strict Laravel patterns so

Routes are defined in `app/routes.php` and are broken down into two groups reviews and deploys.  Follow the pattern to add any new group or endpoint.

Controllers are located in `app/controllers` and should remain pretty skinny.
The Flow of control is
controller -> service -> repository -> model(ORM).

##Local Enviroment Setup - Homestead

###Installing VirtualBox & Vagrant

Once VirtualBox and Vagrant have been installed, you should add the laravel/homestead box to your Vagrant installation using the following command in your terminal. It will take a few minutes to download the box, depending on your Internet connection speed:

	vagrant box add laravel/homestead
If this fails, you may have an older version of vagrant that requires the url of the box. The following should work:

	vagrant box add laravel/homestead https://atlas.hashicorp.com/laravel/boxes/homestead

###Installing Homestead

###With Composer + PHP Tool

Once the box has been added to your Vagrant installation, you are ready to install the Homestead CLI tool using the Composer `global` command:

	composer global require "laravel/homestead=~2.0"
Make sure to place the `~/.composer/vendor/`bin directory in your PATH so the `homestead `executable is found when you run the homestead command in your terminal.

Once you have installed the Homestead CLI tool, run the `init` command to create the Homestead.yaml configuration file:

	homestead init
The `Homestead.yaml` file will be placed in the `~/.homestead` directory. If you're using a Mac or Linux system, you may edit `Homestead.yaml` file by running the homestead edit command in your terminal:

	homestead edit

###Manually Via Git (No Local PHP)

Alternatively, if you do not want to install PHP on your local machine, you may install Homestead manually by simply cloning the repository. Consider cloning the repository into a central `Homestead` directory where you keep all of your Laravel projects, as the Homestead box will serve as the host to all of your Laravel (and PHP) projects:

	git clone https://github.com/laravel/homestead.git Homestead
Once you have installed the Homestead CLI tool, run the `bash init.sh` command to create the `Homestead.yaml` configuration file:

	bash init.sh
The `Homestead.yaml` file will be placed in the `~/.homestead` directory.

###Set Your SSH Key

Next, you should edit the `Homestead.yaml` file. In this file, you can configure the path to your public SSH key, as well as the folders you wish to be shared between your main machine and the Homestead virtual machine.

Don't have an SSH key? On Mac and Linux, you can generally create an SSH key pair using the following command:

	ssh-keygen -t rsa -C "you@homestead"

Once you have created a SSH key, specify the key's path in the authorize property of your `Homestead.yaml` file.

##Configure Your Shared Folders

The `folders` property of the `Homestead.yaml` file lists all of the folders you wish to share with your Homestead environment. As files within these folders are changed, they will be kept in sync between your local machine and the Homestead environment. You may configure as many shared folders as necessary!

###Configure Your Nginx Sites

Not familiar with Nginx? No problem. The `sites` property allows you to easily map a "domain" to a folder on your Homestead environment. A sample site configuration is included in the `Homestead.yaml` file. Again, you may add as many sites to your Homestead environment as necessary. Homestead can serve as a convenient, virtualized environment for every Laravel project you are working on!

You can make any Homestead site use HHVM by setting the `hhvm` option to `true`:

	sites:
   	 - map: homestead.app
   	   to: /home/vagrant/Code/Laravel/public
   	   hhvm: true
###Bash Aliases

To add Bash aliases to your Homestead box, simply add to the `aliases` file in the root of the `~/.homestead` directory.

###Launch The Vagrant Box

Once you have edited the `Homestead.yaml` to your liking, run the `homestead up` command in your terminal. If you installed Homestead manually and are not using the PHP `homestead` tool, run `vagrant up` from the directory that contains your cloned Homestead Git repository.

Vagrant will boot the virtual machine, and configure your shared folders and Nginx sites automatically! To destroy the machine, you may use the `homestead destroy` command. For a complete list of available Homestead commands, run `homestead list`.

Don't forget to add the "domains" for your Nginx sites to the `hosts` file on your machine! The `hosts` file will redirect your requests for the local domains into your Homestead environment. On Mac and Linux, this file is located at `/etc/hosts`. On Windows, it is located at `C:\Windows\System32\drivers\etc\hosts`. The lines you add to this file will look like the following:

	192.168.10.10  homestead.app
Make sure the IP address listed is the one you set in your `Homestead.yaml` file. Once you have added the domain to your `hosts file`, you can access the site via your web browser!

	http://homestead.app


##Sample Homstead.yaml file
Here is my sample homestead file

    ip: "192.168.10.10"
    memory: 2048
    cpus: 1
    authorize: ~/.ssh/id_rsa.pub
    keys:
        - ~/.ssh/id_rsa
    folders:
        - map: /Users/pcunningham/Projects/slackBot
            to: /home/vagrant/Code
    sites:
        - map: homestead.app
            to: /home/vagrant/Code/public
    databases:
        - homestead
    variables:
        - key: APP_ENV
        value: local
##Daily Usage

###Connecting Via SSH

To connect to your Homestead environment via SSH, issue the `homestead ssh` command in your terminal.

###Connecting To Your Databases

A `homestead` database is configured for both MySQL and Postgres out of the box. For even more convenience, Laravel's `local` database configuration is set to use this database by default.

To connect to your MySQL or Postgres database from your main machine via Navicat or Sequel Pro, you should connect to `127.0.0.1` and port 33060 (MySQL) or 54320 (Postgres). The username and password for both databases is `homestead / secret`.

