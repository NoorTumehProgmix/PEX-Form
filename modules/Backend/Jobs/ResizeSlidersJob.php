<?php

namespace Juzaweb\Backend\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Juzaweb\Backend\Models\Resource;

class ResizeSlidersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $folder;

    public function __construct(string $folder)
    {

        $this->folder = $folder;
    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (get_config('webp_extension')) {
            $sliders    = Resource::where('type', 'sliders')->where('status', 'publish')->get();
            foreach ($sliders as $slider) {
                $banner_items = json_decode($slider->getMeta('content'), true);
                foreach ($banner_items as $index => $item) {
                    $item = (object) $item;
                    $processImageJob = new ProcessImage($item->image, "convert", null, null, $this->folder);
                    dispatch($processImageJob->onQueue("sliders"));
                }
            }
        }
    }
}
