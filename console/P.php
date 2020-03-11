<?php

namespace console;

/**
 * Muhammad Imoeb
 * วิธีใช้
 * 
 * ไฟล์ต้องอยู่ใน folder console
 * 
 * เอาไปใช้ที่ไหนก็ได้แค่พิมพ์
 * 
    \console\P::R($array);
 * 
 */

class P
{
    public static function R($data)
    {
        echo '<pre id="pre-console" style="
        background-color: #333333;
        color: #fff;
        font-size: 16px;
        white-space: pre;
        margin-top: 100px;
        overflow-wrap: break-word;
        overflow-x: scroll;
        ">';
        print_r($data);
        echo '</pre>';

        echo <<<HTML
            <script>
                var preTag = document.getElementById('pre-console');
                bodyTag = document.getElementsByTagName('body')[0];
                bodyTag.insertBefore(preTag, bodyTag.firstChild);
            </script> 
            HTML;
    }
}
