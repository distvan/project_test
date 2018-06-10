<?php
require __DIR__ . '/vendor/autoload.php';

use Slim\App;
use Slim\Views\PhpRenderer;
use Distvan\Controller;

###################################### Config

$config = require 'config.php';
$app = new App($config);

######################################  Dependencies

$container = $app->getContainer();

$container['Distvan\Controller'] = function($c){
    return new Controller($c->get('db'), $c->get('view'));
};

$container['db'] = function($c){
    $mysqli = new mysqli(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'), 'project_test');
    
    return $mysqli;
};

$container['view'] = new PhpRenderer('templates');

###################################### Routes

$app->get('/populate_db', 'Distvan\Controller:populateDb');
$app->get('/', 'Distvan\Controller:showTable');
$app->get('/api/books/{filter_name}/{filter_only_adult}/{page}', 'Distvan\Controller:getBooks');

$app->run();