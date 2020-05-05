<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Get image mimetype from resource in PHP/GD
 *
 * If you've got access to the binary data of the image (as the use of imagecreatefromstring() suggests), you can detect the file-type "manually".
 * @see https://stackoverflow.com/a/2826731
 *
 */
class Gisfromstring {
    const proto_default = 'gisfromstring';
    protected static $proto = null;
    protected static $imgdata = null;

    public function __construct()
    {
        // Do something with $params
    }

    static function getImageSize($imgdata) {
        if (null === self::$proto) {
            self::register();
        }
        self::$imgdata = $imgdata;
        // note: @ suppresses "Read error!" notices if $imgdata isn't valid
        return @getimagesize(self::$proto . '://');
    }

    static function getMimeType($imgdata) {
        return is_array($gis = self::getImageSize($imgdata))
            ? $gis['mime']
            : $gis;
    }

    // streamwrapper helper:

    const unregister = null;

    // register|unregister wrapper for the given protocol|scheme
    // return registered protocol or null
    static function register(
        $proto = self::proto_default // protocol or scheme
    ) {
        if (self::unregister === $proto) { // unregister if possible
            if (null === self::$proto) {
                return null;
            }
            if (!stream_wrapper_unregister(self::$proto)) {
                return null;
            }
            $return = self::$proto;
            self::$proto = null;
            return $return;
        }
        if (!preg_match('/\A([a-zA-Z][a-zA-Z0-9.+\-]*)(:([\/\x5c]{0,3}))?/', $proto, $h)) {
            throw new Exception(
                sprintf('could not register invalid scheme or protocol name "%s" as streamwrapper', $proto)
            );
        }
        if (!stream_wrapper_register($proto = $h[1], __CLASS__)) {
            throw new Exception(
                sprintf('protocol "%s" already registered as streamwrapper', $proto)
            );
        }
        return self::$proto = $proto;
    }

    // streamwrapper API:

    function stream_open($path, $mode) {
        $this->str = (string) self::$imgdata;
        $this->fsize = strlen($this->str);
        $this->fpos = 0;
        return true;
    }

    function stream_close() {
        self::$imgdata = null;
    }

    function stream_read($num_bytes) {
        if (!is_numeric($num_bytes) || $num_bytes < 1) {
            return false;
        }
        /* uncomment this if needed
        if ($this->fpos + $num_bytes > 65540 * 4) {
            // prevent getimagesize() from scanning the whole file
            // 65_540 is the maximum possible bytesize of a JPEG segment
            return false;
        }
        */
        if ($this->fpos + $num_bytes > $this->fsize) {
            $num_bytes = $this->fsize - $this->fpos;
        }
        $read = substr($this->str, $this->fpos, $num_bytes);
        $this->fpos += strlen($read);
        return $read;
    }

    function stream_eof() {
        return $this->fpos >= $this->fsize;
    }

    function stream_tell() {
        return $this->fpos;
    }

    function stream_seek($off, $whence = SEEK_SET) {
        if (SEEK_CUR === $whence) {
            $off = $this->fpos + $off;
        }
        elseif (SEEK_END === $whence) {
            $off = $this->fsize + $off;
        }
        if ($off < 0 || $off > $this->fsize) {
            return false;
        }
        $this->fpos = $off;
        return true;
    }
} // EOF Gisfromstring