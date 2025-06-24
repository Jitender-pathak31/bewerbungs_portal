<?php

namespace App\Repositories;

use App\Models\Skill;
use App\Models\ModelInterface;

class SkillRepository implements RepositoryInterface{
    public function save(ModelInterface $model, \PDO $db): bool
    {
        return $model->save($db);
    }

    public function findById(int $id, \PDO $db): ?ModelInterface
    {
        return skill::findById($id, $db);
    }

    public function update(int $id, array $data, \PDO $db):bool
    {
        $model = $this->findById($id, $db);
        return $model ? $model->update($data, $db) : false;
    }

    public function delete(int $id, \PDO $db): bool
    {
        $model = $this->findById($id, $db);
        return $model ? $model->delete($db) : false;
    }

    // function to add user skills
    public function addUserSkill(int $user_id, int $skill_id, \PDO $db): bool
    {
        $stmt = $db->prepare("INSERT INTO user_skills(user_id, skill_id)
                                    VALUES (:user_id, :skill_id)");
        return $stmt->execute(['user_id' => $user_id, 'skill_id' => $skill_id]);
    }

    public function addJobSkill(int $job_id, int $skill_id, \PDO $db): bool
    {
        $stmt = $db->prepare("INSERT INTO job_skills(job_id, skill_id)
                                    VALUES (:job_id, :skill_id)");
        return $stmt->execute(['job_id' => $job_id, 'skill_id'=> $skill_id]);
    }

}