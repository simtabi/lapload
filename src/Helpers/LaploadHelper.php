<?php

namespace Simtabi\Lapload\Helpers;

class LaploadHelper
{
    public const PACKAGE_NAME = 'lapload';

    public static function getPackageName()
    {
        return self::PACKAGE_NAME;
    }

    public static function getLocalDiskUploadPath($directory = null)
    {
        return 'public/' . self::getPackageName() . '/' (!empty($directory) ? $directory . '/' : '');
    }

}