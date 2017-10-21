=== WooCommerce - Store Exporter Deluxe ===

Contributors: visser
Donate link: http://www.visser.com.au/#donations
Tags: e-commerce, woocommerce, shop, cart, ecommerce, export, csv, xml, xls, xlsx, excel, customers, products, sales, orders, coupons, users, attributes, subscriptions
Requires at least: 2.9.2
Tested up to: 4.2.2
Stable tag: 1.9.7

== Description ==

Export store details out of WooCommerce into simple formatted files (e.g. CSV, XML, Excel 2007 XLS, etc.).

Features include:

* Export Products
* Export Products by Product Category
* Export Products by Product Status
* Export Products by Type including Variations
* Export Categories
* Export Tags
* Export Brands
* Export Orders
* Export Orders by Order Status
* Export Orders by Order Date
* Export Orders by Customers
* Export Orders by Coupon Code
* Export Customers
* Export Customers by Order Status
* Export Users
* Export Coupons
* Export Subscriptions
* Export Commissions
* Export Product Vendors
* Export Shipping Classes
* Export Attributes
* Toggle and save export fields
* Field label editor
* Works with WordPress Multisite
* Export to CSV file
* Export to XML file
* Export to Excel 2007 (XLS) file
* Export to Excel 2013 (XLSX) file
* Export to WordPress Media
* Export to e-mail addresses
* Export to remote POST
* Export to remote FTP
* Supports external CRON commands
* Supports scheduled exports

For more information visit: http://www.visser.com.au/woocommerce/

== Installation ==

1. Upload the folder 'woocommerce-exporter-deluxe' to the '/wp-content/plugins/' directory
2. Activate 'WooCommerce - Store Exporter Deluxe' through the 'Plugins' menu in WordPress

If you currently have our basic Store Exporter Plugin activated within your WordPress site we will do our best to automatically de-activate it to avoid conflicts with Store Exporter Deluxe.

See Usage section before for instructions on how to generate export files.

== Usage ==

1. Open WooCommerce > Store Export from the WordPress Administration
2. Select the Export tab on the Store Exporter screen
3. Select which export type and WooCommerce details you would like to export
4. Click Export
5. Download archived copies of previous exports from the Archives tab

Done!

== FOSS Disclaimer ==

One open source library is included with this Plugin (without changes)
> PHPExcel v1.8.0 (2014-03-02) - http://phpexcel.codeplex.com

== Support ==

If you have any problems, questions or suggestions please join the members discussion on our WooCommerce dedicated forum.

http://www.visser.com.au/woocommerce/forums/

== Changelog ==

= 1.9.7 =
* Changed: Using WP_Query by default for Subscriptions export
* Fixed: Filter by Order Status not working in Orders export
* Fixed: PHP warning notices for json_ids on Filter Orders by Product
* Fixed: Failed export notice showing for non-last_export Orders
* Added: Hide option within Field Editor for excess export fields
* Added: Since last export to scheduled export engine
* Added: Has Downloads to Orders export
* Added: Has Downloaded to Orders export
* Added: Disable SFTP scheduled export option if required PHP module is missing

= 1.9.6 =
* Added: Barcode to Order Items within Orders export
* Added: Barcode Type to Order Items within Orders export
* Fixed: Empty Order exports since introducing Select2 Enhanced
* Fixed: Export buttons on Edit Orders screen not working

= 1.9.5 =
* Added: Filter Coupons by Discount Type
* Added: Usage Count to Coupons export
* Added: Used By to Coupons export
* Added: Usage Cost to Coupons export
* Added: Export Orders since last export under Filter Orders by Date
* Added: Export Status column to Orders screen
* Added: Detection of failed export and reset of export flags
* Added: Export support for Barcodes for WooCommerce
* Fixed: Formatting of prefix/suffix within PDF Invoice Number export field
* Changed: Filter Order by Product using Select2 and AJAX
* Changed: Filter Order by Product in scheduled export using Select2 and AJAX
* Added: Biographical Info to Users export
* Added: AIM to Users export
* Added: Yahoo IM to Users export
* Added: Jabberr / Google Talk to Users export
* Changed: Do not include Variations by default

= 1.9.4 =
* Added: Option to split Product Gallery over multiple rows
* Added: Support for Field Editor within unique Order Items Formatting
* Fixed: Serialised arrays now export array values
* Added: WordPress Filters to override XML nodes
* Added: WordPress Filters to override associated Categories to Products
* Added: Coupon Description to Orders export
* Added: WPML integration for Post and Term counts
* Added: Filter Products by Language
* Added: Filter Categories by Language
* Added: Filter Tag by Language
* Added: Filter Orders by Sequential Order Number via CRON
* Added: Filter Orders by Sequential Order Number Pro via CRON
* Added: Support for WooCommerce EU VAT Number in Orders
* Added: Support for WooCommerce Hear About Us in Order, Customer and User exports
* Fixed: Detection of duplicate store to hide prompts after dismiss
* Added: Support for WooCommerce Wholesale Pricing in Products export
* Added: Filter Products by Status via scheduled export engine
* Added: Filter Products by Status via CRON engine
* Added: Warning notice to Enable Archives
* Changed: Enable Archives is disabled by default
* Added: Trigger export on new Order to Settings pane
* Added: Option to enable/disable trigger on new Order
* Added: Option to control export format of trigger export on new Order
* Added: WordPress Filters to override WP_Query, WP_User_Query and get_terms
* Added: Show on screen tab options for columns within the Archives tab
* Added: Filesize column to Archives tab
* Added: Rows column to Archives tab
* Fixed: Compatibilty sharing PHPExcel class with other WordPress Plugins
* Changed: Filter Orders by multiple Customers
* Fixed: Field escaping formatting in CSV export files
* Added: Custom User field support to Customer exports
* Fixed: Column mismatch for unique Order Items Formatting rule in Order exports
* Changed: Filter Orders by Product includes Variations
* Fixed: Variations within Filter Orders by Product include Attribute values
* Added: Filter Orders by Product to scheduled exports

= 1.9.3 =
* Fixed: Saving Default e-mail subject within Settings
* Changed: Increased maxlength on Once every x minutes interval
* Added: SFTP protocol for scheduled exports
* Added: Filter to override path to sys_get_temp_dir()
* Fixed: Currency symbol beside price fields in latest WooCommerce
* Fixed: Strip HTML from price fields in latest WooCommerce
* Added: Filter Orders by Payment Gateway
* Added: Payment Gateway count to Filter Orders by Payment Gateway
* Added: Configure panel to Recent Scheduled Exports widget on WordPress Dashboard
* Added: Number of recent scheduled exports form field to Dashboard widget
* Added: Disable scheduled exports on duplicate site or staging site detection
* Changed: Filter Orders by Billing Country supports multple options
* Changed: Filter Orders by Order Status supports multiple options
* Changed: Filter Products by Product Type supports multiple exports
* Added: Orders Screen section to Settings screen
* Added: Actions display fields to show/hide export actions on Orders screen
* Changed: Hide Filter Products by Brand if Brands are unavailable
* Changed: Filter Orders by Order Status supports multple options
* Changed: Filter Orders by User Role supports multiple options
* Changed: Filter Products by Product Type supports multiple options
* Changed: Filter Products by Product Status supports multiple options
* Added: Filter Orders by Shipping Method
* Fixed: Encoding issue affecting UTF-8 in PHPExcel formats

= 1.9.2 =
* Fixed: Variable date ranges in Order exports
* Added: Order Items Booking ID to Orders export
* Added: Order Items Booking Start Date to Orders export
* Added: Order Items Booking End Date to Orders export
* Added: Scheduling section for Scheduled Exports
* Changed: Moved "Once every (x) minutes) to Scheduling section
* Added: Export daily/weekly/monthly to Scheduling section
* Added: Commence exports from now option to Scheduling section
* Added: Commence exports from date option to Scheduling section
* Added: Override XML nodes via Field Editor for exports
* Fixed: Leading 0's being stripped from numbers in CSV, XLS, XLSX
* Fixed: Subscriptions exports for stores with the is_large_store flag set
* Added: Notice to indicate where is_large_store flag is set within Subscriptions 
* Added: WordPress filter to override Order Shipping ID
* Fixed: Links to WordPress Plugins Search using Term filter instead of Tag
* Added: Time support to scheduling exports
* Added: Support for custom Product Add-ons in Orders
* Changed: Hide Add New button on Export screen
* Added: Fixed filename support for Export to FTP scheduled exports
* Changed: Error styling within Recent Scheduled Exports widget on WordPress Dashboard

= 1.9.1 =
* Fixed: Subscriptions export not working
* Changed: Filter Subscriptions by Subscripion Product uses jQuery Chosen
* Added: Filter Orders by Billing Country to scheduled export
* Added: Filter Orders by Shipping Country to scheduled export
* Added: Filter Orders by Product to scheduled export
* Fixed: Filter Orders by Coupon not working
* Changed: Exclude Variations from Filter Orders by Product dropdown
* Fixed: Default empty Order Items Type to Line Item for CRON Order exports
* Added: order_items_types support to CRON attributes
* Added: Notice when fatal error is encountered from memory/timeout
* Fixed: Conflict of XML class name
* Fixed: Order Items: Stock missing in individual Order Items Formatting of Orders export
* Fixed: Scenario where open_basedir is enabled and ./tmp is off limits
* Fixed: Delimiter override not working in CRON exports
* Added: WooCommerce Bookings integration for Booking Date in Orders export
* Added: Booking to Filter Products by Product Type in Products export
* Added: Booking Has Persons to Products export
* Added: Booking Has Resources to Products export
* Added: Booking Base Price to Products export
* Added: Booking Block Price to Products export
* Added: Booking Display Price to Products export
* Added: Booking Requires Confirmation to Products export
* Added: Booking Can Be Cancelled to Products export
* Added: Export to XLSX to Orders screen
* Fixed: Order Discount not being filled in WooCommerce
* Changed: Renamed Order Excl. Tax to Order Subtotal Excl. Tax
* Added: Order Total Tax to Orders export
* Added: Order Tax Percentage to Orders export

= 1.9 =
* Fixed: Default to Attribute Name if Label is empty
* Fixed: Product Gallery exporting only Image ID
* Changed: Reduced memory requirements for Products export
* Changed: Reduced memory requirements for Orders export
* Fixed: Filter Products by multiple tax_query arguments
* Fixed: Variant Products with empty Price
* Fixed: Advanced Google Product Feed: Product Type not exporting
* Added: Support for XLSX Excel 2013 export format
* Changed: Using PHPExcel library for CSV, XLS and XLSX export file generation
* Fixed: Delete multiple archives via bulk actions
* Added: RSS export format
* Added: RSS Settings section to Settings tab
* Added: RSS Title option to Settings tab
* Added: RSS Link option to Settings tab
* Added: RSS Description option to Settings tab
* Fixed: FTP Host now strips out excess prefixes
* Fixed: WooCommerce Checkout Manager Pro integration
* Added: Support for multiple function/class detection on Export Modules
* Fixed: Empty SKU on Order Items in Orders export
* Added: Total Order Items to Orders export
* Added: Strip tags from Description/Excerpt to Export Options
* Fixed: Filter Orders by Product missing loads of Products
* Changed: Filter Orders by Product uses jQuery Chosen
* Changed: Filter Orders by Product Category uses jQuery Chosen
* Changed: Filter Orders by Product Tag uses jQuery Chosen
* Changed: Filter Orders by Product Brand uses jQuery Chosen
* Changed: Filter Orders by Coupon Code uses jQuery Chosen
* Changed: Filter Products by Product Category uses jQuery Chosen
* Changed: Filter Products by Product Tag uses jQuery Chosen
* Changed: Filter Products by Product Brand uses jQuery Chosen
* Changed: Filter Products by Product Vendor uses jQuery Chosen
* Fixed: Default Order Line Types for Orders export to Line Item
* Fixed: Order Items: SKU empty for Product Variations in Orders export
* Fixed: Exclude CRON exports from Recent Scheduled Exports Dashboard widget
* Changed: Filter Products by Stock Status now takes Stock Qty into consideration

= 1.8.9 =
* Fixed: Export Product Attributes in Product export
* Added: Support for custom Attributes in Product export
* Added: Default Attributes to Product export
* Fixed: Attribute taxonomy missing from Order Items: Product Variation in WC 2.2+
* Added: Support for Ship to Multiple Address for Order export
* Changed: Export to FTP now deletes the archived export
* Fixed: Variables not being included in Product export when filtering by Categories/Tags/Brands/Vendors
* Added: Support for Sequential Order ID within WooCommerce Jetpack
* Added: Support for Sequential Order ID formatting within WooCommerce Jetpack Plus
* Fixed: Return default Post ID where Sequential Order ID is empty
* Fixed: Delete export file after e-mailing via Scheduled export
* Changed: Moved default e-mail receipient and e-mail subject to Export method options
* Changed: Moved Default remote POST to Export method options
* Added: Delete All archives button to Archives screen
* Fixed: Incorrect mime type for some XML exports
* Changed: Archives table uses WP_List_Table class
* Added: Format colunn to Archives list
* Added: Pagination to Archives list
* Added: Number of Archives to Screen Options on Archives list
* Changed: Removed media icon from Archives list
* Fixed: Date filtering of Orders is now WP_Query-based
* Added: Support for exporting WooCommerce Brands
* Added: Support for Featured Image Thumbnail in Product export
* Added: Support for Product Gallery Thumbnail in Product export
* Fixed: Filter Orders by Order Status via CRON
* Fixed: Ordering of Product ID's when exporting Product Variations

= 1.8.8 =
* Fixed: Product Price broken for non-decimal currencies
* Fixed: Total Sales not included in Orders export
* Added: Notice for empty exports
* Changed: Remove Trashed Products from exports

= 1.8.7 =
* Added: Total Quantity export field for Orders
* Added: Filter Orders by Billing Country for Orders
* Added: Filter Orders by Shipping Country for Orders
* Fixed: Filter Orders by Date radio options are selected via jQuery calendar or variable date
* Added: MSRP Pricing to Orders export
* Added: Order Items: RRP to Orders export
* Added: Product Subscription details to Products export
* Fixed: CRON exports and scheduled exports not working with WOO_CD_DEBUG activated
* Added: Filter Orders by Product for Orders
* Added: Download link to attachments on Archives screen
* Added: Filter Products by Product Category to Scheduled Exports
* Added: Filter Products by Product Tag to Scheduled Exports
* Added: Reset Sorting link to Export Fields box
* Added: Custom Order meta support to Subscriptions exports
* Added: Order Item Attribute fields to Orders export

= 1.8.6 =
* Fixed: Prices not formatted to local currency
* Added: Plugin update notification where Visser Labs Updater is not installed
* Added: Filter Orders by Product Brand
* Added: Filter Products by Product Brand
* Added: Notice when WordPress transient fails to store debug log
* Added: Bulk export Orders from the Orders screen
* Added: Filter Products by Product Vendor
* Added: Support for line break as Category Separator in CSV and XLS
* Fixed: Commission Status count not working
* Added: Filter Products by Stock Status in Scheduled Exports
* Fixed: stock_status filter not working in CRON
* Fixed: Extra Product Options support in Orders
* Fixed: Ignore page breaks, section breaks and hidden fields in Gravity Forms integration
* Fixed: Ignore duplicate fields in Gravity Forms integration
* Changed: Order Notes and Customer Notes uses line break instead of category separator
* Changed: Order Items: Product Variation uses line break instead of category separator
* Added: Comment Date to Order Notes and Customer Notes
* Added: Filters to more export fields for Theme/Plugin overrides
* Fixed: Fill Billing: E-mail with User e-mail address if empty in Order
* Fixed: WooCommerce Sequential Order Numbers Pro checking for wrong class on Export Modules

= 1.8.5 =
* Fixed: Include all Order Status for WooCommerce 2.2+ in Orders export
* Fixed: Integration with Custom Billing fields in WooCommerce Checkout Fields Editor
* Fixed: Support for Custom Shipping fields in WooCommerce Checkout Fields Editor
* Fixed: Support for Custom Fields in WooCommerce Checkout Fields Editor
* Changed: Purchase Date to Order Date
* Changed: Purchase Time to Order Time
* Added: Support for Today in Filter Orders by Order Date
* Added: Support for Yesterday in Filter Orders by Order Date
* Added: Support for Current Week in Filter Orders by Order Date
* Added: Support for Last Week in Filter Orders by Order Date
* Fixed: Filter Orders by Order Date for Current Month
* Added: Support for variable filtering of Order Date in scheduled exports
* Added: Heading to Order Checkout field labels for WooCommerce Checkout Manager Pro
* Fixed: Multiple e-mail addresses within Default e-mail recipient
* Fixed: Variation Product Type filter for Products breaking export
* Added: Support for WooCommerce Follow-Up Emails Opt-outs for Customer exports
* Fixed: Filter Order by Order Status in Scheduled Exports not saving
* Added: All default option to Filter Order by Order Date in Scheduled Exports
* Added: Filter Subscriptions by Subscription Product
* Added: Filter Orders by Order Status to CRON engine
* Added: Filter Orders by mulitple Order ID to CRON engine
* Added: Filter Orders by Order Date to CRON engine
* Fixed: Filter Orders by Order Date in scheduled export
* Fixed: Customer Meta fields not being filled in Customer export
* Added: Filter Commissions by Commission Date
* Added: Filter Orders by Order ID
* Fixed: Formatting of Post Status in Commissions export
* Fixed: Default value for Paid Status in Commissions export
* Added: Product SKU to Commissions export
* Added: New tab to Help pulldown on Store Export screen
* Added: Filter Customers by User Role filter
* Added: Line ending formatting option to Settings screen
* Changed: Moved add_action for export options to admin.php
* Fixed: Remove scheduled export from WP CRON if de-activated
* Added: Download as CSV to Actions list on Orders screen
* Added: Download as XML to Actions list on Orders screen
* Added: Download as Excel 2007 (XLS) to Actions list on Orders screen
* Added: Download as CSV to Action list on Edit Order screen
* Added: Download as XML to Action list on Edit Order screen
* Added: Download as Excel 2007 (XLS) to Action list on Edit Order screen
* Added: Compatibility for WC 2.1 for Action list on Orders screen
* Added: Filter Products by Product Status to CRON engine

= 1.8.4 =
* Fixed: Saving Default e-mail recipient on Settings screen
* Fixed: Changing the Scheduled Export interval forces WP_CRON to reload the export
* Fixed: Scheduled Export of Orders filtered by Order Status not working
* Changed: File Download is now Download File URL
* Added: Download File Name support to Products export
* Added: Variation Formatting option to Products export
* Added: Product URL supports Variation URL with attributes
* Fixed: Filter Products by Product Type for Downloadable and Virtual
* Fixed: Count of Downloadable and Virtual within Filter Products by Product Type
* Fixed: Order Status displaying 'publish' in WooCommerce pre-2.2
* Fixed: Formatting of Post Status in Orders export
* Changed: Moved woo_ce_format_product_status to formatting.php
* Changed: Renamed woo_ce_format_product_status to woo_ce_format_post_status
* Added: Disregard column headers in CSV/XLS export option to Settings screen
* Changed: Hide Post Status on Subscriptions export for pre-WooCommerce 2.2 stores
* Fixed: Formatting of Order Status on Subscriptions exports
* Added: Filter Subscriptions by Subscription Status
* Fixed: Multi-line fields breaking CSVTable in Debug Mode
* Fixed: Support for exporting Orders in WooCommerce 2.2.5-2.2.6

= 1.8.3 =
* Fixed: Next Scheduled export in... not accounting for GMT offset
* Added: Order Subtotal field to Orders export
* Added: Shipping Classes to Archives filter list
* Added: In-line links of Settings page to Overview screen
* Added: Check that get_customer_meta_fields method exists within Users export
* Added: Check that get_customer_meta_fields method exists within Subscription export
* Added: Support for WooCommerce Checkout fields in Subscription export
* Added: Support for custom User meta in Subscription export
* Added: Vendor ID to Product Vendors export
* Added: Product Vendor URL to Product Vendors export
* Added: Support for exporting Commissions generated by Product Vendors
* Added: Commission ID to Commissions export
* Added: Commission Date to Commissions export
* Added: Commission Title to Commissions export
* Added: Product ID to Commissions export
* Added: Product Name to Commissions export
* Added: Vendor User ID to Commissions export
* Added: Vendor Username to Commissions export
* Added: Vendor Name to Commissions export
* Added: Commission Amount to Commissions export
* Added: Commission Status to Commissions export
* Added: Post Status to Commissions export
* Added: Support for sorting commissions
* Added: Filter Commissions by Product Vendor
* Added: Filter Commissions by Commission Status
* Fixed: PHP warnings showing for PHP 5.2 installs

= 1.8.2 =
* Added: Order support for Extra Product Options
* Fixed: Custom Product meta not showing up for Order Items
* Fixed: Custom Order meta not showing up for Orders
* Fixed: Detect corrupted Date Format
* Added: Detection of corrupted WordPress options at export time
* Fixed: Gravity Forms error affecting Orders
* Added: Gravity Form label to Order Items export fields
* Added: Export Fields to CRON Settings to control fields
* Added: Export Fields to Scheduled Exports Settings to control fields
* Added: Product Excerpt to Order Items for Orders export
* Added: Product Description to Order Items for Orders export
* Added: Total Sales to Products export
* Fixed: Advanced Google Product Feed not being included in Products export
* Added: Custom User meta to Customers export
* Added: Support for exporting Shipping Classes

= 1.8.1 =
* Changed: Product URL is now External URL
* Added: Product URL is the absolute URL to the Product
* Added: Support for custom User fields
* Fixed: Admin notice not showing for saving custom fields

= 1.8 =
* Added: Export Modules to the Export list

= 1.7.9 =
* Added: Notice for unsupported PHP 5.2
* Fixed: Fatal error due to anonymous functions under PHP 5.2

= 1.7.8 =
* Fixed: Subscription export not working via CRON
* Added: Support for exporting Product Vendors in Products export
* Added: Support for exporting Vendor Commission in Products export
* Added: Support for exporting Product Vendors in Orders export
* Added: Product Vendors export type
* Added: Support for Term ID in Product Vendors export
* Added: Support for Name in Product Vendors export
* Added: Support for Slug in Product Vendors export
* Added: Support for Description in Product Vendors export
* Added: Support for Commission in Product Vendors export
* Added: Support for Vendor Username in Product Vendors export
* Added: Support for Vendor User ID in Product Vendors export
* Added: Support for PayPal E-mail Address in Product Vendors export
* Added: Support for WooCommerce Branding
* Added: E-mail Subject field to Settings screen for Scheduled Exports
* Added: Default notices on Settings screen for export types with no filters
* Added: Default notices on Settings screen for export methods with no filters
* Added: Export to FTP for Scheduled Exports and CRON engine
* Fixed: Fatal error affecting CRON engine (introduced in 1.7.7)
* Added: Dashicons to the Export and Settings screen
* Added: Dashboard widget showing next scheduled export and controls
* Fixed: Warning notice on export of Products
* Added: Order By as XML attribute
* Added: Order as XML attribute
* Added: Limit Volume as XML attribute
* Added: Volume Offset as XML attribute

= 1.7.7 =
* Added: E-mail Address to Subscriptions export
* Changed: Moved User related functions to users.php
* Fixed: Sorting error affecting Products export
* Fixed: Compatibility with PHP 5.3 and above
* Fixed: Compatibility with WooCommerce 2.2+
* Added: Backwards compatibility for Order Status with pre-2.2
* Changed: Moved Brands sorting to brands.php

= 1.7.6 =
* Fixed: Category Image generating PHP warning notices
* Fixed: Default Export Type to Product if not set
* Fixed: Default Date Format for exports if not set
* Changed: Renamed Order Items Types to Order Item Types under Export Options
* Fixed: Ordering of export fields not saving
* Added: Support for filtering Products by Product Type in scheduled exports
* Added: Support for custom date formatting under Settings screen

= 1.7.5 =
* Added: Gravity Form ID to Orders export
* Added: Gravity Form Name to Orders export
* Added: Support for changing the export format of scheduled exports
* Fixed: Display of multiple queued Admin notices
* Fixed: PHP warning on Subscriptions export
* Fixed: Attributes showing Term Slug in Products export
* Fixed: Attributes not including Taxonomy based Terms in Products export
* Fixed: Empty export rows under certain environments in Products export
* Added: Support for filtering Orders by Order Dates for scheduled exports
* Added: Field Editor for all export types
* Added: Sortable export fields

= 1.7.4 =
* Fixed: Limit volume for Users export
* Fixed: Offset for Users export
* Fixed: Pickup Location Plus not working with unique Order Items formatting
* Added: Billing: Street Address 1 to Orders export
* Added: Billing: Street Address 2 to Orders export
* Added: Shipping: Street Address 1 to Orders export
* Added: Shipping: Street Address 2 to Orders export
* Fixed: Validation of $export arguments on CRON export
* Added: Filter Orders by Product Category to Orders export
* Added: Filter Orders by Product Tags to Orders export
* Fixed: XML file export generating surplus HTML
* Added: Basic support for WooCommerce Checkout Add-ons in Orders export
* Added: Support for filtering Orders by Order Status for scheduled exports

= 1.7.3 =
* Added: Support for custom Shipping and Billing fields (Poor Guys Swiss Knife) in Orders export
* Added: Support for exporting as XLS file
* Changed: Moved Default e-mail recipient to General Settings
* Changed: Moved Default remote URL POST to General Settings
* Fixed: Product Addons not showing when using unique export formatting for Orders
* Added: Support for checkbox/multiple answers in Product Addons for Orders export
* Fixed: Empty Settings options on Plugin activation in some stores
* Fixed: Skip generator Customer count for large User stores
* Fixed: Alternative Filter Orders by Customer widget for large User stores
* Fixed: Reduced memory footprint for generating User counts
* Fixed: Reduced memory footprint for generating Order counts
* Fixed: Added detection and correction of incorrect file extensions when exporting
* Fixed: Export support for Currency Switcher in Orders

= 1.7.2 =
* Added: Support for Invoice Date (WooCommerce Print Invoice & Delivery Note) in Orders export
* Added: Support for custom Product Attributes using Custom Product meta dialog
* Fixed: Saving XML files to WordPress Media and Archives screen
* Fixed: Debug mode with XML files
* Fixed: Exporting custom Product Attributes in Product export
* Added: Support for Order Item: Stock in Orders export

= 1.7.1 =
* Added: Support for Invoice Number (WooCommerce PDF Invoices & Packing Slips) in Orders export
* Added: Support for Invoice Date (WooCommerce PDF Invoices & Packing Slips) in Orders export

= 1.7 =
* Added: Subscriptions export type
* Added: Support for Subscription Key in Subscriptions export
* Added: Support for Subscription Status in Subscriptions export
* Added: Support for Subscription Name in Subscriptions export
* Added: Support for User in Subscriptions export
* Added: Support for User ID in Subscriptions export
* Added: Support for Order ID in Subscriptions export
* Added: Support for Order Status in Subscriptions export
* Added: Support for Post Status in Subscriptions export
* Added: Support for Start Date in Subscriptions export
* Added: Support for Expiration in Subscriptions export
* Added: Support for End Date in Subscriptions export
* Added: Support for Trial End Date in Subscriptions export
* Added: Support for Last Payment in Subscriptions export
* Added: Support for Next Payment in Subscriptions export
* Added: Support for Renewals in Subscriptions export
* Added: Support for Product ID in Subscriptions export
* Added: Support for Product SKU in Subscriptions export
* Added: Support for Variation ID in Subscriptions export
* Added: Support for Coupon Code in Subscription export
* Added: Support for Limit Volume in Subscription export

= 1.6.6 =
* Fixed: Admin notices not being displayed
* Fixed: CRON export not e-mailing to Default Recipient by default
* Added: Export filters support for Scheduled Exports
* Added: Filter Orders by Order Status within Scheduled Exports

= 1.6.5 =
* Fixed: Only export published Orders, no longer include trashed Orders
* Added: WordPress filter to override published only Orders export rule
* Changed: Filter Orders by Customer matches export screen UI
* Added: Post Status export field for Orders
* Added: Order count indicators for Filter Orders by Coupon Code
* Added: Order count indiciator for Filter Orders by Order Status
* Added: Export type is remembered between screen refreshes
* Changed: Moved Product Sorting widget to products.php
* Changed: Moved Filter Products by Product Category widget to products.php
* Changed: Moved Filter Products by Product Tag widget to products.php
* Changed: Moved Filter Products by Product Status widget to products.php
* Added: Order Item Variation support for non-taxonomy variants
* Fixed: Order Item Variation empty for some Order exports
* Fixed: Scheduled export email template filename outdated
* Added: Check that scheduled export email template exists
* Added: Customer Message export field for Orders
* Fixed: Customer Notes working for WooCommerce 2.0.20+
* Fixed: Order Notes working for WooCommerce 2.0.20+
* Fixed: Empty Shipping Method and Shipping Method ID in WooCommerce 2.1+

= 1.6.4 =
* Fixed: Check for wc_format_localized_price() in older releases of WooCommerce
* Added: Brands export type
* Added: Support for Brand Name in Brands export
* Added: Support for Brand Description in Brands export
* Added: Support for Brand Slug in Brands export
* Added: Support for Parent ID in Brands export
* Added: Support for Brand Image in Brands export
* Added: Support for sorting options in Brands export
* Fixed: Added checks for 3rd party classes and legacy WooCommerce functions for 2.0.20
* Added: Support for Category Description in Categories export
* Added: Support for Category Image in Categories export
* Added: Support for Display Type in Categories export

= 1.6.3 =
* Added: Brands support to Orders export
* Added: Brands support for Order Items in Orders export
* Fixed: PHP warning notice in Orders export
* Added: Option to filter different Order Items types from Orders export
* Changed: Payment Status to Order Status to reduce confusion
* Fixed: Gravity Forms Order Items support
* Added: Export support for weight of Order Items
* Added: Export support for total weight of Order Items
* Added: Export support for total weight of Order

= 1.6.2 =
* Fixed: Fatal PHP error on first time activation

= 1.6.1 =
* Changed: Removed requirement for basic Store Exporter Plugin
* Added: Coupon Code to Orders export
* Added: Export Users
* Added: Support for User ID in Users export
* Added: Support for Username in Users export
* Added: Support for User Role in Users export
* Added: Support for First Name in Users export
* Added: Support for Last Name in Users export
* Added: Support for Full Name in Users export
* Added: Support for Nickname in Users export
* Added: Support for E-mail in Users export
* Added: Support for Website in Users export
* Added: Support for Date Registered in Users export
* Added: Support for WooCommerce User Profile fields in Users export
* Added: Product Gallery formatting support includes Media URL
* Added: Sorting support for Users export
* Added: Sorting options for Coupons
* Added: Filter Orders by Coupon Codes

= 1.6 =
* Fixed: Empty category separator on Order Items
* Added: Support for exporting Checkout Field Manager
* Added: Support for exporting WooCommerce Sequential Order Numbers (free!)
* Added: Support for exporting WooCommerce Print Invoice & Delivery Note
* Fixed: Support for WooCommerce Checkout Manager (Free!)
* Added: Support for WooCommerce Checkout Manager Pro
* Added: Support for Currency Switcher in Orders export
* Added: Support for Checkout Field Editor

= 1.5.8 =
* Changed: Removed User ID binding for Customers export
* Fixed: Empty exports
* Changed: Better detection of empty exports
* Changed: Better detection of empty data types
* Added: Customer Filter to Export screen
* Added: Filter Customers by Order Status option 
* Added: Using is_wp_error() throughout CPT and Term requests

= 1.5.7 =
* Added: XML Settings section to Settings screen
* Added: Presentation options for attributes within XML export

= 1.5.6 =
* Fixed: Force file extension if removed from the Filename option on Settings screen
* Changed: Reduced memory load by storing $args in $export global

= 1.5.5 =
* Fixed: Fatal error if Store Exporter is not activated

= 1.5.4 =
* Fixed: Fatal error on individual cart item export in XML

= 1.5.3 =
* Fixed: Coupon export to XML issue

= 1.5.2 =
* Added: Support for exporting as XML file
* Added: XML export support for Products
* Added: XML export support for Categories
* Added: XML export support for Tags
* Added: XML export support for Orders
* Added: XML export support for Customers
* Added: XML export support for Coupons
* Changed: Created new functions-xml.php file
* Added: wpsc_cd_generate_xml_filename() to functions-xml.php
* Added: wpsc_cd_generate_xml_header() to functions-xml.php

= 1.5.1 =
* Added: Scheduled export type option
* Fixed: Scheduled export not triggering
* Changed: Remove last scheduled export immediately instead of waiting to run
* Added: Support for overriding field delimiter in CRON exports
* Added: Support for overriding category separator in CRON exports
* Added: Support for overriding BOM support in CRON exports
* Added: Support for overriding encoding in CRON exports
* Added: Support for overriding limit in CRON exports
* Added: Support for overriding offset in CRON exports
* Added: Support for overriding escape formatting in CRON exports

= 1.5 =
* Added: Support for scheduled exports
* Changed: Using WP_Query instead of get_posts for bulk export
* Changed: Moved export function into common space for CRON and scheduled exports
* Added: Support for exporting Local Pickup Plus fields in Orders
* Changed: Removed duplicate Order Items: Type field
* Fixed: Faster processing of Shipping Method and Payment Methods labels

= 1.4.9 =
* Added: Support for exporting Local Pickup Plus fields in Orders
* Added: Support for e-mailing exported CSV via CRON
* Added: Export e-mail template to available WooCommerce e-mails

= 1.4.8 =
* Changed: Moved Max unique Order items option to Settings tab
* Added: Support for CRON triggered exports
* Added: Support for exporting CSV URL via CRON
* Added: Support for exporting file system path of CSV via CRON
* Added: Support for setting ordering of export types via CRON
* Added: Support for setting ASC/DESC sorting of export types via CRON
* Added: Support for saving CSV to WordPress Media via CRON
* Added: Support for authenticating CRON secret key
* Added: uninstall.php
* Changed: Added Plugin activation functions for generating default CRON secret key

= 1.4.7 =
* Fixed: Ordering of Order Items: Product Variations for multiple variations

= 1.4.6 =
* Added: Support for multiple variation within Order Items: Product Variation
* Added: Order Items: Category and Tags to Orders export
* Fixed: Empty Quantity in Order Items: Quantity for unique order items formatting

= 1.4.5 =
* Added: Advanced Custom Fields support for Products export
* Changed: Dropped $wpsc_ce global
* Added: Using Plugin constants
* Added: Cost of Goods integration for Orders export

= 1.4.4 =
* Changed: Removed functions-alternatives.php
* Fixed: Compatibility with legacy WooCommerce 1.6

= 1.4.3 =
* Fixed: Formatting of Order Items: Type for tax
* Added: Memory optimisations for get_posts()
* Changed: Removed functions-alternatives.php
* Added: Custom Product Fields support
* Fixed: Filter Orders by Date option

= 1.4.2 =
* Fixed: PHP error affecting Coupons export
* Fixed: Date Format support for Purchase Date and Expiry Date

= 1.4.1 =
* Added: Cost of Goods integration for Products export
* Added: Per-Product Shipping integration for Products export
* Fixed: Export Orders by User Roles
* Added: Formatting of User Role

= 1.4 =
* Fixed: User Role not displaying within Customers export in WordPress 3.8
* Added: New automatic Plugin updater

= 1.3.9 =
* Added: Payment Gateway ID to Orders export
* Added: Shipping Method ID to Orders export
* Added: Shipping Cost to Orders export
* Added: Checkout IP Address to Orders export
* Added: Checkout Browser Agent to Orders export
* Added: Filter Orders by User Role for Orders export
* Added: User Role to Orders export
* Added: User Role to Customers export

= 1.3.8 =
* Added: Support for Sequential Order Numbers Pro
* Fixed: Fatal error affecting Order exports

= 1.3.7 =
* Changed: Added Docs, Premium Support, Export link to Plugins screen

= 1.3.6 =
* Fixed: Fatal error affecting Order exports

= 1.3.5 =
* Changed: Display detection notices only on Plugins screen
* Added: Display notice when WooCommerce isn't detected
* Fixed: Admin icon on Store Exporter screen
* Added: Export Details widget to Media Library for debug
* Fixed: Fatal error affecting Custom Order Items

= 1.3.4 =
* Fixed: Order Notes on Orders export
* Added: Notice when Store Exporter Plugin is not installed
* Changed: Purchase Date to exclude time
* Added: Total excl. GST
* Added: Purchase Time
* Added: Commenting to each function

= 1.3.3 =
* Changed: Ammended Custom Order Fields note
* Changed: Store Export menu to Export
* Added: Custom Order Items meta support
* Changed: Extended Custom Order meta support
* Added: Help suggestions for Custom Order and Custom Order Item meta
* Added: Product Add Ons integration

= 1.3.2 =
* Added: jQuery Chosen support to Orders Customer dropdown

= 1.3.1 =
* Fixed: Column issue in unique Order Items formatting

= 1.3 =
* Added: New Order date filtering methods
* Added: Order Items formatting
* Added: Order Item Tax Class option
* Added: Order Item Type option
* Added: Formatting of Order Item Tax Class labels
* Added: Formatting of Order Item Type labels
* Fixed: Notices under WP_DEBUG
* Added: N/A value for manual Order creation

= 1.2.8 =
* Fixed: Error notice under WP_DEBUG

= 1.2.7 =
* Added: Escape field formatting option
* Added: Payment Status (number) option
* Added: Filter Orders by Customer option
* Added: Filter Orders by Order Status option

= 1.2.6 =
* Fixed: Order Date to include todays Orders
* Fixed: Removed excess separator at end of each line
* Moved: Order Dates to Order Options
* Added: Order Options section

= 1.2.5 =
* Fixed: Coupons export

= 1.2.4 =
* Changed: Added formatting to Purchase Date
* Fixed: Limit Volume and Offset affecting Orders

= 1.2.3 =
* Fixed: Error on landing page for non-base Plugin users
* Fixed: Link on landing page to Install Plugins

= 1.2.2 =
* Fixed: Customers report
* Added: Total Spent to Customers report
* Added: Completed Orders to Customers report
* Added: Total Orders to Customers report
* Fixed: Customers counter
* Added: Prefix and full Country and State name support

= 1.2.1 =
* Added: Custom Sale meta support

= 1.2 =
* Fixed: Sale export

= 1.1 =
* Added: Admin notice if Store Exporter is not activated
* Added: WordPress Plugin search link to Store Exporter
* Added: Export link to Plugins screen
* Fixed: Duplicate Store Export menu links

= 1.0 =
* Added: First working release of the Plugin

== Disclaimer ==

It is not responsible for any harm or wrong doing this Plugin may cause. Users are fully responsible for their own use. This Plugin is to be used WITHOUT warranty.