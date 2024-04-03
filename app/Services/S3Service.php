<?php
namespace App\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response as Download;

class S3Service
{
    public $s3;
    public mixed $bucket;
    public function __construct($storage = 's3')
    {
        $this->s3 = Storage::disk($storage);
        $this->bucket = config('filesystems.disks.' . $storage . '.bucket');
    }

    /**
     * @param $path
     * @param string $expiry
     * @return string|null
     */
    // Do not use this function
    public function getPutSignUrl($path, string $expiry = "+1 hours"): ?string
    {
        try {
            $client = Storage::disk('s3')->getClient();
            $command = $client->getCommand('PutObject', [
                'Bucket' => $this->bucket,
                'Key'    => $path,
                'acl'    => 'public-read'
            ]);

            $request = $client->createPresignedRequest($command, $expiry);

            return (string)$request->getUri();
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @param $path
     * @param string $expiry
     * @return string|null
     */
    // Do not use this function
    public function getReadSignUrl($path, string $expiry = "+1 hours"): ?string
    {
        try {
            $client = Storage::disk('s3')->getClient();
            $command = $client->getCommand('GetObject', [
                'Bucket' => $this->bucket,
                'Key'    => $path,
            ]);
            $request = $client->createPresignedRequest($command, $expiry);
            return (string) $request->getUri();
        } catch (\Exception $exception) {
            return null;
        }
    }

    public function exitsFile($path)
    {
        for ($crashes = 0; $crashes < 3; $crashes++) {
            try {
                return $this->s3->exists($path);
            } catch (\Exception $e) {
                sleep(1);
            }
        }
        return false;
    }

    public function uploadFileToS3($file, $filePath = null)
    {
        $name = sprintf('%s_%s', now()->format('d-m-Y-H-i-s'), $file->getClientOriginalName());
        $s3Response = $this->s3->putFileAs($filePath, new File($file), $name, 'private');;
        return $s3Response;
    }

    public function getPrivateFile($url)
    {
        try {
            $temporaryUrl = $this->s3->temporaryUrl($url, Carbon::now()->addMinutes(15));
            return $temporaryUrl;
        } catch(Exception $ex) {
            Log::error($ex->getMessage());
            return null;
        }

    }

    public function removeFile($url)
    {
        return $this->s3->delete($url);
    }
}
