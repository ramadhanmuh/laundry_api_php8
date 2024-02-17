<?php

namespace Src\Systems;

use Src\Configurations\Environment;
use Src\Helpers\ResponseHelper;
use Src\Systems\Router;

class Application
{
    protected $result;

    public function __construct() {
        $this->result = [
            'response' => [
                'status' => 'Error',
                'code' => 500,
                'message' => 'Server sedang bermasalah.',
                'data' => null,
                'time' => [
                    'format' => 'UNIX',
                    'zone' => Environment::$timezone
                ]
                ],
            'type' => 'Content-Type: application/json; charset=utf-8',
            'status' => 0
        ];
    }

    /**
     * Untuk memulai proses aplikasi
     * 
     * @return void
     */
    function run() {
        // Mengatur agar tidak dicache oleh pengguna
        header('Cache-Control: no-cache');

        // Jika aplikasi sudah dalam tahap siap digunakan
        if (Environment::$production) {
            error_reporting(0);

            try {
                $this->process();
            } catch (\Throwable $th) {
                $this->result = ResponseHelper::createInternalServerError($th);

                ResponseHelper::sendResponse($this->result['response'], $this->result['type']);
            }
        } else {
            $this->process();
        }
    }

    /**
     * Untuk memilih objek-objek yang perlu dijalankan dan menjalankan objek objek tersebut
     * 
     * @return void
     */
    private function process() {
        $routerResults = Router::getObjects();

        if ($routerResults['controller']) {
            $this->result['status'] = 1;
        } else {
            $this->result = ResponseHelper::createNotFound();
            ResponseHelper::sendResponse($this->result['response'], $this->result['type']);
            return;
        }

        Model::getConnection();

        if (!empty($routerResults['middleware'])) {
            foreach ($routerResults['middleware'] as $middlewareName) {
                $middleware = new $middlewareName;

                $this->result = $middleware->run();

                if (!$this->result['status']) {
                    break;
                }
            }
        }

        if ($this->result['status']) {
            $controller = new $routerResults['controller'];
    
            $this->result = $controller->{$routerResults['function']}();
        }
        
        ResponseHelper::sendResponse($this->result['response'], $this->result['type']);
    }
}
