<?php
namespace App\Services;

class FileService{
    private string $uploadDir;

    public function __construct(string $uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }

    public function uploadResume(array $file): string
    {
        $filename = uniqid(). '_'.basename($file['name']);
        $path = $this->uploadDir. '/'. $filename;
        if(move_uploaded_file($file['temp_name'], $path)){
            return $path;
        }
        throw new \Exception('File upload failed');
    }
}