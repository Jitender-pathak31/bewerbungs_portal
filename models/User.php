<?php
namespace App\Models;

class User implements ModelInterface {
    private int $user_id;
    private string $first_name;
    private string $last_name;
    private string $email;
    private string $password;
    private ?string $phone;
    private  ?string $resume;
    private string $role;

    public function __construct(array $data = [])
    {
        $this->user_id = $data['user_id'] ?? 0;
        $this->first_name = $data['first_name'] ?? '';
        $this->last_name = $data['last_name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->phone = $data['phone'] ?? null;
        $this->resume = $data['resume'] ?? null;
        $this->role = $data['role'] ?? 'Applicant';

    }

    public function save(\PDO $db): bool
    {
        $stmt = $db->prepare(
            "INSERT INTO users (first_name, last_name, email, password, phone, resume, role)
            VALUES(:first_name, :last_name, :email, :password, :phone, :resume, :role)"
        );
        $this->password = $this->hashPassword($this->password);
        return $stmt->execute([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email'=> $this->email,
            'password' => $this->password,
            'phone' => $this->phone,
            'resume' => $this->resume,
            'role' => $this->role
        ]);

    }

    public static function findById (int $id, \PDO $db): ? self
    {
        $stmt = $db->prepare(
            "SELECT * FROM users WHERE user_id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $data ? new self($data): null;

    }

    public function update(array $data, \PDO $db): bool
    {
        $stmt = $db->prepare(
            "UPDATE users SET first_name = :first_name, last_name = :last_name,
                 email = :email WHERE user_id = :id");
        return $stmt->execute([
            'id' => $this->user_id,
            'first_name' => $data['first_name'] ?? $this->first_name,
            'last_name' => $data['last_name'] ?? $this->last_name,
            'email' => $data['email'] ?? $this->email
        ]);

    }

    public function delete(\PDO $db): bool
    {
        $stmt = $db->prepare("DELETE FROM users WHERE user_id = :id");
        return $stmt->execute(['id' => $this->user_id]);

    }

    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);

    }

    public function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;

    }

    // Getter function
    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function getPhone(): int
    {
        return $this->phone;
    }
    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getResume()
    {
        return $this->resume;
    }
}