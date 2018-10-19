<?php
require_once 'autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

$serviceAccount = ServiceAccount::fromJsonFile('C:\xampp\htdocs\php-firebase\secrete\php-carekhun-65ae90d8b361.json');

$firebase = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->withDatabaseUri('https://php-carekhun.firebaseio.com/')
    ->create();

$database = $firebase->getDatabase();

$newPost = $database
    ->getReference('blog/posts')
    ->push([
        'title' => 'Post title',
        'body' => 'This should probably be longer.'
    ]);