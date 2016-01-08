## BadgeOne (0.9) beta4; urgency=low
  * Released: 20160108
  * New:
    - css: added new field portfolio.css
    - dir: directory www/files/users/pictures
    - sql: added new fields to table `users`

      ALTER TABLE users ADD COLUMN `about_user` text 
      DEFAULT NULL AFTER `name`;

      ALTER TABLE users ADD COLUMN `picture` varchar(150) 
      DEFAULT NULL AFTER `about_user`;

      ALTER TABLE users ADD COLUMN `url_website` varchar(400) 
      DEFAULT NULL AFTER `picture`;

      ALTER TABLE users ADD COLUMN `url_social_facebook` 
      varchar(400) DEFAULT NULL AFTER `url_website`;

      ALTER TABLE users ADD COLUMN `url_social_twitter` 
      varchar(400) DEFAULT NULL AFTER `url_social_facebook`;

      ALTER TABLE users ADD COLUMN `url_social_gplus` 
      varchar(400) DEFAULT NULL AFTER `url_social_twitter`;

      ALTER TABLE users ADD COLUMN `url_social_linkedin` 
      varchar(400) DEFAULT NULL AFTER `url_social_gplus`;

  * Updated:
    - css: changes main.css
    - img: img/logo.png
    - myportfolio.php: added additional user info
    - settings.php: addedd new constants (users pictures)
    - install.php: control new directory (users pictures)
    - functions.php : new function upload_global_files
    - events.php: update user profile
    - my_profile.php: update form additional user info
 
## BadgeOne (0.9) beta3; urgency=low

  * Released : 20160105
  * New: myportfolio.php
    - show earned badges to everybody
  * Updated:
    - sql: added new field to table `badges_earns`
      ALTER TABLE badges_earns ADD COLUMN `show_public` 
      tinyint(1) unsigned NOT NULL DEFAULT '1' AFTER published;
    - events.php: added function set_public_earn
    - locale files: en_EN.po|en_EN.mo|es_ES.po|es_ES.mo
    - my_earn.php: added control show_public

## BadgeOne (0.9) beta2; urgency=low

  * Released : 20150704
  * Issue #1 fix
    - Uploaded images getting cut off halfway

## BadgeOne (0.9) beta; urgency=low

  * Released : 20150612
  * Renamed project name
  * Added Multilang (default EN, sample ES)

## IBLOpenBadges-Server (0.1-1) master; urgency=low

  * Released : 20150605
  * Added image donwload using Mozilla Badge Baking API

## IBLOpenBadges-Server (0.1) master; urgency=low

  * Initial Release : 20150520
