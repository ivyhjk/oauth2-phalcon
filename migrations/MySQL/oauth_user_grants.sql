DROP TABLE IF EXISTS `oauth_user_grants`;
CREATE TABLE IF NOT EXISTS `oauth_user_grants` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `grant_id` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `oauth_user_grants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `grant_id` (`grant_id`);

ALTER TABLE `oauth_user_grants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
