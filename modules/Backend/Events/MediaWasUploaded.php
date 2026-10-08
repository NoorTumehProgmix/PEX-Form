<?php


namespace Juzaweb\Backend\Events;

use Juzaweb\Backend\Models\MediaFile;

class MediaWasUploaded
{
    public $mediaFile;

    public function __construct(MediaFile $mediaFile)
    {
        $this->mediaFile = $mediaFile;
    }
}
