<?php

namespace Shield\Shield\Test;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Assert;
use Shield\Shield\Contracts\Service;
use Shield\Shield\Http\Middleware\Shield;
use Shield\Shield\Manager;
use Shield\Testing\TestCase;

/**
 * Class MiddlewareTest
 *
 * @package \Shield\Shield\Test
 */
class MiddlewareTest extends TestCase
{
    /** @test */
    public function it_responds_with_bad_request_if_check_fails()
    {
        $this->app['config']['shield.services.example'] = [
            'driver' => Bad::class
        ];

        $manager = new Manager;
        $middleware = new Shield($manager);

        /** @var Response $response */
        $response = $middleware->handle($this->request(), function (){}, 'example');

        Assert::assertInstanceOf(Response::class, $response);
        Assert::assertEquals(400, $response->getStatusCode());
        Assert::assertEquals('Bad Request', $response->getContent());
    }

    /** @test */
    public function it_calls_next_if_successful()
    {
        $this->app['config']['shield.services.example'] = [
            'driver' => Good::class
        ];

        $manager = new Manager;
        $middleware = new Shield($manager);

        $resp = Response::create('Test', 200);

        $closure =  function () use ($resp) {
            return $resp;
        };

        /** @var Response $response */
        $response = $middleware->handle($this->request(), $closure, 'example');

        Assert::assertSame($resp, $response);
    }
}

class Good implements Service {

    public function verify(Request $request, Collection $config): bool
    {
        return true;
    }

    public function headers(): array
    {
        return [];
    }
}

class Bad implements Service {

    public function verify(Request $request, Collection $config): bool
    {
        return false;
    }

    public function headers(): array
    {
        return [];
    }
}
