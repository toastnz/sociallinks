<?php

namespace Toast\SocialLinks\Models;

use SilverStripe\ORM\DB;
use SilverStripe\ORM\DataObject;
use Toast\SocialLinks\Helpers\Helper;

class SocialLink extends DataObject
{
    private static $table_name = 'SocialLink';

    private static $db = [
        'Title'     => 'Varchar(64)',
        'Platform'  => 'Varchar(64)',
        'Link'      => 'Varchar(255)',
        'SortOrder' => 'Int',
    ];

    private static $default_sort = 'ID ASC';

    public function canDelete($member = null)
    {
        return $this->canRemove();
    }

    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        // if ($this->isInDB()) return;
        // Grab the current site config
        $siteConfig = Helper::getCurrentSiteConfig();

        // If there is no site config, return
        if (!$siteConfig) return;

        // Get the social platforms from the config yml
        $socialPlatforms = $this->getSocialPlatforms();

        // Exit if there are no social platforms to create
        if (count($socialPlatforms) == 0) return;

        foreach ($socialPlatforms as $platformKey) {
            // Check if a social link with the same Title already exists in the site config
            $existingLinks = $siteConfig->SocialLinkItems()->filter(['Platform' => $platformKey]);

            if ($existingLinks->count() > 0) continue;

            // Create a new Social Link record
            $platform = new SocialLink();

            // Assign the title
            $platform->Title = ucwords($platformKey);
            $platform->Platform = $platformKey;

            // Write the record
            $platform->write();

            // Add the colour to the site config
            $siteConfig->SocialLinkItems()->add($platform->ID);

            // Write a message to the devlog that the colour was created successfully
            DB::alteration_message("'$platformKey' Social link created", 'created');
        }
    }

    // Method to get the social platform from the yml config
    protected function getSocialPlatforms()
    {
        return $this->config()->get('platforms') ?: [];
    }

    public function canRemove()
    {
        // Get the social platforms from the config yml and prevent deletion if in the list
        $socialPlatforms = $this->getSocialPlatforms();

        if (in_array($this->Platform, $socialPlatforms)) {
            return false;
        }

        return true;
    }
}
