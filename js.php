<?php
/**
* js.php?f=index.js
* f 目标文件，会被替换为 当前 php 文件目录下 js/index.js
*
* 自动引用替换， // @import 文件名A,文件B
*/
    Class JS {
        static $imported = array();
        static $prefix = 'js/';
        function imports() {
            if ($_REQUEST['o']) {
                $_o = explode(',', $_REQUEST['o']);
                foreach ($_o as $api) {
                    JS::$imported[JS::$prefix . trim($api)] = true;
                }
            }
            header("Content-type:text/javascript;charset=utf-8");
            $content = JS::importIn(explode(",", $_REQUEST['f']));
            return $content;
        }
        function importIn($sApi, $code = "") {
            foreach ($sApi as $api) {
                $api = JS::$prefix . trim($api);

                if (substr($api, -3) !== '.js') {
                    JS::$imported[$api] = true;
                    continue;
                }
                if (JS::$imported[$api]) {
                    continue;
                }
                JS::$imported[$api] = true;
                if (is_file($api)) {
                    $content = file_get_contents($api);
                    preg_match_all("/(?<=^|\n)\s*\/\/\s*@import\s+([^\n]+)/ies", $content, $match);
                    foreach ((array)$match[1] as $k => $v) {
                        $_api = explode(',', $v);
                        $_src = $match[0][$k];
                        if (JS::$imported[$_src]) {
                            continue;
                        }
                        JS::$imported[$_src] = true;
                        $content             = str_replace($_src, JS::importIn($_api, $content), $content);
                    }
                }
                else {
                    $content = "// 404 " . $api;
                }
                $output .= "\r\n// >>>>> $api \r\n" . $content . "\r\n// $api <<<<< \r\n";
            }
            return trim($output);
        }
    }
    echo JS::imports();
?>