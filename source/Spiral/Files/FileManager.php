<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Files;

use Spiral\Core\Component;
use Spiral\Core\Container\SingletonInterface;
use Spiral\Files\Exceptions\FileNotFoundException;
use Spiral\Files\Exceptions\FilesException;
use Spiral\Files\Exceptions\WriteErrorException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;

/**
 * Default abstraction for file management operations.
 */
class FileManager extends Component implements SingletonInterface, FilesInterface
{
    /**
     * Default file mode for this manager.
     */
    const DEFAULT_FILE_MODE = self::READONLY;

    /**
     * Files to be removed when component destructed.
     *
     * @var array
     */
    private $destructFiles = [];

    /**
     * FileManager constructor.
     */
    public function __construct()
    {
        register_shutdown_function([$this, '__destruct']);
    }

    /**
     * {@inheritdoc}
     *
     * @param bool $recursivePermissions Propagate permissions on created directories.
     */
    public function ensureDirectory(
        string $directory,
        int $mode = null,
        bool $recursivePermissions = true
    ): bool {
        if (empty($mode)) {
            $mode = self::DEFAULT_FILE_MODE;
        }

        //Directories always executable
        $mode = $mode | 0111;
        if (is_dir($directory)) {
            //Exists :(
            return $this->setPermissions($directory, $mode);
        }

        if (!$recursivePermissions) {
            return mkdir($directory, $mode, true);
        }

        $directoryChain = [basename($directory)];

        $baseDirectory = $directory;
        while (!is_dir($baseDirectory = dirname($baseDirectory))) {
            $directoryChain[] = basename($baseDirectory);
        }

        foreach (array_reverse($directoryChain) as $directory) {
            if (!mkdir($baseDirectory = "{$baseDirectory}/{$directory}")) {
                return false;
            }

            chmod($baseDirectory, $mode);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read(string $filename): string
    {
        if (!$this->exists($filename)) {
            throw new FileNotFoundException($filename);
        }

        return file_get_contents($filename);
    }

    /**
     * {@inheritdoc}
     *
     * @param bool $append To append data at the end of existed file.
     */
    public function write(
        string $filename,
        string $data,
        int $mode = null,
        bool $ensureDirectory = false,
        bool $append = false
    ): bool {
        $mode = $mode ?? self::DEFAULT_FILE_MODE;

        try {
            if ($ensureDirectory) {
                $this->ensureDirectory(dirname($filename), $mode);
            }

            if ($this->exists($filename)) {
                //Forcing mode for existed file
                $this->setPermissions($filename, $mode);
            }

            $result = file_put_contents(
                $filename,
                $data,
                $append ? FILE_APPEND | LOCK_EX : LOCK_EX
            );

            if ($result !== false) {
                //Forcing mode after file creation
                $this->setPermissions($filename, $mode);
            }
        } catch (\Exception $e) {
            throw new WriteErrorException($e->getMessage(), $e->getCode(), $e);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function append(
        string $filename,
        string $data,
        int $mode = null,
        bool $ensureDirectory = false
    ): bool {
        return $this->write($filename, $data, $mode, $ensureDirectory, true);
    }

    /**
     * {@inheritdoc}
     */
    public function localFilename(string $filename): string
    {
        if (!$this->exists($filename)) {
            throw new FileNotFoundException($filename);
        }

        //Since default implementation is local we are allowed to do that
        return $filename;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $filename)
    {
        if ($this->exists($filename)) {
            $result = unlink($filename);

            //Wiping out changes in local file cache
            clearstatcache(false, $filename);

            return $result;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see http://stackoverflow.com/questions/3349753/delete-directory-with-files-in-it
     *
     * @param string $directory
     * @param bool   $contentOnly
     *
     * @throws FilesException
     */
    public function deleteDirectory(string $directory, bool $contentOnly = false)
    {
        if (!$this->isDirectory($directory)) {
            throw new FilesException("Undefined or invalid directory {$directory}");
        }

        $files = new \RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                $this->delete($file->getRealPath());
            }
        }

        if (!$contentOnly) {
            rmdir($directory);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function move(string $filename, string $destination): bool
    {
        if (!$this->exists($filename)) {
            throw new FileNotFoundException($filename);
        }

        return rename($filename, $destination);
    }

    /**
     * {@inheritdoc}
     */
    public function copy(string $filename, string $destination): bool
    {
        if (!$this->exists($filename)) {
            throw new FileNotFoundException($filename);
        }

        return copy($filename, $destination);
    }

    /**
     * {@inheritdoc}
     */
    public function touch(string $filename, int $mode = null): bool
    {
        if (!touch($filename)) {
            return false;
        }

        return $this->setPermissions($filename, $mode ?? self::DEFAULT_FILE_MODE);
    }

    /**
     * {@inheritdoc}
     */
    public function exists(string $filename): bool
    {
        return file_exists($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function size(string $filename): int
    {
        if (!$this->exists($filename)) {
            throw new FileNotFoundException($filename);
        }

        return filesize($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function extension(string $filename): string
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * {@inheritdoc}
     */
    public function md5(string $filename): string
    {
        if (!$this->exists($filename)) {
            throw new FileNotFoundException($filename);
        }

        return md5_file($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function time(string $filename): int
    {
        if (!$this->exists($filename)) {
            throw new FileNotFoundException($filename);
        }

        return filemtime($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function isDirectory(string $filename): bool
    {
        return is_dir($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function isFile(string $filename): bool
    {
        return is_file($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissions(string $filename): int
    {
        if (!$this->exists($filename)) {
            throw new FileNotFoundException($filename);
        }

        return fileperms($filename) & 0777;
    }

    /**
     * {@inheritdoc}
     */
    public function setPermissions(string $filename, int $mode)
    {
        if (is_dir($filename)) {
            //Directories must always be executable (i.e. 664 for dir => 775)
            $mode |= 0111;
        }

        return $this->getPermissions($filename) == $mode || chmod($filename, $mode);
    }

    /**
     * {@inheritdoc}
     *
     * @param Finder $finder Pre-configured Finder.
     */
    public function getFiles(string $location, string $pattern = null, Finder $finder = null): array
    {
        $finder = $finder ?? new Finder();
        $finder->files()->in($location);

        if (!empty($pattern)) {
            $finder->name($pattern);
        }

        $result = [];
        foreach ($finder->getIterator() as $file) {
            $result[] = $this->normalizePath((string)$file);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function tempFilename(string $extension = '', string $location = null): string
    {
        if (empty($location)) {
            $location = sys_get_temp_dir();
        }

        $filename = tempnam($location, 'spiral');

        if (!empty($extension)) {
            //I should find more original way of doing that
            rename($filename, $filename = "{$filename}.{$extension}");
            $this->destructFiles[] = $filename;
        }

        return $filename;
    }

    /**
     * {@inheritdoc}
     */
    public function normalizePath(string $path, bool $asDirectory = false): string
    {
        $path = str_replace(['//', '\\'], '/', $path);

        //Potentially open links and ../ type directories?
        return rtrim($path, '/') . ($asDirectory ? '/' : '');
    }

    /**
     * {@inheritdoc}
     *
     * @link http://stackoverflow.com/questions/2637945/getting-relative-path-from-absolute-path-in-php
     */
    public function relativePath(string $path, string $from): string
    {
        $path = $this->normalizePath($path);
        $from = $this->normalizePath($from);

        $from = explode('/', $from);
        $path = explode('/', $path);
        $relative = $path;

        foreach ($from as $depth => $dir) {
            //Find first non-matching dir
            if ($dir === $path[$depth]) {
                //Ignore this directory
                array_shift($relative);
            } else {
                //Get number of remaining dirs to $from
                $remaining = count($from) - $depth;
                if ($remaining > 1) {
                    //Add traversals up to first matching directory
                    $padLength = (count($relative) + $remaining - 1) * -1;
                    $relative = array_pad($relative, $padLength, '..');
                    break;
                } else {
                    $relative[0] = './' . $relative[0];
                }
            }
        }

        return implode('/', $relative);
    }

    /**
     * Destruct every temporary file.
     */
    public function __destruct()
    {
        foreach ($this->destructFiles as $filename) {
            $this->delete($filename);
        }
    }
}
