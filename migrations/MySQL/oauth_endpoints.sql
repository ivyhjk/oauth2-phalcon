DROP TABLE IF EXISTS `oauth_endpoints`;
CREATE TABLE IF NOT EXISTS `oauth_endpoints` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `redirect_uri` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `modified_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `oauth_endpoints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

ALTER TABLE `oauth_endpoints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
