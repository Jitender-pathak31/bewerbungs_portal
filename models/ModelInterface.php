<?php
namespace App\Models;

interface ModelInterface{
    public function save(\PDO $db): bool;
    public static function findById(int $id, \PDO $db): ? self;
    public function update(array $data, \PDO $db): bool;
    public function delete(\PDO $db): bool;
}
