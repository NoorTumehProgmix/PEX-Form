<?php

namespace Juzaweb\CMS\Traits;

use Illuminate\Support\Str;

trait UseSlug
{
    public static function bootUseSlug(): void
    {
        static::saving(
            function ($model) {
                if (empty($model->slug)) {
                    $model->slug = $model->generateSlug();
                }
            }
        );
    }

    public static function findBySlug($slug, $column = []): self
    {
        return self::query()
            ->where('slug', '=', $slug)
            ->first($column);
    }

    public static function findBySlugOrFail($slug): self
    {
        return self::query()
            ->where('slug', '=', $slug)
            ->firstOrFail();
    }

    public function getDisplayName()
    {
        if (empty($this->fieldName)) {
            return $this->name ?: $this->title;
        }

        return $this->{$this->fieldName};
    }

    public function generateSlug($string = null): string
    {
        if (empty($string)) {
            if ($slug = request()->input('slug')) {
                $string = $slug;
            } elseif (isset($this->slug)) {
                $string = $this->slug;
            } else {
                $string = $this->getDisplayName();
            }
        }

        $baseSlug = substr($string, 0, 70);
        $baseSlug = Str::slug($baseSlug);

        $i = 1;

        $slug = $baseSlug;
        if ($slug != "") {
            do {
                $query = self::where('id', '!=', $this->id)
                    ->where('slug', '=', $slug)
                    ->whereNull('deleted_at');
                if ($this->getTable() == "posts") {
                    $query->where(function ($q) {
                        $q->where('rel_id', '!=', $this->id)
                            ->orWhereNull('rel_id');
                    });
                    if (isset($this->rel_id)) {
                        $query->where('rel_id', '!=', $this->rel_id);
                        $query->where('id', '!=', $this->rel_id);
                    }

                    //Same parent (category)
                    if ($this->type == "pages") {
                        if (isset($this->json_metas['parent'])) {
                            $query->where('json_metas->parent', $this->json_metas['parent']);
                        } else {
                            $query->where('json_metas->parent', "");
                        }
                    } else if ($this->type == "posts") {
                        if (isset($this->json_metas['primary_page']) && isset($this->json_metas['primary_page'][0])) {
                            $query->whereJsonContains('json_metas->primary_page', $this->json_metas['primary_page'][0]);
                        }
                    }
                } elseif ($this->getTable() == "taxonomies") {
                    $query->where('lang', '=', $this->lang);
                }

                if ($this->type != null) {
                    if ($this->type == 'posts' || $this->type == 'pages') {
                        $query->where(function ($subQuery) {
                            $subQuery->where('type', '=', 'posts')
                                ->orWhere('type', '=', 'pages');
                        });
                    } elseif ($this->type == 'videos' || $this->type == 'photos') {
                        $query->where(function ($subQuery) {
                            $subQuery->where('type', '=', 'videos')
                                ->orWhere('type', '=', 'photos');
                        });
                    } else {
                        $query->where('type', '=', $this->type);
                    }
                }
                $row = $query->orderBy('slug', 'DESC')
                    ->first();

                if ($row) {
                    $slug = $baseSlug . '-' . $i;
                }

                $i++;
            } while ($row);
        }
        $this->slug = $slug;

        return $slug;
    }
}
