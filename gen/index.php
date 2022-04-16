<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Psr\Cache\InvalidArgumentException;
use RandCache\RandFileCachePool;

$rc = new RandFileCachePool();

// Поиграться с максимальной границей случайного числа
// $rc->setMaxGenerateNumber(40000);

$id = $_GET['id'];
if (!$id) {
    $id = sha1(microtime(true) . '_' . rand(1000, 1e6));
}

try {
    $item = $rc->getItem($id);
    $rc->save($item);

} catch (InvalidArgumentException $e) {
    http_response_code(500);
    print_r($e->getMessage());
    die;
}

http_response_code(200);
print_r($item->toString());