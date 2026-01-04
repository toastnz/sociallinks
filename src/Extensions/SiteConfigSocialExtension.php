<?php

namespace Toast\SocialLinks\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Security\Security;
use SilverStripe\Forms\ReadonlyField;
use Toast\SocialLinks\Helpers\Helper;
use Toast\SocialLinks\Models\SocialLink;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;

class SiteConfigSocialExtension extends Extension
{
    private static $many_many = [
        'SocialLinkItems' => SocialLink::class,
    ];

    public function updateCMSFields(FieldList $fields)
    {
        // Check if the current user is a super admin
        $isSuperAdmin = Helper::isSuperAdmin();
        // Check if the database is ready
        $databaseReady = Security::database_is_ready();

        // Find all the social links related to this site config
        $socialLinkItems = $this->owner->SocialLinkItems();

        // Remove the existing SocialLinkItems field if it exists
        $fields->removeByName(['SocialLinkItems']);

        // Exit here if not super admin or database not ready
        if (!$isSuperAdmin) return;
        if (!$databaseReady) return;

        // Exit if there are no social links
        if (!$socialLinkItems) return;

        // Set up the social links grid field config
        $socialLinkItemsConfig = GridFieldConfig::create()->addComponent(new GridFieldEditableColumns());
        $socialLinkItemsField = GridField::create('SocialLinkItems', 'Social Links', $socialLinkItems, $socialLinkItemsConfig);

        // Allow sorting of social links
        $socialLinkItemsConfig->addComponent(new GridFieldOrderableRows('SortOrder'));

        // Add remove button component
        $socialLinkItemsConfig->addComponent(new GridFieldDeleteAction());

        // Get the fields for the social links grid
        $fieldsForSocialLinkItems = $this->getSocialLinkFieldsForCMS();

        // Configure display fields for theme colours grid
        $socialLinkItemsField->getConfig()->getComponentByType(GridFieldEditableColumns::class)->setDisplayFields($fieldsForSocialLinkItems);

        $fields->addFieldToTab('Root.SocialLinkItems', $socialLinkItemsField);
    }

    public function getSocialLinkFieldsForCMS()
    {
        $fields = [
            'Title' => [
                'title' => 'Title',
                'callback' => fn($record, $column, $grid) => ReadonlyField::create($column, $column, $record->Title),
            ],
            'Link' => [
                'title' => 'Social Link',
                'field' => TextField::class,
            ],
        ];

        // Allow extensions to modify the fields list
        $this->owner->extend('updateSocialLinkFieldsForCMS', $fields);

        return $fields;
    }

    public function onAfterWrite()
    {
        if (!$this->owner->exists()) return;
        $link = new SocialLink();
        $link->requireDefaultRecords();
    }

    public function getSocialLinks()
    {
        // Return the social link items in sorted order filtered by those with Link set
        return $this->owner->SocialLinkItems()
            ->filter('Link:not', null)
            ->sort('SortOrder ASC');
    }
}
