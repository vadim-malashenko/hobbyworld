<?php

namespace Hobbyworld;

require __DIR__ . '/src/App.php';
spl_autoload_register (['Hobbyworld\App', 'autoload']);


echo App
    ::create ([
        'root_dir' => __DIR__,
        'index_file' => __DIR__ . '/assets/index.html',
        'db_file' => __DIR__ . '/articles.sqlite3.db',
        'limit' => 5
    ])
    ->addRoutes ([
        'POST ^\/update\/*$',
        'GET ^\/item\/\d+$',
        'GET ^\/page\/\d+$',
        'GET ^\/*$'
    ])
    ->createController ()
    ->createResponse ()
    ->getBody ();

exit (0);