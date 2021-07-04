=== Advanced Dynamic Pricing for WooCommerce ===
Contributors: algolplus
Donate link: https://paypal.me/ipprokaev/0usd
Tags: woocommerce, discounts, deals, dynamic pricing, pricing deals, bulk discount, pricing rule
Requires PHP: 7.0
Requires at least: 4.8
Tested up to: 5.7
Stable tag: 3.2.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

All discount types. WordPress Customizer supported.

== Description ==

This plugin helps you  quickly set discounts and pricing rules for your WooCommerce store.

Set up any kind of discount or dynamic pricing you like, and activate/deactivate rules as needed.

Configure fixed dollar amount adjustments, percentage adjustments, or set fixed price for the product or group of products.

Also supports role-based prices & bulk pricing. **Bulk tables can be designed with Customizer.** You should setup bulk rule for category/product at first and enable "Show Bulk Table" at tab "Settings".

= Some Examples  =

* Category-level discounts - discount products and provide free shipping
* Buy 4(or more) items on Friday and get 20% off
* Buy product X and get product Y for free - immediately added and visible in cart
* Buy a package -  discount it (each item separately), and also get a free product
* Apply bulk discount for selected items, available only to wholesale buyers
* Give a 10% discount to all "Accessories"(Category) if a product X is present in the cart

Check more examples [on our website](https://docs.algolplus.com/algol_pricing/sample-discount/).

= One pricing rule can  =
* Filter cart items by products, categories, tags or custom fields
* Modify price for each product separately
* Or set total price for whole set
* Apply cart discounts and fees
* Add free products on fly
* Use tables to get bulk rates
* Validate conditions for cart items, user roles or dates
* Track limits (only "max usage" supported currently)

= Interface settings =
* Show/hide original prices
* Show/hide badge "On Sale"
* Show/hide bulk discount table on the product page
* Set rule for  products which already on sale
* and much more ...

[Pro version](https://algolplus.com/plugins/downloads/advanced-dynamic-pricing-woocommerce-pro/) can [adjust product price onfly](https://algolplus.com/plugins/pro-features-in-action/), adds **exclusive rules, extra conditions, a lot of settings, and statistics** (which rules really work, which products are involved and how much does it cost for you).

Have an idea or feature request?
Please create a topic in the "Support" section with any ideas or suggestions for new features.

== Installation ==

= Automatic Installation =
Go to Wordpress dashboard, click  Plugins / Add New  , type 'Advanced Dynamic Pricing for WooCommerce' and hit Enter.
Install and activate plugin, visit WooCommerce > Pricing Rules.

= Manual Installation =
[Please, visit the link and follow the instructions](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation)

== Frequently Asked Questions ==

= The plugin slows down my site a lot. Sometimes the cart page just freezes. =
It seems your websitе calls external API to do shipping calculations. Use following PHP code  to suppress it in our rules
add_filter( "adp_feature_flag_disable_shipping_calculation_during_process", "__return_true" );
= Free product can't be added to the cart. I see message "Sorry, this product cannot be purchased." =
WooCommerce verifies product before adding to the cart. So this product must be published, in stock and has price defined.
= How can I hide original prices? =
It's a PRO feature. You should turn off option "Show striked prices" at tab Settings, for category and product pages.
= I don't see "For sale" badge for variable products =
It's a PRO feature. You should turn on option "Calculate 'On Sale' badge for variable products" at tab Settings, section Calculations.
= Compatibility with my theme/plugin =
Free and pro versions use same core, so you can test it using free version. [Please, visit the link to see detailed reply](https://algolplus.com/plugins/pricing-plugin-compatibility/)
= How to allow customer to select free product =
You should create package rule and set zero price for free product. [Please, check 3rd example](https://algolplus.com/plugins/dynamic-pricing-examples-package-pricing/)
= How to customize bulk tables or row "amount saved" =
You should copy necessary file from folder “BaseVersion/templates” to folder “advanced-dynamic-pricing-for-woocommerce” (create it in active theme)
= The rules are not applied to orders if I use button "Add order" (>WooCommerce>Orders) =
This form adds new order directly to the database. But all pricing plugins work with cart items. Use our plugin [Phone Orders](https://wordpress.org/plugins/phone-orders-for-woocommerce/) to add backend orders.
= I can't change quantity or delete item from cart =
It's a conflict with another plugin which modifies cart items too. You should turn on debugbar and send us report/json file. [Read short guide.](https://algolplus.freshdesk.com/support/solutions/articles/25000020993-debug-bar)
= I marked checkbox "Add products to cart at normal cost and add coupon...", but I don't see any coupons in the cart =
You should visit >WooCommerce>Settings and mark "Enable the use of coupon codes".
= I need custom cart condition =
You should be PHP programmer to do it. [Please, review sample addon and adapt it for your needs](https://algolplus.com/plugins/program-custom-condition/)


== Screenshots ==
1. List of pricing rules
2. Simple rule -  discount for category
3. More complex rule
4. Complex rule was applied to the cart
5. The applied discounts can be viewed  inside the order
6. Settings page


== Changelog ==

= 3.2.5 - 2021-06-28 =
* Allow to filter products by custom attributes
* Apply rules to orders created via REST api
* Adjusted auto-generated texts for bulk tables
* Updated compatibility with "WooCommerce Composite Products", by SomewhereWarm
* Fixed bug - custom taxonomies conditions did not work
* Fixed bug - reset selected shipping method for some cart conditions
* Fixed bug - some products didn't show assigned rules on tab "Pricing rules" (when admin edits product)

= 3.2.4 - 2021-05-24 =
* Show adjusted prices in product schema (JSON-LD)
* Added compatibility with "WooCommerce Deposits", by WooCommerce
* Added compatibility with "Composite Products", by SomewhereWarm
* Added compatibility with "Price Based on Country for WooCommerce", by Oscar Gare
* Updated compatibility with WPML
* Updated compatibility with "WOOCS – WooCommerce Currency Switcher", by realmag777
* Updated compatibility with "WooCommerce Multi Currency – Currency Switcher", by VillaTheme
* Updated compatibility with "WooCommerce Product Bundles", by SomewhereWarm
* Updated compatibility with "Aelia Currency Switcher", by Aelia
* Fixed bug - the bulk table wasn't displayed in the "quick view" popup
* Fixed bug - wrong number of gifts were given by "based on subtotal"
* Fixed bug - date/time conditions worked incorrectly for some date formats
* Fixed bug - wrong text domain for messages in the footer of bulk table

= 3.2.3 - 2021-04-26 =
* Support tag {{regular_price_striked}} in template "Replace price with lowest bulk price"
* Allow to use HTML in bulk message
* Don't allow to use "0" as start value for bulk range
* Renamed labels and options for "Onsale" products​ (>Settings>Calcuations)
* Fixed bug - incorrect price at product page for rules "X items for $Y"
* Fixed bug - shortcode [adp_products_on_sale] didn't work for custom taxonomies
* Fixed bug - displayed the bulk table for the excluded variation
* Fixed bug - compatibility issues with Phone Orders(pro version)
* Fixed bug - importer ignored variations

= 3.2.2 - 2021-03-25 =
* Added tag {regular_price_striked} to templates on the tab >Settings>Product Price
* Fixed bug - shortcodes [adp_products_on_sale] and [adp_products_bogo] didn't work
* Fixed some minor bugs

= 3.2.1 - 2021-03-22 =
* Fixed bug - option "Unique variation only" doesn't work (in section "Product Filter")
* Fixed bug - impossible to turn off option "Replace price with lowest bulk price" (if it was on in version 3.1.x)
* Fixed bug - incorrectly converted template for option"Replace price with lowest bulk price"
* Fixed bug - option "Replace price with lowest bulk price" was not applied if 1st range didn't start with "1"

= 3.2.0 - 2021-03-17 =
* Requires PHP 7.0 or greater
* Fixed bug - shortcode {price_excluding_tax} was not discounted
* Fixed bug - cart conditions ignored the sale price
* Compatibility with plugin "WPCS – WordPress Currency Switcher", by realmag777
* Compatibility with plugin "WooCommerce Multi Currency – Currency Switcher", by VillaTheme
* Compatibility with plugin "WooCommerce Subscriptions", by WebToffee
* Compatibility with plugin "PDF Product Vouchers", by SkyVerge

= 3.1.5 - 2021-01-18 =
* Fixed bug - cart adjustments didn't work correctly

= 3.1.4 - 2021-01-18 =
* Fixed bug - conflict with internal Wordpress postboxes.js library
* Fixed bug - button "Get system report" didn't work if debugbar was inactive
* Fixed bug - coupon/"Amount Saved"/totals was wrong if option "Add products to cart at normal cost" was active
* Fixed bug - mode "Discount regular price" set incorrect price for "onsale" products in the cart (only)

= 3.1.3 - 2020-12-28 =
* Fixed critical bug - simple rules ignored limit "Can be applied"
* Fixed bug - wrong internationalization for selecting gifts

= 3.1.2 - 2020-12-22 =
* Allow to restore deleted gifts from mini-cart
* Fixed bug - can not use variable product as gift
* Compatibility with plugin "Gift Cards", by SomewhereWarm
* Сompatibility with plugin "Aelia Currency Switcher", by Aelia

= 3.1.1 - 2020-12-15 =
* Fixed critical bug - sometimes free products were not added to the cart

= 3.1.0 - 2020-12-10 =
* Allow to remove gifts (added by BOGO rules) from the cart
* Modified options in section Сoupons (tab Settings)
* Main SKU (in product filter) now matches to all variations
* Fixed bug - buttons "Rebuld onsale/bogo list" ignored products when were excluded in Product Filters
* Fixed bug - unneccessary BR tag in template for bulk table


= 3.0.9 - 2020-11-11 =
* The plugin supports multisite network
* Fixed bug - bulk discount didn't work if ending values were empty (for ranges)
* Fixed bug - option "Use first range as minimum quantity if bulk rule is active" didn't work for variation products
* Fixed bug - coupon(automatically added) now split proportionally to cart items cost
* Fixed bug - PHP error "Cannot instantiate interface" for coupons
* Added filter "adp_discount_product_table"


= 3.0.8 - 2020-10-28 =
* Added grouping 'Qty based on cart position' for Tier mode (bulk rules)
* Fixed bug - link "Remove" is visible for coupons added by our plugin
* Fixed bug - the amount of the coupon (for the replacement of the free product) ignored the WC option "price includes tax"
* Fixed bug - fake range "1-xxx" was added to bulk table
* Fixed bug - option "Use first range as minimum quantity if bulk rule is active" didn't work
* Fixed bug - the Customizer showed "Amount saved" twice
* Fixed bug - imported rules had products with names "0"
* Fixed bug - incompatibility issue with "WooCommerce Product Bundles" plugin

= 3.0.7 - 2020-10-21 =
* Fixed bug - can not add more same items if package rule was applied
* Fixed bug - wrong amounts shown in bulk table if cart was non-empty
* Fixed bug - bulk rules didn't work sometimes if first bulk range didn't start with "1"
* Fixed bug - wrong coupon amount calculated (for the replacement of the modified products)
* Fixed bug - coupon amount (for the replacement of the free product) ignores the WC option "price includes tax"
* Fixed bug - incorrect rounding if option "Round up totals to match modified item prices" is off
* Added some hooks to format bulk table

= 3.0.6 - 2020-10-12 =
* Bulk mode "Qty based on meta data" (to split variations with different attributes)
* Added compatibility with WPML
* Tweaked compatibility with WOOCS
* The "Coupon name" field is shown "as is" (not in lower case)
* Modified Customizer to show %% discounts in bulk table
* Added parameters for shortcode [adp_product_bulk_rules_table]
* Fixed bug - link "Remove" was visible for coupons added by our plugin
* Fixed bug - wrong coupons search on tab "Rules"
* Fixed bug - the plugin ignored schedule for sale prices (date range set inside the product)
* Fixed bug - option "Max discount" was not applied to the product discounts sometimes
* Fixed bug - missed 'role discount' and 'excluded products' during export at tab "Tools"
* Fixed bug - missed “.wdp_bulk_table_content” container for simple products (bulk table)
* Fixed bug - option "Hide coupon word" didn't work at checkout page
* Fixed bug - option "Show message after adding free product" showed same messages on page reload
* Fixed bug - Safari showed extra text in title (for each rule)
* Fixed bug - some texts can not be translated
* Fixed bug - can not delete the plugin if WooCommerce is not active

= 3.0.5 2020-08-05 =
* internal, not published

= 3.0.4 - 2020-07-21 =
* Added compatibility with WOOCS
* Added compatibility with "WooCommerce All Products For Subscriptions"
* Added filter "adp_rule_loaded"
* Fixed bug - negative discount didn't work for simple products
* Fixed bug - custom taxonomies were ignored by product filters
* Fixed bug - fatal error for grouped products
* Fixed bug - PHP error "call to undefined function wc_get_chosen_shipping_method_ids"
* Fixed bug - Functions::getDiscountedProductPrice() returns the wrong type
* Fixed bug - Product filter "Custom field" does not check all meta

= 3.0.3 - 2020-07-02 =
* Rule UI updated -  show selector "None/Same product/Same variation" in "Filter by products" ( only if QTY > 1 )
* Added compatibility with "WooCommerce Subscriptions, by WooCommerce"
* Added compatibility with "WooCommerce Product Bundles, by SomewhereWarm"
* Added filter "wdp_allow_to_strike_out_variable_range"
* Fixed bug - negative discount didn't modify prices at the product/category pages
* Fixed bug - free products (based on subtotal amount) were not added to the cart
* Fixed bug - PHP error "Call to undefined function wc_get_cart_item_data_hash()"
* Fixed bug - PHP warning "Illegal string offset"
* Fixed bug - Split discounts did not work correctly
* Fixed bug - Variable product prices are now calculated based on visible children

= 3.0.2 - 2020-06-22 =
* Fixed bug - the plugin didn't show crossed out prices for product at pages and in the cart
* Fixed bug - bulk table didn't show custom message
* Fixed bug - bulk "Tier" mode didn't work at all
* Fixed bug - performance issue for variable products
* Fixed bug - variable price showed range with same numbers
* Fixed bug - compatibility with caching plugins, error "class AbstractConditionCartItems not found"
* Fixed bug - compatibility with caching plugins, error "Uncaught Exception: Serialization of ‘Closure’ is not allowed"
* Fixed bug - wrong qty of free products added for complex Product Filters
* Fixed bug - tab Tools was broken if  there is double quote in rule title
* Fixed bug - PHP warning "Illegal string offset"
* Fixed bug - Condition 'Cart payment method' do not work during purchase
* Fixed bug - Incorrect calculation of the tag "{{price}}" in template the "Replace price with lowest bulk price" for specific rules
* Fixed bug - "Amount saved" label disappears after updating using AJAX on the cart/checkout page
* Fixed bug - Error comparing with WC sale price
* Fixed bug - Restore missing {{price_suffix}} tag
* Added missed hook "wdp_calculate_totals_hook_priority"
* Rename the conditions "Subtotal/Subtotal amount (skip onsale products) *" to "Subtotal (exc. VAT)/Subtotal (skip onsale products and exc. VAT) *".
Now the conditions listed use the subtotal without tax to comparison with the rule value.

= 3.0.1 - 2020-06-17 =
* Fixed bug - compatibility with caching plugins, error "Cannot declare class WP_Object_Cache"
* Fixed bug - compatibility with WPML, error "Call to a member function get_client_currency() on null"
* Fixed bug - rotated bulk table didn't display prices
* Fixed bug - option "from minimum bulk price"  was not applied to variable products
* Fixed bug - double discount was applied in some cases
* Fixed PHP warnings/notices

= 3.0.0 - 2020-06-16 =
* The plugin requires at least WooCommerce 3.6.0!
* Fixed bug - mode "Qty base on product categories in all cart" generated wrong bulk table
* Fixed bug - compatibility issues with our Phone Orders plugin

= 2.3.5 - 2020-05-06 =
* Bulk table is compatible with multilanguage plugins
* Fixed bug - qty based on selected products(categories) showed wrong price
* Fixed bug - wrong calcuations for Tier mode
* A lot of new hooks

= 2.3.3 - 2020-03-24 =
* Compatible with WooCommerce 4.0
* Improved compatibility with WPML (gift/free products)
* Fixed bug - the wrong abbreviations were displayed for the states of different countries
* Fixed bug - bulk table showed price "-"  for variable product if varations have same price
* Fixed some minor bugs (cart processing)
* Added a lot of hooks (for compatibility with other plugins)

= 2.3.2 - 2020-02-05 =
* Security update - added nonce to all ajax requests
* Fixed bug - the plugin didn't show striked price on product page
* Fixed bug - the plugin didn't work for PHP 5.4
* Updated uninstallation code

= 2.3.1 - 2020-01-29 =
* UI updated for rule - add operator "Not containing" for cart conditions
* UI updated for rule - added selector "Role discounts and bulk discounts will be applied in following order" (modes: apply both, use min price, use max price)
* UI updated for rule - added checkbox "Same product" to section "Free Products"
* Updated settings -  added sections "Product price", "Bulk table", "Coupons"
* Added option "Calculate price based on" for bulk tables
* Added system option "Apply pricing rules while doing API request"
* Added html template for product price
* Fixed bug - value for "Amount Saved" was wrong if user marked checkbox "Don't modify prices and add discount to cart as fee/coupon"
* Fixed bug - now if you use "role discounts and bulk discounts" inside one rule - IT CAN INCREASE PRICES

= 2.3.0 - 2019-12-03 =
* Added shortcode [adp_products_bogo] (enable it in >Settings>Rules)
* UI updated for rule - added checkbox "Exclude products modified by other pricing rules" for product filters
* UI updated for rule - added sorting "As appears in the cart" for product filters
* Label "Amount saved" moved to Customizer
* Product option "Show bulk table regardless to conditions"
* Product option "Use first range as minimum quantity if bulk rule is active"
* Cart option "Show message after adding free product"
* Calculation option "Use prices modified by other plugins"
* System option "Apply pricing rules while doing cron"
* Bulk mode "Qty based on selected products"
* Bulk mode "Qty based on cart position"
* Improved compatibility with currency plugins (for example, Currency Switcher)
* Improved compatibility with WPML
* Use stable way to process AJAX calls
* Added button "Get system report" to tab "Tools"
* Fixed bug - incorrectly calculate some conditions for guests
* Fixed bug - extra cookies were sent while processing cart items

= 2.2.4 - 2019-10-07 =
* Fixed bug - bulk table can't be shown if custom product taxonomies are active
* Fixed bug - bulk table can't be shown if products were filtered by category slug
* Fixed bug - incompatibility with WooCommerce 3.3

= 2.2.3 - 2019-09-26 =
* Fixed critical bug - some options can't be turned OFF

= 2.2.2 - 2019-09-25 =
* Changes for bulk tables: new template, option "Display ranges as headers"(products only)
* Tag {{price_striked}} is supported by category option "Replace product price with lowest bulk price"
* Override price range for Grouped products
* Added button "Refresh" to debugbar (useful to check applied rules after ajax calls)
* Tweak default settings
* Fixed bug - plugin showed wrong price for variable products having 30+ variations
* Fixed bug - plugin showed "0.00" if price was just empty
* Fixed bug - option "Suppress other pricing plugins" generates warnings for some hooks
* Fixed bug - now plugin overrides cents only if price was changed
* Fixed bug - attributes filtering doesn't work for some cases

= 2.2.1 - 2019-08-26 =
* Fixed bug - option "Best between discount and sale price" uses sale price
* Fixed bug - the shortcode incorrectly works for variable products
* Fixed bug - non-standard ajax requests use empty cart for price calculations
* Fixed minor bugs for debug bar

= 2.2.0 - 2019-08-19 =
* Debug bar for admins (enable it in >Settings>Debug)
* UI updated for rule - added checkbox "Exclude on sale products" for product filters
* UI updated for rule - added checkbox "Don't set zero price and add discount to cart as coupon" for free products
* UI updated for rule - added ranges for product/category conditions (when compare qty of items)
* Category option "Replace product price with lowest bulk price"
* Cart option "Disable external coupons only if any of cart items updated"
* Cart option "Show striked subtotal"
* Cart option "Hide word Coupon"
* Support UTF8 coupons
* Partially support WooCommerce Subscriptions (adjust only amount for period)
* Improved performance for product filters
* Fixed bug - showed warning for role discount if roles were not selected
* Fixed bug - shortcodes printed some html even if product have no bulk ranges

= 2.1.1 - 2019-07-16 =
* New mode for "Free products", repeat counter = subtotal amount divided by XXX
* Fixed bug - plugin showed wrong price for the products which are not affected by rules
* Fixed bug - default settings were not applied to Customizer, user had to modify any option and publish changes
* Fixed bug - plugin rounded fractional qty in the cart

= 2.1.0 - 2019-07-02 =
* The plugin requires at least WooCommerce 3.3.0 !
* Added shortcode [adp_products_on_sale] (enable it in >Settings>Rules)
* New product filter - "Any product"
* Category filter  updated , parent category filter is applied even if only child category was selected in the product
* UI updated for rule  - added checkbox "Don't modify prices and add discount to cart as fee/coupon"
* New cart rules - repeatable fixed fee and discounts
* New cart option "Calculate totals based on modified item price"
* Fixed bug - tab "Tools" exported only settings
* Fixed bug - fatal error for bulk rules (created in previous versions -  1.6.0 or earlier)
* Fixed bug - system option "Still allow edit Phone orders" worked incorrectly
* Fixed bug - the plugin disabled coupon form in the cart (for some themes only)

= 2.0.0 - 2019-05-27 =
* New calculation algorithm, the plugin works much faster
* Tab "Tools" can export/import settings and rules
* Rule UI change - Different product attributes can be selected in same filter
* Rule UI change - Allow to exclude products in product filters (must be turned ON at tab Settings)
* Added pagination to list of rules
* New rule option "Automatically disable rule if it runs longer than X seconds" (default - 5 seconds)
* New customizer option "Show bulk table message as table header"
* New cart option "Label for saved amount" (previous label "Discount Amount" confused customers)
* New cart option "Disable external coupons if any rule was applied"
* New system option "Still allow to edit prices in Phone Orders"
* Fixed bug - Customizer didn't work if we used only shortcodes
* Fixed bug - filter "Product taxonomies" didn't work for variable products
* Fixed bug - path (for custom templates) has  ignored folders
* Fixed bug - missed role "Guest" in conditions and role-based rules

= 1.6.0 - 2019-03-12 =
* **Warning!** If you used bulk to packages - switch mode to "Qty equals count of sets"
* New calculation modes for bulk/tier discount - "Qty based on product categories" and "Qty based on selected categories"
* New discont types for bulk/tier discount - "Fixed price for set" and "Fixed discount for set"
* Section "Free products" allows to use multiple gifted products
* Section "Free products" adds all variations if user selects variable product
* Section "Product Filter" supports custom product taxonomies
* New option "Don't modify product price at product page"
* Tab "Settings" was facelifted
* Fixed bug - option "override cents" was applied to zero prices
* Added a lot of hooks (for compatibility with other plugins)

= 1.5.2 - 2019-01-10 =
* Added operation "not in list" to product filters
* Added two modes for cart conditions:  AND (all conditions must be valid) and OR (any condition must be valid)
* Apply pricing rules to [Phone Orders](https://wordpress.org/plugins/phone-orders-for-woocommerce/). You must turn on "Apply pricing rules to backend orders" in >Settings>System.
* Fixed bug - showed SALE badge for all products
* Fixed bug - date range didn't work for some locales
* Fixed bug - didn't show price suffix in modified price
* Speeded up calculations for ajax requests

= 1.5.1 - 2018-11-26 =
* "Role discounts" and "Bulk discounts" can be used together (drag them to set priority, added mode "Skip bulk rules if role rule was applied")
* Correctly works with sold individually products
* New tab "Settings"
* Show bulk range as a single number, if "beginning of range" is equivalent to "end of range"
* Allow negative discounts (for price increase!)
* Speeded up calculations if there are many active rules
* Update price when user increases quantity on product page (pro version only), [see it in action](https://algolplus.com/plugins/pro-features-in-action/)
* Update price for cross-sells in the cart (pro version only)

= 1.5.0 - 2018-10-30 =
* Bulk tables can be tweaked using Customizer (visit tab "Settings" and click "Customize")
* Added new mode for on-sale products - "Best between discounted regular price and sale price"
* Fixed bug: "Free shipping" stayed in the cart if you delete products

= 1.4.4 - 2018-10-10 =
* Added mode "quantity based on" for bulk rules (default - all products)
* Added option to show discounted price in bulk table
* Display bulk table for selected variation
* Allow translate custom messages for bulk table (via WPML)
* Added new filter - category slug
* Speeded up calculations for category pages
* Speeded up calculations for cart having many units of same product, finally

= 1.4.3 - 2018-07-26 =
* Added new filter - product SKU
* Added option to show "On Sale" badge if product price was modified by pricing rules
* Speeded up calculations for cart having many units of same product
* Fixed display bugs for variable products

= 1.4.2 - 2018-06-04 =
* Added ability to select position for table with bulk rules (thanks to @nessunluogo)
* Added shorcodes  [adp_category_bulk_rules_table] and [adp_product_bulk_rules_table] to use in category/product pages
* Fixed critical bug: product filter by attributes didn't work for some setups
* Fixed bug:  "on sale" badge was hidden
* Allow to customize bulk tables, you should copy files from folder "templates" to folder "advanced-dynamic-pricing-for-woocommerce" (create it in active theme)

= 1.4.1 - 2018-04-09 =
* Added ability to show bulk table at category page
* Fixed critical bug: product filter by category/tags/custom fields didn't work for variable products

= 1.4.0 - 2018-02-19 =
* New condition "Active subscriptions"
* New condition "Customer order count"
* New setting "Override cents" (round discounted prices  to xxx.99)
* Updated buttons in UI
* Preserve external coupons in cart
* Show total discount amount in cart and checkout
* Show applied discounts in order popup (WooCommerce 3.3.0 functionality)

= 1.3 - 2017-12-20 =
* Fixed critical bug: now  we don't rebuild the cart if no rules were applied
* Added the message on activation

= 1.2 - 2017-12-08 =
* Support taxes for items and shipping
* Added condition "Product custom fields"
* Added tab "Help"
* Fixed some minor bugs

= 1.1 - 2017-11-21 =
* Added condition "Customer Role"
* Added documentation link

= 1.0 - 2017-11-10 =
* First release.
