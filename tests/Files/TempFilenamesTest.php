<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tests\Files;

use Spiral\Files\FileManager;
use Spiral\Files\FilesInterface;

class TempFilenamesTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $files = new FileManager();
        $files->ensureDirectory(FIXTURE_DIRECTORY, FilesInterface::RUNTIME);
    }

    public function tearDown()
    {
        $files = new FileManager();
        $files->deleteDirectory(FIXTURE_DIRECTORY, true);
    }

    public function testTempFilename()
    {
        $files = new FileManager();

        $tempFilename = $files->tempFilename();
        $this->assertTrue($files->exists($tempFilename));
        $this->assertSame('', $files->read($tempFilename));

        $files->write($tempFilename, 'sample-data');
        $this->assertSame('sample-data', $files->read($tempFilename));
    }

    public function testTempExtension()
    {
        $files = new FileManager();

        $tempFilename = $files->tempFilename('txt');
        $this->assertTrue($files->exists($tempFilename));
        $this->assertSame('txt', $files->extension($tempFilename));
        $this->assertSame('', $files->read($tempFilename));

        $files->write($tempFilename, 'sample-data');
        $this->assertSame('sample-data', $files->read($tempFilename));
    }

    public function testTempCustomLocation()
    {
        $files = new FileManager();

        $tempFilename = $files->tempFilename('txt', FIXTURE_DIRECTORY);
        $this->assertTrue($files->exists($tempFilename));

        $this->assertSame('txt', $files->extension($tempFilename));
        $this->assertSame(
            $files->normalizePath(FIXTURE_DIRECTORY, true),
            $files->normalizePath(dirname($tempFilename), true)
        );

        $this->assertSame('', $files->read($tempFilename));

        $files->write($tempFilename, 'sample-data');
        $this->assertSame('sample-data', $files->read($tempFilename));
    }

    public function testAutoRemovalFilesWithExtensions()
    {
        $files = new FileManager();

        $tempFilename = $files->tempFilename('txt');
        $this->assertTrue($files->exists($tempFilename));
        $this->assertSame('', $files->read($tempFilename));

        $files->write($tempFilename, 'sample-data');
        $this->assertSame('sample-data', $files->read($tempFilename));

        $files->__destruct();
        $this->assertFileNotExists($tempFilename);
    }
}