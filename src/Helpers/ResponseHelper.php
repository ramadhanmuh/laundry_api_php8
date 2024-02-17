<?php

namespace Src\Helpers;

use Src\Configurations\Environment;

/**
 * Objek untuk membuat keterangan-keterangan dari proses yang dijalankan
 */
class ResponseHelper
{
    private static $jsonType = 'Content-Type: application/json; charset=utf-8',
                    $htmlType = 'Content-Type: text/html; charset=utf-8';

    /**
     * Untuk membuat format keterangan-keterangan respon
     * 
     * @param $status Keterangan status
     * @param int $code Kode respon HTTP
     * @param @message Pesan dari aplikasi
     * @param $data Data yang dikirimkan oleh aplikasi
     * @param string $type Tipe data yang dirimkan oleh aplikasi
     * 
     * @return array Keterangan-keterangan respon
     */
    private static function createFormat($status, $code, $message, $data, $type = ['Content-Type: application/json; charset=utf-8'], $result = 1) {
        return [
            'response' => [
                'status' => $status,
                'code' => $code,
                'message' => $message,
                'data' => $data,
                'time' => [
                    'format' => 'UNIX',
                    'zone' => Environment::$timezone
                ]
                ],
            'type' => $type,
            'status' => $result
        ];
    }

    /**
     * Untuk mengirimkan respon Bad Request (400)
     * 
     * @param array $errors Kumpulan error
     * 
     * @return array Keterangan-keterangan respon
     */
    static function createBadRequest($errors) {
        return self::createFormat('error', 400, $errors, null, [self::$jsonType], 0);
    }

    /**
     * Untuk mengirimkan respon OK (200)
     * 
     * @param array $data Kumpulan data
     * @param string $message Pesan yang dikirim
     * 
     * @return array Keterangan-keterangan respon
     */
    static function createOK($data, $message) {
        return self::createFormat('success', 200, $message, $data);
    }

    /**
     * Untuk mengirimkan respon OK (200) pada permintaan GET
     * 
     * @param array $data Kumpulan data
     * 
     * @return array Keterangan-keterangan respon
     */
    static function createSuccessGetData($data) {
        return self::createFormat('success', 200, 'Data berhasil didapatkan.', $data);
    }

    /**
     * Untuk mengirimkan respon OK (200) pada permintaan POST
     * 
     * @param array $data Kumpulan data
     * 
     * @return array Keterangan-keterangan respon
     */
    static function createSuccessCreateData($data) {
        return self::createFormat('success', 200, 'Data berhasil dibuat.', $data);
    }

    /**
     * Untuk mengirimkan respon OK (200) pada permintaan PUT
     * 
     * @param array $data Kumpulan data
     * 
     * @return array Keterangan-keterangan respon
     */
    static function createSuccessUpdateData($data) {
        return self::createFormat('success', 200, 'Data berhasil diubah', $data);
    }

    /**
     * Untuk mengirimkan respon OK (200) pada permintaan DELETE
     * 
     * @param array $data Kumpulan data
     * 
     * @return array Keterangan-keterangan respon
     */
    static function createSuccessDeleteData() {
        return self::createFormat('success', 200, 'Data berhasil dihapus', [
            'deletedAt' => time()
        ]);
    }

    /**
     * Untuk mengirimkan respon Unauthorized (401)
     * 
     * @param $message Pesan yang dikirim
     * 
     * @return array Keterangan-keterangan respon
     */
    static function createUnauthorized($message) {
        return self::createFormat('error', 401, $message, null, [self::$jsonType], 0);
    }

    /**
     * Untuk mengirimkan respon Internal Server Error (500)
     * 
     * @param array $message Pesan yang dikirim
     * 
     * @return array Keterangan-keterangan respon
     */
    static function createInternalServerError($message = 'Server sedang bermasalah.') {
        return self::createFormat('error', 500, $message, null, [self::$jsonType], 0);
    }

    /**
     * Untuk mengirimkan respon Not Found (404)
     * 
     * @return array Keterangan-keterangan respon
     */
    static function createNotFound() {
        return self::createFormat('error', 404, 'Halaman tidak ditemukan.', null, [self::$jsonType], 0);
    }

    /**
     * Untuk mengirimkan respon Too Many Request (429)
     * 
     * @return array Keterangan-keterangan respon
     */
    static function createTooManyRequest() {
        return self::createFormat('error', 429, 'Permintaan terlalu banyak.', null, [self::$jsonType], 0);
    }

    /**
     * Untuk mengirimkan respon Service Unavailble (503)
     * 
     * @return array Keterangan-keterangan respon
     */
    static function createServiceUnavailable() {
        return self::createFormat('error', 503, 'Layanan sedang tidak tersedia.', null, [self::$jsonType], 0);
    }

    /**
     * Untuk mengirimkan respon Forbidden (403)
     * 
     * @return array Keterangan-keterangan respon
     */
    static function createForbidden($message = 'Dilarang mengakses URL ini.') {
        return self::createFormat('error', 403, $message, null, [self::$jsonType], 0);
    }

    static function createFileResponse($filePath, $mime, $fileName) {
        return self::createFormat(
            'success', 200, 'Berhasil membuka berkas.', './' . $filePath,
            [
                'Content-Type: ' . $mime,
                'Content-Disposition: attachment; filename="' . $fileName . '"'
            ]
        );
    }

    /**
     * Untuk membuat respon ke pengguna
     * 
     * @param array $response Sebuah kumpulan data yang dikirim ke pengguna
     * @return void
     */
    static function sendResponse($response, $headers) {
        http_response_code($response['code']);

        foreach ($headers as $value) {
            header($value);
        }

        if (in_array(self::$jsonType, $headers)) {
            echo json_encode($response);
        } else {
            if (in_array(self::$htmlType, $headers)) {
                echo $response['message'];
            } else {
                readfile($response['data']);
            }
        }

    }
}
