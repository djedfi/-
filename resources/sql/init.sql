INSERT INTO `db_autos`.`companies` (`name`) VALUES ('AA Motors');

INSERT INTO `branches` (`id`, `company_id`, `name`, `address_p`, `address_s`, `telephone`, `cellphone`, `city`, `created_at`, `updated_at`) VALUES
(1, 1, 'AA Motors Los Angeles', 'Direccion Principal', '\'\'', '2290229058', '7290229058', 'LA', '2022-01-13 23:25:13', '2022-01-13 23:25:13');

INSERT INTO `db_autos`.`options_app` (`name`, `description`, `path_option`, `group_option`, `icono`) VALUES ('Users', 'CRUD Users', 'user', '1','fa-id-card-o');
INSERT INTO `db_autos`.`options_app` (`name`, `description`, `path_option`, `group_option`, `icono`) VALUES ('Makes', 'CRUD Makes', 'make', '1','fa-cubes');
INSERT INTO `db_autos`.`options_app` (`name`, `description`, `path_option`, `group_option`, `icono`) VALUES ('Models', 'CRUD Model', 'model', '1','fa-scribd');
INSERT INTO `db_autos`.`options_app` (`name`, `description`, `path_option`, `group_option`, `icono`) VALUES ('Trims', 'CRUD Trim', 'trim', '1','fa-cogs');
INSERT INTO `db_autos`.`options_app` (`name`, `description`, `path_option`, `group_option`, `icono`) VALUES ('Styles', 'CRUD Style', 'style', '1','fa-ship');
INSERT INTO `db_autos`.`options_app` (`name`, `description`, `path_option`, `group_option`, `icono`) VALUES ('Config', 'CRUD Config', 'config_app', '1','fa-cog');
INSERT INTO `db_autos`.`options_app` (`name`, `description`, `path_option`, `group_option`, `icono`) VALUES ('Cars', 'CRUD Cars', 'car', '2','fa-car');
INSERT INTO `db_autos`.`options_app` (`name`, `description`, `path_option`, `group_option`, `icono`) VALUES ('Customer', 'CRUD Customer', 'customer', '2','fa-users');
INSERT INTO `db_autos`.`options_app` (`name`, `description`, `path_option`, `group_option`, `icono`) VALUES ('Car Loans', 'CRUD Sales', 'loan', '2','fa-university');

INSERT INTO `db_autos`.`states` (`name`) VALUES ('California');
INSERT INTO `db_autos`.`states` (`name`) VALUES ('Florida');
INSERT INTO `db_autos`.`states` (`name`) VALUES ('Alaska');

INSERT INTO `users` (`id`,`branch_id`, `first_name`, `last_name`, `email`, `cargo`,`email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES(1,1, 'Edwin', 'Figueroa', 'edwin@gmail.com', 'Gerente',NULL, '$2y$10$gRSW26vzbFjm9gZJjTNY8.HdZhaWj0zhiZ5K3n4HUe/l6ZQp063yS', NULL, '2022-01-13 23:12:34', '2022-01-13 23:12:34');

INSERT INTO `user_optios`(`user_id`, `option_id`) values (1,1);
INSERT INTO `user_optios`(`user_id`, `option_id`) values (1,2);
INSERT INTO `user_optios`(`user_id`, `option_id`) values (1,3);
INSERT INTO `user_optios`(`user_id`, `option_id`) values (1,4);
INSERT INTO `user_optios`(`user_id`, `option_id`) values (1,5);
INSERT INTO `user_optios`(`user_id`, `option_id`) values (1,6);
INSERT INTO `user_optios`(`user_id`, `option_id`) values (1,7);
INSERT INTO `user_optios`(`user_id`, `option_id`) values (1,8);
INSERT INTO `user_optios`(`user_id`, `option_id`) values (1,9);

insert into `configs` (`branch_id`,`long_term_default`,`porc_downpay_default`,`int_rate_default`,`latefee_default`,`dayslate_default`,`taxes_rate_default`) values(1,4,20,5.9,50,10,13.5);
