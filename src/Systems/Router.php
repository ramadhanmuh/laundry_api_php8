<?php

namespace Src\Systems;

use Src\Configurations\Environment;
use Src\Routes\AllRoutes;

class Router
{
    /**
     * @var array $segments Kumpulan segmen dari rute yang dituju oleh pengguna
     * @var string $fullPath Rute yang dituju oleh pengguna
     * @var array $allRoutes Rute yang ada dalam aplikasi ini
     * @var array $result Objek-objek dari rute yang dituju oleh pengguna
     */
    static $segments,
        $fullPath,
        $allRoutes,
        $result = [
            'controller' => 0,
            'middleware' => []
    ];

    /**
     * Untuk mendapatkan objek-objek yang perlu dijalankan dalam suatu rute
     * 
     * @return array Mengambalikan controller, function, middleware
     */
    static function getObjects() : array {
        // Mendapatkan protokol HTTP (http atau https)
        $url = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
   
        // Mendapatkan dan menambahkan nama host (domain)   
        $url .= $_SERVER['HTTP_HOST'];   

        // Mendapatkan dan menambahkan path atau URI yang diminta 
        $url .= $_SERVER['REQUEST_URI'];

        // Mendapatkan path yang dituju
        self::$fullPath = explode(Environment::$baseURL, $url)[1];

        // Memisahkan parameter dengan path
        self::$fullPath = explode('?', self::$fullPath)[0];

        // Memecah path menjadi per segmen
        self::$segments = explode('/', self::$fullPath);

        // Mendapatkan semua rute yang dibuat
        self::$allRoutes = AllRoutes::getList();

        // Atur berkas-berkas yang dijalankan berdasarkan rute yang dituju
        self::setRoute(self::$allRoutes);

        return self::$result;
    }

    /**
     * Untuk mengatur objek-objek yang akan ada sesuai rute
     * 
     * @param array $routes Rute-rute yang ada
     * @param int $segmentKey Urutan segmen dari kumpulan segmen rute
     * 
     * @return void
     */
    private static function setRoute($routes, $segmentKey = 0) : void {
        foreach ($routes as $route) {
            // Jika urutan segmen rute ada yang rute yang dituju
            if (array_key_exists($segmentKey, self::$segments)) {
                // Jika rute yang didaftarkan sama dengan segmen rute yang dituju
                if ($route['path'] === self::$segments[$segmentKey]) {
                    // Jika rute yang tersedia ditemukan
                    if (self::findRoute($route, $segmentKey)) {
                        break;
                    }
                }

                // Jika segmen rute yang tersedia dengan nilai bebas
                if ($route['path'] === '$') {
                    // Jika rute yang tersedia ditemukan
                    if (self::findRoute($route, $segmentKey)) {
                        break;
                    }
                }
            } else {
                // Jika rute yang tersedia adalah string kosong
                if ($route['path'] === '') {
                    // Jika rute yang tersedia ditemukan
                    if (self::findRoute($route, $segmentKey)) {
                        break;
                    }
                }

                // Jika segmen rute yang tersedia dengan nilai bebas
                if ($route['path'] === '$') {
                    // Jika rute yang tersedia ditemukan
                    if (self::findRoute($route, $segmentKey)) {
                        break;
                    }
                }
            }
        }
    }
    
    /**
     * Untuk menemukan rute yang didaftarkan yang cocok berdasarkan rute yang dituju
     * 
     * @param array $route Rute yang didaftarkan
     * @param int $segmentKey Segment dari suatu rute
     * 
     * @return int Berisi 0 jika tidak ditemukan dan berisi 1 jika ditemukan
     */
    private static function findRoute($route, $segmentKey) : int {
        $nextSegmentKey = $segmentKey + 1;
        
        if (array_key_exists('list', $route) && (!array_key_exists('method', $route) || $route['method'] !== $_SERVER['REQUEST_METHOD'])) {
            self::setRoute($route['list'], $segmentKey + 1);
            return 1;
        }

        if ($route['method'] === $_SERVER['REQUEST_METHOD'] && !array_key_exists($nextSegmentKey, self::$segments)) {
            self::setTheFiles($route);
            return 1;
        }

        return 0;
    }

    /**
     * Untuk mengatur objek-objek untuk suatu rute
     * 
     * @param array $route Rute yang telah didaftarkan
     * 
     * @return void
     */
    private static function setTheFiles($route) : void {
        self::$result['controller'] = $route['controller'];
        self::$result['function'] = $route['function'];
        self::$result['middleware'] = $route['middleware'];
    }
}
