# Social Links Module

This module provides a way to manage social media links in SilverStripe via SiteConfig. It allows add and sort social links for their website.

## Features

- Define supported social platforms via YAML config
- Automatically create default social link records for each platform
- Manage social links in SiteConfig with a sortable grid field
- Prevent deletion of links for platforms defined in config
- Easily extendable for additional fields or platforms

## Installation

1. Place the module in your SilverStripe project under `sociallinks/`.
2. Run `composer install` if required.
3. Add your desired platforms to a yml config file, e.g., `app/_config/sociallinks.yml`:

    ```yaml
    Toast\SocialLinks\Models\SocialLink:
      platforms:
        - x
        - facebook
        - instagram
        - linkedin
        - youtube
        - pinterest
    ```

4. Run `/dev/build?flush` in your browser.

## Usage

- Go to **Settings > Social Links** in the SilverStripe CMS.
- Add or edit links for each platform.
- Drag to reorder links.
- Platforms listed in the config will be protected from deletion.
- Platforms cannot be added manually, they must be defined in the config.

## Templates

```ss
<% with $SiteConfig %>
    <% if $SocialLinks.Count %>
        <ul class="socials">
            <% loop $SocialLinks %>
                <li class="socials-item">
                    <a href="{$Link}" class="socials-item__link socials-item__link--{$Platform}" target="_blank" aria-label="Click to view our {$Title.ATT} page. (Opens in a new tab)"></a>
                </li>
            <% end_loop %>
        </ul>
    <% end_if %>
<% end_with %>
```

## Extending

You can extend the `SocialLink` model or the `SiteConfigSocialExtension` to add more fields or customize behavior.

## Troubleshooting

- If you cannot delete a social link, ensure it is not listed in the `platforms` array in your config and run `/dev/build?flush`.
- If you add new platforms, run `/dev/build?flush` to create default records.

## License

MIT
