# Ilmenite Visitor Consent
A developer friendly plugin to ask the visitor to consent to some message before being able to view the site.

## Options
This plugin comes with a small settings page located under the `Apperance` menu.
Here you can configure a custom title, description, accept button text and background overlay opacity for the consent box. If no data is provided, the plugin defaults will be used.

**Overlay Opacity Values**
The overlay opacity takes values from 0 to 1, where 0 is transparent and 1 is fully opague.
The default opactiy value is `0.9`.

## Translation
A complete .pot-file has been provided in the `/languages` directory for your convenience. If you translate this to a new language, we would be happy to include it here.

Currently, the languages supported by this plugin are:

- English (en_US)
- Swedish (sv_SE)

## For Developers
We have strived to make this plugin as lightweight as possible to allow you to style and alter as much as possible for your clients' needs.

### Remove Default Stylesheet
The stylesheet can always be dequeued using the WordPress default `wp_dequeue_style`. We have also added a constant `ILVC_NOSTYLE`, which when set to `false` will prevent the stylesheets from enqueuing in the first place.

### Contributing
If you wish to contribute to this plugin, we're just happy about it. Just send us a pull request from the GitHub repository.

## Authors
This plugin has been developed by Bernskiold Media [http://www.bernskioldmedia.com/]. We had a use for this for some of our clients' websites and thought we would turn it into a nice and reusable plugin, which we are sharing.

The following people have contributed to the code of this plugin:

- Erik Bernskiold [http://www.erikbernskiold.com]

## Support
We provide this plugin as-is with limited support. If you have a problem or feature request, let us know though because we want to commit to make sure it is working as expected.

## License
This plugin is licensed under *GPL*.

This program is free software: you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.