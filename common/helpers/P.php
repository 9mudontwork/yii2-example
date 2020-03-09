<?php

namespace common\helpers;

/**
 * // Muhammad Imoeb
 * ===== วิธีใช้ =====
 * 
 * 
    use common\helpers\P;
    
    P::R($array)
 */

class P
{
    public static function R($data)
    {
        echo '<pre style="
        background-color: #333333;
        color: #fff;
        font-size: 16px;
        white-space: normal;
        overflow-wrap: break-word;
        ">';
        print_r($data);
        echo '</pre>';
    }
}
