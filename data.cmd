
cd C:\xampp\mysql\bin

mysql -u root 

create database erp5;
use erp5;
CREATE TABLE `system` (
  `id` int(10) UNSIGNED NOT NULL,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `system` (`id`, `key`, `value`) VALUES
(1, 'db_version', '3.6.7'),
(2, 'default_business_active_status', '1'),
(3, 'woocommerce_version', '2.5'),
(4, 'essentials_version', '2.2'),
(5, 'productcatalogue_version', '0.5'),
(6, 'project_version', '1.6'),
(7, 'repair_version', '0.8'),
(8, 'superadmin_version', '2.0'),
(9, 'app_currency_id', '136'),
(10, 'invoice_business_name', 'OR version'),
(11, 'invoice_business_landmark', 'Landmark'),
(12, 'invoice_business_zip', 'Zip'),
(13, 'invoice_business_state', 'State'),
(14, 'invoice_business_city', 'City'),
(15, 'invoice_business_country', 'Country'),
(16, 'email', 'superadmin@example.com'),
(17, 'package_expiry_alert_days', '5'),
(18, 'enable_business_based_username', '0'),
(19, 'superadmin_register_tc', NULL),
(20, 'welcome_email_subject', NULL),
(21, 'welcome_email_body', NULL),
(22, 'additional_js', NULL),
(23, 'additional_css', NULL),
(24, 'superadmin_enable_register_tc', '0'),
(25, 'allow_email_settings_to_businesses', '0'),
(26, 'enable_new_business_registration_notification', '1'),
(27, 'enable_new_subscription_notification', '1'),
(28, 'enable_welcome_email', '1'),
(29, 'manufacturing_version', '2.0');
ALTER TABLE `system`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `system`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;
cmd /k 
