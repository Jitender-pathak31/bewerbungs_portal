<?php
namespace App\Repositories;

use App\Models\Application;
use App\Models\ModelInterface;

class ApplicationRepository implements RepositoryInterface {
    public function save(ModelInterface $model, \PDO $db): bool {
        if (!$model instanceof Application) {
            throw new \InvalidArgumentException('Model must be an instance of Application');
        }
        return $model->save($db);
    }

    public function findById(int $id, \PDO $db): ?ModelInterface {
        return Application::findById($id, $db);
    }

    public function update(int $id, array $data, \PDO $db): bool {
        $model = $this->findById($id, $db);
        if (!$model) {
            return false;
        }
        $fields = [];
        $params = [':id' => $id];
        if (isset($data['status'])) {
            $fields[] = 'status = :status';
            $params[':status'] = $data['status'];
        }
        if (empty($fields)) {
            return false;
        }
        $sql = "UPDATE applications SET " . implode(', ', $fields) . " WHERE application_id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id, \PDO $db): bool {
        $model = $this->findById($id, $db);
        if (!$model) {
            return false;
        }
        return $model->delete($db);
    }
}