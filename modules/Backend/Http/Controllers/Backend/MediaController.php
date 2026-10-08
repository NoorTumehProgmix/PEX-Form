<?php



namespace Juzaweb\Backend\Http\Controllers\Backend;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Juzaweb\Backend\Events\AddFolderSuccess;
use Juzaweb\Backend\Http\Requests\Media\AddFolderRequest;
use Juzaweb\Backend\Http\Requests\Media\UpdateRequest;
use Juzaweb\Backend\Models\MediaFile;
use Juzaweb\Backend\Models\MediaFolder;
use Juzaweb\Backend\Repositories\MediaFileRepository;
use Juzaweb\Backend\Repositories\MediaFolderRepository;
use Juzaweb\CMS\Facades\Facades;
use Juzaweb\CMS\Http\Controllers\BackendController;

class MediaController extends BackendController
{
    public function __construct(
        protected MediaFileRepository $fileRepository,
        protected MediaFolderRepository $folderRepository
    ) {}

    public function index(Request $request, $folderId = null): View
    {
        global $jw_user;
        if (!$jw_user->can('media.index')) {
            abort(403);
        }

        $title = trans_cms('cms::app.media');
        $type = $request->get('type', '');
        $nameQ = $request->get('search', '');

        if ($folderId) {
            $this->addBreadcrumb(
                [
                    'title' => $title,
                    'url' => route('admin.media.index'),
                ]
            );

            $folder = $this->folderRepository->find($folderId);
            $folder->load('parent');
            $this->addBreadcrumbFolder($folder);
            $title = $folder->name;
        }

        $query = collect(request()->query());
        $mediaFolders = collect([]);
        if ($request->input('page', 1) == 1) {
            $mediaFolders = $this->getDirectories($query, $folderId);
        }

        $mediaFiles = $this->getFiles($query, 36 - $mediaFolders->count(), $folderId);

        $maxSize = config("juzaweb.filemanager.types.{$type}.max_size");
        $mimeTypes = config("juzaweb.filemanager.types.{$type}.valid_mime");
        if (empty($mimeTypes)) {
            $mimeTypes = config("juzaweb.filemanager.types.file.valid_mime");
        }

        return view(
            'cms::backend.media.index',
            [
                'fileTypes' => $this->getFileTypes(),
                'folderId' => $folderId,
                'mediaFolders' => $mediaFolders,
                'mediaFiles' => $mediaFiles,
                'title' => $title,
                'mimeTypes' => $mimeTypes,
                'type' => $type,
                'search' => $nameQ,
                'maxSize' => $maxSize,
            ]
        );
    }

    public function update(UpdateRequest $request, $id): JsonResponse | RedirectResponse
    {
        global $jw_user;
        if (!$jw_user->can('media.edit')) {
            abort(403);
        }

        if ($request->input('is_file')) {
            $model = $this->fileRepository->find($id);
        } else {
            $model = $this->folderRepository->find($id);
        }

        try {
            $model->update($request->only(['name']));

            // Insert values for caption, text, description, and media_files_id columns in media_meta table
            $mediaMetaData = [
                'caption' => $request->input('caption'),
                'text' => $request->input('text'),
                'description' => $request->input('description'),
                'lang' => Lang::locale(),
                'media_files_id' => $model->id,
                'updated_at' => Carbon::now(),
            ];
            // Update or insert the data into media_meta table based on media_files_id and lang columns
            DB::table('media_metas')->updateOrInsert(
                ['media_files_id' => $model->id, 'lang' => Lang::locale()],
                $mediaMetaData
            );

            DB::commit();

            $content = [
                'method' => "PUT",
                'table' => $model->table,
                'id' => $model->id,
                'type' => "file",
                'label' => "updated a file",
                'title' => $model->name,
                'path' => upload_url($model->path),
                'form_data' => $request->all(),
            ];
            log_action($content);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $this->success(trans_cms('cms::app.updated_successfully'));
    }

    public function download($id)
    {
        global $jw_user;
        if (!$jw_user->can('media.index')) {
            abort(403);
        }

        $model = $this->fileRepository->find($id);
        $storage = Storage::disk(config('juzaweb.filemanager.disk'));

        if (!$storage->exists($model->path)) {
            abort(404, 'File not exists.');
        }

        if (config('juzaweb.filemanager.disk') == 'public') {
            return response()->download($storage->path($model->path));
        } else {
            return redirect()->away($storage->url($model->path));
        }
    }

    public function destroy(Request $request, $id): JsonResponse | RedirectResponse
    {

        global $jw_user;
        if (!$jw_user->can('media.delete')) {
            abort(403);
        }

        if ($request->input('is_file') == "false") {
            $model = $this->folderRepository->find($id);
            $model->deleteFolder();
        } else {
            $model = $this->fileRepository->find($id);
        }

        DB::beginTransaction();
        try {

            $content = [
                'method' => "DELETE",
                'table' => $model->table,
                'id' => $model->id,
                'type' => "file",
                'label' => "deleted a file",
                'title' => $model->name,
                'path' => "",
            ];
            log_action($content);

            $model->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $this->success(trans_cms('cms::app.deleted_successfully'));
    }

    public function addFolder(AddFolderRequest $request): JsonResponse | RedirectResponse
    {
        global $jw_user;
        if (!$jw_user->can('media.create')) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            $folder = MediaFolder::create($request->all());
            DB::commit();

            $content = [
                'method' => "POST",
                'table' => "media_folders",
                'id' => $folder->id,
                'type' => "folder",
                'label' => "created a new folder",
                'title' => $folder->name,
                'path' => "",
            ];
            log_action($content);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        event(new AddFolderSuccess($folder));

        return $this->success(
            trans_cms('cms::filemanager.add-folder-successfully')
        );
    }

    protected function getFileTypes()
    {
        return config('juzaweb.filemanager.types');
    }

    protected function addBreadcrumbFolder($folder)
    {
        $parent = $folder->parent;
        if ($parent) {
            $this->addBreadcrumb(
                [
                    'title' => $parent->name,
                    'url' => route('admin.media.folder', $parent->id),
                ]
            );

            $parent->load('parent');
            if ($parent->parent) {
                $this->addBreadcrumbFolder($parent);
            }
        }
    }

    /**
     * Get files in folder
     *
     * @param Collection $sQuery
     * @param int $limit
     * @param int|null $folderId
     * @return LengthAwarePaginator
     */
    protected function getFiles(Collection $sQuery, int $limit = 40, ?int $folderId = null): LengthAwarePaginator
    {
        $query = MediaFile::query();

        if (!$sQuery->has('type') && !$sQuery->has('search')) {
            $query->whereFolderId($folderId);
        } else {
            if ($folderId !== null) {
                $query->whereFolderId($folderId);
            }
        }
        if ($sQuery->get('type')) {
            //$extensions = $this->getTypeExtensions($sQuery->get('type'));
            $query->where('type', $sQuery->get('type'));
        }
        if ($sQuery->get('search')) {
            $nameQ = $sQuery->get('search');
            $query->where(function ($query) use ($nameQ) {
                $query->where('name', 'LIKE', "%{$nameQ}%")
                    ->orWhereHas('mediaMeta', function ($query) use ($nameQ) {
                        $query->where('caption', 'LIKE', "%{$nameQ}%")
                            ->orWhere('description', 'LIKE', "%{$nameQ}%");
                    });
            });
        }

        $query->with([
            'mediaMeta' => function ($query) {
                $query->where('lang', Lang::Locale());
            },
        ])->orderBy('id', 'DESC');

        return $query->paginate();
    }

    /**
     * Get directories in folder
     *
     * @param Collection $sQuery
     * @param int|null $folderId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getDirectories(Collection $sQuery, ?int $folderId): \Illuminate\Database\Eloquent\Collection
    {
        $query = MediaFolder::whereFolderId($folderId);

        if ($sQuery->get('search')) {
            $nameQ = $sQuery->get('search');
            $query->where(function ($query) use ($nameQ) {
                $query->where('name', 'LIKE', "%{$nameQ}%")
                    ->orWhereHas('mediaMeta', function ($query) use ($nameQ) {
                        $query->where('caption', 'LIKE', "%{$nameQ}%")
                            ->orWhere('description', 'LIKE', "%{$nameQ}%");
                    });
            });
        }

        return $query->get();
    }

    protected function getTypeExtensions(string $type)
    {
        $extensions = config("juzaweb.filemanager.types.{$type}.extensions");
        if (empty($extensions)) {
            $extensions = match ($type) {
                'file' => Facades::defaultFileExtensions(),
                'image' => Facades::defaultImageExtensions(),
            };
        }

        return $extensions;
    }
}
