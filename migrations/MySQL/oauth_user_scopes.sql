DROP TABLE IF EXISTS `oauth_user_scopes`;
CREATE TABLE IF NOT EXISTS `oauth_user_scopes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `scope_id` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `oauth_user_scopes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `scope_id` (`scope_id`);

ALTER TABLE `oauth_user_scopes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
