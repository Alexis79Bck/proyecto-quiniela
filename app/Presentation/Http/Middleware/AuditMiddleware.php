<?php

namespace App\Presentation\Http\Middleware;

use App\Infrastructure\Logging\AuditLogger\AuditLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    /**
     * Routes that should not be audited.
     */
    protected array $except = [
        'telescope*',
        'horizon*',
        'pulse*',
        '_ignition*',
        'storage*',
    ];

    /**
     * Create a new middleware instance.
     */
    public function __construct(
        protected AuditLogger $auditLogger
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip all audit logging in testing environment
        if (app()->environment('testing')) {
            return $next($request);
        }

        // Skip excluded routes
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        // Log API request
        if ($request->is('api/*')) {
            $this->auditLogger->logApiRequest(
                $request->method(),
                $request->path(),
                [
                    'query' => $request->query(),
                    'headers' => $this->getRequestHeaders($request),
                ]
            );
        }

        // Process request
        $response = $next($request);

        // Log API response
        if ($request->is('api/*')) {
            $this->auditLogger->logApiResponse(
                $request->method(),
                $request->path(),
                $response->getStatusCode(),
                [
                    'response_time' => $this->getResponseTime($request),
                ]
            );
        }

        return $response;
    }

    /**
     * Determine if the request should be skipped.
     */
    protected function shouldSkip(Request $request): bool
    {
        foreach ($this->except as $except) {
            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get relevant request headers (excluding sensitive data).
     */
    protected function getRequestHeaders(Request $request): array
    {
        return [
            'accept' => $request->header('Accept'),
            'content-type' => $request->header('Content-Type'),
            'user-agent' => $request->header('User-Agent'),
            'x-requested-with' => $request->header('X-Requested-With'),
            // NOTA: No incluir Authorization, Cookie, u otros headers sensibles
        ];
    }

    /**
     * Calculate response time.
     */
    protected function getResponseTime(Request $request): ?float
    {
        $startTime = defined('LARAVEL_START') ? LARAVEL_START : null;

        if ($startTime) {
            return round((microtime(true) - $startTime) * 1000, 2);
        }

        return null;
    }

    /**
     * Handle the response after it has been prepared.
     */
    public function terminate(Request $request, Response $response): void
    {
        // Log terminated requests (optional)
        // This is called after the response has been sent to the client
    }
}
