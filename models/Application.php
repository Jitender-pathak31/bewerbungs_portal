<?php
namespace App\Models;

class Application implements ModelInterface {
    private int $application_id;
    private int $user_id;
    private int $job_id;
    private string $status;
    private ?string $cover_letter;
    private ?string $application_date;

    public function __construct(array $data = []) {
        $this->application_id = isset($data['application_id']) ? (int)$data['application_id'] : 0;
        $this->user_id = isset($data['user_id']) ? (int)$data['user_id'] : 0;
        $this->job_id = isset($data['job_id']) ? (int)$data['job_id'] : 0;
        $this->status = $data['status'] ?? 'Submitted';
        $this->cover_letter = $data['cover_letter'] ?? null;
        $this->application_date = $data['application_date'] ?? null;
    }

    public function save(\PDO $db): bool {
        $stmt = $db->prepare(
            "INSERT INTO applications (user_id, job_id, status, cover_letter) 
             VALUES (:user_id, :job_id, :status, :cover_letter)"
        );
        $success = $stmt->execute([
            'user_id' => $this->user_id,
            'job_id' => $this->job_id,
            'status' => $this->status,
            'cover_letter' => $this->cover_letter
        ]);
        if ($success) {
            $this->application_id = (int)$db->lastInsertId();
        }
        return $success;
    }

    public static function findById(int $id, \PDO $db): ?self {
        $stmt = $db->prepare("SELECT * FROM applications WHERE application_id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $data ? new self($data) : null;
    }

    public function update(array $data, \PDO $db): bool {
        return false; // Handled by ApplicationRepository
    }

    public function delete(\PDO $db): bool {
        $stmt = $db->prepare("DELETE FROM applications WHERE application_id = :id");
        return $stmt->execute(['id' => $this->application_id]);
    }

    // Getters
    public function getApplicationId(): int {
        return $this->application_id;
    }
    public function getUserId(): int {
        return $this->user_id;
    }
    public function getJobId(): int {
        return $this->job_id;
    }
    public function getStatus(): string {
        return $this->status;
    }
    public function getCoverLetter(): ?string {
        return $this->cover_letter;
    }
    public function getApplicationDate(): ?string {
        return $this->application_date;
    }
}