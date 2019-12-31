INSERT INTO  `limo66`.`sys_rx_ru_menulist` (
`id` ,
`pid` ,
`name` ,
`code` ,
`sort` ,
`public`
)
VALUES (
NULL ,  '0',  'Верхнее меню',  'topmenu',  '1',  '1'
);


INSERT INTO `sys_rx_ru_menupunkti` (`id`, `pid`, `mid`, `name`, `link`, `sort`, `public`, `page_id`, `target`) VALUES
(31, 0, 2, 'Автопарк', 'avtopark', 0, '1', 10, '_self'),
(32, 0, 2, 'Услуги', 'praisi', 1, '1', 13, '_self'),
(33, 0, 2, 'Отзывы', 'feedback', 6, '1', 19, '_self');
