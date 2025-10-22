<?php

namespace Toast\SocialLinks\Helpers;

use SilverStripe\ORM\DataObject;
use SilverStripe\Core\Environment;
use SilverStripe\Security\Security;
use SilverStripe\SiteConfig\SiteConfig;

class Helper
{
    static function getCurrentSiteConfig()
    {
        if ($siteConfig = DataObject::get_one(SiteConfig::class)) {
            return $siteConfig;
        }
        return;
    }

    static function isSuperAdmin()
    {
        if ($defaultUser = Environment::getEnv('SS_DEFAULT_ADMIN_USERNAME')) {
            if ($currentUser = Security::getCurrentUser()) {
                return $currentUser->Email == $defaultUser;
            }
        }
        return false;
    }
}
