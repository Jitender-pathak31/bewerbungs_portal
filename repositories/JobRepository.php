<?php

namespace App\Repositories;

use App\Models\Job;
use App\Models\ModelInterface;

class JobRepository implements RepositoryInterface
{
    public function save(ModelInterface $model, \PDO $db): bool
    {
        if (!$model instanceof Job) {
            throw new \InvalidArgumentException('Model must be an instance of Job');
        }
        return $model->save($db);
    }

    public function findById(int $id, \PDO $db): ?ModelInterface
    {
        return Job::findById($id, $db);
    }

    public function update(int $id, array $data, \PDO $db): bool
    {
        $model = $this->findById($id, $db);
        if (!$model) {
            return false;
        }
        return $model->update($data, $db);
    }

    public function delete(int $id, \PDO $db): bool
    {
        $model = $this->findById($id, $db);
        if (!$model) {
            return false;
        }
        return $model->delete($db);
    }
}