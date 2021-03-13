<?php
declare(strict_types=1);

namespace Ctw\Middleware\GeneratedByMiddleware;

use Ctw\Middleware\AbstractMiddleware;
use Exception;
use Ramsey\Uuid\Uuid;

abstract class AbstractGeneratedByMiddleware extends AbstractMiddleware
{
    protected function getServerId(array $serverParams): string
    {
        $name = '';

        foreach (['SERVER_ADDR', 'SERVER_NAME'] as $key) {
            $value = (string) $serverParams[$key] ?? '';
            $value = trim($value);
            $value = strtolower($value);
            $name  .= $value;
        }

        if (empty($name)) {
            return '';
        }

        try {
            $ret = Uuid::uuid5(Uuid::NAMESPACE_URL, $name)->toString();
        } catch (Exception $e) {
            $ret = '';
        }

        return $ret;
    }
}

