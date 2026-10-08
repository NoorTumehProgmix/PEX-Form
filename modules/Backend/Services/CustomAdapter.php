<?php

namespace Juzaweb\Backend\Services;

use League\Flysystem\Config;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\StorageAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToCopyFile;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Juzaweb\Backend\Models\MediaFile;
use Illuminate\Support\Facades\Session;

class CustomAdapter implements FilesystemAdapter
{
    protected $baseUrl;
    protected $imagesUrl;
    protected $apiKey;
    protected $client;
    protected $tempUrlStorage;

    public function __construct(string $baseUrl, string $imagesUrl, string $apiKey)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey = $apiKey;
        $this->imagesUrl = rtrim($imagesUrl, '/');
        $this->client = new Client();
    }

    public function getUrl($path)
    {
        return "{$this->imagesUrl}/{$path}";
    }


    public function write(string $path, string $contents, Config $config): void
    {
        try {
            $uploadFolder = $config->get('uploadFolder');

            $response = $this->client->post("{$this->baseUrl}/save", [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                ],
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => $contents,
                        'filename' => basename($path),
                    ],
                    [
                        'name'     => 'uploadFolder',
                        'contents' => $uploadFolder,
                    ],
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                Session::put('uploaded_file_url', 'Max size upload exceeded');
                throw new UnableToWriteFile('Failed to upload file to custom storage');
            }

            $url = json_decode($response->getBody()->getContents())?->url;
            Session::put('uploaded_file_url', $url);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : null;
            $errorMessage = match ($statusCode) {
                413 => 'error: Max size upload exceeded',
                422 => 'error: Unprocessable entity',
                default => 'error: Failed to upload file to custom storage: ' . $e->getMessage(),
            };
            Session::put('uploaded_file_url', $errorMessage);
        } catch (\Exception $e) {
            Session::put('uploaded_file_url', 'error:' . $e->getMessage());
            throw new UnableToWriteFile('Failed to upload file to custom storage: ' . $e->getMessage());
        }
    }

    public function read(string $path): string
    {
        try {
            $response = $this->client->get("{$this->baseUrl}/read", [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                ],
                'query' => [
                    'path' => $path,
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new UnableToReadFile('Failed to read file from custom storage');
            }

            return $response->getBody()->getContents();
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            throw new UnableToReadFile('Failed to read file from custom storage: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new UnableToReadFile('Failed to read file from custom storage: ' . $e->getMessage());
        }
    }


    public function delete(string $path): void
    {
        throw new UnableToDeleteFile('Failed to delete file from custom storage');
    }

    public function fileExists(string $path): bool
    {
        return false;
    }

    public function directoryExists(string $path): bool
    {
        $response = $this->client->get("{$this->baseUrl}/exists", [
            'headers' => [
                'x-api-key' => $this->apiKey,
            ],
            'query' => [
                'path' => $path,
            ],

        ]);
        $logResponse = json_decode($response->getBody()->getContents(), true);
        return $logResponse['exists'] ?? false;
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        try {
            $stream = is_resource($contents) ? stream_get_contents($contents) : $contents;
            $this->write($path, $stream, $config);
        } catch (\Exception $e) {
            throw new UnableToWriteFile($e->getMessage(), 400);
        }
    }

    public function readStream(string $path)
    {

        throw new UnableToReadFile("Failed to read the file at path: {$path}, not supported");
        return "Failed to read the file at path: {$path}, not supported";
    }

    public function deleteDirectory(string $path): void
    {
        //Log::info('deleteDirectory, not supported');
        throw new UnableToDeleteFile("Deleting directories is not supported");
    }

    public function createDirectory(string $path, Config $config): void
    {
        //Log::info('createDirectory, not supported');
        throw new UnableToCreateDirectory("Creating directories is not supported");
    }

    public function setVisibility(string $path, string $visibility): void
    {
        //Log::info('setVisibility, not supported');
        throw new \LogicException("Setting visibility is not supported");
    }

    public function visibility(string $path): FileAttributes
    {
        //Log::info('visibility, not supported');
        throw new \LogicException("Getting visibility is not supported");
    }

    public function mimeType(string $path): FileAttributes
    {
        //Log::info('mimeType, not supported');
        throw new \LogicException("Getting MIME type is not supported");
    }

    public function lastModified(string $path): FileAttributes
    {
        //Log::info('lastModified, not supported');
        throw new \LogicException("Getting last modified time is not supported");
    }

    public function fileSize(string $path): FileAttributes
    {
        //Log::info('fileSize, not supported');
        throw new \LogicException("Getting file size is not supported");
    }

    public function listContents(string $path, bool $deep): iterable
    {
        //Log::info('listContents, not supported');
        throw new \LogicException("Listing contents is not supported");
    }

    public function move(string $source, string $destination, Config $config): void
    {
        //Log::info('move, not supported');
        throw new UnableToMoveFile("Failed to move the file from {$source} to {$destination}, not supported");
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        //Log::info('copy, not supported');
        throw new UnableToCopyFile("Failed to copy the file from {$source} to {$destination}, not supported");
    }
}
