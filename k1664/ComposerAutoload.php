<?php

//第 1 行代码会为 vendor 目录下的所有包添加 autoload

$loader = require_once __DIR__.'/vendor/autoload.php';
//print_r($loader);die;
 
// reg my bundles 第 2 行代码则注册了我自己的包(bundles)目录，我只需要在项目目录创建一个 bundles 文件夹
$loader->add('Bundles', __DIR__ . '/vendor');

?>