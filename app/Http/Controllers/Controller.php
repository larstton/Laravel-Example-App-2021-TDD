<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param  null  $guard
     * @return Authenticatable|User|null
     */
    public function user($guard = null)
    {
        return auth($guard)->user();
    }

    /**
     * @param  null  $guard
     * @return Team|null
     */
    public function team($guard = null)
    {
        return optional(auth($guard)->user())->team;
    }

    /**
     * Respond with a created response and associate a location if provided.
     *
     * @param  null  $content
     * @param  null  $location
     * @return JsonResponse
     */
    public function created($content = null, $location = null): JsonResponse
    {
        return tap($this->json($content, 201), function (JsonResponse $response) use ($location) {
            if (! is_null($location)) {
                $response->header('Location', $location);
            }
        });
    }

    /**
     * @param  string|array|object  $content
     * @param  int  $statusCode
     * @return JsonResponse
     */
    public function json($content, $statusCode = 200): JsonResponse
    {
        return response()->json($content, $statusCode);
    }

    /**
     * Respond with an accepted response and associate a location and/or content if provided.
     *
     * @param  mixed  $content
     *
     * @param  null|string  $location
     * @return JsonResponse
     */
    public function accepted($content = null, $location = null): JsonResponse
    {
        return tap($this->json($content, 202), function (JsonResponse $response) use ($location) {
            if (! is_null($location)) {
                $response->header('Location', $location);
            }
        });
    }

    /**
     * Respond with a no content response.
     *
     * @return JsonResponse
     */
    public function noContent(): JsonResponse
    {
        return $this->json(null, 204);
    }

    public function success($content = null): JsonResponse
    {
        return $this->json($content, 200);
    }

    /**
     * Return a 404 not found error.
     *
     * @param  string  $message
     * @return void
     * @throws NotFoundHttpException
     */
    public function errorNotFound($message = 'Not Found'): void
    {
        $this->error($message, 404);
    }

    /**
     * Return an error response.
     *
     * @param  string  $message
     * @param  int  $statusCode
     * @return void
     * @throws HttpException|NotFoundHttpException|HttpResponseException
     */
    public function error($message, $statusCode): void
    {
        abort($statusCode, $message);
    }

    /**
     * Return a 400 bad request error.
     *
     * @param  string  $message
     *
     * @return void
     * @throws HttpException
     */
    public function errorBadRequest($message = 'Bad Request'): void
    {
        $this->error($message, 400);
    }

    /**
     * Return a 403 forbidden error.
     *
     * @param  string  $message
     * @return void
     * @throws HttpException
     */
    public function errorForbidden($message = 'Forbidden'): void
    {
        $this->error($message, 403);
    }

    /**
     * Return a 500 internal server error.
     *
     * @param  string  $message
     * @return void
     * @throws HttpException
     */
    public function errorInternal($message = 'Internal Error'): void
    {
        $this->error($message, 500);
    }

    /**
     * Return a 401 unauthorized error.
     *
     * @param  string  $message
     * @return void
     * @throws HttpException
     */
    public function errorUnauthorized($message = 'Unauthorized'): void
    {
        $this->error($message, 401);
    }

    /**
     * Return a 405 method not allowed error.
     *
     * @param  string  $message
     * @return void
     * @throws HttpException
     */
    public function errorMethodNotAllowed($message = 'Method Not Allowed'): void
    {
        $this->error($message, 405);
    }
}
