-- 1. Сначала заполняем таблицу users (нет внешних ключе
INSERT INTO users (id, username, display_name, role, bio, avatar_media_id, is_active, external_id, created_at) VALUES
(1, 'admin', 'Администратор', 'admin', 'Главный администратор сайта', NULL, 1, 'ext_001', '2024-01-01 10:00:00'),
(2, 'john_doe', 'Иван Петров', 'author', 'Технический писатель, специалист по веб-разработке', NULL, 1, 'ext_002', '2024-01-05 12:30:00'),
(3, 'jane_smith', 'Мария Сидорова', 'editor', 'Редактор контента, журналист', NULL, 1, 'ext_003', '2024-01-10 09:15:00'),
(4, 'bob_writer', 'Алексей Иванов', 'author', 'Блогер, пишет о технологиях', NULL, 1, 'ext_004', '2024-02-01 14:20:00');

-- 2. Заполняем media (зависит от users)
INSERT INTO media (id, uuid, filename, original_name, file_path, mime_type, file_size, width, height, alt_text, uploaded_by, created_at) VALUES
(1, '550e8400-e29b-41d4-a716-446655440001', 'avatar1.jpg', 'profile.jpg', '/uploads/2024/01/avatar1.jpg', 'image/jpeg', 45678, 200, 200, 'Аватар администратора', 1, '2024-01-01 10:05:00'),
(2, '550e8400-e29b-41d4-a716-446655440002', 'post_image1.jpg', 'cms_architecture.jpg', '/uploads/2024/01/post_image1.jpg', 'image/jpeg', 156789, 1200, 800, 'Архитектура CMS системы', 2, '2024-01-15 11:00:00'),
(3, '550e8400-e29b-41d4-a716-446655440003', 'post_image2.jpg', 'database_design.jpg', '/uploads/2024/01/post_image2.jpg', 'image/jpeg', 234567, 1200, 800, 'Дизайн базы данных', 3, '2024-01-20 14:30:00'),
(4, '550e8400-e29b-41d4-a716-446655440004', 'banner.jpg', 'main_banner.jpg', '/uploads/2024/02/banner.jpg', 'image/jpeg', 345678, 1920, 600, 'Главный баннер сайта', 1, '2024-02-01 09:00:00'),
(5, '550e8400-e29b-41d4-a716-446655440005', 'tutorial.pdf', 'mysql_guide.pdf', '/uploads/2024/02/tutorial.pdf', 'application/pdf', 1234567, NULL, NULL, 'Руководство по MySQL', 2, '2024-02-05 16:45:00');

-- Обновляем аватары пользователей
UPDATE users SET avatar_media_id = 1 WHERE id = 1;

-- 3. Заполняем categories (сама на себя ссылается)
INSERT INTO categories (id, name, slug, description, parent_id, seo_title, seo_description, sort_order, created_at) VALUES
(1, 'Технологии', 'technologies', 'Все о современных технологиях', NULL, 'Технологии - статьи и новости', 'Последние новости в мире технологий', 1, '2024-01-01 10:00:00'),
(2, 'Веб-разработка', 'web-development', 'Статьи о веб-разработке', 1, 'Веб-разработка', 'Уроки и статьи по веб-разработке', 1, '2024-01-01 10:05:00'),
(3, 'Базы данных', 'databases', 'Все о базах данных', 1, 'Базы данных', 'Статьи о проектировании и оптимизации баз данных', 2, '2024-01-01 10:10:00'),
(4, 'Программирование', 'programming', 'Языки программирования', 1, 'Программирование', 'Изучаем языки программирования', 3, '2024-01-01 10:15:00'),
(5, 'Новости', 'news', 'Последние новости', NULL, 'Новости сайта', 'Актуальные новости', 2, '2024-01-01 10:20:00'),
(6, 'MySQL', 'mysql', 'Все о СУБД MySQL', 3, 'MySQL базы данных', 'Уроки и руководства по MySQL', 1, '2024-01-05 11:00:00'),
(7, 'PHP', 'php', 'Программирование на PHP', 4, 'PHP программирование', 'Уроки PHP для начинающих и профессионалов', 1, '2024-01-05 11:05:00');

-- 4. Заполняем tags
INSERT INTO tags (id, name, slug, created_at) VALUES
(1, 'CMS', 'cms', '2024-01-01 10:00:00'),
(2, 'MySQL', 'mysql', '2024-01-01 10:05:00'),
(3, 'PHP', 'php', '2024-01-01 10:10:00'),
(4, 'JavaScript', 'javascript', '2024-01-01 10:15:00'),
(5, 'SQL', 'sql', '2024-01-01 10:20:00'),
(6, 'веб-разработка', 'web-dev', '2024-01-05 11:00:00'),
(7, 'tutorial', 'tutorial', '2024-01-05 11:05:00'),
(8, 'beginner', 'beginner', '2024-01-05 11:10:00'),
(9, 'database', 'database', '2024-01-10 12:00:00'),
(10, 'optimization', 'optimization', '2024-01-10 12:05:00');

-- 5. Заполняем posts (зависит от users, categories, media)
INSERT INTO posts (id, title, slug, content, excerpt, status, published_at, created_at, updated_at, author_id, category_id, featured_image_id, meta_title, meta_description, views_count, is_featured) VALUES
(1, 'Как создать CMS с нуля', 'kak-sozdat-cms-s-nulya', '<h1>Создание CMS системы</h1><p>В этой статье мы рассмотрим основные принципы создания CMS...</p><h2>Архитектура системы</h2><p>Первым шагом является проектирование базы данных...</p>', 'Подробное руководство по созданию собственной CMS системы с нуля. Рассмотрим архитектуру, базу данных и основные компоненты.', 'published', '2024-01-15 10:00:00', '2024-01-10 09:00:00', '2024-01-15 10:00:00', 2, 2, 2, 'Как создать CMS с нуля | Полное руководство', 'Научитесь создавать CMS систему с нуля. Пошаговое руководство по проектированию и разработке.', 1250, 1),
(2, 'Оптимизация MySQL запросов', 'optimizaciya-mysql-zaprosov', '<h1>Оптимизация запросов MySQL</h1><p>Эффективная работа с базами данных...</p><h2>Индексы</h2><p>Правильное использование индексов...</p>', 'Советы и рекомендации по оптимизации SQL запросов в MySQL для повышения производительности.', 'published', '2024-01-20 12:00:00', '2024-01-18 14:00:00', '2024-01-20 12:00:00', 3, 6, 3, 'Оптимизация MySQL запросов', 'Узнайте как оптимизировать MySQL запросы для ускорения работы вашего приложения.', 890, 1),
(3, 'Основы PHP для начинающих', 'osnovy-php-dlya-nachinayushchikh', '<h1>PHP с нуля</h1><p>Начинаем изучать PHP...</p><h2>Синтаксис</h2><p>Основные конструкции языка...</p>', 'Полное руководство по изучению PHP для начинающих. Базовый синтаксис и первые программы.', 'published', '2024-02-01 09:00:00', '2024-01-25 10:00:00', '2024-02-01 09:00:00', 4, 7, NULL, 'PHP для начинающих', 'Изучите основы PHP с нуля. Пошаговое руководство для новичков.', 2340, 1),
(4, 'Проектирование баз данных', 'proektirovanie-baz-dannykh', '<h1>Проектирование БД</h1><p>Принципы проектирования...</p>', 'Руководство по проектированию реляционных баз данных. Нормализация и связи.', 'published', '2024-02-05 14:00:00', '2024-02-01 11:00:00', '2024-02-05 14:00:00', 2, 3, NULL, 'Проектирование баз данных', 'Изучите принципы проектирования реляционных баз данных.', 670, 0),
(5, 'Новые возможности 2024', 'novye-vozmozhnosti-2024', '<h1>Что нового в 2024</h1><p>Обзор новых технологий...</p>', 'Обзор новых технологий и трендов 2024 года в веб-разработке.', 'draft', NULL, '2024-02-10 16:00:00', '2024-02-10 16:00:00', 3, 5, NULL, 'Новые возможности 2024', NULL, 0, 0);

-- 6. Заполняем post_tags (связь многие-ко-многим)
INSERT INTO post_tags (post_id, tag_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 6), (1, 7),
(2, 2), (2, 5), (2, 9), (2, 10),
(3, 3), (3, 6), (3, 7), (3, 8),
(4, 2), (4, 5), (4, 9),
(5, 1), (5, 4), (5, 6);

-- 7. Заполняем pages (зависит от users, media)
INSERT INTO pages (id, title, slug, content, template_name, status, author_id, featured_image_id, meta_title, meta_description, created_at, updated_at) VALUES
(1, 'О нас', 'about', '<h1>О нашем проекте</h1><p>Мы создаем качественный контент о веб-разработке и технологиях.</p><h2>Наша миссия</h2><p>Помочь разработчикам изучать новые технологии...</p>', 'default', 'published', 1, NULL, 'О нас - Наш проект', 'Узнайте больше о нашем проекте и команде.', '2024-01-01 10:00:00', '2024-01-15 12:00:00'),
(2, 'Контакты', 'contacts', '<h1>Контакты</h1><p>Свяжитесь с нами:</p><ul><li>Email: info@example.com</li><li>Телефон: +7 (999) 123-45-67</li></ul>', 'default', 'published', 1, NULL, 'Контакты', 'Свяжитесь с нами', '2024-01-01 10:05:00', '2024-01-01 10:05:00'),
(3, 'Политика конфиденциальности', 'privacy-policy', '<h1>Политика конфиденциальности</h1><p>Мы уважаем вашу конфиденциальность...</p>', 'full-width', 'published', 1, NULL, 'Политика конфиденциальности', 'Политика обработки персональных данных', '2024-01-01 10:10:00', '2024-01-01 10:10:00'),
(4, 'Главная страница', 'home', '<h1>Добро пожаловать</h1><p>Лучшие статьи о веб-разработке</p>', 'homepage', 'published', 1, 4, 'Главная - Веб-разработка и CMS', 'Статьи и руководства по веб-разработке, CMS и базам данных', '2024-01-01 09:00:00', '2024-02-01 10:00:00');

-- 8. Заполняем menus
INSERT INTO menus (id, name, slug, description, created_at) VALUES
(1, 'Главное меню', 'main-menu', 'Основное меню навигации', '2024-01-01 09:00:00'),
(2, 'Меню в подвале', 'footer-menu', 'Меню для подвала сайта', '2024-01-01 09:05:00'),
(3, 'Боковое меню', 'sidebar-menu', 'Дополнительное меню', '2024-01-01 09:10:00');

-- 9. Заполняем menu_items (зависит от menus, сама на себя)
INSERT INTO menu_items (id, menu_id, parent_id, title, url, link_type, link_target_id, target_attr, class_name, sort_order) VALUES
-- Главное меню
(1, 1, NULL, 'Главная', '/', 'page', 4, '_self', 'nav-home', 1),
(2, 1, NULL, 'Статьи', NULL, 'custom', NULL, '_self', '', 2),
(3, 1, 2, 'Технологии', '/category/technologies', 'category', 1, '_self', '', 1),
(4, 1, 2, 'Веб-разработка', '/category/web-development', 'category', 2, '_self', '', 2),
(5, 1, 2, 'Базы данных', '/category/databases', 'category', 3, '_self', '', 3),
(6, 1, NULL, 'О нас', '/about', 'page', 1, '_self', '', 3),
(7, 1, NULL, 'Контакты', '/contacts', 'page', 2, '_self', '', 4),
(8, 1, NULL, 'Блог', '/blog', 'custom', NULL, '_self', 'highlight', 5),
-- Меню в подвале
(9, 2, NULL, 'О нас', '/about', 'page', 1, '_self', '', 1),
(10, 2, NULL, 'Политика конфиденциальности', '/privacy-policy', 'page', 3, '_self', '', 2),
(11, 2, NULL, 'Контакты', '/contacts', 'page', 2, '_self', '', 3),
(12, 2, NULL, 'Мы в соцсетях', 'https://example.com', 'custom', NULL, '_blank', 'social-link', 4),
-- Боковое меню
(13, 3, NULL, 'Популярные статьи', '/popular', 'custom', NULL, '_self', 'sidebar-title', 1),
(14, 3, NULL, 'Категории', NULL, 'custom', NULL, '_self', '', 2),
(15, 3, NULL, 'MySQL', '/category/mysql', 'category', 6, '_self', '', 3),
(16, 3, NULL, 'PHP', '/category/php', 'category', 7, '_self', '', 4);

-- 10. Заполняем comments (зависит от posts, сама на себя)
INSERT INTO comments (id, post_id, author_name, author_email, author_website, content, ip_address, user_agent, status, parent_id, created_at) VALUES
(1, 1, 'Петр Сидоров', 'petr@example.com', 'https://petr-blog.ru', 'Отличная статья! Очень помогла разобраться с архитектурой CMS.', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'approved', NULL, '2024-01-16 14:30:00'),
(2, 1, 'Анна К.', 'anna@mail.ru', NULL, 'Спасибо за подробное руководство. Есть ли продолжение?', '192.168.1.105', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)', 'approved', NULL, '2024-01-17 09:15:00'),
(3, 1, 'admin', 'admin@example.com', NULL, 'Да, продолжение будет на следующей неделе!', '192.168.1.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'approved', 2, '2024-01-17 10:00:00'),
(4, 2, 'DBA_Master', 'dba@example.com', 'https://dbamaster.ru', 'Хорошие советы по оптимизации. Добавлю еще про использование EXPLAIN.', '192.168.1.110', 'Mozilla/5.0 (X11; Linux x86_64)', 'approved', NULL, '2024-01-21 11:20:00'),
(5, 2, 'Новичок', 'newbie@example.com', NULL, 'А можно подробнее про индексы?', '192.168.1.115', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0)', 'approved', NULL, '2024-01-22 16:45:00'),
(6, 3, 'PHP_Developer', 'phpdev@example.com', NULL, 'Отличный материал для начинающих!', '192.168.1.120', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'approved', NULL, '2024-02-02 10:30:00'),
(7, 1, 'Спаммер', 'spam@spam.com', 'https://spam.com', 'Купите у нас ссылки!', '192.168.1.200', 'Mozilla/5.0', 'spam', NULL, '2024-01-18 03:00:00'),
(8, 4, 'Архитектор', 'arch@example.com', NULL, 'Хорошо бы добавить примеры ER-диаграмм.', '192.168.1.125', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'pending', NULL, '2024-02-06 09:00:00');
set sql_safe_updates = 1;
delete from settings;
select * from settings;
-- 11. Заполняем settings
INSERT INTO settings (id, setting_key, setting_value, setting_type, group_name, updated_at) VALUES
(1, 'site_name', 'CMS Portal', 'string', 'general', '2024-01-01 09:00:00'),
(2, 'site_description', 'Портал о веб-разработке и технологиях', 'string', 'general', '2024-01-01 09:00:00'),
(3, 'site_email', 'info@cmsportal.ru', 'string', 'general', '2024-01-01 09:00:00'),
(4, 'posts_per_page', '10', 'number', 'reading', '2024-01-01 09:00:00'),
(5, 'allow_comments', '1', 'boolean', 'discussion', '2024-01-01 09:00:00'),
(6, 'moderate_comments', '1', 'boolean', 'discussion', '2024-01-01 09:00:00'),
(7, 'site_logo', '/uploads/2024/01/logo.png', 'string', 'general', '2024-01-01 09:00:00'),
(8, 'favicon', '/uploads/2024/01/favicon.ico', 'string', 'general', '2024-01-01 09:00:00'),
(9, 'google_analytics', 'UA-XXXXXXXXX-X', 'string', 'seo', '2024-01-01 09:00:00'),
(10, 'meta_keywords', 'CMS, веб-разработка, MySQL, PHP, базы данных', 'string', 'seo', '2024-01-01 09:00:00'),
(11, 'maintenance_mode', '0', 'boolean', 'general', '2024-01-01 09:00:00'),
(12, 'social_vk', 'https://vk.com/cmsportal', 'string', 'social', '2024-01-01 09:00:00'),
(13, 'social_telegram', 'https://t.me/cmsportal', 'string', 'social', '2024-01-01 09:00:00'),
(14, 'social_youtube', 'https://youtube.com/cmsportal', 'string', 'social', '2024-01-01 09:00:00'),
(15, 'max_upload_size', '5242880', 'number', 'media', '2024-01-01 09:00:00'),
(16, 'allowed_extensions', 'jpg,jpeg,png,gif,pdf,doc,docx', 'string', 'media', '2024-01-01 09:00:00');