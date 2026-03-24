<?php

if (!function_exists('_log_exception')) {
    /**
     * Globally reusable exception logger for the Shop System.
     * Use of leading underscore prefix is intentional to avoid functional naming
     * conflicts with built-in or third-party global helper functions.
     *
     * @param string $action Descriptive name of the action being performed.
     * @param \Throwable $e The exception caught.
     * @param array $context Additional metadata (e.g. ['product_id' => 1]).
     * @return void
     */
    function _log_exception(string $action, \Throwable $e, array $context = []): void
    {
        \Illuminate\Support\Facades\Log::error("{$action}: {$e->getMessage()}", array_merge([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'exception_class' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'exception' => $e,
        ], $context));
    }
}
