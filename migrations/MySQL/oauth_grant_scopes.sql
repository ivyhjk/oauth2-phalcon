DROP TABLE IF EXISTS `oauth_grant_scopes`;
CREATE TABLE IF NOT EXISTS `oauth_grant_scopes` (
  `id` int(11) NOT NULL,
  `grant_id` varchar(40) NOT NULL,
  `scope_id` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `oauth_grant_scopes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grant_id` (`grant_id`),
  ADD KEY `scope_id` (`scope_id`);

ALTER TABLE `oauth_grant_scopes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
