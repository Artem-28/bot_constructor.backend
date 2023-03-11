<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AttachmentService
{
    private function getStoragePath(Authenticatable $user): string
    {
        return Attachment::STORAGE_PATH . '/' . $user->account->id;
    }

    private function checkForFileExist(string $path, string $fileName): bool
    {
        return Storage::disk(Attachment::DISK)->exists($path . '/' . $fileName);
    }

    public function upload(UploadedFile $file, Authenticatable $user): array
    {
        $name = $file->getClientOriginalName();
        $type = $file->getClientMimeType();
        $size = $file->getSize();
        $storagePath = $this->getStoragePath($user);
        $data = array(
            'name' => $name,
            'type' => $type,
            'size' => $size
        );

        if ($this->checkForFileExist($storagePath, $name)) {
            $data['status'] = Attachment::STATUS_EXISTS;
            $data['path'] = $storagePath . '/' . $name;
            return $data;
        }

        $path = $file->storeAs($storagePath, $name, Attachment::DISK);
        $data['path'] = $path;

        if(!$path) {
            $data['status'] = Attachment::STATUS_FAILED;
            return $data;
        }

        $data['status'] = Attachment::STATUS_SUCCESS;
        return $data;
    }

    public function save($data, Authenticatable $user)
    {
        $attachment = new Attachment($data);
        return $user->account->attachments()->save($attachment);
    }
}
