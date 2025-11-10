<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Allowed image MIME types
     */
    const ALLOWED_IMAGE_MIMES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
    ];

    /**
     * Allowed document MIME types
     */
    const ALLOWED_DOCUMENT_MIMES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    /**
     * Maximum file size in KB (default: 5MB)
     */
    const MAX_FILE_SIZE = 5120; // 5MB in KB

    /**
     * Upload and validate an image file
     */
    public static function uploadImage(UploadedFile $file, string $folder = 'uploads', int $maxSize = self::MAX_FILE_SIZE): string
    {
        // Validate file
        self::validateFile($file, self::ALLOWED_IMAGE_MIMES, $maxSize);

        // Generate safe filename
        $filename = self::generateSafeFilename($file->getClientOriginalName());
        
        // Store file
        $path = $file->storeAs($folder, $filename, 'public');

        return $path;
    }

    /**
     * Upload and validate a document file
     */
    public static function uploadDocument(UploadedFile $file, string $folder = 'documents', int $maxSize = self::MAX_FILE_SIZE): string
    {
        // Validate file
        self::validateFile($file, array_merge(self::ALLOWED_IMAGE_MIMES, self::ALLOWED_DOCUMENT_MIMES), $maxSize);

        // Generate safe filename
        $filename = self::generateSafeFilename($file->getClientOriginalName());
        
        // Store file
        $path = $file->storeAs($folder, $filename, 'public');

        return $path;
    }

    /**
     * Validate file
     */
    protected static function validateFile(UploadedFile $file, array $allowedMimes, int $maxSize): void
    {
        // Check file size (in KB)
        $fileSizeKB = $file->getSize() / 1024;
        if ($fileSizeKB > $maxSize) {
            throw new \Exception("Dosya boyutu çok büyük. Maksimum boyut: {$maxSize}KB");
        }

        // Check MIME type
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, $allowedMimes)) {
            throw new \Exception("Dosya tipi desteklenmiyor. İzin verilen tipler: " . implode(', ', $allowedMimes));
        }

        // Check file extension (double check)
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = self::getAllowedExtensions($allowedMimes);
        if (!in_array($extension, $allowedExtensions)) {
            throw new \Exception("Dosya uzantısı desteklenmiyor. İzin verilen uzantılar: " . implode(', ', $allowedExtensions));
        }
    }

    /**
     * Generate safe filename
     */
    protected static function generateSafeFilename(string $originalName): string
    {
        // Get extension
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        
        // Generate safe name
        $name = pathinfo($originalName, PATHINFO_FILENAME);
        $name = Str::slug($name);
        $name = preg_replace('/[^a-z0-9-]/', '', $name);
        
        // Add timestamp to prevent collisions
        $timestamp = now()->format('YmdHis');
        
        return $name . '-' . $timestamp . '.' . strtolower($extension);
    }

    /**
     * Get allowed extensions from MIME types
     */
    protected static function getAllowedExtensions(array $mimeTypes): array
    {
        $extensions = [];
        
        $mimeToExt = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        ];

        foreach ($mimeTypes as $mime) {
            if (isset($mimeToExt[$mime])) {
                $extensions[] = $mimeToExt[$mime];
            }
        }

        return array_unique($extensions);
    }

    /**
     * Delete file
     */
    public static function deleteFile(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }
}

