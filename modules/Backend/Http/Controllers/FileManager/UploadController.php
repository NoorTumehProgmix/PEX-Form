<?php

namespace Juzaweb\Backend\Http\Controllers\FileManager;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Juzaweb\Backend\Events\UploadFileSuccess;
use Juzaweb\Backend\Http\Requests\FileManager\ImportRequest;
use Juzaweb\CMS\Support\FileManager;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class UploadController extends FileManagerController
{
    protected array $errors = [];

    public function upload(Request $request): JsonResponse
    {
        global $jw_user;
        if (!$jw_user->can('media.create')) {
            abort(403);
        }
        $folderId = $request->input('working_dir');
        if (empty($folderId)) {
            $folderId = null;
        }

        try {
            $receiver = new FileReceiver('upload', $request, HandlerFactory::classFromRequest($request));

            if ($receiver->isUploaded() === false) {
                throw new UploadMissingFileException();
            }

            $save = $receiver->receive();

            if ($save->isFinished()) {
                $file = FileManager::addFile(
                    $save->getFile(),
                    $this->getType(),
                    $folderId
                );

                event(new UploadFileSuccess($file));

                if (config('juzaweb.filemanager.disk') != 'storage_server' || config('juzaweb.filemanager.disk') != 'azure') {
                    $fPath = Storage::disk(config('juzaweb.filemanager.disk'))->url($file->path);
                } else {
                    $fPath = $file->path;
                }

                $content = [
                    'method' => "POST",
                    'table' => "media_files",
                    'id' => $file->id,
                    'type' => "file",
                    'label' => "uploaded a new media file",
                    'title' => $file->name,
                    'path' =>  $fPath,
                ];

                log_action($content);
                return $this->responseUpload($this->errors);
            }

            $handler = $save->handler();

            return response()->json(
                [
                    "done" => $handler->getPercentageDone(),
                    'status' => true,
                ]
            );
        } catch (\Exception $e) {
            report($e);
            $this->errors[] = $e->getMessage();
            return $this->responseUpload($this->errors);
        }
    }

    public function import(ImportRequest $request): JsonResponse|RedirectResponse
    {
        global $jw_user;
        if (!$jw_user->can('media.create')) {
            abort(403);
        }
        if (!config('juzaweb.filemanager.upload_from_url')) {
            abort(403);
        }

        $folderId = $request->input('working_dir');
        $download = (bool) $request->input('download');

        if (empty($folderId)) {
            $folderId = null;
        }

        DB::beginTransaction();
        try {
            $file = FileManager::make($request->input('url'));
            $file->setType($this->getType());
            $file->setFolder($folderId);
            $file->setDownloadFileUrlToServer($download);
            $media = $file->save();

            DB::commit();
            $content = [
                'method' => "POST",
                'table' => "media_files",
                'id' => $media->id,
                'type' => "file",
                'label' => "imported a new media file",
                'title' => $request->input('url'),
                'path' => $request->input('url'),
            ];
            log_action($content);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return $this->error(trans_cms('cms::message.upload_failed'));
        }

        return $this->success(trans_cms('cms::message.upload_successfull'));
    }

    protected function responseUpload($error): JsonResponse
    {
        $response = count($error) > 0 ? $error : parent::$success_response;
        return response()->json($response, $error ? 422 : 200);
    }
}
