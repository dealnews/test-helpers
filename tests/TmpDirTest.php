<?php

namespace DealNews\TestHelpers\Tests;

use \DealNews\TestHelpers\TmpDir;

class TmpDirTest extends \PHPUnit\Framework\TestCase {

    use TmpDir;

    public function testTmpDir() {
        $dir = $this->tmpDir();

        $this->assertTrue(is_dir($dir));

        $dir2 = $this->tmpDir($dir);

        $this->assertTrue(is_dir($dir2));

        rmdir($dir2);
        rmdir($dir);
    }
}
