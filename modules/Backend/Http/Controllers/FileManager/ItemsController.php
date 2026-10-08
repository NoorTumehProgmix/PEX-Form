<?php

namespace Juzaweb\Backend\Http\Controllers\FileManager;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Juzaweb\Backend\Models\MediaFile;
use Juzaweb\Backend\Models\MediaFolder;
use Juzaweb\CMS\Facades\Facades;

class ItemsController extends FileManagerController
{
    public function getItems(Request $request): array
    {
        $type        = $this->getType();
        $extensions  = $this->getTypeExtensions($type);
        $currentPage = self::getCurrentPageFromRequest();
        $perPage     = 15;

        $working_dir      = $request->get('working_dir');
        $working_dir_path = $working_dir;

        // Normalize working directory: treat empty string as root (null),
        // and extract the last segment when a nested path like "1/2/3" is provided.
        if ($working_dir === '' || $working_dir === null) {
            $working_dir = null;
        } else {
            $working_dir = basename((string) $working_dir);
        }

        $folders = collect([]);

        if ($currentPage == 1) {
            $query = MediaFolder::query()
                ->orderBy('name', 'ASC');

            if ($request->filled('search')) {
                // Global folder search by name (all folders, any level)
                $nameQ = $request->get('search');
                $query->where('name', 'LIKE', "%{$nameQ}%");
            } else {
                // No search: only show folders in the current directory
                $query->where('folder_id', '=', $working_dir);
            }

            $folders = $query->get(['id', 'name', 'folder_id']);
        }

        $query = MediaFile::query()->whereIn('extension', $extensions);

        if ($request->filled('search')) {
            // Global file search by name (all files, any folder)
            $nameQ = $request->get('search');
            $query->where('name', 'LIKE', "%{$nameQ}%");
        } else {
            // No search: list files only from the current folder (or root).
            if ($working_dir !== null) {
                $query->where('folder_id', '=', $working_dir);
            } else {
                $query->whereNull('folder_id');
            }
        }

        $query->with([
            'mediaMeta' => function ($query) {
                $query->where('lang', Lang::Locale());
            },
        ])->orderBy('id', 'DESC');

        $totalFiles = $query->count(['id']);

        $files = $query->paginate(abs($perPage - $folders->count()));

        $items = [];
        foreach ($folders as $folder) {
            $items[] = [
                'icon'      => 'fa-folder-o',
                'is_file'   => false,
                'is_image'  => false,
                'name'      => $folder->name,
                'thumb_url' => asset('jw-styles/juzaweb/images/folder.png'),
                'time'      => false,
                'url'       => $this->getFolderPath($folder, $folders),
                'path'      => $this->getFolderPath($folder, $folders),
            ];
        }

        foreach ($files as $file) {

            $text = $caption = $description = "";

            foreach ($file->mediaMeta as $meta) {
                $caption     = $meta->caption;
                $text        = $meta->text;
                $description = $meta->description;
            }

            $fileIcon  = $this->getFileIcon();
            $icon      = $fileIcon[strtolower($file->extension)] ?? 'fa-file-o';
            $thumb_url = null;
            if ($file->type == "image") {
                $thumb_url = upload_url($file->path, null, '150xauto');
            } else if ($file->type == "url") {
                $thumb_url = "https://i.ytimg.com/vi/$file->mime_type/hqdefault.jpg";
            }
            $items[] = [
                'id'          => $file->id,
                'icon'        => $icon,
                'is_file'     => true,
                'path'        => $file->path,
                'is_image'    => $file->type == 1,
                'name'        => $file->name,
                'thumb_url'   => $thumb_url,
                'time'        => strtotime($file->created_at),
                'url'         => upload_url($file->path),
                'text'        => $text,
                'caption'     => $caption,
                'description' => $description,
            ];
        }

        return [
            'items'            => $items,
            'paginator'        => [
                'current_page' => $currentPage,
                'total'        => $totalFiles + $folders->count(),
                'per_page'     => $perPage,
            ],
            'display'          => 'grid',
            'working_dir'      => $working_dir,
            'working_dir_path' => $working_dir_path,
        ];
    }

    public function move_old()
    {
        $items        = request('items');
        $folder_types = array_filter(
            ['user', 'share'],
            function ($type) {
                return $this->helper->allowFolderType($type);
            }
        );

        return view('filemanager::.move')
            ->with(
                [
                    'root_folders' => array_map(
                        function ($type) use ($folder_types) {
                            $path = $this->lfm->dir($this->helper->getRootFolder($type));

                            return (object) [
                                'name'     => trans_cms('cms::filemanager.title_' . $type),
                                'url'      => $path->path('working_dir'),
                                'children' => $path->folders(),
                                'has_next' => ! ($type == end($folder_types)),
                            ];
                        },
                        $folder_types
                    ),
                ]
            )
            ->with('items', $items);
    }

    public function move()
    {

        $items       = request('items');
        $excludedIds = collect();

        foreach ($items as $item) {
            if (is_numeric($item)) {
                $folder = MediaFolder::find($item);
                if ($folder) {
                    $excludedIds = $excludedIds->merge($folder->getAllDescendantIds());
                }
            }
        }
        $excludedIds = $excludedIds->unique();

        $folders = MediaFolder::with('parent')->orderBy('name', 'ASC')->whereNotIn('id', $excludedIds)->get(['id', 'name', 'folder_id']);

        return view('cms::backend.filemanager.move')
            ->with(
                [
                    'root_folders' => $folders,
                    'items'        => $items,
                ]
            );
    }

    public function domove()
    {
        $items = request('items');
        $dest  = request('goToFolder');
        if ($dest === 'root') {
            $dest = null;
        }
        foreach ($items as $item) {

            if (is_numeric($item)) {
                $folder = MediaFolder::find($item);
                if ($folder) {
                    $folder->folder_id = $dest;
                    $folder->save();
                }
            } else {
                $file = MediaFile::where('path', $item)->first();
                if ($file) {
                    $file->folder_id = $dest;
                    $file->save();
                }
            }
        }

        return parent::$success_response;
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

    private static function getCurrentPageFromRequest()
    {
        $currentPage = (int) request()->get('page', 1);
        return max($currentPage, 1);
    }

    private function getFolderPath($folder, $folders)
    {
        if ($folder->folder_id === null) {
            return (string) $folder->id;
        }

        $parent = MediaFolder::find($folder->folder_id);
        return ($parent ? $this->getFolderPath($parent, $folders) : '') . '/' . $folder->id;
    }

    protected function getFileIcon(): array
    {
        return [
            'pdf'     => 'fa-file-pdf-o',
            'doc'     => 'fa-file-word-o',
            'docx'    => 'fa-file-word-o',
            'xls'     => 'fa-file-excel-o',
            'xlsx'    => 'fa-file-excel-o',
            'rar'     => 'fa-file-archive-o',
            'zip'     => 'fa-file-archive-o',
            'gif'     => 'fa-file-image-o',
            'jpg'     => 'fa-file-image-o',
            'jpeg'    => 'fa-file-image-o',
            'png'     => 'fa-file-image-o',
            'ppt'     => 'fa-file-powerpoint-o',
            'pptx'    => 'fa-file-powerpoint-o',
            'mp4'     => 'fa-file-video-o',
            'mp3'     => 'fa-file-audio-o',
            'jfif'    => 'fa-file-image-o',
            'txt'     => 'fa-file-text-o',
            'youtube' => 'fa-youtube-play',
        ];
    }
}
