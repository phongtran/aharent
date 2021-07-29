=== Advanced Product Fields (Product Addons) for WooCommerce ===
Contributors: studiowombat,maartenbelmans
Tags: woocommerce product addons, woocommerce, woocommerce product options, product addons, product fields
Requires at least: 4.5
Tested up to: 5.8
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Stable tag: 1.3.9

Customize WooCommerce product pages with powerful and intuitive options ( = product add-ons).

== description ==

Advaced Product Fields allows you to add custom form fields to your WooCommerce product pages. Whether you want to create a pizza configurator or simply add extra options, this plugin has got you covered!

The backend looks & works similar to ACF, so you are familiar with setting up the options without a hassle.

= Quick Links =

* [&raquo; Demos](https://product-demo.studiowombat.com/)
* [&raquo; More info](https://studiowombat.com/plugin/advanced-product-fields-for-woocommerce/)

= Features =

* Intuitive and beautiful backend builder which looks like Advanced Custom Fields.
* 9 different form elements to choose from
  * **Text field** - Normal text field
  * **Text area** - For multi-line text
  * **Email** - To accept only email addresses
  * **URL** - To accept only links (URLs)
  * **Number** - To accept numbers (both decimal or whole numbers)
  * **Select (drop-down)** - To select one option from a list
  * **Checkboxes** - To choose one or more options
  * **Radio buttons** - To select one option from multiple options
  * **True/false** - A "yes/no" checkbox
* Conditional logic (show/hide fields based on other fields)
* Dynamic product price: Change the price depending on field value(s)
* Works with your WooCommerce tax settings
* Optimized for speed and faster than other plugins in this genre
* Visibility options: decide on which product(s) to show your fields
* Works with both simple & variable products and also supports variable products over ajax.
* Frontend already translated in English, French, German, Spannish, Dutch.
* Translation-ready for other languages.

= Requirements =

 * WooCommerce 3.4.0 or higher
 * WordPress 4.7 or higher
 * PHP 5.6 or higher

= Limitations of the free version =
Our free version is great for small stores or stores that don't need to add many (complex) options to their products.
There are two main drawbacks of the free version that you should be aware about:

* The free version does not integrate with 3rd party plugins such as WooCommerce Subscriptions or a multi-currency plugin.
* The free version does not work with themes using Ajax for their product "add to cart".

If you need those aspects, you may need our premium version. You can read ahead what's included with premium.

= Premium Features =

Everything from the free version, plus:

* More field types:
  * **File upload** - allowing your customers to upload (multiple) files.
  * **Image swatches** - multi and single choice options with images.
  * **Color swatches** - multi and single color options.
  * **Text swatches** - text options.
  * **Date** - to add a calendar picker.
* Content fields to add content (no form inputs):
  * **Paragraph** - so you can add content and shortcodes to your product page.
  * **Image** - to add images.
  * **Section** - to group fields in a section.
* Support for themes using "ajax add to cart" technology.
* The product image on the frontend can be changed when different options are selected.
* Set a minimum/maximum allowed number of selections on multi-select fields.
* More pricing options: formula-based pricing, quantity-based pricing, percentage-based pricing, value-based pricing, and character length pricing.
* You can also give discounts (negative pricing) on options.
* Quantity-based fields: repeat fields if your visitors change product quantity.
* Works with page/product builders like Elementor, Oxygen, and Divi.
* Variable builder for more complex pricing possibilities.
* More options to attach one field group to multiple products at once (via product name, variation or category).
* Support for multilingual stores through WPML or Polylang.
* Works with your WooCommerce tax settings.
* Support for multi-currency stores via the WOOCS plugin.
* More integrations with other plugins (such as WooCommerce Subscriptions) & popular themes. [See here](https://www.studiowombat.com/knowledge-base/which-plugins-and-themes-are-compatible-with-advanced-product-fields-for-woocommerce/).
* More options for text fields: min. and max. length, or HTML validation (regex pattern).
* Actions & filters so you can extend the plugin with additional features.
* HTML output allowing you to more easily style certain aspects with CSS.
* And more..!

Find out more about the [pro version here](https://studiowombat.com/plugin/advanced-product-fields-for-woocommerce/)

 == Installation ==

 From your WordPress dashboard

 1. **Visit** Plugins > Add New
 2. **Search** for "Advanced Product Fields for WooCommerce"
 3. **Activate** the plugin from your Plugins page
 4. **Click** on the new menu item WooCommerce > Product Fields and create your first Custom Field Group.

== Screenshots ==
1. Example of a product with extra options.
2. Example of the backend settings.

== Frequently Asked Questions ==

= What are the system or server requirements? =

 You'll need WooCommerce 3.4.0 or higher, WordPress 4.7 or higher, and PHP 5.6 or higher to make this plugin work.

== Changelog ==

= 1.3.9 =
 * Update: changed all array() functions to shorthand syntax.
 * Update: enhanced/secured "add to cart" validation.
 * Other: verify compatibility with upcoming WP & Woo versions.

= 1.3.8 =
 * Update: changed links to new version of the documentation.
 * Update: verify WooCommerce & WP version tags.

= 1.3.7 =
 * Update: verify WooCommerce & WP version tags.
 * Update: minimum WooCommerce version is now set to 3.4.0. We ensure compatibility with previous minimum version (3.2.0) for the time being.

= 1.3.6 =
 * Update: added translations for Italian, Norwegian, Swedish & Portuguese.
 * Fix: fixed an issue with VAT-inclusive prices in combination with 0% VAT classes and calculating incorrect base price in the cart.
 * Fix: fixed an edge case bug where reordering fields in the backend didn't work.

= 1.3.5 =
 * Fix: fixed an issue with "wp_slash" for versions older than WP 5.5.
 * Fix: removed some deprecated functions in jQuery.
 * Other: tested & updated WP & Woo version tags.

= 1.3.4 =
 * Update: allow more HTML tags in field labels & descriptions (while still being secure).
 * Fix: fixed an issue where saved product fields were serialized twice.

= 1.3.3 =
 * Fix: fixed PHP some notices when saving fields in the backend.

= 1.3.2 =
 * Fix: minor styling issues in WP 5.5+.
 * Fix: fixed "advanced" tab of Yoast SEO plugin not showing.

= 1.3.1 =
 * Added: support for Polylang.
 * Fix: fixeded cart showing options with "(+$ 0.00)" when no pricing method was selected for this option.

= 1.3.0 =
 * Fix: changed translations text-domain to match the plugin slug so transating via translate.wordpress.org is possible.

= 1.2.1 =
 * Fix: fixed a PHP warning with stripslashes().

= 1.2.0 =
* Update: enhanced how field data is stored in the database: as array instead of objects. This ads more compatibility with migration/export plugins.
* Update: disable stripe "pay now" buttons on the product page as they can't pick up custom pricing.
* Fix: fixed an issue when a user entered text with a quote symbol, a slash would appear before it in cart/checkout.

= 1.1.7 =
 * Added: the plugin now fully supports your WooCommerce tax settings.
 * Update: added CSS to make the admin backend play nicer with the new WP styling.
 * Fix: fixed a bug where empty fields were still shown in cart.
 * Fix: fixed a bug where some fields would be wrapped in a new row unintentionally.
 * Fix: fixed a bug with duplicating field groups under "WooCommerce > Product Fields". field ID's weren't unique when duplicating.

= 1.1.6 =
 * Update: changed frontend JS a bit to support more themes.
 * Update: verify Woo 4.0 & 4.0.1 compatibility.
 * Fix: fixed an issue with cart validation of radio buttons, select lists or checkboxes.

= 1.1.5 =
 * Update: UX improvement: admin won't save if choice field labels are not set and display the error.
 * Update: UX improvement: show error message when duplicating field groups doesn't work.
 * Update: allow 5 field groups in free version instead of just 4.
 * Update: tested compatibility with latest Woo version and updated tags.

= 1.1.4 =
 * Fix: fixed a bug when creating a 4th field group. Adding fields to it wasn't possible.
 * Fix: removed a PHP warning.

= 1.1.3 =
 * Fix: fixed typo in "checkbox" template.

= 1.1.2 =
 * Update: added WPML support.

= 1.1.1 =
 * Update: support for "ajax add to cart" themes and "required" fields.
 * Fix: "select options" was sometimes incorrectly showing.

= 1.1.0 =
 * Update: the dropdown label "choose an option" now only appears when necessary.
 * Update: added frontend translations for Dutch, French, German, and Spannish.
 * Fix: fixed an issue with column widths.

= 1.0.9 =
 * Update: better dependency checking on the frontend.
 * Fix: fixed an issue with select lists and pricing.
 * Fix: fixed an issue with true-false field price labeling.

= 1.0.8 =
 * Fix: fixed an issue with duplicating some fields in the backend.
 * Fix: fixed an issue with hidden fields validating on the frontend.
 * Fix: fixed an issue with decimal pricing on the frontend.

= 1.0.7 =
 * Update: When searching for products in the backend, you can now also find products in draft.
 * Update: added extra info dialogs to the pricing options, so you better understand all options available.
 * Update: support variations via ajax.
 * Fix: fixed pricing calculation when a variation is selected by a user.
 * Fix: fixed an issue with hidden fields wrongly validating in cart.
 * Fix: fixed an issue with product price not updating when the user select a variation.

= 1.0.6 =
 * Fix: fixed an issue with decimal price formatting on the single product page.

= 1.0.5 =
 * Update: added .pot file for translators.
 * Update: added filter for devs to change the "product totals" HTML.
 * Update: allow some HTML in field descriptions & labels.

= 1.0.4 =
 * Fix: fixed a bug with item pricing when changing item quantity on the cart page.

= 1.0.3 =
 * Update: small backend Javascript bugfix.

= 1.0.2 =
 * Update: Made frontend Javascript file 19% smaller, to 5.9kb.

= 1.0.1 =
 * HTML Changes so it can more easily be styled with CSS

= 1.0.0 =

Initial release

== Upgrade Notice ==

= 1.3.0 =
If you've generated your own .po/.mo files, you'll need to rename those to match the new slug "advanced-product-fields-for-woocommerce".