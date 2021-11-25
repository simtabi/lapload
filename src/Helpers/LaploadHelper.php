<?php

namespace Simtabi\Lapload\Helpers;

class LaploadHelper
{
    public const PACKAGE_NAME = 'lapload';
    public const IMAGE_MIMES  = 'jpeg,png,jpg,gif,svg';
    public const AVATAR_SIZE  = 4000; // in kilobyte 4000 = 4mb

    public static function getPackageName()
    {
        return self::PACKAGE_NAME;
    }

    public static function getLocalDiskUploadToPath(bool $namespaced = false, $directory = null)
    {
        $directory = !empty($directory) ? $directory . '/' : '';
        $base      = $namespaced ? self::getPackageName() . '/' : '';
        return $base . $directory;
    }

    public static function getImageMimes()
    {
        return self::IMAGE_MIMES;
    }

    public static function getDefaultAvatarSize()
    {
        return self::AVATAR_SIZE;
    }

}
