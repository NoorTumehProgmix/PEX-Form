<?php

namespace Juzaweb\Frontend\Events;

use Juzaweb\Backend\Models\Post;

class PostViewed
{
    public $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }
}
