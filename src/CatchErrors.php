<?php

namespace DealNews\TestHelpers;

/**
 * Catches PHP errors and throws an exception
 *
 * @author      Brian Moon <brianm@dealnews.com>
 * @copyright   1997-Present DealNews.com, Inc
 * @package     DealNews\TestHelpers
 *
 * @phan-suppress PhanUnreferencedClass
 */
trait CatchErrors {

    public function setUp(): void {
        // @phan-suppress-next-line PhanTraitParentReference
        parent::setUp();
        set_error_handler(
            static function ($errno, $errstr) {
                throw new \Exception($errstr, $errno);
            },
            E_ALL
        );
    }

    public function tearDown(): void {
        // @phan-suppress-next-line PhanTraitParentReference
        parent::tearDown();
        restore_error_handler();
    }
}
