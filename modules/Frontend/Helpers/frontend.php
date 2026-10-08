<?php

use Juzaweb\CMS\Support\Email;
use Juzaweb\Backend\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Juzaweb\Backend\Models\Taxonomy;
use Juzaweb\Backend\Models\Plugins\Link;

if (!function_exists('get_direction')) {
    function get_direction(): string
    {
        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'ar');

        return config('app.locales.' . $locale . '.dir')
            ?? config('app.locales.' . $fallbackLocale . '.dir', 'rtl');
    }
}

if (!function_exists('send_email_notification')) {
    function send_email_notification($form_name, $email_to, $dynamicLink)
    {
        $data     = [
            'subject'  => "New $form_name Form Submission",
            'body'     => "A new submission for the $form_name form has been received.",
            'link'     => $dynamicLink,
        ];
        $htmlBody = view('frontend::emails.forms', $data)->render();

        Email::make()
            ->setEmails($email_to)
            ->withTemplate('notification')
            ->setSubject($data['subject'])
            ->setBody($htmlBody)
            ->send();
    }
}

if (!function_exists('send_email_notification_for_forms')) {
    function send_email_notification_for_forms($email_to, $dynamicLink, $formData)
    {
        $data = [
            'subject'  => "New Form Submission",
            'body'     => "A new submission form has been received.",
            'link'     => $dynamicLink,
            'formData' => $formData,
        ];

        Mail::send('frontend::emails.forms', $data, function ($message) use ($email_to, $data) {
            $message->to($email_to)
                ->subject($data['subject']);
        });
    }
}

if (!function_exists('get_message_type')) {
    function get_message_type($sliderId)
    {
        $slider = get_resource($sliderId, 'popups-messages');
        if (isset($slider)) {
            return $slider['metas']['type'];
        }
    }
}

if (!function_exists('generate_slider')) {
    function generate_slider($slider_metas, $pageID)
    {
        $sliderItems = [];
        if (isset($slider_metas)) {
            //Main Slider
            $cacheKey    = "slider-$pageID-" . app()->getLocale();
            $sliderItems = Cache::remember($cacheKey, get_config('cache_duration', 0), function () use ($slider_metas) {
                $slider = get_resource($slider_metas);
                if ($slider) {
                    return json_decode($slider['metas']['content'], true);
                }
                return [];
            });
        }
        return $sliderItems;
    }
}

if (!function_exists('get_related_page')) {
    function get_related_page($pageID)
    {
        $cacheKey = "lang_pages:$pageID-" . app()->getLocale();
        return Cache::remember($cacheKey, get_config('cache_duration', 0), function () use ($pageID) {
            return Post::published()
                ->where(function ($query) use ($pageID) {
                    $query->where("id", $pageID)
                        ->orWhere("rel_id", $pageID);
                })
                ->where("lang", app()->getLocale())
                ->first();
        });
    }
}

if (!function_exists('pluginActive')) {
    function pluginActive($pluginName)
    {
        $plugins = get_config("plugin_statuses");
        if ($plugins) {
            return array_key_exists($pluginName, $plugins);
        }
        return false;
    }
}

if (!function_exists('get_general_link')) {
    function get_general_link($code)
    {
        $link = null;
        if (pluginActive("progmix/links")) {
            $link = Cache::remember($code . app()->getLocale(), get_config('cache_duration', 0), function () use ($code) {
                return Link::where("slug", $code)->first();
            });
        }

        return $link;
    }
}

if (!function_exists('get_general_link_by_id')) {
    function get_general_link_by_id($id)
    {
        $link    = '';
        if (pluginActive("progmix/links")) {
            $link = Cache::remember($id . app()->getLocale(), get_config('cache_duration', 0), function () use ($id) {
                return Link::find($id);
            });
        }

        return $link;
    }
}


if (!function_exists('get_page_by_template')) {
    function get_page_by_template($template, $type = [])
    {
        $cacheKey = $template . '-' . implode(',', $type) . '-' . app()->getLocale();
        return Cache::remember($cacheKey, get_config('cache_duration', 0), function () use ($template, $type) {
            $query = Post::published()
                ->whereJsonContains('json_metas->ctemplate', "$template");
            if (!empty($type)) {
                $query->whereIn('type', $type);
            }
            $query->where('lang', app()->getLocale());
            return $query->firstOrFail();
        });
    }
}

if (!function_exists('get_page_by_id')) {
    function get_page_by_id($id, $type = [])
    {
        $cacheKey = $id . '-' . implode(',', $type) . '-' . app()->getLocale();
        return Cache::remember($cacheKey, get_config('cache_duration', 0), function () use ($id, $type) {
            $query = Post::published()
                ->where('id', $id);
            if (!empty($type)) {
                $query->whereIn('type', $type);
            }
            $query->where('lang', app()->getLocale());
            return $query->firstOrFail();
        });
    }
}

if (!function_exists('get_page_by_slug')) {
    function get_page_by_slug($slug, $type = [])
    {
        if (!is_array($type)) {
            $type = [$type];
        }
        $cacheKey = $slug . '-' . implode(',', $type) . '-' . app()->getLocale();
        return Cache::remember($cacheKey, get_config('cache_duration', 0), function () use ($slug, $type) {
            $query = Post::published()->where('path', '/' . $slug . '/');
            if (!empty($type)) {
                $query->whereIn('type', $type);
            }
            $query->where('lang', app()->getLocale());
            return $query->orderBy('id', 'asc')->firstOrFail();
        });
    }
}

if (!function_exists('get_posts_of_type')) {
    function get_posts_of_type($type, $limit = null, $pinned = null, $haveNoParent = null, $statusLabel = "Published", $request = null)
    {
        return Cache::remember("pages-$type-$limit-$pinned-$haveNoParent" . app()->getLocale(), get_config('cache_duration', 0), function () use ($type, $pinned, $statusLabel, $haveNoParent, $limit, $request) {
            $query = Post::{$statusLabel}()
                ->whereIn('type', [$type])
                ->orderBy('display_order', 'asc')
                ->orderBy('date', 'desc')
                ->orderBy('title', 'asc');
            if ($pinned !== null) {
                $query->where('pin', '1');
            }

            if ($haveNoParent !== null and $haveNoParent === true) {
                $query->where(function ($query) {
                    $query->whereNull('json_metas->parent')
                        ->orWhereJsonContains('json_metas->parent', "");
                });
            } else {
                $query->where(function ($query) {
                    $query->whereNotNull('json_metas->parent')
                        ->where('json_metas->parent', '!=', '');
                });
            }

            if ($request) {
                $category = $request->input('category', '');
                $keyword = $request->input('keyword', '');
                $filter = $request->input('filter', '');
                $tag = $request->input('tag', '');
                $active = $request->input('active', '');
                $not_active = $request->input('not_active', '');
                $start_datetime = $request->input('start_datetime', '');

                if ($active && $active != "") {
                    $query->where(function ($query) use ($active) {
                        $query->whereJsonContains('json_metas->active', $active)
                            ->orWhereJsonContains('json_metas->active', intval($active));
                    });
                }


                if ($not_active != "" && $not_active == 1) {
                    $query->whereRaw(
                        "JSON_UNQUOTE(JSON_EXTRACT(json_metas, '$.active')) IS NULL OR JSON_UNQUOTE(JSON_EXTRACT(json_metas, '$.active')) = ''"
                    );
                }

                if ($active && $active != "") {
                    $query->where(function ($query) use ($active) {
                        $query->whereJsonContains('json_metas->active', $active)
                            ->orWhereJsonContains('json_metas->active', intval($active));
                    });
                }

                if ($start_datetime && $start_datetime != "") {
                    $query->where(function ($query) use ($start_datetime) {
                        $query->whereDate('json_metas->start_datetime', '>', $start_datetime);
                    });
                }

                if ($category && $category != "") {
                    $query->where(function ($query) use ($category) {
                        $query->whereJsonContains('json_metas->parent', "$category")
                            ->orWhereJsonContains('json_metas->parent', intval($category));
                    });
                }

                if ($filter && $filter != "") {
                    $query->whereHas('taxonomies', function (Builder $query) use ($filter) {
                        $query->where('id', $filter);
                    });
                }

                if ($tag && $tag != "") {
                    $query->whereHas('taxonomies', function (Builder $query) use ($tag) {
                        $query->where('slug', $tag);
                    });
                }

                if ($keyword && $keyword != "") {
                    $query->whereSearch(['q' => $keyword]);
                }
            }

            if ($limit !== null) {
                $query->limit($limit);
                return $query->get();
            } else {
                return $query->paginate(get_config('posts_per_page', 12));
            }
        });
    }
}

if (!function_exists('get_pages_of_type')) {
    function get_pages_of_type($type, $limit = null, $pinned = null, $haveNoParent = null, $parent = null, $statusLabel = "Published")
    {
        if (!is_array($type)) {
            $type = [$type];
        }
        $cacheKey = 'pages-' . implode(',', $type) . '-' . $limit . '-' . $pinned . '-' . $haveNoParent . '-' . $parent . '-' . $statusLabel . '-' . app()->getLocale();
        return Cache::remember($cacheKey, get_config('cache_duration', 0), function () use ($type, $pinned, $statusLabel, $limit, $parent, $haveNoParent) {
            $query = Post::{$statusLabel}()
                ->whereIn('type', $type)
                ->orderBy('display_order', 'asc')
                ->orderBy('date', 'desc')
                ->orderBy('title', 'asc');
            if ($pinned !== null) {
                $query->where('pin', '1');
            }
            if ($haveNoParent !== null) {
                if ($haveNoParent === true) {
                    $query->where(function ($query) {
                        $query->whereNull('json_metas->parent')
                            ->orWhereJsonContains('json_metas->parent', "");
                    });
                } elseif ($haveNoParent === false) {
                    $query->whereNotNull('json_metas->parent')
                        ->where(function ($query) {
                            $query->where('json_metas->parent', '!=', '');
                        });
                }
            }
            if ($parent !== null) {
                $query->where(function ($query) use ($parent) {
                    $query->whereJsonContains('json_metas->parent', intval($parent))
                        ->orWhereJsonContains('json_metas->parent', "$parent")
                        ->orWhereJsonContains('json_metas->defaults', "$parent")
                        ->orWhereJsonContains('json_metas->defaults', intval($parent));
                });
            }

            if ($limit !== null) {
                $query->limit($limit);
            }

            return $query->get();
        });
    }
}

if (!function_exists('get_taxonomy_by_name')) {
    function get_taxonomy_by_name($name)
    {
        $cacheKey = 'taxonomy-' . $name . '-' . app()->getLocale();
        return Cache::remember($cacheKey, get_config('cache_duration', 0), function () use ($name) {
            return Taxonomy::where('name', $name)->where('lang', app()->getLocale())
                ->first();
        });
    }
}

if (!function_exists('get_taxonomy_by_id')) {
    function get_taxonomy_by_id($id)
    {
        $cacheKey = 'taxonomy-' . $id . '-' . app()->getLocale();
        return Cache::remember($cacheKey, get_config('cache_duration', 0), function () use ($id) {
            return Taxonomy::where('id', $id)->where('lang', app()->getLocale())
                ->first();
        });
    }
}

if (!function_exists('breadcrumbs')) {
    function breadcrumbs($path)
    {
        $lang        = app()->getLocale();
        $cacheTime   = get_config('cache_duration', 0);
        $breadcrumbs = [];
        $pages_tree  = explode('/', trim($path, '/'));
        array_pop($pages_tree);
        $accumulatedPath = '';

        foreach ($pages_tree as $crumb) {
            $accumulatedPath .= ($accumulatedPath ? '/' : '') . $crumb;
            $cacheKey        = "crumb_" . str_replace('/', '_', $accumulatedPath) . "_" . $lang;

            $crumbPage = Cache::remember($cacheKey, $cacheTime, function () use ($accumulatedPath, $lang) {
                return Post::where('path', '/' . $accumulatedPath . '/')
                    ->where('lang', $lang)
                    ->first();
            });
            if ($crumbPage) {
                $crumbData     = [
                    "id"    => $crumbPage->id,
                    'title' => $crumbPage->title,
                    'path'  => $crumbPage->getRoute(),
                ];
                $breadcrumbs[] = $crumbData;
            }
        }
        return $breadcrumbs;
    }
}

if (!function_exists('get_page_title')) {
    function get_page_title($title, $keywords)
    {
        if (is_array($keywords)) {
            $secondary_titles = implode(', ', $keywords);
        } else {
            $secondary_titles = $keywords;
        }

        $secondary_titles = $secondary_titles ? ' - ' . $secondary_titles : '';

        return $title . $secondary_titles;
    }
}

if (!function_exists('get_logo')) {
    function get_logo($default = null): ?string
    {
        $current_locale = get_locale();
        return upload_url(get_config("logo_$current_locale"), asset($default ?: 'assets/images/logo.webp?v=5'), size: '500x500');
    }
}

if (!function_exists('get_footer_logo')) {
    function get_footer_logo($default = null): ?string
    {
        $current_locale = get_locale();
        return upload_url(get_config("footer_logo_$current_locale"), asset($default ?: 'assets/images/logo.webp?v=5'), size: '500x500');
    }
}

if (!function_exists('get_white_logo')) {
    function get_white_logo($default = null): ?string
    {
        $current_locale = get_locale();
        return upload_url(get_config("white_logo_$current_locale"), asset($default ?: 'assets/images/logo-white.svg?v=5'), size: '500x500');
    }
}

if (!function_exists('getSocialMenu')) {
    function getSocialMenu()
    {
        $socialTitles = ['facebook', 'linkedin', 'twitter', 'instagram', 'youtube', 'telegram', 'tiktok', 'snapchat', 'messenger', 'whatsapp'];
        $socialList   = [];

        foreach ($socialTitles as $socialTitle) {
            if (!empty(get_config($socialTitle))) {
                $icon = 'icon-' . $socialTitle;
                if ($socialTitle === 'twitter') {
                    $icon = 'icon-x';
                }
                array_push($socialList, [
                    'title' => $socialTitle,
                    'icon'  => $icon,
                    'url'   => get_config($socialTitle)
                ]);
            }
        }
        return $socialList;
    }
}

if (!function_exists('getContactInfo')) {
    function getContactInfo()
    {
        $inputs      = [
            ['name' => 'location', 'title' => 'Address', 'icon' => 'icon-pin', 'lingual' => true, 'type' => 'location'],
            ['name' => 'bo_box', 'title' => 'BO Box', 'icon' => 'icon-box', 'lingual' => true, 'type' => 'bo_box'],
            ['name' => 'mail', 'title' => 'Email', 'icon' => 'icon-mail', 'lingual' => false, 'type' => 'mail'],
            ['name' => 'phone', 'title' => 'Phone', 'icon' => 'icon-phone', 'lingual' => false, 'type' => 'phone'],
            ['name' => 'fax', 'title' => 'Fax', 'icon' => 'icon-fax', 'lingual' => false, 'type' => 'fax']
        ];
        $contactInfo = [];

        foreach ($inputs as $input) {
            $field = $input['name'];
            if ($input['lingual']) {
                $field = $input['name'] . '_' . app()->getLocale();
            }
            $url = '';
            if ($input['type'] === 'location') {
                $url = !empty(get_config('map_link')) ? get_config('map_link') : '';
            } elseif ($input['type'] === 'phone') {
                $url = 'tel:' . get_config($field);
            } elseif ($input['type'] === 'mail') {
                $url = 'mailto:' . get_config($field);
            }
            if (!empty(get_config($field))) {
                $contactInfo[] = [
                    'title' => __('messages.' . $input['name']),
                    'input' => get_config($field),
                    'url'   => $url,
                    'icon'  => $input['icon']
                ];
            }
        }
        return $contactInfo;
    }
}

if (!function_exists('lazyloadImageResize')) {
    function lazyloadImageResize($path, $width, $height, $attrs, $disabled = false)
    {
        $pathEntities = explode('/', ltrim($path, '/'));
        if ($pathEntities[0] === 'storage') {
            $imageUrl = $path;
        } else {
            $size     = $width . 'x' . $height;
            $imageUrl = upload_url($path, null, $size);
        }
        //        if (is_url($path)) {
        //        } else {
        //            $storage  = Storage::disk('public');
        //            $imageUrl = $storage->url($path);
        //        }

        if (!$disabled) {
            if (array_key_exists('class', $attrs)) {
                $attrs['class'] .= ' lazyload';
            } else {
                $attrs['class'] = 'lazyload';
            }
        }

        $htmlProps = [];
        foreach ($attrs as $key => $value) {
            $value       = htmlentities($value);
            $htmlProps[] = "$key=\"$value\"";
        }
        $htmlProps = implode(' ', $htmlProps);

        $lowImage = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';

        $imageTag = "<img src='{$lowImage}' data-src='{$imageUrl}' width='{$width}' height='{$height}' {$htmlProps} />";

        return $imageTag;
    }
}

if (!function_exists('hrefAttr')) {
    function hrefAttr($url)
    {
        $url = trim($url);
        if (empty($url) || isLocalUrl($url)) {
            return '';
        }
        return 'target="_blank" rel="nofollow noopener"';
    }
}

if (!function_exists('isLocalUrl')) {
    function isLocalUrl($url)
    {
        if (strpos($url, 'http') === false) {
            return true;
        }
        $host = parse_url($url, PHP_URL_HOST);
        if (strpos($host, $_SERVER['SERVER_NAME']) !== false) {
            return true;
        }
        return false;
    }
}

if (!function_exists('chars')) {
    function chars($text, $to = 150)
    {
        $strippedText = strip_tags(trim($text));
        $strippedText = html_entity_decode($strippedText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $strippedText = preg_replace('/\x{00A0}/u', ' ', $strippedText);
        $strippedText = preg_replace('/\s+/', ' ', $strippedText);
        $strippedText = trim($strippedText);
        if (mb_strlen($strippedText) > $to) {
            $strippedText = mb_substr($strippedText, 0, $to) . '..';
        }
        return $strippedText;
    }
}

if (!function_exists('first_word')) {
    function first_word($title)
    {
        $titleArray = explode(' ', $title);
        return array_shift($titleArray);
    }
}


if (!function_exists('style_title')) {
    function style_title($text)
    {
        return preg_replace_callback('/\%{2}([^%]*)\%{2}/', function ($matches) {
            return "<span>$matches[1]</span>";
        }, $text);
    }
}

if (!function_exists('remove_title_style')) {
    function remove_title_style($text)
    {
        return preg_replace_callback('/\%{2}([^%]*)\%{2}/', function ($matches) {
            return "$matches[1]";
        }, $text);
    }
}

if (!function_exists('clean_schema_description')) {
    function clean_schema_description($desc): array|string
    {
        $cleanedDescription = trim(stripslashes(html_entity_decode(strip_tags($desc))));
        return str_replace(["\r\n", "\r", "\n"], " ", $cleanedDescription);
    }
}

if (!function_exists('get_schema_social_urls')) {
    function get_schema_social_urls()
    {
        $socialTitles = ['facebook', 'linkedin', 'twitter', 'instagram', 'youtube', 'telegram', 'tiktok', 'snapchat', 'messenger'];
        $socialList = [];

        foreach ($socialTitles as $socialTitle) {
            if (!empty(get_config($socialTitle))) {
                array_push($socialList, get_config($socialTitle));
            }
        }
        return $socialList;
    }
}

if (!function_exists('strip_editor_tags')) {
    function strip_editor_tags($content): string
    {
        if (!is_string($content) || $content === '') {
            return '';
        }

        $content = preg_replace('#<style[^>]*>.*?</style>#is', '', $content);
        $content = preg_replace('#<script[^>]*>.*?</script>#is', '', $content);
        $content = preg_replace('/<!--.*?-->/s', '', $content);
        $content = preg_replace('/\.[A-Za-z0-9_\-]+[\s\S]*?\{[\s\S]*?\}/u', '', $content);
        $content = preg_replace(
            '#</?(?:div|p|h[1-6]|li|ul|ol|br|tr|table|thead|tbody|tfoot|section|article|header|footer|blockquote)[^>]*>#i',
            "\n",
            $content
        );
        $content = strip_tags($content);
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $content = preg_replace('/\x{00A0}/u', ' ', $content); // non-breaking spaces => normal space
        $content = preg_replace('/[ \t]{2,}/u', ' ', $content); // multiple spaces/tabs => one space
        $content = preg_replace('/(?:\r\n|\r|\n){2,}/', "\n\n", $content); // multiple newlines => double newline
        $content = preg_replace('/[ \t]+\n/u', "\n", $content); // trailing spaces before newline
        $content = trim($content);

        return $content;
    }
}

/*
|--------------------------------------------------------------------------
| PEX Homepage Section Helpers
|--------------------------------------------------------------------------
| دوال مساعدة لتجهيز بيانات أقسام الصفحة الرئيسية (agenda, speakers, about,
| sponsorship, contact) بحيث لا يبقى أي منطق تحويل بيانات داخل ملفات
| ال blade.php نفسها - فقط استدعاء الدالة وعرض النتيجة.
*/

if (!function_exists('get_speaker_directory')) {
    /**
     * يبني ملف تعريف كامل لكل متحدث/مدير جلسة (مرتّب بالاسم) + خريطة
     * id => name. يُستخدم من agenda.blade.php و speakers.blade.php لحل
     * معرّفات chair/session_speakers إلى ملفات تعريف كاملة.
     *
     * @return array{profiles: array<string, array>, namesById: \Illuminate\Support\Collection}
     */
    function get_speaker_directory(): array
    {
        $speakerPosts = get_pages_of_type('speakers');

        $profiles = [];
        foreach ($speakerPosts as $p) {
            $profiles[$p->title] = [
                'name'        => $p->title,
                'role'        => $p->subtitle,
                'roleType'    => $p->getMeta('role_type', 'speaker'),
                'photo'       => $p->getThumbnail(),
                'bio'         => $p->getMeta('bio'),
                'description' => strip_editor_tags($p->content ?? ''),
            ];
        }

        return [
            'profiles'  => $profiles,
            'namesById' => $speakerPosts->pluck('title', 'id'),
        ];
    }
}

if (!function_exists('resolve_session_participant_names')) {
    /**
     * يحوّل chair id + مصفوفة session_speakers ids إلى مصفوفة أسماء
     * فريدة (chair أولاً، ثم باقي المتحدثين بدون تكرار).
     *
     * @param mixed $chairId
     * @param array $speakerIds
     * @param \Illuminate\Support\Collection|array $namesById
     * @return array
     */
    function resolve_session_participant_names($chairId, array $speakerIds, $namesById): array
    {
        $names = [];

        if ($chairId && isset($namesById[$chairId])) {
            $names[] = $namesById[$chairId];
        }

        foreach ($speakerIds as $sid) {
            $n = $namesById[$sid] ?? null;
            if ($n && !in_array($n, $names, true)) {
                $names[] = $n;
            }
        }

        return $names;
    }
}

if (!function_exists('resolve_speaker_profiles')) {
    /**
     * يحوّل مصفوفة أسماء إلى مصفوفة ملفات تعريف كاملة (يتجاهل الأسماء
     * غير الموجودة في $profiles).
     *
     * @param array $names
     * @param array $profiles
     * @return array
     */
    function resolve_speaker_profiles(array $names, array $profiles): array
    {
        return array_values(array_filter(array_map(
            fn ($n) => $profiles[$n] ?? null,
            $names
        )));
    }
}

if (!function_exists('get_agenda_items')) {
    /**
     * يبني مصفوفة بنود الأجندة الجاهزة للعرض (وقت البداية/النهاية، نوع
     * الجلسة، المشاركون كملفات تعريف كاملة...) انطلاقاً من منشورات
     * 'agenda' وخريطة أسماء المتحدثين الناتجة عن get_speaker_directory().
     *
     * @param \Illuminate\Support\Collection|array $namesById
     * @param array $profiles
     * @return array
     */
    function get_agenda_items($namesById, array $profiles): array
    {
        return get_pages_of_type('agenda')->map(function ($p) use ($namesById, $profiles) {
            $chairId = $p->getMeta('chair');
            $speakerIds = $p->getMeta('session_speakers', []);

            $chairName = $chairId ? ($namesById[$chairId] ?? null) : null;
            $participantNames = resolve_session_participant_names($chairId, $speakerIds, $namesById);
            $participants = resolve_speaker_profiles($participantNames, $profiles);

            return [
                'start'        => $p->getMeta('start_time'),
                'end'          => $p->getMeta('end_time'),
                'type'         => $p->getMeta('session_type'),
                'typeKey'      => $p->getMeta('type_key', 'plain'),
                'title'        => $p->title,
                'chairName'    => $chairName,
                'participants' => $participants,
                'hasDetails'   => (bool) $p->getMeta('has_details'),
                'description'  => strip_editor_tags($p->content ?? ''),
            ];
        })->values()->all();
    }
}

if (!function_exists('get_speaker_first_sessions')) {
    /**
     * يبني خريطة: اسم المتحدث => {label, names[]} لأول جلسة أجندة يظهر
     * فيها (كمدير جلسة أو كمتحدث). تُستخدم من speakers.blade.php لفتح
     * نافذة التعريف مع "المشاركين في الجلسة نفسها".
     *
     * @param \Illuminate\Support\Collection|array $namesById
     * @return array
     */
    function get_speaker_first_sessions($namesById): array
    {
        $firstSessions = [];

        foreach (get_pages_of_type('agenda') as $item) {
            $chairId = $item->getMeta('chair');
            $speakerIds = $item->getMeta('session_speakers', []);
            $names = resolve_session_participant_names($chairId, $speakerIds, $namesById);

            if (!$names) {
                continue;
            }

            foreach ($names as $n) {
                if (!isset($firstSessions[$n])) {
                    $firstSessions[$n] = ['label' => $item->title, 'names' => $names];
                }
            }
        }

        return $firstSessions;
    }
}

if (!function_exists('get_speaker_session_summary')) {
    /**
     * يحوّل اسم متحدث إلى {label, participants[]} الجاهزة لعرضها في
     * نافذة التعريف (data-session-label / data-session).
     *
     * @param string $name
     * @param array $firstSessions ناتج get_speaker_first_sessions()
     * @param array $profiles
     * @return array{label: string, participants: array}
     */
    function get_speaker_session_summary(string $name, array $firstSessions, array $profiles): array
    {
        $session = $firstSessions[$name] ?? ['label' => '', 'names' => [$name]];

        return [
            'label'        => $session['label'],
            'participants' => resolve_speaker_profiles($session['names'], $profiles),
        ];
    }
}

if (!function_exists('get_about_stats')) {
    /**
     * يحوّل حقل repeater الخاص بصفحة "about" إلى مصفوفة {number, label}
     * جاهزة للعرض في بطاقات الإحصائيات.
     *
     * @param mixed $page صفحة about (نتيجة get_page_by_template)
     * @return array
     */
    function get_about_stats($page): array
    {
        return collect($page?->getMeta('repeater') ?? [])
            ->map(fn ($item) => [
                'number' => $item['title'] ?? '',
                'label'  => $item['subtitle'] ?? '',
            ])
            ->values()
            ->all();
    }
}

if (!function_exists('get_sponsor_categories')) {
    /**
     * يجمّع منشورات 'sponsors' حسب حقل meta "sponsor_type" (silver /
     * gold / diamond / ...) ويبني مصفوفة فئات جاهزة للعرض في قسم الرعاة.
     *
     * @return array
     */
    function get_sponsor_categories(): array
    {
        $labels = [
            'silver'  => __('messages.sponsor_tier_silver'),
            'gold'    => __('messages.sponsor_tier_gold'),
            'diamond' => __('messages.sponsor_tier_diamond'),
        ];

        return get_pages_of_type('sponsors')
            ->groupBy(fn ($p) => $p->getMeta('sponsor_type', 'other'))
            ->map(fn ($group, $key) => [
                'key'      => $key,
                'label'    => $labels[$key] ?? $key,
                'sponsors' => $group->map(fn ($p) => [
                    'name' => $p->title,
                    'logo' => $p->getThumbnail(),
                    'url'  => $p->getMeta('url'),
                ])->values()->all(),
            ])
            ->values()
            ->all();
    }
}

if (!function_exists('get_contact_data')) {
    /**
     * يجمع كل بيانات قسم "contact" (البريد، الهاتف، الفاكس، الموقع،
     * روابط الخريطة/واتساب/لينكدإن) من إعدادات ال CMS في مصفوفة واحدة
     * جاهزة للعرض.
     *
     * @return array
     */
    function get_contact_data(): array
    {
        $lang = app()->getLocale();
        $websiteUrl = get_config("location_$lang");

        return [
            'email'        => get_config('mail'),
            'phones'       => array_values(array_filter([get_config('phone')])),
            'fax'          => get_config('fax'),
            'website'      => $websiteUrl ? preg_replace('#^https?://(www\.)?#i', '', rtrim($websiteUrl, '/')) : null,
            'websiteUrl'   => $websiteUrl,
            'mapLink'      => get_config('map_link'),
            'whatsappLink' => get_config('whatsapp_link'),
            'linkedinLink' => get_config('linkedin_link'),
        ];
    }
}
