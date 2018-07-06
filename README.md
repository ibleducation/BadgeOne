BadgeOne.com - Open Badges Server
====================================


This is the **BadgeOne** server platform: a self-hosted platform to award your own institution's open badges. 
The open badges you create and earn with this server are conformant to the [Open Badges Technical specifications 1.1](https://openbadgespec.org) (May 2015).

This software was developed by [IBL Studios Education](http://iblstudios.com/), with GW Professor Lorena A. Barba in an advisory role, and with financial and technical support from edX.

The project has its origins in the [first integration of open badges in the Open edX platform](http://iblstudios.com/first-integration-of-open-badges-on-open-edx-supported-by-edxs-ceo/), in November 2014.

The software is free for any site administrator to download, install and use, under a GPL 3 license.

## OpenBadges specifications
* [Mozilla OpenBadges] (http://openbadges.org/)
* [Mozilla OpenBadges wiki] (https://github.com/mozilla/openbadges-backpack/wiki/)
* [Mozilla OpenBadges latest specs] (https://openbadgespec.org/)

### Requirements:
* PHP > 5.3.9 (php5-mysql, php5-json, php-gettext) (Recommended > php 5.4.0)
* MySQL 5.x (PDO connections are used with php5)
* Apache2.4 server (you could use Nginx; remember to configure the options properly)
* mod_rewrite (to protect certain directories with .htaccess files)
* Certain directories require write permissions (defined in installation process)

### Prepare Installation:
* Create your database and user, and grant privileges.
* Load the main database. The SQLdump file is provided under `sql/badgeone_sql_model.dump`.
* Configure your VirtualHost (absolute domain or subdomain). Sample: `virtualhost/apache-2.4-sample.conf`.
* The server, API and OAuth platform files are given under the `www/` directory. Place the contents
  in the path you have defined for your VirtualHost.

### Install and configure the Badges Server:
* Before releasing your server to the public, you will need to correctly configure your database connection
  and your institution's data.
* Request this page with a browser: `http://_your_badges_server_uri_/install.php`.
* Follow step-by-step instructions defined on `install.php`. The installation page will guide you 
  to perfom the changes on the configuration's parameters and set directory permissions correctly.
* The last step allows you to create the `admin` user. Remember to change the default password when you
  access for the first time (default password: `admin123`).
* Important! Remove the `install.php` file when you finish your installation.

### Server API Documentation 

* This document provides the background information you need to use the BadgetOne API [http://badgeone.com/api_doc/] (http://badgeone.com/api_doc/)

### About OAuth2
* This platform uses OAuth2.0 authentication.
   [OAuth 2.0] (http://oauth.net/2/)
   [OAuth 2.0 Server PHP] (http://bshaffer.github.io/oauth2-server-php-docs/)

### Special Thanks
* Professor Lorena A. Barba advised IBL Studios on aspects like how educators might use open badges; the inter-relations between an open-source badge server, issuers and earners; the Open Badge Infrastructure standards; and the potential for open badges in MOOCs.
