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
