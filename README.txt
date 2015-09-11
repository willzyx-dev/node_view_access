CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Troubleshooting
 * Maintainers


INTRODUCTION
------------
Node View Access Permissions is a simple content access module that covers the
lacks of "view permission" for nodes in drupal 7 and drupal 8 core.This module
simply enables permissions "View own content" and "View any content" for each
content type on permissions page.

 * For a full description of the module, visit the project page:
   https://www.drupal.org/sandbox/willzyx/2393133

 * To submit bug reports and feature suggestions, or to track changes:
   https://www.drupal.org/project/issues/search/2393133


REQUIREMENTS
------------
 * Drupal core Node module.


INSTALLATION
------------
 * Install as usual, see http://drupal.org/node/1897420 for further information.


CONFIGURATION
-------------
 * Configure user permissions in Administration » People » Permissions:

   - Type: View content

     Users in roles with the "Type: View content" permission will access
     node of the specified type.


   - Type: View own content

     Users in roles with the "Type: View own content" permission will access
     own node of the specified type.


TROUBLESHOOTING
---------------
 * If users can't access nodes :

   - Are the "Type: View content" or "Type: View own content" permissions
     enabled for the appropriate roles?


   - Are the "View published content" permission enabled for the appropriate
     roles?


MAINTAINERS
-----------
 * willzyx - https://drupal.org/user/1043862
