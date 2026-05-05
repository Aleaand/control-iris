<?php

namespace App\Traits;

trait HasResponsivePagination
{
    public function getPerPage()
    {
        $userAgent = request()->header('User-Agent');
        $isMobile = preg_match('/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i', $userAgent);
        return $isMobile ? 7 : 10;
    }
}
