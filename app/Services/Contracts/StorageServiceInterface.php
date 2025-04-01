<?php

namespace App\Services\Contracts;

interface StorageServiceInterface
{
    public function upload(string $bucket, string $path, $file): string;
    public function getUrl(string $bucket, string $path): string;
    public function delete(string $bucket, string $path): bool;
}