<?php

namespace Simtabi\Lapload\Helpers;

class LaploadHelper
{
    public const PACKAGE_NAME = 'lapload';
    public const IMAGE_MIMES  = 'jpeg,png,jpg,gif,svg';

    public static function getPackageName()
    {
        return self::PACKAGE_NAME;
    }

    public static function getLocalDiskUploadPath($directory = null)
    {
        return 'public/' . self::getPackageName() . '/' .(!empty($directory) ? $directory . '/' : '');
    }

    public static function getImageMimes()
    {
        return self::IMAGE_MIMES;
    }

}
