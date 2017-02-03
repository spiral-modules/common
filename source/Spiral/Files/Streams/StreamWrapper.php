<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Files\Streams;

use Psr\Http\Message\StreamInterface;
use Spiral\Files\Exceptions\WrapperException;

/**
 * Spiral converter of PSR-7 streams to virtual filenames. Static as hell.
 */
class StreamWrapper
{
    /**
     * @var bool
     */
    private static $registered = false;

    /**
     * Uris associated with StreamInterfaces.
     *
     * @var array
     */
    private static $uris = [];

    /**
     * @var array
     */
    private static $modes = [
        'r'   => 33060,
        'rb'  => 33060,
        'r+'  => 33206,
        'rb+' => 33206,
        'w'   => 33188,
        'wb'  => 33188
    ];

    /**
     * @var StreamInterface
     */
    private $stream = null;

    /**
     * @var int
     */
    private $mode = 0;

    /**
     * Stream context.
     *
     * @var resource
     */
    public $context = null;

    /**
     * Check if StreamInterface ended.
     *
     * @return bool
     */
    public function stream_eof()
    {
        return $this->stream->eof();
    }

    /**
     * Open pre-mocked StreamInterface by it's unique uri.
     *
     * @param string $path
     * @param int    $mode
     * @param int    $options
     * @param string &$opened_path
     *
     * @return bool
     */
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        if (!isset(self::$uris[$path])) {
            return false;
        }

        $this->stream = self::$uris[$path];
        $this->mode = $mode;

        $this->stream->rewind();

        return true;
    }

    /**
     * Read data from StreamInterface.
     *
     * @param int $count
     *
     * @return string
     */
    public function stream_read($count)
    {
        return $this->stream->read($count);
    }

    /**
     * Seek into StreamInterface.
     *
     * @param int $offset
     * @param int $whence = SEEK_SET
     *
     * @return bool
     */
    public function stream_seek($offset, $whence = SEEK_SET)
    {
        //Note, MongoDB native streams DO NOT support seeking at the moment
        //@see https://jira.mongodb.org/browse/PHPLIB-213
        $this->stream->seek($offset, $whence);

        return true;
    }

    /**
     * Get StreamInterface stats.
     *
     * @see stat()
     * @return array|null
     */
    public function stream_stat()
    {
        return $this->getStreamStats($this->stream);
    }

    /**
     * Get StreamInterface position.
     *
     * @return int
     */
    public function stream_tell()
    {
        //Note, MongoDB native streams DO NOT support seeking at the moment
        //@see https://jira.mongodb.org/browse/PHPLIB-213
        return $this->stream->tell();
    }

    /**
     * Write content into StreamInterface.
     *
     * @param string $data
     *
     * @return int
     */
    public function stream_write($data)
    {
        return $this->stream->write($data);
    }

    /**
     * Get stats based on wrapped StreamInterface by it's mocked uri.
     *
     * @see stat()
     *
     * @param string $path
     * @param int    $flags
     *
     * @return array|null
     */
    public function url_stat($path, $flags)
    {
        if (!isset(self::$uris[$path])) {
            return null;
        }

        return $this->getStreamStats(self::$uris[$path]);
    }

    /**
     * Helper method used to correctly resolve StreamInterface stats.
     *
     * @param StreamInterface $stream
     *
     * @return array
     */
    private function getStreamStats(StreamInterface $stream)
    {
        $mode = $this->mode;
        if (empty($mode)) {
            if ($stream->isReadable()) {
                $mode = 'r';
            }

            if ($stream->isWritable()) {
                $mode = !empty($mode) ? 'r+' : 'w';
            }
        }

        return [
            'dev'     => 0,
            'ino'     => 0,
            'mode'    => self::$modes[$mode],
            'nlink'   => 0,
            'uid'     => 0,
            'gid'     => 0,
            'rdev'    => 0,
            'size'    => (string)$stream->getSize(),
            'atime'   => 0,
            'mtime'   => 0,
            'ctime'   => 0,
            'blksize' => 0,
            'blocks'  => 0
        ];
    }

    /**
     * Register stream wrapper.
     */
    public static function register()
    {
        if (self::$registered) {
            return;
        }

        stream_wrapper_register('spiral', __CLASS__);
        self::$registered = true;
    }

    /**
     * Register StreamInterface and get unique url for it.
     *
     * @param StreamInterface $stream
     *
     * @return string
     */
    public static function localFilename(StreamInterface $stream)
    {
        self::register();

        $uri = 'spiral://' . spl_object_hash($stream);
        self::$uris[$uri] = $stream;

        return $uri;
    }

    /**
     * Check if given uri points to one of wrapped streams.
     *
     * @param string $uri
     *
     * @return bool
     */
    public static function isWrapped($uri)
    {
        return isset(self::$uris[$uri]);
    }

    /**
     * Create StreamInterface associated resource.
     *
     * @param StreamInterface $stream
     *
     * @return resource
     * @throws WrapperException
     */
    public static function getResource(StreamInterface $stream)
    {
        $mode = null;
        if ($stream->isReadable()) {
            $mode = 'r';
        }

        if ($stream->isWritable()) {
            $mode = !empty($mode) ? 'r+' : 'w';
        }

        if (empty($mode)) {
            throw new WrapperException("Stream is not available in read or write modes");
        }

        return fopen(self::localFilename($stream), $mode);
    }

    /**
     * Free uri dedicated to specified StreamInterface. Method is useful for long living
     * applications.
     *
     * @param string|StreamInterface $uri String uri or StreamInterface.
     */
    public static function releaseUri($uri)
    {
        if ($uri instanceof StreamInterface) {
            $uri = 'spiral://' . spl_object_hash($uri);
        }

        unset(self::$uris[$uri]);
    }
}
