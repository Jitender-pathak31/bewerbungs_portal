<?php

namespace App\Models;

class Skill implements ModelInterface{
    private int $skill_id;
    private string $name;

    public function __construct(array $data = [])
    {
        $this->skill_id = $data['skill_id'] ?? 0;
        $this->name = $data['name'] ?? '';
    }

    public function save(\PDO $db): bool
    {
        $stmt = $db->prepare("INSERT INTO skills (name) VALUES(:name)");
        return $stmt->execute(['name' => $this->name]);

    }

    public static function findById(int $id, \PDO $db): ? self
    {
        $stmt = $db->prepare("SELECT * FROM skills WHERE skill_id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $data ? new self($data): null;

    }

    public function update(array $data, \PDO $db): bool
    {
        $stmt = $db->prepare("UPDATE skills SET name = :name WHERE skill_id = :id");
        return $stmt->execute([
            'id' => $this->skill_id,
            'name' => $data['name'] ?? $this->name
        ]);

    }

    public function delete(\PDO $db): bool
    {
        $stmt = $db->prepare("DELETE FROM skills WHERE skills_id = :id");
        return $stmt->execute(['id' => $this->skill_id]);

    }

    // Getter function
    public function getskillId(): int
    {
        return $this->skill_id;
    }
}