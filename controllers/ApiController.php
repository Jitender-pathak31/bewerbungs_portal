<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Job;
use App\Models\Application;
use App\Models\Skill;
use App\Repositories\UserRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\JobRepository;
use App\Repositories\ApplicationRepository;
use App\Repositories\SkillRepository;
use App\Services\FileService;

class ApiController {
    private UserRepository $userRepo;
    private CompanyRepository $companyRepo;
    private JobRepository $jobRepo;
    private ApplicationRepository $appRepo;
    private SkillRepository $skillRepo;
    private FileService $fileService;
    private \PDO $db;

    public function __construct(\PDO $db) {
        $this->db = $db;
        $this->userRepo = new UserRepository();
        $this->companyRepo = new CompanyRepository();
        $this->jobRepo = new JobRepository();
        $this->appRepo = new ApplicationRepository();
        $this->skillRepo = new SkillRepository();
        $uploadDir = __DIR__ . '/../uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }
        $this->fileService = new FileService($uploadDir);
    }

    public function handleRequest(string $method, string $path, array $data): array {
        try {
            // User CRUD
            if ($method === 'POST' && $path === '/users') {
                $user = new User($data);
                if (!$user->validateEmail($data['email'])) {
                    return ['status' => 'error', 'message' => 'Invalid email', 'code' => 400];
                }
                $this->userRepo->save($user, $this->db);
                return ['status' => 'success', 'data' => ['user_id' => $user->getUserId()]];
            } elseif ($method === 'GET' && preg_match('/^\/users\/(\d+)$/', $path, $matches)) {
                $user = $this->userRepo->findById((int)$matches[1], $this->db);
                if ($user) {
                    $userData = [
                        'user_id' => $user->getUserId(),
                        'first_name' => $user->getFirstName() ?? '',
                        'last_name' => $user->getLastName() ?? '',
                        'email' => $user->getEmail() ?? '',
                        'phone' => $user->getPhone() ?? '',
                        'role' => $user->getRole() ?? 'Applicant',
                        'resume' => $user->getResume() ?? null
                    ];
                    return ['status' => 'success', 'data' => $userData];
                }
                return ['status' => 'error', 'message' => 'User not found', 'code' => 404];
            } elseif ($method === 'GET' && $path === '/users') {
                $stmt = $this->db->query("SELECT user_id, first_name, last_name, email, phone, role, resume FROM users");
                $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                return ['status' => 'success', 'data' => $users];
            } elseif ($method === 'PUT' && preg_match('/^\/users\/(\d+)$/', $path, $matches)) {
                $success = $this->userRepo->update((int)$matches[1], $data, $this->db);
                return $success ? ['status' => 'success', 'message' => 'User updated'] : ['status' => 'error', 'message' => 'Update failed or user not found', 'code' => 404];
            } elseif ($method === 'DELETE' && preg_match('/^\/users\/(\d+)$/', $path, $matches)) {
                $success = $this->userRepo->delete((int)$matches[1], $this->db);
                return $success ? ['status' => 'success', 'message' => 'User deleted'] : ['status' => 'error', 'message' => 'Delete failed or user not found', 'code' => 404];
            }
            // Company CRUD
            if ($method === 'POST' && $path === '/companies') {
                $company = new Company($data);
                $this->companyRepo->save($company, $this->db);
                return ['status' => 'success', 'data' => ['company_id' => $company->getCompanyId()]];
            } elseif ($method === 'GET' && preg_match('/^\/companies\/(\d+)$/', $path, $matches)) {
                $company = $this->companyRepo->findById((int)$matches[1], $this->db);
                if ($company) {
                    $companyData = [
                        'company_id' => $company->getCompanyId(),
                        'name' => $company->getName() ?? '',
                        'description' => $company->getDescription() ?? '',
                        'location' => $company->getLocation() ?? '',
                        'website' => $company->getWebsite() ?? ''
                    ];
                    return ['status' => 'success', 'data' => $companyData];
                }
                return ['status' => 'error', 'message' => 'User not found', 'code' => 404];
            } elseif ($method === 'GET' && $path === '/companies') {
                $stmt = $this->db->query("SELECT * FROM companies");
                $companies = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                return ['status' => 'success', 'data' => $companies];
            } elseif ($method === 'PUT' && preg_match('/^\/companies\/(\d+)$/', $path, $matches)) {
                $success = $this->companyRepo->update((int)$matches[1], $data, $this->db);
                return $success ? ['status' => 'success', 'message' => 'Company updated'] : ['status' => 'error', 'message' => 'Update failed or Company not found', 'code' => 404];
            } elseif ($method === 'DELETE' && preg_match('/^\/companies\/(\d+)$/', $path, $matches)) {
                $success = $this->companyRepo->delete((int)$matches[1], $this->db);
                return $success ? ['status' => 'success', 'message' => 'Company deleted'] : ['status' => 'error', 'message' => 'Delete failed or Company not found', 'code' => 404];
            }
            // Job CRUD
            if ($method === 'POST' && $path === '/jobs') {
                $job = new Job($data);
                $this->jobRepo->save($job, $this->db);
                return ['status' => 'success', 'data' => ['job_id' => $job->getJobId()]];
            } elseif ($method === 'GET' && preg_match('/^\/jobs\/(\d+)$/', $path, $matches)) {
                $job = $this->jobRepo->findById((int)$matches[1], $this->db);
                if ($job) {
                    $jobData = [
                        'job_id' => $job->getJobId(),
                        'title' => $job->getTitle(),
                        'description' => $job->getDescription(),
                        'company_id' => $job->getCompanyId(),
                        'location' => $job->getLocation(),
                        'salary' => $job->getSalary(),
                        'posted_date' => $job->getPostedDate(),
                        'status' => $job->getStatus(),
                        'recruiter_id' => $job->getRecruiterId(),
                    ];
                    return ['status' => 'success', 'data' => $jobData];
                }
                return ['status' => 'error', 'message' => 'Job not found', 'code' => 404];
            } elseif ($method === 'GET' && $path === '/jobs') {
                $stmt = $this->db->query("SELECT * FROM jobs");
                $jobs = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                return ['status' => 'success', 'data' => $jobs];
            } elseif ($method === 'PUT' && preg_match('/^\/jobs\/(\d+)$/', $path, $matches)) {
                $success = $this->jobRepo->update((int)$matches[1], $data, $this->db);
                return $success ? ['status' => 'success', 'message' => 'Job updated'] : ['status' => 'error', 'message' => 'Update failed or Job not found', 'code' => 404];

            } elseif ($method === 'DELETE' && preg_match('/^\/jobs\/(\d+)$/', $path, $matches)) {
                $success = $this->jobRepo->delete((int)$matches[1], $this->db);
                return $success ? ['status' => 'success', 'message' => 'Job deleted'] : ['status' => 'error', 'message' => 'Delete failed or Job not found', 'code' => 404];
            }

            // Application CRUD
            if ($method === 'POST' && $path === '/applications') {
                $application = new Application($data);
                $this->appRepo->save($application, $this->db);
                return ['status' => 'success', 'data' => ['application_id' => $application->getApplicationId()]];
            } elseif ($method === 'GET' && preg_match('/^\/applications\/(\d+)$/', $path, $matches)) {
                $app = $this->appRepo->findById((int)$matches[1], $this->db);
                if ($app) {
                    $appData = [
                        'application_id' => $app->getApplicationId(),
                        'user_id' => $app->getUserId() ?? '',
                        'job_id' => $app->getJobId() ?? '',
                        'application_date' => $app->getApplicationDate() ?? '',
                        'status' => $app->getStatus() ?? '',
                        'cover_letter' => $app->getCoverLetter() ?? ''
                    ];
                    return ['status' => 'success', 'data' => $appData];
                }
                return ['status' => 'error', 'message' => 'Application not found', 'code' => 404];
            } elseif ($method === 'GET' && $path === '/applications' && isset($_GET['job_id'])) {
                $jobId = (int)$_GET['job_id'];
                $stmt = $this->db->prepare(
                    "SELECT a.application_id, a.status, 
                            CONCAT(u.first_name, ' ', u.last_name) AS user_name, 
                            j.title AS job_title
                     FROM applications a
                     JOIN users u ON a.user_id = u.user_id
                     JOIN jobs j ON a.job_id = j.job_id
                     WHERE a.job_id = :job_id"
                );
                $stmt->execute(['job_id' => $jobId]);
                $applications = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                return ['status' => 'success', 'data' => $applications];
            } elseif ($method === 'GET' && preg_match('/^\/applications\/count$/', $path) && isset($_GET['job_id'])) {
                $jobId = (int)$_GET['job_id'];
                $stmt = $this->db->prepare("SELECT COUNT(*) AS count FROM applications WHERE job_id = :job_id");
                $stmt->execute(['job_id' => $jobId]);
                $count = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];
                return ['status' => 'success', 'data' => ['count' => $count]];

            } elseif ($method === 'PUT' && preg_match('/^\/applications\/(\d+)$/', $path, $matches)) {
                $success = $this->appRepo->update((int)$matches[1], $data, $this->db);
                return $success ? ['status' => 'success', 'message' => 'Application updated'] : ['status' => 'error', 'message' => 'Update failed or application not found', 'code' => 404];
            }
            // Skill CRUD
            if ($method === 'POST' && $path === '/skills') {
                $skill = new Skill($data);
                $this->skillRepo->save($skill, $this->db);
                return ['status' => 'success', 'data' => ['skill_id' => $skill->getSkillId()]];

            } elseif ($method === 'GET' && preg_match('/^\/skills\/(\d+)$/', $path, $matches)) {
                $skill = $this->skillRepo->findById((int)$matches[1], $this->db);
                return $skill ? ['status' => 'success', 'data' => (array)$skill] : ['status' => 'error', 'message' => 'Skill not found', 'code' => 404];

            } elseif ($method === 'GET' && $path === '/skills') {
                $stmt = $this->db->query("SELECT * FROM skills");
                $skills = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                return ['status' => 'success', 'data' => $skills];

            } elseif ($method === 'PUT' && preg_match('/^\/skills\/(\d+)$/', $path, $matches)) {
                $this->skillRepo->update((int)$matches[1], $data, $this->db);
                return ['status' => 'success', 'message' => 'Skill updated'];

            } elseif ($method === 'DELETE' && preg_match('/^\/skills\/(\d+)$/', $path, $matches)) {
                $this->skillRepo->delete((int)$matches[1], $this->db);
                return ['status' => 'success', 'message' => 'Skill deleted'];

            } elseif ($method === 'POST' && $path === '/user_skills') {
                $this->skillRepo->addUserSkill($data['user_id'], $data['skill_id'], $this->db);
                return ['status' => 'success', 'message' => 'Skill added to user'];

            } elseif ($method === 'POST' && $path === '/job_skills') {
                $this->skillRepo->addJobSkill($data['job_id'], $data['skill_id'], $this->db);
                return ['status' => 'success', 'message' => 'Skill added to job'];
            }
            // File Upload
            if ($method === 'POST' && $path === '/upload_resume') {
                $path = $this->fileService->uploadResume($_FILES['resume']);
                return ['status' => 'success', 'data' => ['path' => $path]];
            }
            return ['status' => 'error', 'message' => 'Invalid route', 'code' => 404];
        } catch (\Exception $e) {
            error_log("API Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage(), 'code' => 500];
        }
    }
}