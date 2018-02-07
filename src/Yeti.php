<?php

namespace Newcity;

class Yeti {

    /**
     * generate image src as base64 data
     *
     * @param [type] $src
     * @param boolean $show_dims
     * @return string
     */
    public static function src($src, $show_dims = TRUE) {

        $info = self::parse_path($src);
        $width = isset($info['width'])? $info['width'] : 0;
        $height = isset($info['height'])? $info['height'] : 0;
        
        $hash = md5($src);
        $tmp = sys_get_temp_dir();
        $fn = $tmp . "/" . $hash . ".jpg";

        if (!file_exists($fn)) {
            $ch = curl_init($src);
            $fp = fopen($fn, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
        }

        $data = file_get_contents($fn);

        if ($show_dims) {
            $text = $width . " x " . $height;
            $im = imagecreatefromstring($data);
            $color = imagecolorallocate($im, 255, 255, 255);

            $font = __DIR__ . "/../fonts/arial.ttf";
            if (file_exists($font)) {
                imagettftext($im, 36, 0, 30, 50, $color, $font, $text);
            }
            else {
                imagestring($im, 5, 30, 30, $text, $color);
            }

            ob_start (); 
            imagejpeg ($im);
            $data = ob_get_contents();
            ob_end_clean();
        }

        $data64 = base64_encode($data);
        return "data:image/jpeg;base64," . $data64;
    }

    /**
     * try to replace image markup with cached src
     *
     * @param string $img
     * @return string <img>
     */
    public static function replace_img($img) {
        if (preg_match('/src *= *[\'\"](.*)[\'\"]/', $img, $matches)) {
            $src = trim($matches[1]);
            $img = str_replace($matches[0], self::src($src), $img);
        }
        return $img;
    }

    /**
     * parse a URL for width and height
     *
     * @param string $url
     * @return array of image info
     */
    public static function parse_path($url) {
        $p = parse_url($url);
        $path = explode('/', $p['path']);
        
        $info = [];
        if (count($path) >= 2) {
            $info['width'] = $path[0];
            $info['height'] = $path[1];
        }

        return $info;
    }


}