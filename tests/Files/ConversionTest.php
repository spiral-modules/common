<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tests\Files;

use Spiral\Files\FileManager;

class ConversionTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalizeFilePath()
    {
        $files = new FileManager();

        $this->assertSame('/abc/file.name', $files->normalizePath('/abc\\file.name'));
        $this->assertSame('/abc/file.name', $files->normalizePath('\\abc//file.name'));
    }

    public function testNormalizeDirectoryPath()
    {
        $files = new FileManager();

        $this->assertSame('/abc/dir/', $files->normalizePath('\\abc/dir', true));
        $this->assertSame('/abc/dir/', $files->normalizePath('\\abc//dir', true));
    }

    public function testRelativePath()
    {
        $files = new FileManager();

        $this->assertSame(
            'some-filename.txt',
            $files->relativePath('/abc/some-filename.txt', '/abc')
        );

        $this->assertSame(
            '../some-filename.txt',
            $files->relativePath('/abc/../some-filename.txt', '/abc')
        );
    }
}