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

class InformationTest extends \PHPUnit_Framework_TestCase
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

    public function testTime()
    {
        $files = new FileManager();
        $filename = FIXTURE_DIRECTORY . '/test.txt';

        $files->write($filename, 'data', FilesInterface::READONLY);
        $this->assertEquals(filemtime($filename), $files->time($filename));
    }

    /**
     * @expectedException \Spiral\Files\Exceptions\FileNotFoundException
     * @expectedExceptionMessageRegExp /File '.*test.txt' not found/
     */
    public function testTimeMissingFile()
    {
        $files = new FileManager();
        $filename = FIXTURE_DIRECTORY . '/test.txt';

        $files->time($filename);
    }

    public function testMD5()
    {
        $files = new FileManager();
        $filename = FIXTURE_DIRECTORY . '/test.txt';

        $files->write($filename, 'data');
        $this->assertEquals(md5_file($filename), $files->md5($filename));
        $this->assertEquals(md5('data'), $files->md5($filename));
    }

    /**
     * @expectedException \Spiral\Files\Exceptions\FileNotFoundException
     * @expectedExceptionMessageRegExp /File '.*test.txt' not found/
     */
    public function testMD5MissingFile()
    {
        $files = new FileManager();
        $filename = FIXTURE_DIRECTORY . '/test.txt';

        $files->md5($filename);
    }

    public function testExtension()
    {
        $files = new FileManager();

        $this->assertSame('txt', $files->extension('test.txt'));
        $this->assertSame('txt', $files->extension('test.TXT'));
        $this->assertSame('txt', $files->extension('test.data.TXT'));
    }

    public function testExists()
    {
        $files = new FileManager();
        $filename = FIXTURE_DIRECTORY . '/test.txt';

        $this->assertFalse($files->exists($filename));
        $this->assertSame(file_exists($filename), $files->exists($filename));

        $files->write($filename, 'data');
        $this->assertTrue($files->exists($filename));
        $this->assertSame(file_exists($filename), $files->exists($filename));
    }

    public function testSize()
    {
        $files = new FileManager();
        $filename = FIXTURE_DIRECTORY . '/test.txt';

        $this->assertFalse($files->exists($filename));
        $files->write($filename, 'some-data-string');
        $this->assertTrue($files->exists($filename));

        $this->assertSame(strlen('some-data-string'), $files->size($filename));
    }

    /**
     * @expectedException \Spiral\Files\Exceptions\FileNotFoundException
     * @expectedExceptionMessageRegExp /File '.*test.txt' not found/
     */
    public function testSizeMissingFile()
    {
        $files = new FileManager();
        $filename = FIXTURE_DIRECTORY . '/test.txt';

        $this->assertFalse($files->exists($filename));
        $files->size($filename);
    }

    public function testLocalUri()
    {
        $files = new FileManager();
        $filename = FIXTURE_DIRECTORY . '/test.txt';

        $this->assertFalse($files->exists($filename));
        $files->write($filename, 'data');
        $this->assertSame($filename, $files->localFilename($filename));
    }

    /**
     * @expectedException \Spiral\Files\Exceptions\FileNotFoundException
     * @expectedExceptionMessageRegExp /File '.*test.txt' not found/
     */
    public function testLocalUriMissingFile()
    {
        $files = new FileManager();
        $filename = FIXTURE_DIRECTORY . '/test.txt';

        $this->assertFalse($files->exists($filename));
        $files->localFilename($filename);
    }

    public function testIsFile()
    {
        $files = new FileManager();
        $filename = FIXTURE_DIRECTORY . '/test.txt';

        $this->assertFalse($files->exists($filename));
        $files->write($filename, 'data');
        $this->assertTrue($files->exists($filename));

        $this->assertTrue($files->isFile($filename));
        $this->assertSame(is_file($filename), $files->isFile($filename));

        $this->assertFalse($files->isDirectory($filename));
        $this->assertSame(is_dir($filename), $files->isDirectory($filename));
    }

    public function testIsMissingFile()
    {
        $files = new FileManager();
        $filename = FIXTURE_DIRECTORY . '/test.txt';

        $this->assertFalse($files->exists($filename));

        $this->assertFalse($files->isFile($filename));
        $this->assertSame(is_file($filename), $files->isFile($filename));

        $this->assertFalse($files->isDirectory($filename));
        $this->assertSame(is_dir($filename), $files->isDirectory($filename));
    }

    public function testIsDirectory()
    {
        $files = new FileManager();
        $directory = FIXTURE_DIRECTORY . '/directory/';

        $this->assertFalse($files->exists($directory));
        $files->ensureDirectory($directory);
        $this->assertTrue($files->exists($directory));

        $this->assertFalse($files->isFile($directory));
        $this->assertSame(is_file($directory), $files->isFile($directory));

        $this->assertTrue($files->isDirectory($directory));
        $this->assertSame(is_dir($directory), $files->isDirectory($directory));
    }

    public function testIsMissingDirectory()
    {
        $files = new FileManager();
        $directory = FIXTURE_DIRECTORY . '/directory/';

        $this->assertFalse($files->exists($directory));

        $this->assertFalse($files->isFile($directory));
        $this->assertSame(is_file($directory), $files->isFile($directory));

        $this->assertFalse($files->isDirectory($directory));
        $this->assertSame(is_dir($directory), $files->isDirectory($directory));
    }

    public function testIsDirectoryNoSlash()
    {
        $files = new FileManager();
        $directory = FIXTURE_DIRECTORY . '/directory';

        $this->assertFalse($files->exists($directory));
        $files->ensureDirectory($directory);
        $this->assertTrue($files->exists($directory));

        $this->assertFalse($files->isFile($directory));
        $this->assertSame(is_file($directory), $files->isFile($directory));

        $this->assertTrue($files->isDirectory($directory));
        $this->assertSame(is_dir($directory), $files->isDirectory($directory));
    }

    public function testGetFiles()
    {

    }
}