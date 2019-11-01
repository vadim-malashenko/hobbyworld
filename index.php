<?php

define ('HOBBYWORLD_APP_DIR',  __DIR__);

require HOBBYWORLD_APP_DIR . '/src/App.php';

spl_autoload_register (['Hobbyworld\App', 'autoload']);

new Hobbyworld\App (
    [
        [
            'articles',
            'GET',
            '^\/articles\/\d+$',
            'ArticlesController',
            'articles'
        ],
        [
            'article',
            'GET',
            '^\/article\/\d+$',
            'ArticlesController',
            'article'
        ],
        [
            'update',
            'POST',
            '^\/update\/*$',
            'ArticlesController',
            'update'
        ],
        [
            'index',
            'GET',
            '^\/*$',
            'AppController',
            'index'
        ]
    ],
    [
        'articles_per_page' => 5,
        'sqlite3_db_file' => __DIR__ . '/sqlite3.db'
    ]
);