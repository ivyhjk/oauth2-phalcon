DROP TABLE IF EXISTS `oauth_user_clients`;
CREATE TABLE IF NOT EXISTS `oauth_user_clients` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `client_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `oauth_user_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `client_id` (`client_id`);

ALTER TABLE `oauth_user_clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
