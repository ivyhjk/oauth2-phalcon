DROP TABLE IF EXISTS `oauth_access_token_scopes`;
CREATE TABLE IF NOT EXISTS `oauth_access_token_scopes` (
  `id` int(11) NOT NULL,
  `access_token_id` varchar(100) NOT NULL,
  `scope_id` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `oauth_access_token_scopes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `access_token_id` (`access_token_id`),
  ADD KEY `scope_id` (`scope_id`);

ALTER TABLE `oauth_access_token_scopes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

