<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-2-21
 * Time: 上午12:22
 */

namespace Lcd\Network;

use Lcd\Core\Config;

class Response {

    /**
     * 保存http响应状态
     * @var array
     * @access protected
     */
    protected static $_statusCodes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'Unsupported Version'
    );

    /**
     * 保存已知的MIME类型映射
     * @var array
     * @access protected
     */
    protected static $_mimeTypes = array(
        'html' => array('text/html', '*/*'),
        'json' => 'application/json',
        'xml' => array('application/xml', 'text/xml'),
        'rss' => 'application/rss+xml',
        'ai' => 'application/postscript',
        'bcpio' => 'application/x-bcpio',
        'bin' => 'application/octet-stream',
        'ccad' => 'application/clariscad',
        'cdf' => 'application/x-netcdf',
        'class' => 'application/octet-stream',
        'cpio' => 'application/x-cpio',
        'cpt' => 'application/mac-compactpro',
        'csh' => 'application/x-csh',
        'csv' => array('text/csv', 'application/vnd.ms-excel'),
        'dcr' => 'application/x-director',
        'dir' => 'application/x-director',
        'dms' => 'application/octet-stream',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'drw' => 'application/drafting',
        'dvi' => 'application/x-dvi',
        'dwg' => 'application/acad',
        'dxf' => 'application/dxf',
        'dxr' => 'application/x-director',
        'eot' => 'application/vnd.ms-fontobject',
        'eps' => 'application/postscript',
        'exe' => 'application/octet-stream',
        'ez' => 'application/andrew-inset',
        'flv' => 'video/x-flv',
        'gtar' => 'application/x-gtar',
        'gz' => 'application/x-gzip',
        'bz2' => 'application/x-bzip',
        '7z' => 'application/x-7z-compressed',
        'hdf' => 'application/x-hdf',
        'hqx' => 'application/mac-binhex40',
        'ico' => 'image/x-icon',
        'ips' => 'application/x-ipscript',
        'ipx' => 'application/x-ipix',
        'js' => 'application/javascript',
        'latex' => 'application/x-latex',
        'lha' => 'application/octet-stream',
        'lsp' => 'application/x-lisp',
        'lzh' => 'application/octet-stream',
        'man' => 'application/x-troff-man',
        'me' => 'application/x-troff-me',
        'mif' => 'application/vnd.mif',
        'ms' => 'application/x-troff-ms',
        'nc' => 'application/x-netcdf',
        'oda' => 'application/oda',
        'otf' => 'font/otf',
        'pdf' => 'application/pdf',
        'pgn' => 'application/x-chess-pgn',
        'pot' => 'application/vnd.ms-powerpoint',
        'pps' => 'application/vnd.ms-powerpoint',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'ppz' => 'application/vnd.ms-powerpoint',
        'pre' => 'application/x-freelance',
        'prt' => 'application/pro_eng',
        'ps' => 'application/postscript',
        'roff' => 'application/x-troff',
        'scm' => 'application/x-lotusscreencam',
        'set' => 'application/set',
        'sh' => 'application/x-sh',
        'shar' => 'application/x-shar',
        'sit' => 'application/x-stuffit',
        'skd' => 'application/x-koan',
        'skm' => 'application/x-koan',
        'skp' => 'application/x-koan',
        'skt' => 'application/x-koan',
        'smi' => 'application/smil',
        'smil' => 'application/smil',
        'sol' => 'application/solids',
        'spl' => 'application/x-futuresplash',
        'src' => 'application/x-wais-source',
        'step' => 'application/STEP',
        'stl' => 'application/SLA',
        'stp' => 'application/STEP',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc' => 'application/x-sv4crc',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        'swf' => 'application/x-shockwave-flash',
        't' => 'application/x-troff',
        'tar' => 'application/x-tar',
        'tcl' => 'application/x-tcl',
        'tex' => 'application/x-tex',
        'texi' => 'application/x-texinfo',
        'texinfo' => 'application/x-texinfo',
        'tr' => 'application/x-troff',
        'tsp' => 'application/dsptype',
        'ttc' => 'font/ttf',
        'ttf' => 'font/ttf',
        'unv' => 'application/i-deas',
        'ustar' => 'application/x-ustar',
        'vcd' => 'application/x-cdlink',
        'vda' => 'application/vda',
        'xlc' => 'application/vnd.ms-excel',
        'xll' => 'application/vnd.ms-excel',
        'xlm' => 'application/vnd.ms-excel',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xlw' => 'application/vnd.ms-excel',
        'zip' => 'application/zip',
        'aif' => 'audio/x-aiff',
        'aifc' => 'audio/x-aiff',
        'aiff' => 'audio/x-aiff',
        'au' => 'audio/basic',
        'kar' => 'audio/midi',
        'mid' => 'audio/midi',
        'midi' => 'audio/midi',
        'mp2' => 'audio/mpeg',
        'mp3' => 'audio/mpeg',
        'mpga' => 'audio/mpeg',
        'ogg' => 'audio/ogg',
        'oga' => 'audio/ogg',
        'spx' => 'audio/ogg',
        'ra' => 'audio/x-realaudio',
        'ram' => 'audio/x-pn-realaudio',
        'rm' => 'audio/x-pn-realaudio',
        'rpm' => 'audio/x-pn-realaudio-plugin',
        'snd' => 'audio/basic',
        'tsi' => 'audio/TSP-audio',
        'wav' => 'audio/x-wav',
        'aac' => 'audio/aac',
        'asc' => 'text/plain',
        'c' => 'text/plain',
        'cc' => 'text/plain',
        'css' => 'text/css',
        'etx' => 'text/x-setext',
        'f' => 'text/plain',
        'f90' => 'text/plain',
        'h' => 'text/plain',
        'hh' => 'text/plain',
        'htm' => array('text/html', '*/*'),
        'ics' => 'text/calendar',
        'm' => 'text/plain',
        'rtf' => 'text/rtf',
        'rtx' => 'text/richtext',
        'sgm' => 'text/sgml',
        'sgml' => 'text/sgml',
        'tsv' => 'text/tab-separated-values',
        'tpl' => 'text/template',
        'txt' => 'text/plain',
        'text' => 'text/plain',
        'avi' => 'video/x-msvideo',
        'fli' => 'video/x-fli',
        'mov' => 'video/quicktime',
        'movie' => 'video/x-sgi-movie',
        'mpe' => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'qt' => 'video/quicktime',
        'viv' => 'video/vnd.vivo',
        'vivo' => 'video/vnd.vivo',
        'ogv' => 'video/ogg',
        'webm' => 'video/webm',
        'mp4' => 'video/mp4',
        'm4v' => 'video/mp4',
        'f4v' => 'video/mp4',
        'f4p' => 'video/mp4',
        'm4a' => 'audio/mp4',
        'f4a' => 'audio/mp4',
        'f4b' => 'audio/mp4',
        'gif' => 'image/gif',
        'ief' => 'image/ief',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpe' => 'image/jpeg',
        'pbm' => 'image/x-portable-bitmap',
        'pgm' => 'image/x-portable-graymap',
        'png' => 'image/png',
        'pnm' => 'image/x-portable-anymap',
        'ppm' => 'image/x-portable-pixmap',
        'ras' => 'image/cmu-raster',
        'rgb' => 'image/x-rgb',
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
        'xbm' => 'image/x-xbitmap',
        'xpm' => 'image/x-xpixmap',
        'xwd' => 'image/x-xwindowdump',
        'ice' => 'x-conference/x-cooltalk',
        'iges' => 'model/iges',
        'igs' => 'model/iges',
        'mesh' => 'model/mesh',
        'msh' => 'model/mesh',
        'silo' => 'model/mesh',
        'vrml' => 'model/vrml',
        'wrl' => 'model/vrml',
        'mime' => 'www/mime',
        'pdb' => 'chemical/x-pdb',
        'xyz' => 'chemical/x-pdb',
        'javascript' => 'application/javascript',
        'form' => 'application/x-www-form-urlencoded',
        'file' => 'multipart/form-data',
        'xhtml' => array('application/xhtml+xml', 'application/xhtml', 'text/xhtml'),
        'xhtml-mobile' => 'application/vnd.wap.xhtml+xml',
        'atom' => 'application/atom+xml',
        'amf' => 'application/x-amf',
        'wap' => array('text/vnd.wap.wml', 'text/vnd.wap.wmlscript', 'image/vnd.wap.wbmp'),
        'wml' => 'text/vnd.wap.wml',
        'wmlscript' => 'text/vnd.wap.wmlscript',
        'wbmp' => 'image/vnd.wap.wbmp',
        'woff' => 'application/x-font-woff',
        'webp' => 'image/webp',
        'appcache' => 'text/cache-manifest',
        'manifest' => 'text/cache-manifest',
        'htc' => 'text/x-component',
        'rdf' => 'application/xml',
        'crx' => 'application/x-chrome-extension',
        'oex' => 'application/x-opera-extension',
        'xpi' => 'application/x-xpinstall',
        'safariextz' => 'application/octet-stream',
        'webapp' => 'application/x-web-app-manifest+json',
        'vcf' => 'text/x-vcard',
        'vtt' => 'text/vtt',
        'mkv' => 'video/x-matroska',
        'pkpass' => 'application/vnd.apple.pkpass'
    );

    /**
     * 是否缓存输出
     * @var bool
     * @access protected
     */
    protected static $_isOutCache = false;

    /**
     * 发送到客户端的标头协议
     * @var string
     * @access protected
     */
    protected static $_protocol = 'HTTP/1.1';

    /**
     * 发送到客户端的状态码
     * @var int
     * @access protected
     */
    protected static $_status = 200;

    /**
     * 内容的发送类型。这是一个“扩展”，将使用$ _mimetypes数组或一个完整的MIME类型转换
     * @var string
     * @access protected
     */
    protected static $_contentType = 'text/html';

    /**
     * 标头缓存列表
     * @var array
     * @access protected
     */
    protected static $_headers = array();

    /**
     * 缓存要响应的内容
     * @var string
     * @access protected
     */
    protected static $_body = null;

    /**
     * 被响应读取文件对象
     * @var
     * @access protected
     */
    protected static $_file = null;

    /**
     * 文件范围。用于请求范围的文件。
     * @var array
     * @access protected
     */
    protected static $_fileRange = null;

    /**
     * 响应内容的字符集编码
     * @var string
     * @access protected
     */
    protected static $_charset = 'utf-8';

    /**
     * 保存所有缓存的指令将转换成头时发送请求
     * @var array
     * @access protected
     */
    protected static $_cacheDirectives = array();

    /**
     * 缓存要发送到客户端的COOKIE
     * @var array
     * @access protected
     */
    protected static $_cookies = array();


    /**
     * 初使化,定义内容，定义状态码，定义状态，定义字符集, CACHE_PAGE缓存判断
     * @access public
     * @param array $options
     */
    public final static function init($options = array()) {
        //这里执行缓存事件
        if(isset(Request::$urlInfo['CACHE_PAGE']) && Request::$urlInfo['CACHE_PAGE'] === true) {
            self::$_isOutCache = true;

            //这里放入缓存事件----------------

            //获取响应头信息
            //获取响应内容
            //------------------
            //设置响应头信息
            //发送内容
            //------------------
            //如果没有缓存或缓存失效,则注册缓存事件

            //
        }

        //内容主体
        if (isset($options['body'])) {
            self::body($options['body']);
        }

        //状态
        if (isset($options['status'])) {
            self::statusCode($options['status']);
        }

        //类型
        if (isset($options['type'])) {
            self::type($options['type']);
        }

        //字符集
        if (!isset($options['charset'])) {
            $options['charset'] = Config::read('DEFAULT_CHARSET');
        }
        self::charset($options['charset']);
    }


    /**
     * @access protected
     * @return void
     */
    protected final static function _setCookies() {
        foreach (self::$_cookies as $name => $c) {
            setcookie(
                $name, $c['value'], $c['expire'], $c['path'],
                $c['domain'], $c['secure'], $c['httpOnly']
            );
        }
    }


    /**
     * @access protected
     * @return void
     */
    protected final static function _setContent() {
        if (self::$_status == 304 || self::$_status ==204) {
            self::body('');
        }
    }


    /**
     * @access protected
     * @return void
     */
    protected final static function _setContentLength() {
        $shouldSetLength = !isset(self::$_headers['Content-Length']) && !in_array(self::$_status, range(301, 307));
        if (isset(self::$_headers['Content-Length']) && self::$_headers['Content-Length'] === false) {
            unset(self::$_headers['Content-Length']);
            return;
        }
        if ($shouldSetLength && !self::outputCompressed()) {
            $offset = ob_get_level() ? ob_get_length() : 0;
            if (ini_get('mbstring.func_overload') & 2) {
                self::length($offset + mb_strlen(self::$_body, '8bit'));
            } else {
                self::length(self::$_headers['Content-Length'] = $offset + strlen(self::$_body));
            }
        }
    }


    /**
     * @access protected
     * @return void
     */
    protected final static function _setContentType() {
        if (self::$_status == 304 || self::$_status == 204) {
            return;
        }
        $whiteList = array(
            'application/javascript',
            'application/json',
            'application/xml',
            'application/rss+xml'
        );

        $charset = false;
        if (
            self::$_charset &&
            (strpos(self::$_contentType, 'text/') === 0 || in_array(self::$_contentType, $whiteList))
        ) {
            $charset = true;
        }

        if ($charset) {
            self::header('Content-Type', self::$_contentType . "; charset=" . self::$_charset);
        } else {
            self::header('Content-Type', self::$_contentType);
        }
    }


    /**
     * @access protected
     * @param string $name header 名称
     * @param string $value header 值
     * @return void
     */
    protected final static function _sendHeader($name, $value = null) {
        if (!headers_sent()) {
            if ($value === null) {
                header($name);
            } else {
                header("{$name}: {$value}");
            }
        }
    }


    /**
     * @access protected
     * @param string $content 响应的内容
     * @return void
     */

    protected final static function _sendContent($content) {
        echo $content;
    }


    /**
     * 发送完整的响应给客户，包括标头和内容主体。
     * @access public
     * @return void
     */
    public final static function send() {
        if (isset(self::$_headers['Location']) && self::$_status === 200) {
            self::statusCode(302);
        }

        self::_setCookies();
        self::_sendHeader(self::$_protocol . ' ' . self::$_status . ' ' . self::$_statusCodes[self::$_status]);
        self::_setContent();
        self::_setContentLength();
        self::_setContentType();

        foreach (self::$_headers as $header => $values) {
            foreach ((array)$values as $value) {
                self::_sendHeader($header, $value);
            }
        }

        if (self::$_file) {
            self::_sendFile(self::$_file, self::$_fileRange);
            self::$_file = self::$_fileRange = null;
        } else {
            self::_sendContent(self::$_body);
            if(self::$_isOutCache) {
                //这里执行输出缓存

            }
        }
    }


    /**
     * 辅助方法从集合中的其它方法的选择产生一个有效的缓存控制头
     * @access protected
     */
    protected final static function _setCacheControl() {
        $control = '';
        foreach (self::$_cacheDirectives as $key => $val) {
            $control .= $val === true ? $key : sprintf('%s=%s', $key, $val);
            $control .= ', ';
        }
        $control = rtrim($control, ', ');
        self::header('Cache-Control', $control);
    }


    /**
     * 返回一个DateTime对象初始化参数和使用UTC时间美元时区
     * @access protected
     */
    protected final static function _getUTCDate($time = null) {
        if ($time instanceof \DateTime) {
            $result = clone $time;
        } elseif (is_int($time)) {
            $result = new \DateTime(date('Y-m-d H:i:s', $time));
        } else {
            $result = new \DateTime($time);
        }
        $result->setTimeZone(new \DateTimeZone('UTC'));
        return $result;
    }


    /**
     * 申请文件到文件和设置端部偏移。
     * @access protected
     * @param string $filePath 文件设定范围
     * @param string $httpRange 应用范围
     * @return void
     */
    protected final static function _fileRange($filePath, $httpRange) {
        list(, $range) = explode('=', $httpRange);
        list($start, $end) = explode('-', $range);

        $fileSize = filesize($filePath);
        $lastByte = $fileSize - 1;

        if ($start === '') {
            $start = $fileSize - $end;
            $end = $lastByte;
        }
        if ($end === '') {
            $end = $lastByte;
        }

        if ($start > $end || $end > $lastByte || $start > $lastByte) {
            self::statusCode(416);
            self::header(array(
                'Content-Range' => 'bytes 0-' . $lastByte . '/' . $fileSize
            ));
            return;
        }

        self::header(array(
            'Content-Length' => $end - $start + 1,
            'Content-Range' => 'bytes ' . $start . '-' . $end . '/' . $fileSize
        ));

        self::statusCode(206);
        self::$_fileRange = array($start, $end);
    }


    /**
     * 读取一个文件，发送内容给客户。
     * @access protected
     * @param mixed $filePath 文件路径
     * @param array $range 从文件的指定范围中读取
     * @return boolean
     */
    protected final static function _sendFile($filePath, $range) {
        $compress = self::outputCompressed();

        //打开文件
        $file = fopen($filePath, 'rb');

        $end = $start = false;
        if ($range) {
            list($start, $end) = $range;
        }
        if ($start !== false) {
            if ($start === false && is_resource($file)) {
                $start = ftell($file);
            }
            fseek($file, $start, SEEK_SET);
        }

        $bufferSize = 8192;
        set_time_limit(0);
        session_write_close();
        while (!feof($file)) {
            if (!self::_isActive()) {
                fclose($file);
                return false;
            }

            $offset = fseek($file, false, SEEK_SET);

            if ($end && $offset >= $end) {
                break;
            }

            if ($end && $offset + $bufferSize >= $end) {
                $bufferSize = $end - $offset + 1;
            }

            echo fread($file, $bufferSize);

            if (!$compress) {
                self::_flushBuffer();
            }
        }
        fclose($file);
        return true;
    }


    /**
     * 如果连接依然活跃，返回true
     * @access protected
     */
    protected final static function _isActive() {
        return connection_status() === CONNECTION_NORMAL && !connection_aborted();
    }


    /**
     * 将标头发送到缓存*返回缓冲标头的完整列表
     * @access public
     * @param  $header
     * @param  $value
     * @return array
     */
    public final static function header($header = null, $value = null) {
        if ($header === null) {
            return self::$_headers;
        }
        $headers = is_array($header) ? $header : array($header => $value);
        foreach ($headers as $header => $value) {
            if (is_numeric($header)) {
                list($header, $value) = array($value, null);
            }
            if ($value === null) {
                list($header, $value) = explode(':', $header, 2);
            }
            self::$_headers[$header] = is_array($value) ? array_map('trim', $value) : trim($value);
        }
        return self::$_headers;
    }


    /**
     * @access public
     * @param  $url string
     * @return string
     */
    public final static function location($url = null) {
        if ($url === null) {
            $headers = self::header();
            return isset($headers['Location']) ? $headers['Location'] : null;
        }
        self::header('Location', $url);
        return $url;
    }


    /**
     * 设置响应消息
     * @access public
     * @param string $content
     * @return string
     */
    public final static function body($content = null) {
        if ($content === null) {
            return self::$_body;
        }
        return self::$_body = $content;
    }


    /**
     * 设置要发送的HTTP状态代码
     * @access public
     * @param integer $code
     * @return integer
     * @throws \Exception
     */
    public final static function statusCode($code = null) {
        if ($code === null) {
            return self::$_status;
        }
        if (!isset(self::$_statusCodes[$code])) {
            throw new \Exception('未知的状态代码');
        }
        return self::$_status = $code;
    }


    /**
     * fsdf
     * @access public
     * @param integer|array $code
     * @return mixed
     * @throws \Exception
     */
    public final static function httpCodes($code = null) {
        if (empty($code)) {
            return self::$_statusCodes;
        }
        if (is_array($code)) {
            $codes = array_keys($code);
            $min = min($codes);
            if (!is_int($min) || $min < 100 || max($codes) > 999) {
                throw new \Exception('无效的状态代码');
            }
            self::$_statusCodes = $code + self::$_statusCodes;
            return true;
        }
        if (!isset(self::$_statusCodes[$code])) {
            return null;
        }
        return array($code => self::$_statusCodes[$code]);
    }


    /**
     * 设置响应内容类型。它可以是一个文件扩展名
     * @access public
     * @param mixed $contentType
     * @return mixed
     */
    public final static function type($contentType = null) {
        if ($contentType === null) {
            return self::$_contentType;
        }
        if (is_array($contentType)) {
            foreach ($contentType as $type => $definition) {
                self::$_mimeTypes[$type] = $definition;
            }
            return self::$_contentType;
        }
        if (isset(self::$_mimeTypes[$contentType])) {
            $contentType = self::$_mimeTypes[$contentType];
            $contentType = is_array($contentType) ? current($contentType) : $contentType;
        }
        if (strpos($contentType, '/') === false) {
            return false;
        }
        return self::$_contentType = $contentType;
    }


    /**
     * 返回一个别名的MIME类型定义
     * @access public
     */
    public final static function getMimeType($alias) {
        if (isset(self::$_mimeTypes[$alias])) {
            return self::$_mimeTypes[$alias];
        }
        return false;
    }


    /**
     * 范围内返回一个别名
     * @access public
     * @param string|array $cType
     * @return mixed
     */
    public final static function mapType($cType) {
        if (is_array($cType)) {
            return array_map('\Lcd\Network\Response::mapType', $cType);
        }

        foreach (self::$_mimeTypes as $alias => $types) {
            if (in_array($cType, (array)$types)) {
                return $alias;
            }
        }
        return null;
    }


    /**
     * 设置字符集
     * 设置响应的字符集，如果参数为空则返回当前字符集
     * @access public
     * @param string $charset
     * @return string
     */
    public final static function charset($charset = null) {
        if ($charset === null) {
            return self::$_charset;
        }
        return self::$_charset = $charset;
    }


    /**
     * 设置正确的标头指示客户端不要缓存响应
     * @access public
     * @return void
     */
    public final static function disableCache() {
        self::header(array(
            'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
            'Last-Modified' => gmdate("D, d M Y H:i:s") . " GMT",
            'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0'
        ));
    }

    /**
     * 设置正确的标头指示客户端缓存响应。
     * @access public
     * @param string $since //上次修改时间
     * @param string $time //缓存到期时间
     * @return void
     */
    public final static function cache($since, $time = '+1 day') {
        if (!is_int($time)) {
            $time = strtotime($time);
        }

        //请求发送的日期和时间
        self::header(array(
            'Date' => gmdate("D, j M Y G:i:s ", time()) . 'GMT'
        ));

        //设置上次修改时间
        self::modified($since);

        //设置过期时间
        self::expires($time);

        //设置页面可以缓存
        self::sharable(true);

        //设置缓存有效时间
        self::maxAge($time - time());
    }


    /**
     * 设置响应是否是可以缓存
     * @access public
     * @param boolean $public
     * @param integer $time
     * @return boolean
     */
    public final static function sharable($public = null, $time = null) {
        if ($public === null) {
            $public = array_key_exists('public', self::$_cacheDirectives);
            $private = array_key_exists('private', self::$_cacheDirectives);
            $noCache = array_key_exists('no-cache', self::$_cacheDirectives);
            if (!$public && !$private && !$noCache) {
                return null;
            }
            $sharable = $public || ! ($private || $noCache);
            return $sharable;
        }
        if ($public) {
            self::$_cacheDirectives['public'] = true;
            unset(self::$_cacheDirectives['private']);
            //设置缓存时间
            self::sharedMaxAge($time);
        } else {
            self::$_cacheDirectives['private'] = true;
            unset(self::$_cacheDirectives['public']);
            //设置缓存时间
            self::maxAge($time);
        }
        if (!$time) {
            self::_setCacheControl();
        }
        return (bool)$public;
    }


    /**
     * 设置缓存时间;public时有效
     * 设置缓存控制s-maxage指令。单位:秒。
     * @access public
     * @param integer $seconds
     * @return integer
     */
    public final static function sharedMaxAge($seconds = null) {
        if ($seconds !== null) {
            self::$_cacheDirectives['s-maxage'] = $seconds;
            self::_setCacheControl();
        }
        if (isset(self::$_cacheDirectives['s-maxage'])) {
            return self::$_cacheDirectives['s-maxage'];
        }
        return null;
    }


    /**
     * 设置缓存时间
     * 设置缓存控制指令的最大缓存时间,,单位:秒。
     * @access public
     * @param integer $seconds
     * @return integer
     */
    public final static function maxAge($seconds = null) {
        if ($seconds !== null) {
            self::$_cacheDirectives['max-age'] = $seconds;
            self::_setCacheControl();
        }
        if (isset(self::$_cacheDirectives['max-age'])) {
            return self::$_cacheDirectives['max-age'];
        }
        return null;
    }


    /**
     * 设置缓存控制指令必须重新验证。
     * @access public
     * @param integer $enable
     * @return boolean
     */
    public final static function mustRevalidate($enable = null) {
        if ($enable !== null) {
            if ($enable) {
                self::$_cacheDirectives['must-revalidate'] = true;
            } else {
                unset(self::$_cacheDirectives['must-revalidate']);
            }
            self::_setCacheControl();
        }
        return array_key_exists('must-revalidate', self::$_cacheDirectives);
    }


    /**
     * 设置Expires标头以到期时间响应
     * @access public
     * @param string $time 响应过期的日期和时间
     * @return string
     */
    public final static function expires($time = null) {
        if ($time !== null) {
            $date = self::_getUTCDate($time);
            self::$_headers['Expires'] = $date->format('D, j M Y H:i:s') . ' GMT';
        }
        if (isset(self::$_headers['Expires'])) {
            return self::$_headers['Expires'];
        }
        return null;
    }


    /**
     * 设置Last-Modified标头以修改时间响应
     * @access public
     * @param string $time 请求资源的上次修改时间
     * @return string
     */
    public final static function modified($time = null) {
        if ($time !== null) {
            $date = self::_getUTCDate($time);
            self::$_headers['Last-Modified'] = $date->format('D, j M Y H:i:s') . ' GMT';
        }
        if (isset(self::$_headers['Last-Modified'])) {
            return self::$_headers['Last-Modified'];
        }
        return null;
    }


    /**
     * 设置响应通过去除身体的任何内容不改
     * 设置状态代码“304不改”，删除所有的
     * 矛盾的标题
     * @access public
     */
    public final static function notModified() {
        self::statusCode(304);
        self::body('');
        $remove = array(
            'Allow',
            'Content-Encoding',
            'Content-Language',
            'Content-Length',
            'Content-MD5',
            'Content-Type',
            'Last-Modified'
        );
        foreach ($remove as $header) {
            unset(self::$_headers[$header]);
        }
    }


    /**
     * 设置不同的响应头，如果数组传递，
     * 的值将被连接成一个以逗号分隔的字符串。如果没有
     * 参数传递，然后与现有的阵列不同的标题
     * 返回值
     * @access public
     */
    public final static function vary($cacheVariances = null) {
        if ($cacheVariances !== null) {
            $cacheVariances = (array)$cacheVariances;
            self::$_headers['Vary'] = implode(', ', $cacheVariances);
        }
        if (isset(self::$_headers['Vary'])) {
            return explode(', ', self::$_headers['Vary']);
        }
        return null;
    }


    /**
     * 设置Etag响应，ETags是一个强大的表示的响应
     * @access public
     */
    public final static function etag($tag = null, $weak = false) {
        if ($tag !== null) {
            self::$_headers['Etag'] = sprintf('%s"%s"', ($weak) ? 'W/' : null, $tag);
        }
        if (isset(self::$_headers['Etag'])) {
            return self::$_headers['Etag'];
        }
        return null;
    }


    /**
     * 设置正确的输出缓冲处理程序来发送压缩的响应。的反应
     * 用Zlib压缩，如果可以延期。
     * @access public
     */
    public final static function compress() {
        $compressionEnabled = ini_get("zlib.output_compression") !== '1' &&
            extension_loaded("zlib") &&
            (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false);
        return $compressionEnabled && ob_start('ob_gzhandler');
    }


    /**
     * 返回是否产生的输出将被压缩的PHP
     * @access public
     */
    public final static function outputCompressed() {
        return strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false
        && (ini_get("zlib.output_compression") === '1' || in_array('ob_gzhandler', ob_list_handlers()));
    }


    /**
     * 设置正确的标题来指示浏览器下载响应作为一个文件。
     * @access public
     * @param string $filename
     */
    public final static function download($filename) {
        self::header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }


    /**
     * 设置要在发送响应使用的协议。缺省为HTTP／1.1
     * 如果不带参数调用，它将返回当前配置的协议
     * @access public
     * @param string $protocol
     * @return mixed
     */
    public final static function protocol($protocol = null) {
        if ($protocol !== null) {
            self::$_protocol = $protocol;
        }
        return self::$_protocol;
    }


    /**
     * 设置响应内容长度标头
     * 如果不带参数调用返回最后设置内容长度
     * @access public
     * @param integer $bytes
     * @return integer|null
     */
    public final static function length($bytes = null) {
        if ($bytes !== null) {
            self::$_headers['Content-Length'] = $bytes;
        }
        if (isset(self::$_headers['Content-Length'])) {
            return self::$_headers['Content-Length'];
        }
        return null;
    }


    /**
     * 检查响应是否未被修改根据“如果没有匹配的
     *（ETags）和“If-Modified-Since”（上次修改日期）的要求
     * 标题标题。如果检测到响应时不修改，它
     * 标记为，因此客户可以告知。
     * @access public
     */
    public final static function checkNotModified() {
        $etags = preg_split('/\s*,\s*/', Request::header('If-None-Match'), null, PREG_SPLIT_NO_EMPTY);
        $modifiedSince = Request::header('If-Modified-Since');
        if ($responseTag = self::etag()) {
            $etagMatches = in_array('*', $etags) || in_array($responseTag, $etags);
        }
        if ($modifiedSince) {
            $timeMatches = strtotime(self::modified()) == strtotime($modifiedSince);
        }
        $checks = compact('etagMatches', 'timeMatches');
        if (empty($checks)) {
            return false;
        }
        $notModified = !in_array(false, $checks, true);
        if ($notModified) {
            self::notModified();
        }
        return $notModified;
    }


    /**
     * 输出body
     * @access public
     * @return string
     */

    //public  function __toString() {

    //}


    /**
     * 获取/设置cookie脚本
     * @access public
     * @param mixed $options
     * @return mixed
     */
    public final static function cookie($options = null) {
        if ($options === null) {
            return self::$_cookies;
        }

        if (is_string($options)) {
            if (!isset(self::$_cookies[$options])) {
                return null;
            }
            return self::$_cookies[$options];
        }

        $defaults = array(
            'name' => 'CakeCookie[default]',
            'value' => '',
            'expire' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httpOnly' => false
        );
        $options += $defaults;

        self::$_cookies[$options['name']] = $options;

        return null;
    }


    /**
     * 设置显示或下载文件。
     * @access public
     * @param string $path 文件路径
     * @param array $options
     * @throws \Exception
     */
    public final static function file($path, $options = array()) {
        $options += array(
            'name' => null,
            'download' => null
        );

        if (!is_file($path)) {
            $path = ROOT_PATH . $path;
        }

        $fileInfo = pathinfo($path);

        if (!is_file($path) || !is_readable($path)) {
            if (Config::read('debug')) {
                throw new \Exception("所请求的文件 $path 不存在或不可读");
            }
            throw new \Exception('所请求的文件没有找到');
        }

        $extension = strtolower($fileInfo['extension']);

        $download = $options['download'];

        if ((!$extension || self::type($extension) === false) && $download === null) {
            $download = true;
        }

        $fileSize = filesize($path);

        if ($download) {
            $agent = env('HTTP_USER_AGENT');

            if (preg_match('%Opera(/| )([0-9].[0-9]{1,2})%', $agent)) {
                $contentType = 'application/octet-stream';
            } elseif (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $agent)) {
                $contentType = 'application/force-download';
            }

            if (!empty($contentType)) {
                self::type($contentType);
            }
            if ($options['name'] === null) {
                $name = $fileInfo['basename'];
            } else {
                $name = $options['name'];
            }

            self::download($name);
            self::header('Accept-Ranges', 'bytes');
            self::header('Content-Transfer-Encoding', 'binary');

            $httpRange = env('HTTP_RANGE');

            if (isset($httpRange)) {
                self::_fileRange($path, $httpRange);
            } else {
                self::header('Content-Length', $fileSize);
            }
        } else {
            self::header('Content-Length', $fileSize);
        }
        self::_clearBuffer();
        self::$_file = $path;
    }


    /**
     * 清除的最上面的输出缓冲区的内容和抛弃他们
     * @access protected
     */
    protected final static function _clearBuffer() {
        return @ob_end_clean();
    }


    /**
     * 刷新输出缓冲区的内容
     * @access protected
     */
    protected final static function _flushBuffer() {
        @flush();
        @ob_flush();
    }
}