<?php

namespace Shield\Shield\Test\Unit\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Assert;
use Shield\Shield\Contracts\Service;
use Shield\Shield\Support\BasicAuth;
use Shield\Testing\TestCase;

/**
 * Class BasicAuthTest
 *
 * @package \Shield\Shield\Test\Unit\Support
 */
class BasicAuthTest extends TestCase
{
    /**
     * @var \Shield\Shield\Test\Unit\Support\Example
     */
    private $service;

    public function setUp()
    {
        parent::setUp();

        $this->service = new Example;
    }

    /** @test */
    public function it_will_fail_if_invalid_credentials()
    {
        $request = $this->request();
        $request->headers->add([
            'PHP-AUTH-USER' => 'user',
            'PHP-AUTH-PW' => 'password',
        ]);

        Assert::assertFalse($this->service->checkBasic($request, 'user', 'pass'));
    }

    /** @test */
    public function it_will_fail_if_invalid_headers()
    {
        $request = $this->request();
        $request->headers->add([
            'PHP-AUTH-USER' => 'user',
            'PHP-AUTH-PASS' => 'pass',
        ]);

        Assert::assertFalse($this->service->checkBasic($request, 'user', 'pass'));
    }

    /** @test */
    public function it_will_pass_if_correct_credentials()
    {
        $request = $this->request();
        $request->headers->add([
            'PHP-AUTH-USER' => 'user',
            'PHP-AUTH-PW' => 'pass',
        ]);

        Assert::assertTrue($this->service->checkBasic($request, 'user', 'pass'));
    }
}

class Example implements Service {

    use BasicAuth;

    public function verify(Request $request, Collection $config): bool
    {
        return true;
    }

    public function headers(): array
    {
        return [];
    }
}
