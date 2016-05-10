DROP TABLE IF EXISTS `oauth_grants`;
CREATE TABLE IF NOT EXISTS `oauth_grants` (
  `id` varchar(40) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `modified_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `oauth_grants`
  ADD PRIMARY KEY (`id`);
