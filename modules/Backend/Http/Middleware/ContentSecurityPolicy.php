<?php

namespace Juzaweb\Backend\Http\Middleware;

use Google\Service\BigtableAdmin\Frame;
use Spatie\Csp\Directive;
use Spatie\Csp\Policies\Basic;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;

class ContentSecurityPolicy extends Basic
{
    public function configure()
    {
        parent::configure();
        $this->addDirective('frame-src', 'https://recaptcha.google.com/recaptcha/');
        $this->addDirective('connect-src', 'https://www.google-analytics.com');
        $this->addDirective(Directive::SCRIPT, 'www.google.com/recaptcha/');
        $this->addDirective(Directive::SCRIPT, 'www.gstatic.com/recaptcha/');
        $this->addDirective(Directive::STYLE, 'cdn.jsdelivr.net');
        $this->addDirective('frame-src', 'www.google.com/recaptcha/');
        $this->addDirective('frame-src', 'recaptcha.google.com/recaptcha/');
        $this->addDirective('img-src', 'https://progmix.dev');
        $this->addDirective('frame-src', 'self');
        $this->addDirective('img-src', 'self');
        $this->addDirective('img-src', 'data:'); // Allow data URIs for images
        $this->addDirective('font-src', 'self');
        $this->addDirective('font-src', 'https://progmix.dev');
        $this->addDirective('font-src', 'https://cdn.jsdelivr.net');
        $this->addDirective('font-src', 'data:'); // Allow data URIs for fonts
        $this->addDirective('font-src', 'https://fonts.googleapis.com');
        $this->addDirective('font-src', 'https://fonts.gstatic.com');
        $this->addDirective(Directive::SCRIPT, 'https://www.googletagmanager.com');
        $this->addDirective('connect-src', 'https://firebase.googleapis.com');
        $this->addDirective('style-src', 'https://fonts.googleapis.com');
        $this->addDirective('script-src', 'https://connect.facebook.net');
        $this->addDirective(Directive::STYLE, 'https://connect.facebook.net/');
        $this->addDirective('frame-src', 'https://www.youtube.com');
        $this->addDirective('frame-src', 'http://www.youtube.com');
        $this->addDirective('frame-src', 'www.youtube.com');
        $this->addDirective('img-src', 'https://img.youtube.com');
        $this->addDirective('img-src', 'http://img.youtube.com');
        $this->addDirective('frame-ancestors', 'self');
        $this->addDirective('img-src', 'https://tile.openstreetmap.org');

        // Google Maps
        $this->addDirective('frame-src', 'https://maps.google.com');
        $this->addDirective('frame-src', 'https://www.google.com');
        $this->addDirective('img-src', 'https://maps.gstatic.com');
        $this->addDirective('img-src', 'https://maps.googleapis.com');
        $this->addDirective(Directive::SCRIPT, 'https://maps.googleapis.com');
        $this->addDirective('connect-src', 'https://maps.googleapis.com');

        $this->addDirective('connect-src', 'https://www.facebook.com');
        $this->addDirective('img-src', 'https://www.facebook.com');
    }
}
