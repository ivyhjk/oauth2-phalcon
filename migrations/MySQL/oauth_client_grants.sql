DROP TABLE IF EXISTS `oauth_client_grants`;
CREATE TABLE IF NOT EXISTS `oauth_client_grants` (
  `id` int(11) NOT NULL,
  `client_id` varchar(100) NOT NULL,
  `grant_id` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `oauth_client_grants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `grant_id` (`grant_id`);

ALTER TABLE `oauth_client_grants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;



