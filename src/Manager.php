<?php

namespace Shield\Shield;

use Illuminate\Support\Arr;
use Shield\Shield\Contracts\Service;
use Shield\Shield\Exceptions\UnknownServiceException;
use Closure;
use Illuminate\Http\Request;
use Shield\Shield\Exceptions\UnsupportedDriverException;

class Manager
{
    public function passes(string $service, Request $request): bool
    {
        /** @var Service $instance */
        $instance = $this->validate($service);

        if ($this->checkHeaders($instance->headers(), $request)) {
            return $instance->verify($request, collect(config('shield.services.' . $service . '.options', [])));
        }

        return false;
    }

    /**
     * @param string $service
     *
     * @return Service
     */
    protected function validate(string $service)
    {
        throw_unless(Arr::exists(config('shield.services'), $service), UnknownServiceException::class, sprintf('Service [%s] not found.', $service));
        throw_unless(Arr::exists(config('shield.services.' . $service), 'driver'), UnknownServiceException::class, sprintf('Service [%s] must have a driver.', $service));

        $service = app(config('shield.services.' . $service . '.driver'));

        throw_unless($service instanceof Service, UnsupportedDriverException::class, sprintf('Driver [%s] must implement [%s].', get_class($service), Service::class));

        return $service;
    }

    public function checkHeaders(array $headers, Request $request)
    {
        foreach ($headers as $header) {
            if (!$request->hasHeader($header)) return false;
        }

        return true;
    }
}
