<?php

namespace Simtabi\Lapload\Helpers;

class LaploadHelper
{
    public const PACKAGE_NAME  = 'lapload';
    public const IMAGE_MIMES   = 'jpeg,png,jpg,gif,svg';
    public const FILE_MIMES    = '';
    public const MAX_FILE_SIZE = 4000; // in kilobytes 4000 = 4mb

    public static function getPackageName()
    {
        return self::PACKAGE_NAME;
    }

    public static function getImageMimeTypes()
    {
        return self::IMAGE_MIMES;
    }

    public static function getFileMimeTypes()
    {
        return self::FILE_MIMES;
    }

    public static function getDefaultMaxFileSize()
    {
        return self::MAX_FILE_SIZE;
    }

    public static function getLocalUploadPath($directory = null)
    {
        return !empty($directory) ? trim($directory, '/') . '/' : '';
    }

}
