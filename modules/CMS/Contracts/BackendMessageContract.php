<?php


namespace Juzaweb\CMS\Contracts;

interface BackendMessageContract
{
    public function all(): array;

    public function add(string $group, array|string $message, string $status): void;

    public function delete(string $id): bool;

    public function deleteGroup(string $group): bool;
}
