<?php

namespace App\Models;

use Dotenv\Store\StringStore;

class Company implements ModelInterface
{
    private int $company_id;
    private string $name;
    private ?string $description;
    private ?string $location;
    private ?string $website;

    public function __construct(array $data = [])
    {
        $this->company_id = $data['company_id'] ?? 0;
        $this->name = $data['name'] ?? '';
        $this->description = $data['description'] ?? null;
        $this->location = $data['location'] ?? null;
        $this->website = $data['website'] ?? null;
    }

    public function save(\PDO $db): bool
    {
        $stmt = $db->prepare(
            "INSERT INTO companies (name, description, location, website) 
             VALUES (:name, :description, :location, :website)"
        );
        return $stmt->execute([
            'name' => $this->name,
            'description' => $this->description,
            'location' => $this->location,
            'website' => $this->website
        ]);
    }

    public static function findById(int $id, \PDO $db): ?self
    {
        $stmt = $db->prepare("SELECT * FROM companies WHERE company_id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $data ? new self($data) : null;
    }

    public function update(array $data, \PDO $db): bool
    {
        $stmt = $db->prepare(
            "UPDATE companies SET name = :name, description = :description, location = :location, website = :website 
             WHERE company_id = :id"
        );
        return $stmt->execute([
            'id' => $this->company_id,
            'name' => $data['name'] ?? $this->name,
            'description' => $data['description'] ?? $this->description,
            'location' => $data['location'] ?? $this->location,
            'website' => $data['website'] ?? $this->website
        ]);
    }

    public function delete(\PDO $db): bool
    {
        $stmt = $db->prepare("DELETE FROM companies WHERE company_id = :id");
        return $stmt->execute(['id' => $this->company_id]);
    }

    public function getCompanyId(): int
    {
        return $this->company_id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getWebsite(): String
    {
        return $this->website;
    }

    public function getLocation(): string
    {
        return $this->location;
    }
}