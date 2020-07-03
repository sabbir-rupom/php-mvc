<?php
namespace Core\Log;

/**
 * Core Logger class interface
 */
interface LoggerInterface
{
    public static function create($messageData, string $type = ''): bool;

    public function prepare();

    public function write();

    public function get(array $options): string;
}
