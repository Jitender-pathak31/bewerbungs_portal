<?php

namespace App\Repositories;
use App\Models\ModelInterface;

interface RepositoryInterface
{
    public function save(ModelInterface $model, \PDO $db): bool;
    public function findById(int $id, \PDO $db): ? ModelInterface;
    public function update(int $id, array $data, \PDO $db): bool;
    public function delete(int $id, \PDO $db): bool;

}