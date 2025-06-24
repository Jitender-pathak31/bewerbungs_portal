<?php

namespace App\Models;

class Job implements ModelInterface
{
    private int $job_id;
    private string $title;
    private string $description;
    private int $company_id;
    private ?string $location;
    private ?float $salary;
    private string $posted_date;
    private string $status;
    private int $recruiter_id;

    public function __construct(array $data = [])
    {
        $this->job_id = $data['job_id'] ?? 0;
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->company_id = $data['company_id'] ?? 0;
        $this->location = $data['location'] ?? null;
        $this->salary = $data['salary'] ?? null;
        $this->posted_date = $data['posted_date'] ?? date('Y-m-d H:i:s');
        $this->status = $data['status'] ?? 'Open';
        $this->recruiter_id = $data['recruiter_id'] ?? 0;
    }

    public function save(\PDO $db): bool
    {
        $stmt = $db->prepare(
            "INSERT INTO jobs (title, description, company_id, location, salary, posted_date, status, recruiter_id) 
             VALUES (:title, :description, :company_id, :location, :salary, :posted_date, :status, :recruiter_id)"
        );
        return $stmt->execute([
            'title' => $this->title,
            'description' => $this->description,
            'company_id' => $this->company_id,
            'location' => $this->location,
            'salary' => $this->salary,
            'posted_date' => $this->posted_date,
            'status' => $this->status,
            'recruiter_id' => $this->recruiter_id
        ]);
    }

    public static function findById(int $id, \PDO $db): ?self
    {
        $stmt = $db->prepare("SELECT * FROM jobs WHERE job_id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $data ? new self($data) : null;
    }

    public function update(array $data, \PDO $db): bool
    {
        $stmt = $db->prepare(
            "UPDATE jobs SET title = :title, description = :description, location = :location, salary = :salary, status = :status 
             WHERE job_id = :id"
        );
        return $stmt->execute([
            'id' => $this->job_id,
            'title' => $data['title'] ?? $this->title,
            'description' => $data['description'] ?? $this->description,
            'location' => $data['location'] ?? $this->location,
            'salary' => $data['salary'] ?? $this->salary,
            'status' => $data['status'] ?? $this->status
        ]);
    }

    public function delete(\PDO $db): bool
    {
        $stmt = $db->prepare("DELETE FROM jobs WHERE job_id = :id");
        return $stmt->execute(['id' => $this->job_id]);
    }

    public function getJobId(): int
    {
        return $this->job_id;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function getCompanyId(): int
    {
        return $this->company_id;
    }
    public function getLocation(): string
    {
        return $this->location;
    }
    public function getSalary(): int
    {
        return $this->salary;
    }
    public function getPostedDate()
    {
        return $this->posted_date;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getRecruiterId(): int
    {
        return $this->recruiter_id;
    }

}
