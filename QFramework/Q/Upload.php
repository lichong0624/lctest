<?php

/**
 * 文件上传类
 * $config = array(
 *  'module'        => 'images',
 *  'domain'        => '',
 *  'uploadDir'     => '',
 *  'maxSize'       => '',
 *  'maxHeight'     => '',
 *  'maxFilename'   => '',
 *  'allowedTypes'  => '',
 * );
 * 使用说明：Q_Upload::instance($config)->doUpload($files);
 *           获取返回数据  Q_Upload->instance()->data();
 *
 * @author       wukun<charmfocus@gmail.com>
 * @copyright(c) 2014-11-21
 * @version      $id$
 */
class Q_Upload
{
    const UPDATE_TYPE_FORM   = 0;
    const UPDATE_TYPE_BASE64 = 1;
    const UPDATE_TYPE_BIN    = 2;
    const UPDATE_TYPE_PATH   = 3;

    const CREATE_TYPE_COPY = 0;
    const CREATE_TYPE_MOVE = 1;


    /**
     * 状态码：成功
     */
    const STATUS_SUCCESS = 1;

    /**
     * 状态码：失败
     */
    const STATUS_ERROR = 0;

    const PATH_TYPE_DATE           = 'date';
    const PATH_TYPE_DATE_VALUE_YMD = '/Y/m/d/';
    const PATH_TYPE_DATE_VALUE_YM  = '/Y/m/';
    const PATH_TYPE_DATE_VALUE_Y   = '/Y/';

    const PATH_TYPE_HASH         = 'hash';
    const PATH_TYPE_HASH_VALUE_2 = 2;
    const PATH_TYPE_HASH_VALUE_3 = 3;
    const PATH_TYPE_HASH_VALUE_4 = 4;


    protected static $_instance;

    //先上传到tmp目录
    public $uploadTmp          = true;
    public $module             = 'images';
    public $domain             = '';
    public $uploadDir          = '';
    public $maxSize            = 0;
    public $maxWidth           = 0;
    public $maxHeight          = 0;
    public $maxFilename        = 0;
    public $allowedTypes       = [];
    public $files              = [];
    public $fileTemp           = '';
    public $fileName           = '';
    public $origName           = '';
    public $fileMimeType       = '';
    public $fileSize           = '';
    public $fileOrigExt        = '';
    public $safeFileExt        = [];
    public $ignoreCheckFileExt = [];
    public $fileExt            = '';
    public $fileNoDotExt       = '';
    public $fileMd5            = '';
    public $fileSha1           = '';
    public $logPath            = '';

    public $uploadPath      = '';
    public $rewriteFileName = true;
    public $encryptName     = false;
    public $isImage         = false;
    public $imageWidth      = '';
    public $imageHeight     = '';
    public $imageType       = '';
    public $imageSize       = '';
    public $errorMsg        = array();

    public $pathType      = self::PATH_TYPE_DATE;
    public $pathTypeValue = self::PATH_TYPE_DATE_VALUE_YMD;

    public    $removeSpaces      = true;
    public    $tempPrefix        = "temp_file_";
    public    $clientName        = '';
    protected $_fileNameOverride = '';

    private $_fileInfo = null;

    private static $_mimes = array(
        'application/mac-binhex40'                                                => 'hqx',
        'application/mac-compactpro'                                              => 'cpt',
        'text/x-comma-separated-values'                                           => 'csv',
        'text/comma-separated-values'                                             => 'csv',
        'application/octet-stream'                                                => ['word' => 'word', 'lzma' => 'lzma'],
        'application/x-csv'                                                       => 'csv',
        'text/x-csv'                                                              => 'csv',
        'text/csv'                                                                => 'csv',
        'application/csv'                                                         => 'csv',
        'application/excel'                                                       => 'xls',
        'application/msexcel'                                                     => 'xls',
        'application/vnd.ms-excel'                                                => 'xls',
        'application/vnd.msexcel'                                                 => 'csv',
        'text/plain'                                                              => ['txt' => 'txt', 'csv' => 'csv', 'log' => 'log'],
        'application/macbinary'                                                   => 'bin',
        'application/x-msdownload'                                                => 'exe',
        'application/x-dosexec'                                                   => ['exe' => 'exe', 'dll' => 'dll'],
        'application/x-photoshop'                                                 => 'psd',
        'application/oda'                                                         => 'oda',
        'application/pdf'                                                         => 'pdf',
        'application/x-download'                                                  => 'pdf',
        'application/postscript'                                                  => 'ps',
        'application/smil'                                                        => 'smil',
        'application/vnd.mif'                                                     => 'mif',
        'application/powerpoint'                                                  => 'ppt',
        'application/vnd.ms-powerpoint'                                           => 'ppt',
        'application/wbxml'                                                       => 'wbxml',
        'application/wmlc'                                                        => 'wmlc',
        'application/x-director'                                                  => 'dxr',
        'application/x-dvi'                                                       => 'dvi',
        'application/x-gtar'                                                      => 'gtar',
        'application/x-gzip'                                                      => 'gz',
        'application/x-httpd-php'                                                 => 'phtml',
        'application/x-httpd-php-source'                                          => 'phps',
        'application/x-javascript'                                                => 'js',
        'application/x-shockwave-flash'                                           => 'swf',
        'application/x-stuffit'                                                   => 'sit',
        'application/x-tar'                                                       => 'tgz',
        'application/x-gzip-compressed'                                           => 'tgz',
        'application/xhtml+xml'                                                   => 'xhtml',
        'application/zip'                                                         => ['zip' => 'zip', 'apk' => 'apk'],
        'application/x-zip'                                                       => 'zip',
        'application/x-zip-compressed'                                            => 'zip',
        'audio/midi'                                                              => 'midi',
        'audio/mpeg'                                                              => 'mp3',
        'audio/mpg'                                                               => 'mp3',
        'audio/mpeg3'                                                             => 'mp3',
        'audio/mp3'                                                               => 'mp3',
        'video/mp4'                                                               => 'mp4',
        'video/x-flv'                                                             => 'flv',
        'audio/x-aiff'                                                            => 'aifc',
        'audio/x-pn-realaudio'                                                    => 'rm',
        'audio/x-pn-realaudio-plugin'                                             => 'rpm',
        'audio/x-realaudio'                                                       => 'ra',
        'video/vnd.rn-realvideo'                                                  => 'rv',
        'audio/x-wav'                                                             => 'wav',
        'audio/wave'                                                              => 'wav',
        'audio/wav'                                                               => 'wav',
        'image/bmp'                                                               => 'bmp',
        'image/x-windows-bmp'                                                     => 'bmp',
        'image/gif'                                                               => 'gif',
        'image/webp'                                                              => 'webp',
        'image/jpeg'                                                              => 'jpg',
        'image/pjpeg'                                                             => 'jpg',
        'image/png'                                                               => 'png',
        'image/x-png'                                                             => 'png',
        'image/tiff'                                                              => 'tif',
        'text/css'                                                                => 'css',
        'text/html'                                                               => 'shtml',
        'text/x-log'                                                              => 'log',
        'text/richtext'                                                           => 'rtx',
        'text/rtf'                                                                => 'rtf',
        'text/xml'                                                                => 'xsl',
        'application/xml'                                                         => 'xml',
        'video/mpeg'                                                              => 'mpeg',
        'video/quicktime'                                                         => 'mov',
        'video/x-msvideo'                                                         => 'avi',
        'video/x-ms-asf'                                                          => 'wmv',
        'video/x-ms-wmv'                                                          => 'wmv',
        'application/vnd.rn-realmedia'                                            => 'rmvb',
        'video/x-sgi-movie'                                                       => 'movie',
        'application/msword'                                                      => ['word' => 'word', 'doc' => 'doc', 'dot' => 'dot'],
        'application/wps-office.doc'                                              => 'doc',
        'application/wps-office.docx'                                             => 'docx',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'       => 'xlsx',
        'message/rfc822'                                                          => 'eml',
        'application/json'                                                        => 'json',
        'text/json'                                                               => 'json',
        'application/java-archive'                                                => 'apk',
        'application/vnd.android.package-archive'                                 => 'apk',
    );

    /**
     * 实例化
     *
     * @param array $config
     * @param bool  $renew 是否重新创建新对象
     * @return $this
     */
    public static function instance($config = array(), $renew = false)
    {
        $class = null;
        if ($renew || !isset(self::$_instance)) {
            $class           = new self();
            self::$_instance = $class;

            $mime         = Q_Config::get(['MimeType', APP_NAME . '_MimeType']);
            self::$_mimes = array_merge(self::$_mimes, $mime);
        } else {
            $class = self::$_instance;
        }

        if (!empty($config)) {
            $class->init($config);
        }
        return $class;
    }


    // --------------------------------------------------------------------

    /**
     * 初始化配置参数
     *
     * @param array $config 配置
     * @return $this
     */
    public function init($config)
    {
        foreach ($config as $_key => $_val) {
            if (isset($this->$_key)) {
                $this->$_key = $_val;
            }
        }

        $this->setAllowedTypes($this->allowedTypes);
        $this->setSafeFileExt($this->safeFileExt);
        $this->setIgnoreCheckFileExt($this->ignoreCheckFileExt);

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * 上传文件
     *
     * @param  array|string $files 上传文件 $_FILES句柄|base64字符串|bin|url|path
     * @param bool|int      $type  上传类型：
     *                             self::UPDATE_TYPE_FORM 表单
     *                             self::UPDATE_TYPE_BASE64 base64
     *                             self::UPDATE_TYPE_BIN 二进制
     *                             self::UPDATE_TYPE_PATH 路径
     * @param int           $createType
     * @return array
     */
    public function doUpload($files, $type = self::UPDATE_TYPE_FORM, $createType = self::CREATE_TYPE_COPY)
    {
        if (!isset($files)) {
            return $this->resultReturn(self::STATUS_ERROR, '上传文件没有被选中。');
        }

        if ($type) {
            $files = $this->_convertToFiles($files, $type);
        }

        // Was the file able to be uploaded? If not, determine the reason why.
        if ((!$type && !is_uploaded_file($files['tmp_name'])) || ($type && !empty($files['error']))) {
            $error = (!isset($files['error'])) ? 4 : $files['error'];

            switch ($error) {
                case 1: // UPLOAD_ERR_INI_SIZE
                    return $this->resultReturn(self::STATUS_ERROR, '上传文件大小超过限制。');
                    break;
                case 2: // UPLOAD_ERR_FORM_SIZE
                    return $this->resultReturn(self::STATUS_ERROR, '上传文件超过表单限制。');
                    break;
                case 3: // UPLOAD_ERR_PARTIAL
                    return $this->resultReturn(self::STATUS_ERROR, '上传文件没有传完。');
                    break;
                case 4: // UPLOAD_ERR_NO_FILE
                    return $this->resultReturn(self::STATUS_ERROR, '上传文件没有被选中。');
                    break;
                case 6: // UPLOAD_ERR_NO_TMP_DIR
                    return $this->resultReturn(self::STATUS_ERROR, '没有临时上传文件目录。');
                    break;
                case 7: // UPLOAD_ERR_CANT_WRITE
                    return $this->resultReturn(self::STATUS_ERROR, '上传文件不能被写入。');
                    break;
                case 8: // UPLOAD_ERR_EXTENSION
                    return $this->resultReturn(self::STATUS_ERROR, '上传文件扩展名被禁止。');
                    break;
                default :
                    return $this->resultReturn(self::STATUS_ERROR, '上传文件没有被选中。');
                    break;
            }
        }


        $this->files              = $files;
        $this->fileTemp           = $files['tmp_name'];
        $this->fileSize           = $files['size'];
        $this->fileMimeType       = $this->_fileMimeType();
        $this->fileName           = $files['name'];
        $this->fileOrigExt        = $this->getExtension(false);
        $this->safeFileExt        = $this->getSafeFileExt();
        $this->ignoreCheckFileExt = $this->getIgnoreCheckFileExt();

        $this->fileExt = $this->getExtensionFromMimeType();
        if (isset($this->safeFileExt[$this->fileOrigExt]) || isset($this->ignoreCheckFileExt[$this->fileOrigExt])) {
            $this->fileExt = $this->fileOrigExt;
        }

        $this->fileNoDotExt = $this->getExtensionFromMimeType(false);
        if (isset($this->safeFileExt[$this->fileOrigExt]) || isset($this->ignoreCheckFileExt[$this->fileOrigExt])) {
            $this->fileNoDotExt = $this->fileOrigExt;
        }

        $this->clientName = $this->fileName;
        $this->fileMd5    = md5_file($this->fileTemp);
        $this->fileSha1   = sha1_file($this->fileTemp);

        $this->_createUploadPath();
        // Is the upload path valid?
        $uploadPath = $this->validateUploadPath();

        if (is_array($uploadPath)) {
            return $uploadPath;
        }

        // Is the file type allowed to be uploaded?
        if (!$this->isAllowedFileType()) {
            return $this->resultReturn(self::STATUS_ERROR, '上传文件类型验证不通过。');
        }


        // Convert the file size to kilobytes
        if ($this->fileSize > 0) {
            $this->fileSize = round($this->fileSize / 1024, 2);
        }

        // Is the file size within the allowed maximum?
        if (!$this->isAllowedFilesize()) {
            return $this->resultReturn(self::STATUS_ERROR, '上传文件大小验证不通过。');
        }

        // Are the image dimensions within the allowed size?
        // Note: This can fail if the server has an open_basdir restriction.
        if (!$this->isAllowedDimensions()) {
            return $this->resultReturn(self::STATUS_ERROR, '上传文件尺寸大小验证不通过。');
        }

        // Sanitize the file name for security
        $this->fileName = $this->cleanFileName($this->fileName);

        // Truncate the file name if it's too long
        if ($this->maxFilename > 0) {
            $this->fileName = $this->limitFilenameLength($this->fileName, $this->maxFilename);
        }

        // Remove white spaces in the name
        if ($this->removeSpaces == true) {
            $this->fileName = preg_replace("/\s+/", "_", $this->fileName);
        }

        /*
         * Validate the file name
         * This function appends an number onto the end of
         * the file if one with the same name already exists.
         * If it returns false there was a problem.
         */
        $this->origName = $this->fileName;

        if ($this->rewriteFileName) {
            $this->fileName = $this->_createFileName($this->pathType);

            if ($this->fileName === false) {
                return $this->resultReturn(self::STATUS_ERROR, '上传文件名错误。');
            }
        }


        $saveFunc = $type ? ($createType === self::CREATE_TYPE_MOVE ? 'rename' : 'copy') : 'move_uploaded_file';

        if (!$saveFunc($this->fileTemp, $this->uploadPath . $this->fileName)) {
            return $this->resultReturn(self::STATUS_ERROR, '上传到指定目录出现错误。');
        }

        /*
         * Set the finalized image dimensions
         * This sets the image width/height (assuming the
         * file was an image).  We use this information
         * in the "data" function.
         */
        $this->setImageProperties($this->uploadPath . $this->fileName);

        if ($this->uploadTmp) {
            $infoFile = $this->_getInfoFilePath();
            file_put_contents($infoFile, json_encode($this->data()));
        }

        $this->saveLog();

        return $this->resultReturn(self::STATUS_SUCCESS, '上传成功。');
    }

    public function saveLog()
    {
        if ($this->logPath && !$this->uploadTmp) {
            $logPath = $this->logPath . str_replace('http://', '', $this->domain) . $this->module . '.log';
            $log     = $this->getCreateUrl();

            Q_File::write($log . PHP_EOL, $logPath, FILE_APPEND);
        }
    }

    /**
     * 转File数组对象
     *
     * @param string $code 文件内容
     * @param int    $type
     * @return array $_FILES
     */
    private function _convertToFiles($code, $type)
    {
        switch ($type) {
            case self::UPDATE_TYPE_BASE64:
                $code = base64_decode($code);
                break;
            case self::UPDATE_TYPE_BIN:
                break;
            case self::UPDATE_TYPE_PATH:
                break;
        }

        return $this->_binToFiles($code, $type);
    }

    private function _binToFiles($code, $type)
    {
        $_error = 0;
        if ($type == self::UPDATE_TYPE_PATH) {
            $_tmpFile = $code;
        } else {
            $_tmpFile = tempnam(sys_get_temp_dir(), 'upload_');
            file_put_contents($_tmpFile, $code);
        }


        $mime    = mime_content_type($_tmpFile);
        $extName = self::getExtensionFromMimeType(true, $mime);

        if (!$extName) {
            $_error = 8;
        }

        $files = array(
            'tmp_name' => $_tmpFile,
            'size'     => filesize($_tmpFile),
            'name'     => basename($_tmpFile) . $extName,
            'type'     => $mime,
            'error'    => $_error,
        );

        return $files;
    }

    /**
     * 保存文件到正式目录
     *
     * @param string|array $src 一个或多个文件
     * @return array|bool|mixed|string
     */
    public function save($src)
    {
        if (!$this->uploadTmp || empty($src)) {//无需移动
            return false;
        }

        $uploadDir = rtrim($this->uploadDir, '/');

        if (is_array($src)) {
            foreach ($src as &$_src) {
                $_src = $this->save($_src);
            }
            return $src;
        } else {
            $path     = $uploadDir . '/' . $src;
            $realPath = realpath($path);
            if (!$path || !$realPath || $realPath != $path) {//检查目录是否合法,及文件是否存在
                return false;
            }

            if (substr($src, 0, 4) == 'tmp/') {//错误的目录结构
                $_src   = substr($src, 4);
                $toPath = $uploadDir . '/' . $_src;
                $toDir  = dirname($toPath);
                is_dir($toDir) || mkdir($toDir, 0777, true);
                if (rename($path, $toPath)) {
                    $this->_cacheFileInfo($src);
                    $infoFile = $path . '.info';
                    is_file($infoFile) && unlink($infoFile);
                    return $_src;
                }
            }
            $this->saveLog();
            return $src;
        }
    }



    // --------------------------------------------------------------------

    /**
     * 返回上传文件信息
     *
     * @return    array
     */
    public function data()
    {
        return array(
            'filename'      => $this->fileName,
            'fileType'      => $this->fileMimeType,
            'filePath'      => $this->uploadPath,
            'fullPath'      => $this->uploadPath . $this->fileName,
            'fileUrl'       => $this->getFileUrl(),
            'fileCreateUrl' => $this->getCreateUrl(),
            'rawName'       => substr($this->origName, 0, strrpos($this->origName, $this->fileExt)),
            'origName'      => $this->origName,
            'clientName'    => $this->clientName,
            'fileExt'       => $this->fileExt,
            'fileSize'      => $this->fileSize,
            'isImage'       => $this->isImage(),
            'imageWidth'    => $this->imageWidth,
            'imageHeight'   => $this->imageHeight,
            'imageType'     => $this->imageType,
            'imageSize'     => $this->imageSize,
            'fileMd5'       => $this->fileMd5,
            'fileSha1'      => $this->fileSha1,
        );
    }


    /**
     * 获取文件信息
     *
     * @param $file
     * @return array|bool|mixed
     */
    public function getFileInfo($file)
    {
        if (is_array($file)) {
            $file = array_flip($file);
            foreach ($file as $_file => &$_row) {
                $_row = $this->getFileInfo($_file);
            }
            return $file;
        }
        if (!empty($this->_fileInfo[$file])) {

            return $this->_fileInfo[$file];
        }

        return $this->_cacheFileInfo($file);
    }

    /**
     * 缓存info数据
     *
     * @param $file
     * @return mixed|null
     */
    private function _cacheFileInfo($file)
    {
        $data      = null;
        $uploadDir = rtrim($this->uploadDir, '/');
        $path      = $uploadDir . '/' . $file;
        $infoFile  = $path . '.info';

        if (is_file($infoFile)) {
            $data = json_decode(file_get_contents($infoFile), true);
        }
        $this->_fileInfo[$file] = $data;
        return $data;
    }

    // --------------------------------------------------------------------

    /**
     * 上传路径前缀
     *
     * @return string
     */
    private function _makeUploadPrefixPath()
    {
        return rtrim($this->uploadDir, '/') . ($this->uploadTmp ? '/tmp/' : '/') . $this->module;
    }

    /**
     * 设置上传文件路径
     *
     * @param  string $path 上传路径
     * @return string
     */
    public function setUploadPath($path = null)
    {
        if (!$path) {
            $path = $this->_makeUploadPrefixPath() . $this->_makeSubDir($this->pathType, $this->pathTypeValue);
        }
        $this->uploadPath = rtrim($path, '/') . '/';
        return $this->uploadPath;
    }


    private function _makeSubDir($type = self::PATH_TYPE_DATE, $value = self::PATH_TYPE_DATE_VALUE_YMD)
    {
        $subDir = '';
        if ($type == self::PATH_TYPE_DATE) {
            $subDir = date($value);
        } else if ($type == self::PATH_TYPE_HASH) {
            $value  = (int)$value ? (int)$value : 2;
            $subDir = '/' . chunk_split(substr(md5($this->fileMd5 . $this->fileSha1), -($value * 2)), 2, '/');
        }
        return $subDir;
    }

    /**
     * 生成文件
     *
     * @param string $path 文件路径
     * @return void
     */
    private function _createUploadPath($path = null)
    {
        if (!$path) {
            $path = $this->setUploadPath();
        }
        //disable error message! 解决多并发下创建目录出错的问题
        Q_Error::setShow(false);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        //enable error message!
        Q_Error::setShow(true);
    }

    // --------------------------------------------------------------------

    /**
     * 生成唯一文件名
     *
     * @param string $type
     * @return string
     */
    private function _createFileName($type = self::PATH_TYPE_DATE)
    {
        $filename = '';
        if ($type == self::PATH_TYPE_DATE) {
            $time     = number_format(microtime(true), 5, '', '');
            $filename = $time . mt_rand(100000, 999999);
        } else if ($type == self::PATH_TYPE_HASH) {
            $filename = md5($this->fileMd5 . $this->fileSha1);
        } else {
            $filename = $this->fileName;
        }

        $filename .= '.' . ltrim($this->fileExt, '.');
        return $filename;
    }

    private function _getInfoFilePath()
    {
        return $this->uploadPath . $this->fileName . '.info';
    }

    // --------------------------------------------------------------------

    /**
     * 设置最大文件大小
     *
     * @param  integer $n 文件大小
     * @return void
     */
    public function setMaxFileSize($n)
    {
        $this->maxSize = ((int)$n < 0) ? 0 : (int)$n;
    }

    // --------------------------------------------------------------------

    /**
     * 设置文件名最长长度
     *
     * @param  integer $n 长度
     * @return void
     */
    public function setMaxFilename($n)
    {
        $this->maxFilename = ((int)$n < 0) ? 0 : (int)$n;
    }

    // --------------------------------------------------------------------

    /**
     * 设置最大宽度
     *
     * @param  integer $n 宽度
     * @return void
     */
    public function setMaxWidth($n)
    {
        $this->maxWidth = ((int)$n < 0) ? 0 : (int)$n;
    }

    // --------------------------------------------------------------------

    /**
     * 设置最大高度
     *
     * @param  integer $n 高度
     * @return void
     */
    public function setMaxHeight($n)
    {
        $this->maxHeight = ((int)$n < 0) ? 0 : (int)$n;
    }

    // --------------------------------------------------------------------

    /**
     * 设置允许的文件类型
     *
     * @param  string|array $types 文件类型 例jpg|png|jif
     * @return void
     */
    public function setAllowedTypes($types)
    {
        if (!is_array($types) && $types == '*') {
            $this->allowedTypes = '*';
            return;
        }
        $this->allowedTypes = explode('|', $types);
    }


    /**
     * 设置安全的文件扩展名
     *
     * @param  array $safeFileExt 安全文件扩展
     * @return void
     */
    public function setSafeFileExt($safeFileExt)
    {
        $this->safeFileExt = $safeFileExt;
    }


    /**
     * 获取安全的文件扩展名
     *
     * @return array
     */
    public function getSafeFileExt()
    {
        return $this->safeFileExt;
    }

    /**
     * 设置忽略的文件扩展名
     *
     * @param  array $ignoreCheckFileExt 忽略的文件扩展
     * @return void
     */
    public function setIgnoreCheckFileExt($ignoreCheckFileExt)
    {
        $this->ignoreCheckFileExt = $ignoreCheckFileExt;
    }


    /**
     * 获取忽略的文件扩展名
     *
     * @return array
     */
    public function getIgnoreCheckFileExt()
    {
        return $this->ignoreCheckFileExt;
    }

    // --------------------------------------------------------------------

    /**
     * 设置图片属性
     *
     * @param    string $path 图片路径
     * @return    void
     */
    public function setImageProperties($path = '')
    {
        if (!$this->isImage()) {
            return;
        }

        if (function_exists('getimagesize')) {
            if (false !== ($D = getimagesize($path))) {
                $types = array(
                    IMAGETYPE_GIF  => 'gif',
                    IMAGETYPE_JPEG => 'jpeg',
                    IMAGETYPE_PNG  => 'png',
                    IMAGETYPE_BMP  => 'bmp',
                    IMAGETYPE_WEBP => 'webp'
                );

                $this->imageWidth  = $D['0'];
                $this->imageHeight = $D['1'];
                $this->imageType   = (!isset($types[$D['2']])) ? 'unknown' : $types[$D['2']];
                $this->imageSize   = $D['3'];  // string containing height and width
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * 验证是否是图片
     *
     * @return    bool
     */
    public function isImage()
    {
        // IE will sometimes return odd mime-types during upload, so here we just standardize all
        // jpegs or pngs to the same file type.

        $_mimes = array(
            'image/gif'   => 'image/gif',
            'image/png'   => 'image/png',
            'image/x-png' => 'image/png',
            'image/bmp'   => 'image/bmp',
            'image/jpeg'  => 'image/jpeg',
            'image/jpg'   => 'image/jpeg',
            'image/jpe'   => 'image/jpeg',
            'image/pjpeg' => 'image/jpeg',
            'image/webp'  => 'image/webp'
        );

        if (!empty($_mimes[$this->fileMimeType])) {
            $this->fileMimeType = $_mimes[$this->fileMimeType];
            return true;
        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * 验证是否是允许的文件类型
     *
     * @param bool $ignoreMime 忽略mime
     * @return bool
     */
    public function isAllowedFileType($ignoreMime = false)
    {
        if ($this->allowedTypes == '*') {
            return true;
        }

        if (count($this->allowedTypes) == 0 OR !is_array($this->allowedTypes)) {
            return false;
        }

        $ext = $this->fileNoDotExt;

        if (!in_array($ext, $this->allowedTypes)) {
            return false;
        }


        if ($ignoreMime === true) {
            return true;
        }

        if (!empty(self::$_mimes[$this->fileMimeType])) {
            return true;
        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * 验证是否是允许的文件大小
     *
     * @return    bool
     */
    public function isAllowedFilesize()
    {
        if ($this->maxSize != 0 AND $this->fileSize > $this->maxSize) {
            return false;
        } else {
            return true;
        }
    }

    // --------------------------------------------------------------------

    /**
     * 验证是否是允许的形状
     *
     * @return    bool
     */
    public function isAllowedDimensions()
    {
        if (!$this->isImage()) {
            return true;
        }

        if (function_exists('getimagesize')) {
            $d = getimagesize($this->fileTemp);

            if ($this->maxWidth > 0 && $d['0'] > $this->maxWidth) {
                return false;
            }

            if ($this->maxHeight > 0 && $d['1'] > $this->maxHeight) {
                return false;
            }

            return true;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * 验证上传路径
     *
     * @return array|bool
     */
    public function validateUploadPath()
    {
        if ($this->uploadPath == '') {
            return $this->resultReturn(self::STATUS_ERROR, '没有上传文件路径。');
        }

        if (function_exists('realpath') && realpath($this->uploadPath) !== false) {
            $this->uploadPath = str_replace("\\", "/", realpath($this->uploadPath));
        }

        if (!is_dir($this->uploadPath)) {
            return $this->resultReturn(self::STATUS_ERROR, '没有上传文件路径。');
        }

        if (!is_writable($this->uploadPath)) {
            return $this->resultReturn(self::STATUS_ERROR, '上传文件路径不可写。');
        }

        $this->uploadPath = preg_replace("/(.+?)\/*$/", "\\1/", $this->uploadPath);

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * 获取文件扩展名
     *
     * @param bool $hasDot
     * @return string
     */
    public function getExtension($hasDot = true)
    {
        $pathInfo = pathinfo($this->fileName);
        $ext      = empty($pathInfo['extension']) ? '' : $pathInfo['extension'];

        $hasDot && ($ext = '.' . $ext);

        return $ext;
    }

    public function getExtensionFromMimeType($hasDot = true, $mime = null)
    {
        $mime = empty($mime) ? $this->fileMimeType : $mime;
        $ext  = empty(self::$_mimes[$mime]) ? '' : self::$_mimes[$mime];

        if (is_array($ext)) {
            $_origExt = $this->fileOrigExt;
            if (empty($ext[$_origExt])) {
                return reset($ext);
            } else {
                $ext = $ext[$_origExt];
            }
        }

        if ($hasDot) {
            $ext = '.' . $ext;
        }
        return $ext;
    }

    /*
     * 获取文件访问路径
     *
     * @return string
     */
    public function getFileUrl($tmp = true)
    {
        $file = $this->domain . ($tmp ? 'tmp/' : '') . $this->module
                . $this->_makeSubDir($this->pathType, $this->pathTypeValue)
                . $this->fileName;
        return $file;
    }

    /**
     * 获取文件创建路径
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return ($this->uploadTmp ? 'tmp/' : '')
               . stristr($this->uploadPath . $this->fileName, $this->module);
    }

    // --------------------------------------------------------------------

    /**
     * 使文件名纯净
     *
     * @param  string $filename 文件名
     * @return string
     */
    public function cleanFileName($filename)
    {
        $bad = array(
            "<!--",
            "-->",
            "'",
            "<",
            ">",
            '"',
            '&',
            '$',
            '=',
            ';',
            '?',
            '/',
            "%20",
            "%22",
            "%3c", // <
            "%253c", // <
            "%3e", // >
            "%0e", // >
            "%28", // (
            "%29", // )
            "%2528", // (
            "%26", // &
            "%24", // $
            "%3f", // ?
            "%3b", // ;
            "%3d"  // =
        );

        $filename = str_replace($bad, '', $filename);

        return stripslashes($filename);
    }

    // --------------------------------------------------------------------

    /**
     * 限制文件名长度
     *
     * @param  string  $filename 文件名
     * @param  integer $length   长度
     * @return string
     */
    public function limitFilenameLength($filename, $length)
    {
        if (strlen($filename) < $length) {
            return $filename;
        }

        $ext = '';
        $dot = strrpos($filename, '.');
        if ($dot !== false) {
            $ext      = substr($filename, $dot);
            $filename = substr($filename, 0, $dot);
        }
        return substr($filename, 0, ($length - strlen($ext))) . $ext;
    }

    // --------------------------------------------------------------------

    /**
     * 返回错误消息
     *
     * @param int    $status
     * @param string $message 错误消息
     * @return array
     */
    public function resultReturn($status = self::STATUS_ERROR, $message)
    {
        return array('status' => $status, 'data' => $message);
    }

    /**
     * 文件mime类型
     * Detects the (actual) MIME type of the uploaded file, if possible.
     * The input array is expected to be $_FILES[$field]
     *
     * @return string
     */
    protected function _fileMimeType()
    {
        $fileType = mime_content_type($this->files['tmp_name']);
        $fileType = $fileType ? $fileType : $this->files['type'];
        $fileType = preg_replace("/^(.+?);.*$/", "\\1", $fileType);
        $fileType = strtolower(trim(stripslashes($fileType), '"'));

        $this->fileMimeType = $fileType;
        return $fileType;
    }
}
