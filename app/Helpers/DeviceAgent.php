<?php

namespace App\Helpers;

use Detection\MobileDetect;

class DeviceAgent
{
    protected $detect;

    public function __construct($userAgent = null)
    {
        $this->detect = new MobileDetect();

        // Si no se pasa user agent, usa el actual del request
        if (!$userAgent) {
            $userAgent = request()->header('User-Agent', 'Unknown');
        }

        $this->detect->setUserAgent($userAgent);
    }

    // Detecta plataforma aproximada
    public function platform()
    {
        if ($this->detect->isAndroidOS()) return 'Android';
        if ($this->detect->isiOS()) return 'iOS';
        if ($this->detect->is('Windows')) return 'Windows';
        if ($this->detect->is('Mac')) return 'Mac';
        if ($this->detect->is('Linux')) return 'Linux';

        return 'Unknown';
    }

    // No detecta navegador, siempre 'Unknown'
    public function browser()
    {
        return 'Unknown';
    }

    // Detecta si es escritorio
    public function isDesktop()
    {
        return !$this->detect->isMobile() && !$this->detect->isTablet();
    }
}
