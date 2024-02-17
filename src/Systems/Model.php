<?php

namespace Src\Systems;

use Src\Configurations\Database;
use Src\Configurations\Environment;
use PDO;
use PDOException;
use Src\Helpers\ResponseHelper;

class Model
{
    /**
     * @var $pdo PHP Data Objects
     * @var $statement Statement
     * @var array $queries Kumpulan query-query SQL
     * @var array $statements Kumpulan statement-statement SQL
     * @var array $result Kumpulan hasil query
     * @var array $parameterType Kumpulan parameter untuk PDO
     */
    private static
        $pdo = null,
        $statement = null,
        $queries = [],
        $statements = [],
        $parameterType = [
            'string' => PDO::PARAM_STR,
            'integer' => PDO::PARAM_INT,
            'null' => PDO::PARAM_NULL,
        ];

    private static function selectQuery($columns) {
        return 'SELECT ' . $columns;
    }

    private static function fromQuery($table) {
        return 'FROM ' . $table;
    }

    private static function whereQuery($column, $operator = '=') {
        return 'WHERE ' . $column . ' ' . $operator . ' :' . $column;
    }

    private static function limitQuery($limit) {
        return 'LIMIT ' . $limit;
    }

    /**
     * Mendapatkan 1 data berdasarkan isi dari suatu kolom
     * 
     * @param string $table Nama tabel yang ingin didapatkan
     * @param string $select Nama-nama kolom yang ingin didapatkan
     * @param string $column Kolom yang ingin dicari
     * @param string $value Isi kolom yang ingin dicari
     * @param string $operator Kondisi antara kolom dan isi
     * 
     * @return array 1 Data yang diambil
     */
    static function getOne($table, $select, $column, $value, $operator = '=') {
        self::setQuery(
            self::selectQuery($select) . ' ' . self::fromQuery($table)
            . ' ' . self::whereQuery($column, $operator) . ' ' .
            self::limitQuery(1)
        );

        return self::first([
            $column => $value
        ]);
    }

    /**
     * Mendapatkan 1 data berdasarkan isi dari suatu kolom tanpa isi dari suatu kolom
     * 
     * @param string $table Nama tabel yang ingin didapatkan
     * @param string $columns Nama-nama kolom yang ingin didapatkan
     * @param array $condition Kondisi yang ingin diambil (Kolom dan Isi).
     * @param array $exception Data yang ingin dikecualikan (Kolom dan Isi)
     * 
     * @return array 1 Data yang diambil
     */
    static function getOneWithException($table, $columns, $condition, $exception) {
        self::setQuery(
            self::selectQuery($columns) . ' ' . self::fromQuery($table) .
            ' ' . self::whereQuery($condition['column']) . ' AND ' .
            $exception['column'] . ' != :' . $exception['column'] .
            ' ' . self::limitQuery(1)
        );

        return self::first([
            $condition['column'] => $condition['value'],
            $exception['column'] => $exception['value']
        ]);
    }

    /**
     * Untuk memasukkan 1 data ke tabel
     * 
     * @param string $table Nama tabel
     * @param array $columnsAndValues Nama-nama kolom beserta isinya
     * 
     * @return integer Jika benar akan dikembalikan 1
     */
    static function insert($table, $columnsAndValues) {
        $columns = '';
        $values = '';

        foreach ($columnsAndValues as $column => $value) {
            $columns .= $column . ',';
            $values .= ':' . $column . ',';
        }

        self::setQuery(
            'INSERT INTO ' . $table . ' ('
                . substr($columns, 0, -1)
            . ') VALUES('
                . substr($values, 0, -1)
            . ')'
        );

        return self::execute($columnsAndValues);
    }

    /**
     * Untuk menghapus 1 data dalam tabel berdasarkan kolom id
     * 
     * @param string $table Nama tabel
     * @param string $primaryKey Isi kolom id (primary key)
     * 
     * @return integer Jika benar akan dikembalikan 1
     */
    static function delete($table, $primaryKey) {
        self::setQuery('
            DELETE
            FROM '
                . $table . ' '
            . self::whereQuery('id') .
            ' ' . self::limitQuery(1)
        );

        return self::execute([
            'id' => $primaryKey
        ]);
    }

    /**
     * Untuk mengubah 1 data tabel berdasarkan isi kolom id
     * 
     * @param string $table Nama tabel
     * @param array $columnsAndValues Kolom dan isinya
     * @param string $primaryKey Isi kolom id (primary key)
     * 
     * @return integer Jika benar akan dikembalikan 1
     */
    static function update($table, $columnsAndValues, $primaryKey) {
        $setQuery = '';

        foreach ($columnsAndValues as $column => $value) {
            $setQuery .= $column . ' = :' . $column . ',';
        }

        self::setQuery('
            UPDATE '
                . $table .
            ' SET '
                . substr($setQuery, 0, -1) . ' ' . self::whereQuery(
                    'id'
                ) .
            ' ' . self::limitQuery(1)
        );

        $columnsAndValues['id'] = $primaryKey;

        return self::execute($columnsAndValues);
    }

    /**
     * Untuk mendapatkan data berupa multidimensional array
     * 
     * @param array $params Isi-isi yang ingin dibinding
     * 
     * @return array Data array kosong atau berisi
     */
    protected static function getList($params = null) {
        if (!self::execute($params)) {
            return false;
        }

        $result = self::$statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    /**
     * Untuk mendapatkan data sebanyak 1 saja
     * 
     * @param array $params Isi-isi yang ingin dibinding
     * 
     * @return array Data array berupa key sebagai kolom dan value berupa isi kolom dari tabel.
     *               Berisi NULL jika tidak ada data yang didapatkan.
     */
    protected static function first($params = null) {
        if (!self::execute($params)) {
            return false;
        }
        
        $result = self::$statement->fetch(PDO::FETCH_ASSOC);

        return $result ? $result : null;
    }

    /**
     * Untuk mendapatkan total baris
     * 
     * @param string $column Nama kolom yang dibuat sebagai penampung jumlah baris
     * 
     * @return int Total baris
     */
    protected static function count($column = 'total') {
        if (!self::execute()) {
            return false;
        }

        $result = self::$statement->fetch(PDO::FETCH_ASSOC);

        return $result[$column];
    }

    /**
     * Untuk menyiapkan query
     * 
     * @param string $query Query SQL
     * 
     * @return void
     */
    protected static function setQuery($query) {
        self::$statement = self::$pdo->prepare($query);
    }

    /**
     * Mengikat parameter ke nama variabel yang ditentukan
     * 
     * @param string $key Nama variabel
     * @param $value Isi variabel
     * @param string $type Tipe data isi
     * 
     * @return void
     */
    protected static function setParameter($key, $value, $type = 'string') {
        self::$statement->bindParam($key, $value, self::$parameterType[$type]);
    }

    /**
     * Menjalankan perintah yang telah disiapkan
     * 
     * @return integer
     */
    protected static function execute($params = null) {
        try {
            self::$statement->execute($params);
        } catch (PDOException $error) {
            self::errorHandler($error);
            return false;
        }

        return true;
    }

    /**
     * Menjalankan perintah yang telah disiapkan dan menghitung jumlah baris yang tereksekusi
     * 
     * @return array
     */
    protected static function executeAndCountRow() {
        if (!self::execute()) {
            return false;
        }

        return self::$statement->rowCount();
    }

    protected static function getExecutedRow() {
        if (!self::execute()) {
            return false;
        }

        $result = self::$statement->rowCount();

        return $result;
    }

    protected static function beginTransaction() {
        self::$pdo->beginTransaction();
    }

    protected static function commit() {
        $success = 1;

        try {
            self::$pdo->commit();
        } catch (PDOException $error) {
            $success = 0;

            self::$pdo->rollBack();

            self::errorHandler($error);

            return false;
        }

        return $success;
    }

    protected static function setParameterByQuery($number, $key, $value, $type = 'string') {
        self::$statements[$number]->bindParam($key, $value, self::$parameterType[$type]);
    }

    protected static function getListByNumber($number) {
        if (self::$statements[$number]->execute()) {
            return self::$statements[$number]->fetchAll(PDO::FETCH_ASSOC);            
        }

        return [];
    }

    protected static function getOneByNumber($number) {
        if (self::$statements[$number]->execute()) {
            return self::$statements[$number]->fetch(PDO::FETCH_ASSOC);
        }

        return null;
    }

    protected static function setQueryByNumber($query, $number) {
        self::$queries[$number] = $query;

        self::$statements[$number] = self::$pdo->prepare(self::$queries[$number]);
    }

    protected static function executeByNumber($number, $bind = null) {
        try {
            self::$statements[$number]->execute($bind);
        } catch (PDOException $error) {
            self::$pdo->rollback();

            self::errorHandler($error);

            return false;
        }

        return true;
    }

    static function getConnection() {
        // Buat koneksi
        self::$pdo = new PDO(
            'mysql:host=' . Database::$host
            . ';dbname=' . Database::$databaseName,
            Database::$username,
            Database::$password
        );

        // Atur mode error
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    private static function errorHandler($error) {
        if (!Environment::$production) {
            var_dump($error);
            die;
        }
    }
}
