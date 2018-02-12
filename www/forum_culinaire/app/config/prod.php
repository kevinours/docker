<?php

// Doctrine (db)
$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'charset'  => 'utf8',
    'host'     => 'db',
    'port'     => '3306',
    'dbname'   => 'forum_culinaire',
    'user'     => 'user',
    'password' => 'mdp',
);
