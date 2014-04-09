CREATE TABLE IF NOT EXISTS `lh_abstract_auto_responder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteaccess` varchar(3) NOT NULL,
  `wait_message` varchar(250) NOT NULL,
  `wait_timeout` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `timeout_message` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `siteaccess_position` (`siteaccess`,`position`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `lh_abstract_email_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `from_name` varchar(150) NOT NULL,
  `from_name_ac` tinyint(4) NOT NULL,
  `from_email` varchar(150) NOT NULL,
  `from_email_ac` tinyint(4) NOT NULL,
  `content` text NOT NULL,
  `subject` varchar(250) NOT NULL,
  `subject_ac` tinyint(4) NOT NULL,
  `reply_to` varchar(150) NOT NULL,
  `reply_to_ac` tinyint(4) NOT NULL,
  `recipient` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO `lh_abstract_email_template` (`id`, `name`, `from_name`, `from_name_ac`, `from_email`, `from_email_ac`, `content`, `subject`, `subject_ac`, `reply_to`, `reply_to_ac`, `recipient`) VALUES
(1, 'Send mail to user', 'Blesta Live Chat', 0, '', 0, 'Dear {user_chat_nick},\r\n\r\n{additional_message}\r\n\r\nLive Support response:\r\n{messages_content}\r\n\r\nSincerely,\r\nLive Support Team\r\n', '{name_surname} has responded to your request', 1, '', 1, ''),
(2, 'Support request from user', 'Blesta Live Chat', 0, '', 0, 'Hello,\r\n\r\nUser request data:\r\nName: {name}\r\nEmail: {email}\r\nPhone: {phone}\r\nDepartment: {department}\r\nCountry: {country}\r\nCity: {city}\r\nIP: {ip}\r\n\r\nMessage:\r\n{message}\r\n\r\nAdditional data, if any:\r\n{additional_data}\r\n\r\nURL of page from which user has send request:\r\n{url_request}\r\n\r\nLink to chat if any:\r\n{prefillchat}\r\n\r\nSincerely,\r\nLive Support Team', 'Support request from user', 0, '', 0, ''),
(3, 'User mail for himself', 'Blesta Live Chat', 0, '', 0, 'Dear {user_chat_nick},\r\n\r\nTranscript:\r\n{messages_content}\r\n\r\nSincerely,\r\nLive Support Team\r\n', 'Chat transcript', 0, '', 0, ''),
(4, 'New chat request', 'Blesta Live Chat', 0, '', 0, 'Hello,\r\n\r\nUser request data:\r\nName: {name}\r\nEmail: {email}\r\nPhone: {phone}\r\nDepartment: {department}\r\nCountry: {country}\r\nCity: {city}\r\nIP: {ip}\r\n\r\nMessage:\r\n{message}\r\n\r\nURL of page from which user has send request:\r\n{url_request}\r\n\r\nClick to accept chat automatically\r\n{url_accept}\r\n\r\nSincerely,\r\nLive Support Team', 'New chat request', 0, '', 0, ''),
(5, 'Chat was closed', 'Blesta Live Chat', 0, '', 0, 'Hello,\r\n\r\n{operator} has closed a chat\r\nName: {name}\r\nEmail: {email}\r\nPhone: {phone}\r\nDepartment: {department}\r\nCountry: {country}\r\nCity: {city}\r\nIP: {ip}\r\n\r\nMessage:\r\n{message}\r\n\r\nAdditional data, if any:\r\n{additional_data}\r\n\r\nURL of page from which user has send request:\r\n{url_request}\r\n\r\nSincerely,\r\nLive Support Team', 'Chat was closed', 0, '', 0, '');

CREATE TABLE IF NOT EXISTS `lh_abstract_proactive_chat_invitation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteaccess` varchar(10) NOT NULL,
  `time_on_site` int(11) NOT NULL,
  `pageviews` int(11) NOT NULL,
  `message` text NOT NULL,
  `executed_times` int(11) NOT NULL,
  `hide_after_ntimes` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `wait_message` varchar(250) NOT NULL,
  `timeout_message` varchar(250) NOT NULL,
  `referrer` varchar(250) NOT NULL,
  `wait_timeout` int(11) NOT NULL,
  `show_random_operator` int(11) NOT NULL,
  `operator_name` varchar(100) NOT NULL,
  `position` int(11) NOT NULL,
  `identifier` varchar(50) NOT NULL,
  `requires_email` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `time_on_site_pageviews_siteaccess_position` (`time_on_site`,`pageviews`,`siteaccess`,`identifier`,`position`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `lh_canned_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msg` text NOT NULL,
  `position` int(11) NOT NULL,
  `delay` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `lh_canned_msg` (`id`, `msg`, `position`, `delay`) VALUES
(1, 'Hello,\r\n\r\nHow can I help you?', 0, 0);

CREATE TABLE IF NOT EXISTS `lh_chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nick` varchar(50) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `status_sub` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `hash` varchar(40) NOT NULL,
  `referrer` text NOT NULL,
  `session_referrer` text NOT NULL,
  `chat_variables` text NOT NULL,
  `remarks` text NOT NULL,
  `ip` varchar(100) NOT NULL,
  `dep_id` int(11) NOT NULL,
  `user_status` int(11) NOT NULL DEFAULT '0',
  `support_informed` int(11) NOT NULL DEFAULT '0',
  `email` varchar(100) NOT NULL,
  `country_code` varchar(100) NOT NULL,
  `country_name` varchar(100) NOT NULL,
  `user_typing` int(11) NOT NULL,
  `user_typing_txt` varchar(50) NOT NULL,
  `operator_typing` int(11) NOT NULL,
  `operator_typing_id` int(11) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `has_unread_messages` int(11) NOT NULL,
  `last_user_msg_time` int(11) NOT NULL,
  `fbst` tinyint(1) NOT NULL,
  `online_user_id` int(11) NOT NULL,
  `last_msg_id` int(11) NOT NULL,
  `additional_data` varchar(250) NOT NULL,
  `timeout_message` varchar(250) NOT NULL,
  `lat` varchar(10) NOT NULL,
  `lon` varchar(10) NOT NULL,
  `city` varchar(100) NOT NULL,
  `mail_send` int(11) NOT NULL,
  `wait_time` int(11) NOT NULL,
  `wait_timeout` int(11) NOT NULL,
  `wait_timeout_send` int(11) NOT NULL,
  `chat_duration` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  `chat_initiator` int(11) NOT NULL,
  `transfer_timeout_ts` int(11) NOT NULL,
  `transfer_timeout_ac` int(11) NOT NULL,
  `transfer_if_na` int(11) NOT NULL,
  `na_cb_executed` int(11) NOT NULL,
  `nc_cb_executed` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `user_id` (`user_id`),
  KEY `online_user_id` (`online_user_id`),
  KEY `dep_id` (`dep_id`),
  KEY `has_unread_messages_dep_id_id` (`has_unread_messages`,`dep_id`,`id`),
  KEY `status_dep_id_id` (`status`,`dep_id`,`id`),
  KEY `status_dep_id_priority_id` (`status`,`dep_id`,`priority`,`id`),
  KEY `status_priority_id` (`status`,`priority`,`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

CREATE TABLE IF NOT EXISTS `lh_chatbox` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `chat_id` int(11) NOT NULL,
  `active` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `identifier` (`identifier`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

INSERT INTO `lh_chatbox` (`id`, `identifier`, `name`, `chat_id`, `active`) VALUES
(2, 'default', 'Chatbox', 5, 1);

CREATE TABLE IF NOT EXISTS `lh_chat_accept` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chat_id` int(11) NOT NULL,
  `hash` varchar(50) NOT NULL,
  `ctime` int(11) NOT NULL,
  `wused` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `hash` (`hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `lh_chat_archive_range` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `range_from` int(11) NOT NULL,
  `range_to` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `lh_chat_blocked_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `datets` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `lh_chat_config` (
  `identifier` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `explain` varchar(250) NOT NULL,
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`identifier`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `lh_chat_config` (`identifier`, `value`, `type`, `explain`, `hidden`) VALUES
('tracked_users_cleanup', '160', 0, 'How many days keep records of online users.', 0),
('list_online_operators', '1', 0, 'List online operators.', 0),
('voting_days_limit', '7', 0, 'How many days voting widget should not be expanded after last show', 0),
('track_online_visitors', '1', 0, 'Enable online site visitors tracking', 0),
('pro_active_invite', '0', 0, 'Is pro active chat invitation active. Online users tracking also has to be enabled', 0),
('customer_company_name', 'Blesta Live Chat', 0, 'Your company name - visible in bottom left corner', 0),
('customer_site_url', 'http://dev.on.gweb.pt', 0, 'Your site URL address', 0),
('smtp_data', 'a:5:{s:4:"host";s:0:"";s:4:"port";s:2:"25";s:8:"use_smtp";i:0;s:8:"username";s:0:"";s:8:"password";s:0:"";}', 0, 'SMTP configuration', 1),
('chatbox_data', 'a:6:{i:0;b:0;s:20:"chatbox_auto_enabled";i:0;s:19:"chatbox_secret_hash";s:9:"9xhd2i9lu";s:20:"chatbox_default_name";s:7:"Chatbox";s:17:"chatbox_msg_limit";i:50;s:22:"chatbox_default_opname";s:7:"Manager";}', 0, 'Chatbox configuration', 1),
('start_chat_data', 'a:27:{i:0;b:0;s:21:"name_visible_in_popup";b:1;s:27:"name_visible_in_page_widget";b:1;s:19:"name_require_option";s:8:"required";s:22:"email_visible_in_popup";b:1;s:28:"email_visible_in_page_widget";b:1;s:20:"email_require_option";s:8:"required";s:24:"message_visible_in_popup";b:1;s:30:"message_visible_in_page_widget";b:1;s:22:"message_require_option";s:8:"required";s:22:"phone_visible_in_popup";b:1;s:28:"phone_visible_in_page_widget";b:1;s:20:"phone_require_option";s:8:"optional";s:21:"force_leave_a_message";b:1;s:29:"offline_name_visible_in_popup";b:1;s:35:"offline_name_visible_in_page_widget";b:1;s:27:"offline_name_require_option";s:8:"required";s:30:"offline_phone_visible_in_popup";b:1;s:36:"offline_phone_visible_in_page_widget";b:1;s:28:"offline_phone_require_option";s:8:"optional";s:32:"offline_message_visible_in_popup";b:1;s:38:"offline_message_visible_in_page_widget";b:1;s:30:"offline_message_require_option";s:8:"required";s:20:"tos_visible_in_popup";b:0;s:26:"tos_visible_in_page_widget";b:0;s:28:"offline_tos_visible_in_popup";b:0;s:34:"offline_tos_visible_in_page_widget";b:0;}', 0, '', 1),
('application_name', 'a:8:{s:2:"en";s:16:"Blesta Live Chat";s:2:"es";s:16:"Blesta Live Chat";s:2:"pt";s:16:"Blesta Live Chat";s:2:"de";s:16:"Blesta Live Chat";s:2:"ru";s:16:"Blesta Live Chat";s:2:"it";s:16:"Blesta Live Chat";s:2:"fr";s:16:"Blesta Live Chat";s:10:"site_admin";s:16:"Blesta Live Chat";}', 1, 'Support application name, visible in browser title.', 0),
('track_footprint', '1', 0, 'Track users footprint. For this also online visitors tracking should be enabled', 0),
('pro_active_limitation', '-1', 0, 'Pro active chats invitations limitation based on pending chats, (-1) do not limit, (0,1,n+1) number of pending chats can be for invitation to be shown.', 0),
('pro_active_show_if_offline', '0', 0, 'Should invitation logic be executed if there is no online operators', 0),
('export_hash', 'ipdbo614k', 0, 'Chats export secret hash', 0),
('message_seen_timeout', '24', 0, 'Proactive message timeout in hours. After how many hours proactive chat mesasge should be shown again.', 0),
('reopen_chat_enabled', '1', 0, 'Reopen chat functionality enabled', 0),
('ignorable_ip', '', 0, 'Which ip should be ignored in online users list, separate by comma', 0),
('run_departments_workflow', '0', 0, 'Should cronjob run departments transfer workflow, even if user leaves a chat', 0),
('geo_location_data', 'a:3:{s:4:"zoom";i:4;s:3:"lat";s:7:"49.8211";s:3:"lng";s:7:"11.7835";}', 0, '', 1),
('xmp_data', 'a:9:{i:0;b:0;s:4:"host";s:15:"talk.google.com";s:6:"server";s:9:"gmail.com";s:8:"resource";s:6:"xmpphp";s:4:"port";s:4:"5222";s:7:"use_xmp";i:0;s:8:"username";s:0:"";s:8:"password";s:0:"";s:11:"xmp_message";s:77:"You have a new chat request\r\n{messages}\r\nClick to accept a chat\r\n{url_accept}";}', 0, 'XMP data', 1),
('run_unaswered_chat_workflow', '0', 0, 'Should cronjob run unanswered chats workflow and execute unaswered chats callback, 0 - no, any other number bigger than 0 is a minits how long chat have to be not accepted before executing callback.', 0),
('disable_popup_restore', '1', 0, 'Disable option in widget to open new window. Restore icon will be hidden', 0),
('accept_tos_link', '#', 0, 'Change to your site Terms of Service', 0),
('file_configuration', 'a:7:{i:0;b:0;s:5:"ft_op";s:43:"gif|jpe?g|png|zip|rar|xls|doc|docx|xlsx|pdf";s:5:"ft_us";s:26:"gif|jpe?g|png|doc|docx|pdf";s:6:"fs_max";i:2048;s:18:"active_user_upload";b:0;s:16:"active_op_upload";b:1;s:19:"active_admin_upload";b:1;}', 0, 'Files configuration item', 1),
('accept_chat_link_timeout', '300', 0, 'How many seconds chat accept link is valid. Set 0 to force login all the time manually.', 0),
('session_captcha', '0', 0, 'Use session captcha. LHC have to be installed on the same domain or subdomain.', 0),
('sync_sound_settings', 'a:15:{i:0;b:0;s:12:"repeat_sound";i:1;s:18:"repeat_sound_delay";i:5;s:10:"show_alert";b:1;s:22:"new_chat_sound_enabled";b:1;s:31:"new_message_sound_admin_enabled";b:1;s:30:"new_message_sound_user_enabled";b:1;s:14:"online_timeout";d:300;s:22:"check_for_operator_msg";d:10;s:21:"back_office_sinterval";d:10;s:22:"chat_message_sinterval";d:3.5;s:20:"long_polling_enabled";b:0;s:30:"polling_chat_message_sinterval";d:1.5;s:29:"polling_back_office_sinterval";d:5;s:18:"connection_timeout";i:30;}', 0, '', 1),
('sound_invitation', '1', 0, 'Play sound on invitation to chat.', 0),
('explicit_http_mode', '', 0, 'Please enter explicit http mode. Either http: or https:, do not forget : at the end.', 0),
('track_domain', 'dev.on.gweb.pt', 0, 'Set your domain to enable user tracking across different domain subdomains.', 0),
('max_message_length', '500', 0, 'Maximum message length in characters', 0),
('geo_data', 'a:3:{i:0;b:0;s:21:"geo_detection_enabled";i:1;s:22:"geo_service_identifier";s:9:"freegeoip";}', 0, '', 1);

CREATE TABLE IF NOT EXISTS `lh_chat_file` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `upload_name` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `extension` varchar(255) NOT NULL,
  `chat_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_id` (`chat_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

CREATE TABLE IF NOT EXISTS `lh_chat_online_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vid` varchar(50) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `current_page` text NOT NULL,
  `page_title` varchar(250) NOT NULL,
  `referrer` text NOT NULL,
  `chat_id` int(11) NOT NULL,
  `invitation_seen_count` int(11) NOT NULL,
  `invitation_id` int(11) NOT NULL,
  `last_visit` int(11) NOT NULL,
  `first_visit` int(11) NOT NULL,
  `total_visits` int(11) NOT NULL,
  `pages_count` int(11) NOT NULL,
  `tt_pages_count` int(11) NOT NULL,
  `invitation_count` int(11) NOT NULL,
  `dep_id` int(11) NOT NULL,
  `user_agent` varchar(250) NOT NULL,
  `user_country_code` varchar(50) NOT NULL,
  `user_country_name` varchar(50) NOT NULL,
  `operator_message` varchar(250) NOT NULL,
  `operator_user_proactive` varchar(100) NOT NULL,
  `operator_user_id` int(11) NOT NULL,
  `message_seen` int(11) NOT NULL,
  `message_seen_ts` int(11) NOT NULL,
  `lat` varchar(10) NOT NULL,
  `lon` varchar(10) NOT NULL,
  `city` varchar(100) NOT NULL,
  `time_on_site` int(11) NOT NULL,
  `tt_time_on_site` int(11) NOT NULL,
  `requires_email` int(11) NOT NULL,
  `identifier` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `vid` (`vid`),
  KEY `dep_id` (`dep_id`),
  KEY `last_visit_dep_id` (`last_visit`,`dep_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

CREATE TABLE IF NOT EXISTS `lh_chat_online_user_footprint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chat_id` int(11) NOT NULL,
  `online_user_id` int(11) NOT NULL,
  `page` varchar(250) NOT NULL,
  `vtime` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_id_vtime` (`chat_id`,`vtime`),
  KEY `online_user_id` (`online_user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

CREATE TABLE IF NOT EXISTS `lh_departament` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `xmpp_recipients` varchar(250) NOT NULL,
  `xmpp_group_recipients` varchar(250) NOT NULL,
  `priority` int(11) NOT NULL,
  `department_transfer_id` int(11) NOT NULL,
  `transfer_timeout` int(11) NOT NULL,
  `disabled` int(11) NOT NULL,
  `delay_lm` int(11) NOT NULL,
  `identifier` varchar(50) NOT NULL,
  `mod` tinyint(1) NOT NULL,
  `tud` tinyint(1) NOT NULL,
  `wed` tinyint(1) NOT NULL,
  `thd` tinyint(1) NOT NULL,
  `frd` tinyint(1) NOT NULL,
  `sad` tinyint(1) NOT NULL,
  `sud` tinyint(1) NOT NULL,
  `start_hour` int(2) NOT NULL,
  `end_hour` int(2) NOT NULL,
  `inform_close` int(11) NOT NULL,
  `inform_options` varchar(250) NOT NULL,
  `online_hours_active` tinyint(1) NOT NULL,
  `inform_delay` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `identifier` (`identifier`),
  KEY `disabled` (`disabled`),
  KEY `oha_sh_eh` (`online_hours_active`,`start_hour`,`end_hour`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `lh_departament` (`id`, `name`, `email`, `xmpp_recipients`, `xmpp_group_recipients`, `priority`, `department_transfer_id`, `transfer_timeout`, `disabled`, `delay_lm`, `identifier`, `mod`, `tud`, `wed`, `thd`, `frd`, `sad`, `sud`, `start_hour`, `end_hour`, `inform_close`, `inform_options`, `online_hours_active`, `inform_delay`) VALUES
(1, 'Comercial Dep.', 'comercial@localhost.com', '', '', 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'a:0:{}', 0, 0);

CREATE TABLE IF NOT EXISTS `lh_forgotpasswordhash` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `hash` varchar(40) NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `lh_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

INSERT INTO `lh_group` (`id`, `name`) VALUES
(1, 'Administrators'),
(2, 'Operators');

CREATE TABLE IF NOT EXISTS `lh_grouprole` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`role_id`,`group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

INSERT INTO `lh_grouprole` (`id`, `group_id`, `role_id`) VALUES
(1, 1, 1),
(2, 2, 2);

CREATE TABLE IF NOT EXISTS `lh_groupuser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`),
  KEY `group_id_2` (`group_id`,`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO `lh_groupuser` (`id`, `group_id`, `user_id`) VALUES
(4, 2, 1),
(5, 1, 1);

CREATE TABLE IF NOT EXISTS `lh_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msg` text NOT NULL,
  `time` int(11) NOT NULL,
  `chat_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `name_support` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_id_id` (`chat_id`,`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=40 ;

CREATE TABLE IF NOT EXISTS `lh_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

INSERT INTO `lh_role` (`id`, `name`) VALUES
(1, 'Administrators'),
(2, 'Operators');

CREATE TABLE IF NOT EXISTS `lh_rolefunction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `module` varchar(100) NOT NULL,
  `function` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

INSERT INTO `lh_rolefunction` (`id`, `role_id`, `module`, `function`) VALUES
(1, 1, '*', '*'),
(2, 2, 'lhuser', 'selfedit'),
(3, 2, 'lhuser', 'changeonlinestatus'),
(4, 2, 'lhchat', 'use'),
(5, 2, 'lhchat', 'chattabschrome'),
(6, 2, 'lhchat', 'singlechatwindow'),
(7, 2, 'lhchat', 'allowopenremotechat'),
(8, 2, 'lhchat', 'allowchattabs'),
(9, 2, 'lhchat', 'use_onlineusers'),
(10, 2, 'lhfront', 'use'),
(11, 2, 'lhsystem', 'use'),
(12, 2, 'lhchat', 'allowblockusers'),
(13, 2, 'lhsystem', 'generatejs'),
(14, 2, 'lhsystem', 'changelanguage'),
(15, 2, 'lhchat', 'allowtransfer'),
(16, 2, 'lhchat', 'administratecannedmsg'),
(19, 2, 'lhchatbox', 'manage_chatbox'),
(20, 2, 'lhxml', '*'),
(21, 2, 'lhfile', 'use_operator'),
(22, 2, 'lhfile', 'file_delete_chat');

CREATE TABLE IF NOT EXISTS `lh_transfer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chat_id` int(11) NOT NULL,
  `dep_id` int(11) NOT NULL,
  `transfer_user_id` int(11) NOT NULL,
  `from_dep_id` int(11) NOT NULL,
  `transfer_to_user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dep_id` (`dep_id`),
  KEY `transfer_user_id_dep_id` (`transfer_user_id`,`dep_id`),
  KEY `transfer_to_user_id` (`transfer_to_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `lh_userdep` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `dep_id` int(11) NOT NULL,
  `last_activity` int(11) NOT NULL,
  `hide_online` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `last_activity_hide_online_dep_id` (`last_activity`,`hide_online`,`dep_id`),
  KEY `dep_id` (`dep_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

INSERT INTO `lh_userdep` (`id`, `user_id`, `dep_id`, `last_activity`, `hide_online`) VALUES
(3, 1, 0, 0, 0);

CREATE TABLE IF NOT EXISTS `lh_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(40) NOT NULL,
  `password` varchar(40) NOT NULL,
  `email` varchar(100) NOT NULL,
  `time_zone` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `filepath` varchar(200) NOT NULL,
  `filename` varchar(200) NOT NULL,
  `xmpp_username` varchar(200) NOT NULL,
  `skype` varchar(50) NOT NULL,
  `disabled` tinyint(4) NOT NULL,
  `hide_online` tinyint(1) NOT NULL,
  `all_departments` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `hide_online` (`hide_online`),
  KEY `email` (`email`),
  KEY `xmpp_username` (`xmpp_username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

INSERT INTO `lh_users` (`id`, `username`, `password`, `email`, `time_zone`, `name`, `surname`, `filepath`, `filename`, `xmpp_username`, `skype`, `disabled`, `hide_online`, `all_departments`) VALUES
(1, 'admin', '766205dec9dfab23b04a2301b150a20514aaecae', 'admin@localhost.com', '', 'Blesta', 'Admin', '', '', '', '', 0, 0, 1);

CREATE TABLE IF NOT EXISTS `lh_users_remember` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

CREATE TABLE IF NOT EXISTS `lh_users_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `identifier` varchar(50) NOT NULL,
  `value` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`identifier`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

INSERT INTO `lh_users_setting` (`id`, `user_id`, `identifier`, `value`) VALUES
(1, 1, 'user_language', 'en_EN'),
(2, 1, 'enable_pending_list', '1'),
(3, 1, 'enable_active_list', '1'),
(4, 1, 'enable_close_list', '1'),
(5, 1, 'enable_unread_list', '1'),
(6, 1, 'new_chat_sound', '1'),
(7, 1, 'chat_message', '1'),
(8, 2, 'user_language', 'en_EN'),
(9, 2, 'enable_pending_list', '1'),
(10, 2, 'enable_active_list', '1'),
(11, 2, 'enable_close_list', '0'),
(12, 2, 'enable_unread_list', '1'),
(13, 2, 'new_chat_sound', '1'),
(14, 2, 'chat_message', '1');

CREATE TABLE IF NOT EXISTS `lh_users_setting_option` (
  `identifier` varchar(50) NOT NULL,
  `class` varchar(50) NOT NULL,
  `attribute` varchar(40) NOT NULL,
  PRIMARY KEY (`identifier`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `lh_users_setting_option` (`identifier`, `class`, `attribute`) VALUES
('chat_message', '', ''),
('new_chat_sound', '', ''),
('enable_pending_list', '', ''),
('enable_active_list', '', ''),
('enable_close_list', '', ''),
('enable_unread_list', '', '');