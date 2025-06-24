<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\ModelInterface;

class CompanyRepository implements RepositoryInterface
{
    public function save(ModelInterface $model, \PDO $db): bool
    {
        if (!$model instanceof Company) {
            throw new \InvalidArgumentException('Model must be an instance of Company');
        }
        return $model->save($db);
    }

    public function findById(int $id, \PDO $db): ?ModelInterface
    {
        return Company::findById($id, $db);
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