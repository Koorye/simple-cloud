<?php
$CONFIG = array (
  'htaccess.RewriteBase' => '/',
  'memcache.local' => '\\OC\\Memcache\\APCu',
  'apps_paths' => 
  array (
    0 => 
    array (
      'path' => '/var/www/html/apps',
      'url' => '/apps',
      'writable' => false,
    ),
    1 => 
    array (
      'path' => '/var/www/html/custom_apps',
      'url' => '/custom_apps',
      'writable' => true,
    ),
  ),
  'instanceid' => 'ocp7wups0p4c',
  'passwordsalt' => '7JgqM94a1KwpJbWB2HPKDx1hwuKT32',
  'secret' => 'Y2fApAFqdEE/4kfkN4+0fYrKhD3FJescxKdXcwdu+oHp9iGs',
  'trusted_domains' => 
  array (
    0 => 'localhost:15000',
    1 => 'localhost:8080',
    2 => '192.168.0.1:8080',
  ),
  'datadirectory' => '/var/www/html/data',
  'dbtype' => 'mysql',
  'version' => '27.1.1.0',
  'overwrite.cli.url' => 'http://localhost:15000',
  'dbname' => 'nextcloud',
  'dbhost' => 'db',
  'dbport' => '',
  'dbtableprefix' => 'oc_',
  'mysql.utf8mb4' => true,
  'dbuser' => 'nextcloud',
  'dbpassword' => 'nextcloud!@',
  'installed' => true,
);
