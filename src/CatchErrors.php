<?php

namespace DealNews\TestHelpers;

/**
 * Catches PHP errors and throws an exception
 *
 * @author      Brian Moon <brianm@dealnews.com>
 * @copyright   1997-Present DealNews.com, Inc
 * @package     DealNews\TestHelpers
 */
trait CatchErrors {

    public function setUp(): void {
        parent::setUp();
        set_error_handler(
            static function ($errno, $errstr) {
                throw new \Exception($errstr, $errno);
            },
            E_ALL
        );
    }

    public function tearDown(): void {
        parent::tearDown();
        restore_error_handler();
    }
}
