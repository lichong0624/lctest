<?php
/**
 * 图片验证码类
 * 使用方法：
 * $conf = array(
 *  'width' => 100,#长
 *  'height' => '20',#高
 *  'len' => 4,#长度
 *  'fontColor' => '#000000',#字体颜色
 *  'fontSize' => 16,#字体大小
 *  'fontFamily' = > '/fonts/xx.ttf',#字体文件
 *  'bgColor' => ''#背景色，不写默认为随机
 *  'lang' => Q_Captcha::LANG_TYPE_EN,#是英文还是中文
 *  'noisePoint' => 30,#干扰点数量
 *  'noiseLine' => 3,#干扰线数量
 *  'distortion' => true,是否扭曲
 *  'showBorder' => false,是否显示边框
 * );
 * $captcha = new Q_Captcha($conf);
 *
 * @author        wiki<charmfocus@gmail.com>
 * @copyright (c) 2012-3-6
 * @version       v1.0
 */
class Q_Captcha
{
    const LANG_TYPE_EN = 'en';
    const LANG_TYPE_CN = 'cn';
    /**
     * 宽
     *
     * @var int
     */
    private $_width;

    /**
     * 高
     *
     * @var int
     */
    private $_height;

    /**
     * 显示长度
     *
     * @var int
     */
    private $_len = 4;

    /**
     * 显示文本
     *
     * @var string
     */
    private $_text;

    /**
     * 字体颜色
     *
     * @var string
     */
    private $_fontColor = '';

    /**
     * 随机字体颜色
     *
     * @var int
     */
    private $_randFontColor;


    /**
     * 字体大小
     *
     * @var int
     */
    private $_fontSize = 16;

    /**
     * 字体
     *
     * @var string
     */
    private $_fontFamily = '';

    /**
     * 背景颜色
     *
     * @var string
     */
    private $_bgColor = '';

    /**
     * 随机背景颜色
     *
     * @var int
     */
    private $_randBgColor;

    /**
     * 语言
     *
     * @var string
     */
    private $_lang = self::LANG_TYPE_EN;

    /**
     * 干扰点数量
     *
     * @var int
     */
    private $_noisePoint = 30;

    /**
     * 干扰线数量
     *
     * @var int
     */
    private $_noiseLine = 3;

    /**
     * 是否扭曲
     *
     * @var bool
     */
    private $_distortion = false;

    /**
     * 是否显示边框
     *
     * @var bool
     */
    private $_showBorder = false;

    /**
     * 图片源
     *
     * @var resource
     */
    private $_img;

    /**
     * 扭曲后的图片源
     */
    private $_distortionImg;


    /**
     * 初始化函数
     *
     * @param array $conf array(
     *                    'width' => 100,#长
     *                    'height' => '20',#高
     *                    'len' => 4,#长度
     *                    'fontColor' => '#000000',#字体颜色
     *                    'fontSize' => 16,#字体大小
     *                    'fontFamily' = > '/fonts/xx.ttf',#字体文件
     *                    'bgColor' => ''#背景色，不写默认为随机
     *                    'lang' => Q_Captcha::LANG_TYPE_EN,#是英文还是中文
     *                    'noisePoint' => 30,#干扰点数量
     *                    'noiseLine' => 3,#干扰线数量
     *                    'distortion' => true,是否扭曲
     *                    'showBorder' => false,是否显示边框
     *                    );
     */
    public function __construct(array $conf = array())
    {
        foreach ($conf as $k => $v) {
            if (empty($v)) {
                continue;
            }
            $_name        = '_' . $k;
            $this->$_name = $v;
        }
    }

    /**
     * 初始化画布
     *
     * @return $this
     */
    private function _createCanvas()
    {
        $this->_width  = empty($this->_width) ? floor($this->_fontSize * 1.3) * $this->_len + 10 : $this->_width;
        $this->_height = empty($this->_height) ? $this->_fontSize * 2 : $this->_height;
        $this->_img    = imagecreatetruecolor($this->_width, $this->_height);
        $bgColor       = $this->_bgColor ? sscanf($this->_bgColor, '#%2x%2x%2x') : array(mt_rand(100, 255), mt_rand(100, 255), mt_rand(100, 255));
        $randBgColor   = imagecolorallocate($this->_img, $bgColor[0], $bgColor[1], $bgColor[2]);
        imagefill($this->_img, 0, 0, $randBgColor);
        $this->_randBgColor = $randBgColor;
        return $this;
    }


    /**
     * 生成随机字符
     */
    private function _randENText()
    {
        $str    = 'ABCDEFGHJKLMNPQRSTUVWXY3456789';
        $len    = strlen($str);
        $string = '';
        for ($i = 0; $i < $this->_len; ++$i) {
            $string .= $str[mt_rand(0, $len - 1)];
        }
        return $string;
    }

    private function _randCNText()
    {
        $string = '';
        for ($i = 0; $i < $this->_len; ++$i) {
            $string .= chr(mt_rand(0xB0, 0xCC)) . chr(mt_rand(0xA1, 0xBB));
        }
        return iconv('GB2312', 'UTF-8', $string);
    }

    private function _getRandText()
    {
        $funcName = '_rand' . strtoupper($this->_lang) . 'Text';
        if (!method_exists($this, $funcName)) {
            throw new Q_Exception('the ' . $funcName . ' method not exists!');
        }

        return $this->$funcName();
    }

    /**
     * 打印字
     *
     * @return $this
     * @throws Q_Exception
     */
    private function _createText()
    {
        if (!$this->_fontFamily) {
            throw new Q_Exception('font family not defined!');
        }

        $fontColor     = $this->_fontColor ? sscanf($this->_fontColor, '#%2x%2x%2x') : array(mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));
        $randFontColor = imagecolorallocate($this->_img, $fontColor[0], $fontColor[1], $fontColor[2]);

        $textStr = $this->_getRandText();

        for ($i = 0; $i < $this->_len; ++$i) {
            $text  = mb_substr($textStr, $i, 1, 'UTF-8');
            $angle = mt_rand(-1, 1) * mt_rand(1, 20);
            imagettftext($this->_img, $this->_fontSize, $angle, 5 + $i * floor($this->_fontSize * 1.3), floor($this->_height * 0.75), $randFontColor, $this->_fontFamily, $text);
        }
        $this->_randFontColor = $randFontColor;
        $this->_text          = $textStr;
        return $this;
    }

    /**
     * 生成干扰点
     *
     * @return $this
     */
    private function _createNoisePoint()
    {
        for ($i = 0; $i < $this->_noisePoint; ++$i) {
            $pointColor = imagecolorallocate($this->_img, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
            imagesetpixel($this->_img, mt_rand(0, $this->_width), mt_rand(0, $this->_height), $pointColor);
        }
        return $this;
    }

    /**
     * 生成干扰线
     *
     * @return $this
     */
    private function _createNoiseLine()
    {
        for ($i = 0; $i < $this->_noiseLine; ++$i) {
            $lineColor = imagecolorallocate($this->_img, mt_rand(0, 255), mt_rand(0, 255), 20);
            imageline($this->_img, 0, mt_rand(0, $this->_width), $this->_width, mt_rand(0, $this->_height), $lineColor);
        }
        return $this;
    }

    /**
     * 扭曲文字
     *
     * @return $this
     */
    private function _distortionText()
    {
        $distortionImg = imagecreatetruecolor($this->_width, $this->_height);
        imagefill($distortionImg, 0, 0, $this->_randBgColor);

        for ($x = 0; $x < $this->_width; ++$x) {
            for ($y = 0; $y < $this->_height; ++$y) {
                $rgbColor = imagecolorat($this->_img, $x, $y);
                imagesetpixel($distortionImg, (int)($x + sin($y / $this->_height * 2 * M_PI - M_PI * 0.5) * 3), $y, $rgbColor);
            }
        }

        $this->_img           = $distortionImg;
        $this->_distortionImg = $distortionImg;
        return $this;
    }

    /**
     *添加边框
     *
     * @return $this
     */
    private function _createBorder()
    {
        imagerectangle($this->_img, 0, 0, $this->_width - 1, $this->_height - 1, $this->_randFontColor);
        return $this;
    }

    /**
     * 最终显示图片
     *
     * @param bool $header
     * @return string
     */
    public function createImg($header = false)
    {
        $header && header('Content-type:image/png');
        $this->_createCanvas()->_createText()->_createNoisePoint()->_createNoiseLine();
        $this->_distortion && $this->_distortionText();
        $this->_showBorder && $this->_createBorder();
        imagepng($this->_img);
        imagedestroy($this->_img);
        return $this->_text;
    }
}
