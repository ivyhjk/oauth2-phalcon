DROP TABLE IF EXISTS `oauth_client_scopes`;
CREATE TABLE IF NOT EXISTS `oauth_client_scopes` (
  `id` int(11) NOT NULL,
  `client_id` varchar(100) NOT NULL,
  `scope_id` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `oauth_client_scopes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `scope_id` (`scope_id`);

ALTER TABLE `oauth_client_scopes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
