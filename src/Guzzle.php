<?php

namespace DealNews\TestHelpers;

use \GuzzleHttp\Client as GuzzleClient;
use \GuzzleHttp\Handler\MockHandler;
use \GuzzleHttp\HandlerStack;
use \GuzzleHttp\Middleware;
use \GuzzleHttp\Psr7\Response;

/**
 * Helper functions for using Guzzle
 *
 * @author      Brian Moon <brianm@dealnews.com>
 * @author      Jeremy Earle <jearle@dealnews.com>
 * @copyright   1997-Present DealNews.com, Inc
 * @package     \DealNews\TestHelpers
 */
trait Guzzle {

    use Fixtures;

    /**
     * Create a guzzle mock.
     *
     * @param integer|array $codes
     * @param array $fixtures
     * @param array $container
     *
     * @return GuzzleClient
     */
    public function makeGuzzleMock($codes, array $fixtures, array &$container): GuzzleClient {
        $responses = [];

        if (is_array($codes)) {
            $this->assertEquals(count($codes), count($fixtures), 'When using an array of codes, the number of codes must match the number of fixtures');
        }

        foreach ($fixtures as $fixture) {
            if (is_array($fixture)) {
                $data = json_encode($fixture);
            } elseif (is_string($fixture) && $this->isFixtureFile($fixture)) {
                $data = $this->getFixtureData($fixture);
            } else {
                $data = $fixture;
            }
            $code        = is_array($codes) ? array_shift($codes) : $codes;
            $responses[] = new Response($code, ['Content-Type' => 'application/json'], $data);
        }

        $history       = Middleware::history($container);
        $mock          = new MockHandler($responses);
        $handler_stack = HandlerStack::create($mock);
        $handler_stack->push($history);

        return new GuzzleClient(['handler' => $handler_stack]);
    }
}
