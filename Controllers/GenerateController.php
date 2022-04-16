<?php

namespace Controllers;

use Psr\Cache\InvalidArgumentException;
use RandCache\RandFileCachePool;
use Response\Response;

class GenerateController
{
    public function index(): Response
    {
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
            return new Response($e->getMessage(), Response::HTTP_SERVER_ERROR);
        }

        return new Response($item->toString());
    }
}