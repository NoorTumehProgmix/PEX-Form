<?php

namespace Juzaweb\Backend\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\Facades\Image;
use Juzaweb\Backend\Models\JobLog;

class ProcessImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $image;
    protected $operation;
    protected $sizes;
    protected $quality;
    protected $folder_name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($image, $operation, $sizes, $quality, $folder_name = "resized", $postType = null)
    {
        $this->image = $image;
        $this->operation = $operation;
        $this->sizes = $sizes;
        $this->quality = $quality;
        $this->folder_name = $folder_name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        //Process convert to webp for slider
        if ($this->operation == "convert") {
            if ($this->image) {
                $startTime = microtime(true);
                if (!check_webp_exists($this->image, $this->folder_name)) {
                    $status = convert_to_webp($this->image, $this->folder_name);
                } else {
                    $status = "exists";
                }
                $endTime = microtime(true);
                $executionTime = $endTime - $startTime;
                // Log to database
                JobLog::create([
                    'queue' => $this->queue,
                    'job_id' => $this->job->getJobId(),
                    'job_type' => static::class,
                    'path' => $this->image,
                    'size' => "webp",
                    'status' => $status,
                    'execution_time' => $executionTime,
                ]);
            }
        } else {
            // Process resizing
            foreach ($this->sizes as $size) {
                $startTime = microtime(true);
                if (!has_media_image_size($this->image, "{$size}",  $this->folder_name, "{$this->quality}")) {
                    list($width, $height) = explode('x', strtolower($size));
                    $status = intervention_image_resize($this->image, $width, $height, $this->operation, $this->quality, $this->folder_name);
                } else {
                    $status = "exits";
                }
                $endTime = microtime(true);
                $executionTime = $endTime - $startTime;
                // Log to database
                JobLog::create([
                    'queue' => $this->queue,
                    'job_id' => $this->job->getJobId(),
                    'job_type' => static::class,
                    'path' => $this->image,
                    'size' => $size,
                    'status' => $status,
                    'execution_time' => $executionTime,
                ]);
            }
        }
    }
}
