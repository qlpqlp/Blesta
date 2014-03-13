<?php
 return array (
  'settings' => 
  array (
    'site' => 
    array (
      'title' => 'Blesta Live Chat',
      'site_admin_email' => '',
      'locale' => 'en_EN',
      'theme' => 'defaulttheme',
      'installed' => true,
      'secrethash' => '4092137cdb',
      'debug_output' => false,
      'templatecache' => false,
      'templatecompile' => false,
      'modulecompile' => false,
      'force_virtual_host' => false,
      'time_zone' => '',
      'default_site_access' => 'en',
      'extensions' => 
      array (
      ),
      'available_site_access' =>
      array (
        0 => 'en',
        1 => 'es',
        2 => 'pt',
        3 => 'de',
        4 => 'ru',
        5 => 'it',
        6 => 'fr',
        7 => 'site_admin',
      ),
    ),
    'default_url' => 
    array (
      'module' => 'chat',
      'view' => 'startchat',
    ),
    'chat' => 
    array (
      'online_timeout' => 300,
      'back_office_sinterval' => 10,
      'chat_message_sinterval' => 3.5,
      'check_for_operator_msg' => 10,
      'new_chat_sound_enabled' => true,
      'new_message_sound_admin_enabled' => true,
      'new_message_sound_user_enabled' => true,
    ),
    'memecache' => 
    array (
      'servers' => 
      array (
        0 => 
        array (
          'host' => '127.0.0.1',
          'port' => '11211',
          'weight' => 1,
        ),
      ),
    ),
    'redis' => 
    array (
      'server' => 
      array (
        'host' => 'localhost',
        'port' => 6379,
      ),
    ),
    'db' => 
    array (
      'host' => '{{mysqlhost}}',
      'user' => '{{user}}',
      'password' => '{{pass}}',
      'database' => '{{db}}',
      'port' => {{port}},
      'use_slaves' => false,
      'db_slaves' => 
      array (
        0 => 
        array (
          'host' => '',
          'user' => '',
          'port' => 3306,
          'password' => '',
          'database' => '',
        ),
      ),
    ),
    'site_access_options' => 
    array (
      'en' =>
      array (
        'locale' => 'en_EN',
        'content_language' => 'en',
        'dir_language' => 'ltr',
        'default_url' => 
        array (
          'module' => 'chat',
          'view' => 'startchat',
        ),
        'theme' => 
        array (
          0 => 'customtheme',
          1 => 'defaulttheme',
        ),
      ),
      'es' =>
      array (
        'locale' => 'es_MX',
        'content_language' => 'es',
        'dir_language' => 'ltr',
        'title' => '',
        'description' => '',
        'theme' => 
        array (
          0 => 'customtheme',
          1 => 'defaulttheme',
        ),
        'default_url' => 
        array (
          'module' => 'chat',
          'view' => 'startchat',
        ),
      ),
      'pt' =>
      array (
        'locale' => 'pt_PT',
        'content_language' => 'pt',
        'dir_language' => 'ltr',
        'title' => '',
        'description' => '',
        'theme' => 
        array (
          0 => 'customtheme',
          1 => 'defaulttheme',
        ),
        'default_url' => 
        array (
          'module' => 'chat',
          'view' => 'startchat',
        ),
      ),
      'de' =>
      array (
        'locale' => 'de_DE',
        'content_language' => 'de',
        'dir_language' => 'ltr',
        'title' => '',
        'description' => '',
        'theme' => 
        array (
          0 => 'customtheme',
          1 => 'defaulttheme',
        ),
        'default_url' => 
        array (
          'module' => 'chat',
          'view' => 'startchat',
        ),
      ),
      'ru' =>
      array (
        'locale' => 'ru_RU',
        'content_language' => 'ru',
        'dir_language' => 'ltr',
        'title' => '',
        'description' => '',
        'theme' => 
        array (
          0 => 'customtheme',
          1 => 'defaulttheme',
        ),
        'default_url' => 
        array (
          'module' => 'chat',
          'view' => 'startchat',
        ),
      ),
      'it' =>
      array (
        'locale' => 'it_IT',
        'content_language' => 'it',
        'dir_language' => 'ltr',
        'title' => '',
        'description' => '',
        'theme' => 
        array (
          0 => 'customtheme',
          1 => 'defaulttheme',
        ),
        'default_url' => 
        array (
          'module' => 'chat',
          'view' => 'startchat',
        ),
      ),
      'fr' =>
      array (
        'locale' => 'fr_FR',
        'content_language' => 'fr',
        'dir_language' => 'ltr',
        'title' => '',
        'description' => '',
        'theme' => 
        array (
          0 => 'customtheme',
          1 => 'defaulttheme',
        ),
        'default_url' => 
        array (
          'module' => 'chat',
          'view' => 'startchat',
        ),
      ),
      'site_admin' => 
      array (
        'locale' => 'en_EN',
        'content_language' => 'en',
        'dir_language' => 'ltr',
        'theme' => 
        array (
          0 => 'customtheme',
          1 => 'defaulttheme',
        ),
        'login_pagelayout' => 'login',
        'default_url' => 
        array (
          'module' => 'front',
          'view' => 'default',
        ),
      ),
    ),
    'cacheEngine' => 
    array (
      'cache_global_key' => 'global_cache_key',
      'className' => false,
    ),
  ),
  'comments' => NULL,
);
?>