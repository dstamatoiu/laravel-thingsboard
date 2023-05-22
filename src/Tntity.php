<?php

namespace JalalLinuX\Thingsboard;

use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Http;
use JalalLinuX\Thingsboard\Exceptions\TntityExceptionHandler;
use JalalLinuX\Thingsboard\Interfaces\ThingsboardUser;
use Jenssegers\Model\Model;

abstract class Tntity extends Model
{
    protected string $_token;

    protected function api(bool $auth = false, bool $handleException = true): PendingRequest
    {
        $baseUri = config('thingsboard.rest.base_uri');
        $baseUri = str_ends_with($baseUri, '/') ? substr($baseUri, 0, -1) : $baseUri;
        $request = Http::baseUrl("{$baseUri}/api")->acceptJson();

        if ($auth) {
            throw_if(! isset($this->_token), $this->exception('method need authentication token.'));
            $request = $request->withHeaders([
                config('thingsboard.rest.authorization.header_key') => config('thingsboard.rest.authorization.token_type')." {$this->_token}",
            ]);
        }

        return ! $handleException ? $request : $request->throw(
            fn (Response $response, RequestException $e) => TntityExceptionHandler::make($response, $e)->handle()
        );
    }

    public function withUser(ThingsboardUser $user): self
    {
        return tap($this, fn () => $this->_token = Thingsboard::fetchUserToken($user));
    }

    public function get($key = null, $default = null)
    {
        return data_get($this->getAttributes(), $key, $default);
    }

    public function forceAttribute($key)
    {
        throw_if(
            is_null($value = @$this->{$key}),
            $this->exception("{$key} attribute is required in ")
        );

        return $value;
    }

    public function getAttributes(): array
    {
        foreach ($this->attributes as $k => $v) {
            $this->attributes[$k] = $this->hasCast($k) ? $this->castAttribute($k, $v) : $v;
        }

        return $this->attributes;
    }

    protected function exception(string $message, $code = 500): Exception
    {
        $message = debug_backtrace()[1]['class'].'@'.debug_backtrace()[1]['function'].": {$message}";

        return new Exception($message, $code);
    }

    public function toResource(string $class): JsonResource
    {
        return new $class($this);
    }

    public function fill(array $attributes = []): self
    {
        if (empty($attributes)) {
            return $this;
        }

        return parent::fill($attributes);
    }

    public static function instance(array $attributes = []): self
    {
        return new static($attributes);
    }
}