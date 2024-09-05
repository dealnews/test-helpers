<?php

namespace DealNews\TestHelpers;

/**
 * Creates a temporary directory with a random name and returns it
 *
 * @author      Brian Moon <brianm@dealnews.com>
 * @copyright   1997-Present DealNews.com, Inc
 * @package     DealNews\TestHelpers
 */
trait TmpDir {

    public function tmpDir(?string $base_dir = null): string {

        $base_dir ??= sys_get_temp_dir();

        do {
            $success = false;
            $dir = $base_dir . '/' . hash('sha256', (random_bytes(32)));
            if (!file_exists($dir)) {
                $success = mkdir($dir, recursive: true);
            }
        } while (!$success);

        return $dir;
    }
}
