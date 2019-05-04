<?php

namespace RodriigoGS\SocialiteSteam;

use Illuminate\Support\Facades\Facade;

/**
 * @see \RodriigoGS\SocialiteSteam\Skeleton\SkeletonClass
 */
class SocialiteSteamFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'socialite-steam';
    }
}
