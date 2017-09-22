<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 2017/8/10
 * Time: 15:49
 */

namespace JLWx\Xcx;


class ErrorCode
{
    public static $OK = 0;
    public static $IllegalAesKey = -41001;
    public static $IllegalIv = -41002;
    public static $IllegalBuffer = -41003;
    public static $DecodeBase64Error = -41004;
}