<?php
/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace im\DeviceDetect;

use \st\DeviceDetect\DeviceDetectorInterface;
use \Psr\Http\Message\RequestInterface;
use \Mobile_Detect;

class MobileDetect implements DeviceDetectorInterface {

    /**
     * @var \Mobile_Detect
     */
    public $detector;

    public function __construct(){
        $this->detector = new Mobile_Detect();
    }

    /**
     * @var string
     */
    public $detected = null;

    /**
     * @var RequestInterface
     */
    public $request;
    public function setRequest (RequestInterface $request) {
        $this->request = $request;
        $this->detected = null;
    }

    public function detect(){
        if ($this->detected===null){

            $headers = $this->request->getHeaders();
            $userAgent = $this->request->getHeaderLine('user-agent');

            if ($this->detector->isMobile($headers, $userAgent))
                $this->detected = 'mobile';
            else if ($this->detector->isTablet($headers, $userAgent))
                $this->detected = 'tablet';
            else
                $this->detected = 'desktop';
        }
        return $this->detected;
    }

    public function isMobile() {
        return $this->detect()==='mobile';
    }

    public function isDesktop() {
        return $this->detect()==='desktop';
    }

    public function isTablet() {
        return $this->detect()==='tablet';
    }

    public function isDeviceType($deviceType) {
        $negate = substr($deviceType,0,1)==='!';
        $deviceType = $negate ? substr($deviceType,1) : $deviceType;
        if ($negate) return !($this->detect()===$deviceType);
        return $this->detect()===$deviceType;
    }
}
