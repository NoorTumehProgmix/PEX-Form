<?php


namespace Juzaweb\CMS\Contracts;

use Illuminate\Support\Collection;

interface JWQueryContract
{
    public function queryRows(string $table, array $args = []): Collection|null;

    public function queryRow(string $table, array $args = []): object|null;

    public function postTaxonomies(array $post, string $taxonomy = null, array $params = []): array;

    public function relatedPosts(array $post, int $limit = 5, string $taxonomy = null): array;

    public function postTaxonomy(array $post, string $taxonomy = null, array $params = []): mixed;
}
