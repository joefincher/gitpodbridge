CONTENTS OF THIS FILE
---------------------
 * Introduction
 * Usage
 * Running tests
 * Architecture
 * Maintainers


INTRODUCTION
------------

Drupal Module Upgrader is a script that scans the source of a Drupal 7 module,
flags any code that requires updating to Drupal 8/9, points off to any relevant
API change notices from https://www.drupal.org/list-changes/, and (where
possible) will actually attempt to *convert* the Drupal 7 code automatically to
the Drupal 8/9 version!

 * For a full description of the module, visit the project page:
   https://drupal.org/project/drupalmoduleupgrader
 * To submit bug reports and feature suggestions, or to track changes:
   https://drupal.org/project/issues/drupalmoduleupgrader


USAGE
-----

1. Install with composer (the site should already be using composer).

   composer require drupal/drupalmoduleupgrader
   drush en drupalmoduleupgrader

2. To scan the code and get a report of code that needs updating and how, run
   the following inside the Drupal 8/9 root directory (using Drush 8 or 9):

   drush dmu-analyze MODULE_NAME

   This will print a report showing any relevant change notices where you can
   read more.

3. To attempt to upgrade your Drupal 7 module's code to Drupal 8/9 
   automatically, run the following inside the Drupal 8/9 root directory:

   drush dmu-upgrade MODULE_NAME

   The script will output a few lines as it attempts various conversions. Go
   into your modules/MODULE_NAME directory and check out all of your new YAML
   files and such.

4. To clear out Drupal 7 code that has been converted, run the clean command:

   drush dmu-clean MODULE_NAME

   This will do things like delete old .info files and such, so you're closer to
   your port being completed!

RUNNING TESTS
-------------

In a Drupal site installed with composer and the Drupal Module Upgrader enabled,
run

  ./vendor/bin/phpunit -c core --group=DMU

ARCHITECTURE
------------

See the contributors documentation
https://www.drupal.org/documentation/modules/drupalmoduleupgrader/contributors

MAINTAINERS
-----------
Current maintainers:
 * Gábor Hojtsy - https://www.drupal.org/u/gábor-hojtsy

Past maintainers:
 * Adam (phenaproxima) - https://www.drupal.org/u/phenaproxima
 * Angela Byron (webchick) - https://www.drupal.org/u/webchick
 * Dan Feidt (hongpong) - https://drupal.org/u/hongpong
 * Jakob Perry (japerry) - https://www.drupal.org/u/japerry
 * Jess (xjm) - https://www.drupal.org/u/xjm
 * Lisa Baker (eshta) - https://www.drupal.org/u/eshta
 * Wim Leers - https://www.drupal.org/u/wim-leers

Special thanks to:
 * Cameron Zemek (grom358) - https://www.drupal.org/u/grom358 for all the
   pharborist help!

This project has been sponsored by:
* Acquia
  Dream It. Drupal It. https://www.acquia.com

This project has been supported by:
* PreviousNext
  Australia’s premium Drupal website consulting, design and development firm.
  http://www.previousnext.com.au/
