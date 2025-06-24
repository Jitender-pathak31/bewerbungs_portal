<?php
namespace App\Repositories;

use App\Models\User;
use App\Models\ModelInterface;

class UserRepository implements RepositoryInterface{

    public function save(ModelInterface $model, \PDO $db): bool
    {
        if(!$model instanceof User){
            throw new \InvalidArgumentException('Model must be an instance of User');
        }
        return $model->save($db);
    }

    public function findById(int $id, \PDO $db): ?ModelInterface
    {
        return User::findById($id, $db);
    }

    public function update(int $id, array $data, \PDO $db): bool
    {
        $model = $this->findById($id, $db);
        if(!$model){
            return false;
        }
        return $model->update($data, $db);
    }

    public function delete(int $id, \PDO $db): bool
    {
        $model = $this->findById($id, $db);
        if(!$model){
            return false;
        }
        return $model->delete($db);
    }
}