<?php

namespace Shield\Shield\Test;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Assert;
use Shield\Shield\Contracts\Service;
use Shield\Shield\Manager;
use Shield\Testing\TestCase;

/**
 * Class ManagerTest
 *
 * @package \Shield\Shield\Test
 */
class ManagerTest extends TestCase
{
    /**
     * @var \Shield\Shield\Manager
     */
    private $manager;

    public function setUp()
    {
        parent::setUp();

        $this->manager = new Manager;
    }

    /**
     * @test
     * @expectedException \Shield\Shield\Exceptions\UnknownServiceException
     * @expectedExceptionMessage Service [unknown] not found.
     */
    public function it_throws_exception_if_service_not_found()
    {
        $this->manager->passes('unknown', $this->request());
    }

    /**
     * @test
     * @expectedException \Shield\Shield\Exceptions\UnknownServiceException
     * @expectedExceptionMessage Service [unknown] must have a driver.
     */
    public function it_throws_exception_if_service_does_not_have_a_driver()
    {
        $this->app['config']['shield.services.unknown'] = [];

        $this->manager->passes('unknown', $this->request());
    }

    /**
     * @test
     * @expectedException \Shield\Shield\Exceptions\UnsupportedDriverException
     * @expectedExceptionMessage Driver [Shield\Shield\Test\Random] must implement [Shield\Shield\Contracts\Service].
     */
    public function it_throws_exception_if_driver_is_not_a_service()
    {
        $this->app['config']['shield.services.example'] = [
            'driver' => Random::class
        ];

        $this->manager->passes('example', $this->request());
    }

    /** @test */
    public function it_fails_if_expected_header_is_not_there()
    {
        $this->app['config']['shield.services.example'] = [
            'driver' => Example::class
        ];

        Assert::assertFalse($this->manager->passes('example', $this->request()));
    }

    /** @test */
    public function it_passes_if_expected_header_is_there()
    {
        $this->app['config']['shield.services.example'] = [
            'driver' => Example::class
        ];

        $request = $this->request();
        $request->headers->add(['X-Custom-Header' => 'custom data']);

        Assert::assertTrue($this->manager->passes('example', $request));
    }
}

class Example implements Service {

    public function verify(Request $request, Collection $config): bool
    {
        return true;
    }

    public function headers(): array
    {
        return ['X-Custom-Header'];
    }
}

class Random {}
