-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 19, 2025 at 04:53 PM
-- Server version: 10.3.22-MariaDB-log
-- PHP Version: 8.1.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vh1u23185_ck`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('super_admin','user_admin','moderator') COLLATE utf8mb4_unicode_ci NOT NULL,
  `mfa_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `role`, `mfa_secret`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'i', 'i@o.ru', 'i', 'user_admin', '123123', '2025-07-18 20:59:20', '2025-07-18 20:59:54', '2025-07-19 10:52:49'),
(2, '1', 'a@a.ru', '1', 'super_admin', '123123', '2025-07-19 12:51:12', '2025-07-19 12:51:32', '2025-07-19 12:51:55');

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `answer_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`id`, `question_id`, `answer_text`, `is_correct`, `order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Python', 1, 0, '2025-07-19 15:13:18', '2025-07-19 15:13:18'),
(2, 1, 'JavaScript', 0, 1, '2025-07-19 15:13:18', '2025-07-19 15:13:18'),
(3, 1, 'PHP', 0, 2, '2025-07-19 15:13:18', '2025-07-19 15:13:18'),
(4, 1, 'C++', 0, 3, '2025-07-19 15:13:18', '2025-07-19 15:13:18');

-- --------------------------------------------------------

--
-- Table structure for table `assigned_tests`
--

CREATE TABLE `assigned_tests` (
  `id` int(11) NOT NULL,
  `listener_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `due_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assigned_tests`
--

INSERT INTO `assigned_tests` (`id`, `listener_id`, `test_id`, `due_date`, `created_at`) VALUES
(1, 1, 1, '2025-07-31', '2025-07-19 18:27:13'),
(2, 873, 1, '2025-07-31', '2025-07-19 18:31:14'),
(3, 873, 1, '2025-07-20', '2025-07-19 19:04:53'),
(4, 873, 1, '2025-07-19', '2025-07-19 19:18:30');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(2, 'Документы под формой', 'forms-doc', '2023-07-26 14:51:39', '2023-07-26 14:51:39');

-- --------------------------------------------------------

--
-- Table structure for table `directions`
--

CREATE TABLE `directions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `program` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `qualification` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_audience` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int(11) NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `for_it` tinyint(1) DEFAULT NULL,
  `short_name` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `directions`
--

INSERT INTO `directions` (`id`, `name`, `program`, `qualification`, `target_audience`, `order`, `is_published`, `created_at`, `updated_at`, `for_it`, `short_name`) VALUES
(1, 'Системы искусственного интеллекта (для IT)', '1. Введение в СИИ\r\n2. Инжиниринг  интеллектуальных систем\r\n3. Методы и средства анализа больших объемов данных\r\n4. Искусственные нейронные сети в системах ИИ\r\n5. Искусственный  интеллект в творчестве и играх\r\n6. Обработка  естественного языка\r\n7. Компьютерное  зрение\r\n8. Искусственный  интеллект в синтезе проектных решений', 'Специалист по большим данным', 'Набор из ВПИ КТИ СФ ВТФ ФПИК ФАСТИВ ФЭВТ ФЭУ ВГСПУ', 1, 1, '2023-07-25 15:19:21', '2024-08-06 10:27:55', 1, 'СИИ'),
(2, 'Технологии программирования и СУБД', '1. Основы программирования и алгоритмизации на языке Python\r\n2. Системы управления базами данных', '«Программист»', 'Набор из ВПИ КТИ ФАСТиВ ФАТ ФТКМ ХТФ ИАиС ВА МВД ВолгГМУ ВолГАУ ВГАФК', 9, 1, '2023-07-25 15:21:19', '2024-08-16 08:39:50', 0, 'СУБД'),
(3, 'Разработка информационных систем на платформе «1С: Предприятие 8.3» (для IT)', '1. Информационные базы ERP-систем на платформе «1С: Предприятие 8.3»\r\n2. Проекты комплексной автоматизации на базе 1С:ERP Управление предприятием', '«Специалист по информационным системам»', 'Набор из ФАСТиВ, ФЭВТ, ФЭУ, ФАГР ИАиС', 3, 1, '2023-07-25 15:48:32', '2024-08-06 10:29:19', 1, 'РИС'),
(4, 'Программирование и разработка IТ-продуктов на платформе «1С: Предприятие 8.3»', '1. Конфигурации и модули системы 1С: Предприятие\r\n2. ERP-система на платформе «1С: Предприятие 8.3»', '«Специалист по информационным системам»', 'Набор из ВТФ, ФПИК, ФАТ, ФТКМ, ФТПП, ФЭВТ, ФЭУ, ХТФ, ИАиС, ВА МВД, ВГАФК, ВолгГМУ, ВолГАУ', 6, 1, '2023-07-26 14:40:00', '2024-08-16 08:39:59', 0, 'ПиР'),
(5, 'Прикладное программирование в строительстве и архитектуре', '1.Основы программирования и алгоритмизации на языке Python\r\n2. Прикладное программирование в строительстве и архитектуре', '«Специалист в сфере информационного моделирования в строительстве»', 'Набор из ИАиС, ВолГАУ', 5, 1, '2023-07-26 14:40:29', '2024-08-16 08:40:07', 0, 'ППСA'),
(6, 'Прикладное программирование в цифровом производстве', '1. Основы программирования и алгоритмизации на языке Python\r\n2. Прикладное программирование в цифровом производстве', '«Программист»', 'Набор из ФАСТиВ, ФАТ, ФТКМ, ХТФ, ВПИ, ВолгГМУ', 7, 1, '2023-07-26 14:40:52', '2024-08-16 08:39:54', 0, 'ППЦП'),
(7, 'Разработка веб-приложений и сервисов', '1. Основы программирования и алгоритмизации на языке Python\r\n2. Разработка веб приложений и сервисов', 'Разработчик Web и мультимедийных приложений', 'Набор из ВПИ СФ ВТФ ФПИК ФТПП ФЭВТ ФЭУ ВГСПУ', 8, 1, '2023-07-26 14:41:22', '2024-08-16 08:39:53', 0, 'РВП'),
(8, 'Разработка игровых и XR приложений (для IT)', '1. Введение в разработку игр и XR-приложений (GXR)\r\n2. Проектирование GXR\r\n3. Игровые движки\r\n4. Компьютерная графика для разработки GXR\r\n5. Технологии расширенной реальности\r\n6. Искусственный интеллект в разработке GXR\r\n7. Отраслевые GXR решения. Сборка и тестирование GXR проекта.', 'Разработчик игровых и XR приложений', 'Набор ФЭВТ на конкурсной основе.', 2, 1, '2024-07-18 12:18:54', '2024-08-06 10:28:38', 1, 'ИВР'),
(9, 'Информационная безопасность (для IT)', '1. Организационно-правовые основы технической защиты конфиденциальной информации (КИ)\r\n2. Средства и системы обработки информации\r\n3. Способы и средства технической защиты КИ от утечки по техническим каналам\r\n4. Меры и средства технической защиты КИ от несанкционированного доступа\r\n5. Техническая защита КИ от специальных воздействий\r\n6. Организация защиты КИ на объектах информатизации\r\n7. Аттестация объектов информатизации по требованиям безопасности информации\r\n8. Контроль состояния технической защиты кКИ', 'Специалист по защите информации', 'Набор из ФЭВТ, ФАСТиВ на конкурсной основе. Выпускные курсы или наличие диплома о ВО.', 4, 1, '2024-08-16 08:39:36', '2024-08-17 10:30:45', 1, 'ИБ');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `category_id`, `name`, `filename`, `is_published`, `created_at`, `updated_at`) VALUES
(16, 2, 'СОГЛАСИЕ на обработку персональных данных', 'soglasie-na-obrabotku-personalnyx-dannyx.docx', 1, '2024-08-07 07:50:20', '2024-08-07 07:50:20'),
(17, 2, 'СОГЛАСИЕ Биометрия', 'soglasie-biometriia.docx', 1, '2024-08-07 07:52:33', '2024-08-07 07:52:33'),
(18, 2, 'Заявление_2024-2025-ЦК', 'zaiavlenie-2024-2025-ck.docx', 1, '2024-08-23 05:34:44', '2024-08-23 05:34:44');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `listeners`
--

CREATE TABLE `listeners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fio` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `education_level` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `university` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `faculty` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direction_id` bigint(20) UNSIGNED NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `documents_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_foreign` tinyint(1) DEFAULT NULL,
  `specialization` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time_edu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_year` int(11) DEFAULT NULL,
  `diplom_num` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diplom_place` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diplom_napr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diplom_year` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_blocked` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `listeners`
--

INSERT INTO `listeners` (`id`, `fio`, `email`, `education_level`, `university`, `faculty`, `group_name`, `direction_id`, `phone`, `image`, `created_at`, `updated_at`, `documents_number`, `is_foreign`, `specialization`, `birthday`, `country`, `time_edu`, `start_year`, `diplom_num`, `diplom_place`, `diplom_napr`, `diplom_year`, `vk`, `is_blocked`) VALUES
(873, 'Тестова Юлия Александровна', 'yulya.98@mail.ru', 'Специалитет', 'ВолгГМУ', 'Институт общественного здоровья', '202', 7, '8(000) 000-00-00', NULL, '2024-07-10 07:20:00', '2024-07-10 07:20:00', '168-748-253 24', 0, '37.05.01 Клиническая психология', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempt_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(9, '2023_07_08_203216_create_directions_table', 2),
(10, '2023_07_10_125705_create_categories_table', 2),
(11, '2023_07_10_130259_create_documents_table', 3),
(12, '2023_07_11_121752_create_listeners_table', 3),
(13, '2023_07_22_191701_create_news_table', 3),
(14, '2023_07_28_081802_add_qualification_to_directions_table', 4),
(15, '2023_08_02_183944_add_target_audience_to_directions_table', 5),
(16, '2023_08_02_185448_add_university_to_listeners_table', 6);

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `publish_date` date DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `slug`, `url`, `publish_date`, `image_path`, `created_at`, `updated_at`) VALUES
(18, 'Команда ВолгГТУ заняла 2-е место в Марафоне цифровых кафедр', 'komanda_volggtu_zanyala_2_e_mesto_v_marafone_tsifrovykh_kafedr', 'https://www.vstu.ru/university/press-center/news/dostizheniya/komanda_volggtu_zanyala_2_e_mesto_v_marafone_tsifrovykh_kafedr/', '2023-03-23', '/upload/iblock/dac/dacdd5722820dd11d3a5d6daf067bd72.jpg', '2023-07-31 09:38:50', '2023-07-31 09:38:50'),
(19, 'На заседании ученого совета ВолгГТУ говорили об итогах рейтинга ППС, факультетов и кафедр и о развитии дополнительного профобразования', 'na_zasedanii_uchenogo_soveta_volggtu_govorili_ob_itogakh_reytinga_pps_fakultetov_i_kafedr_i_o_razvit', 'https://www.vstu.ru/university/press-center/news/universitetskaya_zhizn/na_zasedanii_uchenogo_soveta_volggtu_govorili_ob_itogakh_reytinga_pps_fakultetov_i_kafedr_i_o_razvit/', '2023-03-01', '/upload/iblock/da8/da898839a8f1979faf4ae67ecdaad218.JPG', '2023-07-31 09:39:06', '2023-07-31 09:39:06'),
(20, 'ГТРК «Волгоград-ТРВ». Цифровое будущее: волгоградских студентов обучают работе с искусственным интеллектом ', 'gtrk_volgograd_trv_tsifrovoe_budushchee_volgogradskikh_studentov_obuchayut_rabote_s_iskusstvennym_in', 'https://www.vstu.ru/university/press-center/news/nauka-i-innovatsii/gtrk_volgograd_trv_tsifrovoe_budushchee_volgogradskikh_studentov_obuchayut_rabote_s_iskusstvennym_in/', '2022-10-11', '/upload/iblock/335/335bac14c6b4b666c4dd094906410c7d.jpg', '2023-07-31 09:39:13', '2023-07-31 09:39:13'),
(21, 'Студенты ВолгГТУ стали участниками «Таврида.АРТ»', 'studenty_volggtu_stali_uchastnikami_tavrida_art', 'https://www.vstu.ru/university/press-center/news/obshchestvo/studenty_volggtu_stali_uchastnikami_tavrida_art/', '2023-08-04', '/upload/iblock/de4/de485bd2aa40a704c5ecddea43f02bf2.jpg', '2023-08-11 08:13:55', '2023-08-11 08:13:55'),
(22, 'ГТРК «Волгоград-ТРВ». Программа «Человек ученый». Искусственный интеллект', 'gtrk_volgograd_trv_programma_chelovek_uchenyy_iskusstvennyy_intellekt', 'https://www.vstu.ru/university/press-center/news/nauka-i-innovatsii/gtrk_volgograd_trv_programma_chelovek_uchenyy_iskusstvennyy_intellekt/', '2023-08-01', '/upload/iblock/251/2513c50894e916acad7732a03695f9ac.jpg', '2023-08-11 08:14:47', '2023-08-11 08:14:47'),
(23, 'Набор на программы Цифровой кафедры ', 'nabor_na_programmy_tsifrovoy_kafedry_', 'https://www.vstu.ru/university/press-center/news/obrazovanie/nabor_na_programmy_tsifrovoy_kafedry_/', '2023-08-21', '/upload/iblock/56d/56d9146b55eb65ede0c7227fb1ce902f.jpg', '2023-08-24 10:52:10', '2023-08-24 10:52:10'),
(24, 'ГТРК «Волгоград-ТРВ»: Программа «Интервью». Искусственный интеллект', 'gtrk_volgograd_trv_programma_intervyu_iskusstvennyy_intellekt', 'https://www.vstu.ru/university/press-center/news/obshchestvo/gtrk_volgograd_trv_programma_intervyu_iskusstvennyy_intellekt/', '2023-08-18', '/upload/iblock/3f5/3f59d5f120afa63d5c44d5f94b06b2a6.jpg', '2023-08-24 10:52:49', '2023-08-24 10:52:49'),
(25, ' НК «Роснефть»: Более 200 студентов пройдут профессиональную подготовку по программам «Сибинтек» на Цифровой кафедре ВолгГТУ', 'nk_rosneft_bolee_200_studentov_proydut_professionalnuyu_podgotovku_po_programmam_sibintek_na_tsifrov', 'https://www.vstu.ru/university/press-center/news/tsifrovaya_kafedra/nk_rosneft_bolee_200_studentov_proydut_professionalnuyu_podgotovku_po_programmam_sibintek_na_tsifrov/', '2023-09-06', '/upload/iblock/6e0/6e0721e7bf508e500162c25ebabc740c.jpg', '2023-09-06 05:04:41', '2023-09-06 05:04:41'),
(27, 'РИАЦ: Волгоградский вуз обучит две сотни слушателей информационным технологиям', 'riats_volgogradskiy_vuz_obuchit_dve_sotni_slushateley_informatsionnym_tekhnologiyam', 'https://www.vstu.ru/university/press-center/news/tsifrovaya_kafedra/riats_volgogradskiy_vuz_obuchit_dve_sotni_slushateley_informatsionnym_tekhnologiyam/', '2023-09-08', '/upload/iblock/cd1/cd1d5c664ca69f0bc18979e4dde16b6d.jpg', '2023-09-18 06:45:33', '2023-09-18 06:45:33'),
(28, 'Сетевое издание «Городские вести»: Более 200 студентов пройдут профессиональную подготовку на Цифровой кафедре ВолгГТУ', 'setevoe_izdanie_gorodskie_vesti_bolee_200_studentov_proydut_professionalnuyu_podgotovku_na_tsifrovoy', 'https://www.vstu.ru/university/press-center/news/tsifrovaya_kafedra/setevoe_izdanie_gorodskie_vesti_bolee_200_studentov_proydut_professionalnuyu_podgotovku_na_tsifrovoy/', '2023-09-11', '/upload/iblock/3b2/3b28aebf37fb1f6e3f0272f8550b8bf4.jpg', '2023-09-18 06:45:39', '2023-09-18 06:45:39'),
(29, 'Сетевое издание «Городские вести»: В Волгоградском техническом университете открыли «Цифровую кафедру»', 'setevoe_izdanie_gorodskie_vesti_v_volgogradskom_tekhnicheskom_universitete_otkryli_tsifrovuyu_kafedr', 'https://www.vstu.ru/university/press-center/news/tsifrovaya_kafedra/setevoe_izdanie_gorodskie_vesti_v_volgogradskom_tekhnicheskom_universitete_otkryli_tsifrovuyu_kafedr/', '2023-10-03', '/upload/iblock/321/3216ecf7d32ef630d481cedcf49ab8aa.jpg', '2023-10-11 06:14:30', '2023-10-11 06:14:30'),
(30, 'В ВолгГТУ состоялось открытие новой аудитории «Цифровой кафедры»', 'v_volggtu_sostoyalos_otkrytie_novoy_auditorii_tsifrovoy_kafedry', 'https://www.vstu.ru/university/press-center/news/tsifrovaya_kafedra/v_volggtu_sostoyalos_otkrytie_novoy_auditorii_tsifrovoy_kafedry/', '2023-09-29', '/upload/iblock/95d/95dd095802dd26ecb9cd15d91a00d8ee.jpg', '2023-10-11 06:15:34', '2023-10-11 06:15:34'),
(31, 'ГТРК «Волгоград-ТРВ». Программа «Человек ученый». Информационные технологии в урбанистике', 'gtrk_volgograd_trv_programma_chelovek_uchenyy_informatsionnye_tekhnologii_v_urbanistike', 'https://www.vstu.ru/university/press-center/news/nauka-i-innovatsii/gtrk_volgograd_trv_programma_chelovek_uchenyy_informatsionnye_tekhnologii_v_urbanistike/', '2023-11-21', '/upload/iblock/74b/74b8db11ba710c69141350f1fe4b706b.jpg', '2023-11-23 05:46:44', '2023-11-23 05:46:44'),
(32, 'Еженедельная газета научного сообщества «Поиск»: Волшебная сила данных. Искусственный интеллект грозит одолеть даже проблемы ЖКХ', 'ezhenedelnaya_gazeta_nauchnogo_soobshchestva_poisk_volshebnaya_sila_dannykh_iskusstvennyy_intellekt_', 'https://www.vstu.ru/university/press-center/news/nauka-i-innovatsii/ezhenedelnaya_gazeta_nauchnogo_soobshchestva_poisk_volshebnaya_sila_dannykh_iskusstvennyy_intellekt_/', '2023-11-17', '/upload/iblock/008/0089ba9906563d86c2eaa17a63edd0a7.jpg', '2023-11-23 05:47:48', '2023-11-23 05:47:48'),
(33, 'В техническом университете прошел II Открытый командный чемпионат ВолгГТУ по программированию', 'v_tekhnicheskom_universitete_proshel_ii_otkrytyy_komandnyy_chempionat_volggtu_po_programmirovaniyu', 'https://www.vstu.ru/university/press-center/news/universitetskaya_zhizn/v_tekhnicheskom_universitete_proshel_ii_otkrytyy_komandnyy_chempionat_volggtu_po_programmirovaniyu/', '2023-11-13', '/upload/iblock/a6e/a6e79a50a01dd9fd76ef3102f8631edd.jpg', '2023-11-23 05:51:15', '2023-11-23 05:51:15'),
(34, 'Команда ВолгГТУ вышла в полуфинал Чемпионата мира по программированию', 'komanda_volggtu_vyshla_v_polufinal_chempionata_mira_po_programmirovaniyu', 'https://www.vstu.ru/university/press-center/news/dostizheniya/komanda_volggtu_vyshla_v_polufinal_chempionata_mira_po_programmirovaniyu/', '2023-11-08', '/upload/iblock/0db/0db9103d8d9d9c9f66032c803c0b91c3.jpg', '2023-11-23 05:53:03', '2023-11-23 05:53:03'),
(35, 'Студенты Волгоградского государственного технического университета были приглашены на 10-дневную стажировку в Sitronics Group', 'studenty_volgogradskogo_gosudarstvennogo_tekhnicheskogo_universiteta_byli_priglasheny_na_10_dnevnuyu', 'https://www.vstu.ru/university/press-center/news/dostizheniya/studenty_volgogradskogo_gosudarstvennogo_tekhnicheskogo_universiteta_byli_priglasheny_na_10_dnevnuyu/', '2023-11-08', '/upload/iblock/0c6/0c61cbefdff37e5c3a3fccbc650b8c92.jpg', '2023-11-23 05:54:54', '2023-11-23 05:54:54'),
(36, 'ГТРК «Волгоград-ТРВ». Программа «Реальное время». Искусственный интеллект в деле', 'gtrk_volgograd_trv_programma_realnoe_vremya_iskusstvennyy_intellekt_v_dele', 'https://www.vstu.ru/university/press-center/publications/nauka/gtrk_volgograd_trv_programma_realnoe_vremya_iskusstvennyy_intellekt_v_dele/', '2023-11-22', '/upload/iblock/f71/f71f0d7d12a8140dcf70d5039f1660a8.jpg', '2023-11-29 03:39:46', '2023-11-29 03:39:46'),
(37, 'В ВПИ (филиале) ВолгГТУ подвели итоги внутривузовской студенческой олимпиады по трехмерному моделированию', 'v_vpi_filiale_volggtu_podveli_itogi_vnutrivuzovskoy_studencheskoy_olimpiady_po_trekhmernomu_modeliro', 'https://www.vstu.ru/university/press-center/news/obrazovanie/v_vpi_filiale_volggtu_podveli_itogi_vnutrivuzovskoy_studencheskoy_olimpiady_po_trekhmernomu_modeliro/', '2024-01-11', '/upload/iblock/928/928ffea939a193d2e01e16e99d61b087.jpg', '2024-01-11 07:20:55', '2024-01-11 07:20:55'),
(38, 'ГТРК «Волгоград-ТРВ». Программа «Общественная экспертиза». Искусственный интеллект в работе?', 'gtrk_volgograd_trv_programma_obshchestvennaya_ekspertiza_iskusstvennyy_intellekt_v_rabote', 'https://www.vstu.ru/university/press-center/publications/obshchestvo/gtrk_volgograd_trv_programma_obshchestvennaya_ekspertiza_iskusstvennyy_intellekt_v_rabote/', '2024-01-31', '/upload/iblock/a53/a53ecf0542794427f2990275ca4a6471.jpg', '2024-01-31 03:30:53', '2024-01-31 03:30:53'),
(39, 'Выпускник Цифровой кафедры Алексей Вальков и руководитель Цифровой кафедры ВолгГТУ профессор Алла Григорьевна Кравец - в Иннополисе!', 'vypusknik_tsifrovoy_kafedry_aleksey_valkov_i_rukovoditel_tsifrovoy_kafedry_volggtu_professor_alla_gr', 'https://www.vstu.ru/university/press-center/news/tsifrovaya_kafedra/vypusknik_tsifrovoy_kafedry_aleksey_valkov_i_rukovoditel_tsifrovoy_kafedry_volggtu_professor_alla_gr/', '2024-02-09', '/upload/iblock/128/128f97fcc57ed61f8cc265909785fbf8.jpg', '2024-02-22 05:28:56', '2024-02-22 05:28:56'),
(40, '\"Центр информационных технологий Волгоградской области\" принял участие в мероприятии ВолгГТУ', 'tsentr_informatsionnykh_tekhnologiy_volgogradskoy_oblasti_prinyal_uchastie_v_meropriyatii_volggtu', 'https://www.vstu.ru/university/press-center/news/tsifrovaya_kafedra/tsentr_informatsionnykh_tekhnologiy_volgogradskoy_oblasti_prinyal_uchastie_v_meropriyatii_volggtu/', '2024-02-16', '/upload/iblock/303/303c01fe00083523af5cac0eaa466e5b.jpg', '2024-02-22 05:32:26', '2024-02-22 05:32:26'),
(41, 'Еженедельник \"Аргументы и факты\": Не так страшен, как малюют. Есть ли шанс ужиться с искусственным интеллектом ', 'ezhenedelnik_argumenty_i_fakty_ne_tak_strashen_kak_malyuyut_est_li_shans_uzhitsya_s_iskusstvennym_in', 'https://www.vstu.ru/university/press-center/news/nauka-i-innovatsii/ezhenedelnik_argumenty_i_fakty_ne_tak_strashen_kak_malyuyut_est_li_shans_uzhitsya_s_iskusstvennym_in/', '2024-02-19', '/upload/iblock/ea3/ea3c6234d3d16490001e5e07d58a7bbc.jpg', '2024-02-22 05:33:11', '2024-02-22 05:33:11'),
(42, 'Студенты Волгоградского государственного технического университета посетили Центр информационных технологий Волгоградской области', 'studenty_volgogradskogo_gosudarstvennogo_tekhnicheskogo_universiteta_posetili_tsentr_informatsionnykh', 'https://www.vstu.ru/university/press-center/news/tsifrovaya_kafedra/studenty_volgogradskogo_gosudarstvennogo_tekhnicheskogo_universiteta_posetili_tsentr_informatsionnykh/', '2024-03-04', '/upload/iblock/340/340b91626a9700fa5473b69091d1bb79.jpg', '2024-03-05 07:26:25', '2024-03-05 07:26:25'),
(43, 'На Цифровой кафедре ВолгГТУ прошел этап «Зарница» в удаленном формате', 'na_tsifrovoy_kafedre_volggtu_proshel_etap_zarnitsa_v_udalennom_formate', 'https://www.vstu.ru/university/press-center/news/tsifrovaya_kafedra/na_tsifrovoy_kafedre_volggtu_proshel_etap_zarnitsa_v_udalennom_formate/', '2024-02-29', '/upload/iblock/687/687a363e70e23d9473841596d08eff6d.jpg', '2024-03-05 07:26:40', '2024-03-05 07:26:40'),
(44, 'Сетевое издание «Волжская правда»: Волжский политехнический институт обучает студентов на «Цифровой кафедре»', 'setevoe_izdanie_volzhskaya_pravda_volzhskiy_politekhnicheskiy_institut_obuchaet_studentov_na_tsifrov', 'https://www.vstu.ru/university/press-center/news/tsifrovaya_kafedra/setevoe_izdanie_volzhskaya_pravda_volzhskiy_politekhnicheskiy_institut_obuchaet_studentov_na_tsifrov/', '2024-03-29', '/upload/iblock/011/0112641a920205ed849c17b0ff9451bb.jpg', '2024-03-30 04:03:11', '2024-03-30 04:03:11'),
(45, 'ГТРК «Волгоград-ТРВ». Приоритет – 2030: волгоградские студенты получают навыки в IT на «Цифровой кафедре»', 'gtrk_volgograd_trv_prioritet_2030_volgogradskie_studenty_poluchayut_navyki_v_it_na_tsifrovoy_kafedre', 'https://www.vstu.ru/university/press-center/news/tsifrovaya_kafedra/gtrk_volgograd_trv_prioritet_2030_volgogradskie_studenty_poluchayut_navyki_v_it_na_tsifrovoy_kafedre/', '2024-03-26', '/upload/iblock/9c3/9c3708972c7ed49b0b53dcdd827ca2ad.jpg', '2024-03-30 04:04:34', '2024-03-30 04:04:34'),
(46, '«Цифровая кафедра» ВолгГТУ заняла третье место на «Марафоне цифровых кафедр 2.0» среди вузов Южного и Северо-Кавказского федеральных округов', 'tsifrovaya_kafedra_volggtu_zanyala_trete_mesto_na_marafone_tsifrovykh_kafedr_2_0_sredi_vuzov_yuzhnog', 'https://www.vstu.ru/university/press-center/news/dostizheniya/tsifrovaya_kafedra_volggtu_zanyala_trete_mesto_na_marafone_tsifrovykh_kafedr_2_0_sredi_vuzov_yuzhnog/', '2024-04-11', '/upload/iblock/75b/75b1313173c8921425a8bd36b3bf166e.jpg', '2024-04-15 05:31:01', '2024-04-15 05:31:01'),
(47, 'ГТРК \"Волгоград-ТРВ\": В Волгограде пройдут образовательные лекции об искусственном интеллекте', 'gtrk_volgograd_trv_v_volgograde_proydut_obrazovatelnye_lektsii_ob_iskusstvennom_intellekte', 'https://www.vstu.ru/university/press-center/news/nauka-i-innovatsii/gtrk_volgograd_trv_v_volgograde_proydut_obrazovatelnye_lektsii_ob_iskusstvennom_intellekte/', '2024-05-18', '/upload/iblock/161/16115673a3b2160a67d5618f2afd1aa2.jpg', '2024-05-18 03:24:21', '2024-05-18 03:24:21'),
(48, 'AI шагает по стране! В российских регионах проходит серия научных лекций по искусственному интеллекту', 'sber', 'https://www.vstu.ru/university/press-center/news/nauka-i-innovatsii/sber/', '2024-05-17', '/upload/iblock/303/30333118d8a1b1e6997917d866a81c64.jpg', '2024-05-18 03:24:46', '2024-05-18 03:24:46'),
(49, 'Выпуск 2024: Вручение дипломов Цифровой кафедры ВолгГТУ состоялось!', 'vypusk_2024_vruchenie_diplomov_tsifrovoy_kafedry_volggtu_sostoyalos', 'https://www.vstu.ru/university/press-center/news/tsifrovaya_kafedra/vypusk_2024_vruchenie_diplomov_tsifrovoy_kafedry_volggtu_sostoyalos/', '2024-07-08', '/upload/iblock/8dd/8dd8a22c2a1716f9112519180410543e.jpg', '2024-07-13 10:26:44', '2024-07-13 10:26:44'),
(50, 'Журнал \"Программная инженерия\" (г.Москва): Новый подход к персонификации сценариев для однопользовательской 3D-игры в стиле Soulslike', 'zhurnal_programmnaya_inzheneriya_g_moskva_novyy_podkhod_k_personifikatsii_stsenariev_dlya_odnopolzov', 'https://www.vstu.ru/university/press-center/news/nauka-i-innovatsii/zhurnal_programmnaya_inzheneriya_g_moskva_novyy_podkhod_k_personifikatsii_stsenariev_dlya_odnopolzov/', '2024-08-09', '/upload/iblock/1b6/1b6c9608b82768db0bcde921568aecca.jpg', '2024-08-13 08:35:53', '2024-08-13 08:35:53'),
(51, 'ГТРК «Волгоград-ТРВ». Программа «Реальное время». Тема: «Дефицит кадров в сфере высоких технологий»', 'gtrk_volgograd_trv_programma_realnoe_vremya_tema_defitsit_kadrov_v_sfere_vysokikh_tekhnologiy', 'https://www.vstu.ru/university/press-center/news/obrazovanie/gtrk_volgograd_trv_programma_realnoe_vremya_tema_defitsit_kadrov_v_sfere_vysokikh_tekhnologiy/', '2024-08-06', '/upload/iblock/37a/37a15d8562d04128a44dce78b901b49a.png', '2024-08-13 08:37:15', '2024-08-13 08:37:15'),
(52, '«Блокнот Волгоград»: Придуманный в гараже волгоградский бизнес впечатлил американскую космонавтику и предприимчивых китайцев', 'bloknot_volgograd_pridumannyy_v_garazhe_volgogradskiy_biznes_vpechatlil_amerikanskuyu_kosmonavtiku_i', 'https://www.vstu.ru/university/press-center/news/obshchestvo/bloknot_volgograd_pridumannyy_v_garazhe_volgogradskiy_biznes_vpechatlil_amerikanskuyu_kosmonavtiku_i/', '2024-08-21', '/upload/iblock/d20/d20c482b448b4b28e2c3eee95d7adc07.jpg', '2024-08-22 05:18:13', '2024-08-22 05:18:13'),
(53, 'ГТРК «Волгоград-ТРВ». Программа «Общественная экспертиза». Люди и роботы', 'gtrk_volgograd_trv_programma_obshchestvennaya_ekspertiza_lyudi_i_roboty', 'https://www.vstu.ru/university/press-center/news/obshchestvo/gtrk_volgograd_trv_programma_obshchestvennaya_ekspertiza_lyudi_i_roboty/', '2024-08-23', '/upload/iblock/493/493525ff6ff546da82c2148b15a5d62e.jpg', '2024-08-24 10:04:45', '2024-08-24 10:04:45');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `test_id` bigint(20) UNSIGNED NOT NULL,
  `question_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_type` enum('single','multiple','text') COLLATE utf8mb4_unicode_ci NOT NULL,
  `points` int(11) NOT NULL DEFAULT 1,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `test_id`, `question_text`, `question_type`, `points`, `order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Какой язык программирования используется в OpenAI?', 'single', 1, 0, '2025-07-19 15:13:18', '2025-07-19 15:13:18');

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

CREATE TABLE `tests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direction_id` bigint(20) UNSIGNED DEFAULT NULL,
  `passing_score` int(11) NOT NULL,
  `time_limit` int(11) DEFAULT NULL COMMENT 'В минутах',
  `attempts_limit` int(11) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `chat_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`id`, `name`, `description`, `direction_id`, `passing_score`, `time_limit`, `attempts_limit`, `is_published`, `created_at`, `updated_at`, `chat_link`, `group_link`) VALUES
(1, 'Пробный тест', 'Тест для проверки', NULL, 5, 10, 2, 1, '2025-07-19 15:08:21', '2025-07-19 15:08:21', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_answers`
--

CREATE TABLE `user_answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_test_id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `answer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `answer_text` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `points` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_answers`
--

INSERT INTO `user_answers` (`id`, `user_test_id`, `question_id`, `answer_id`, `answer_text`, `is_correct`, `points`) VALUES
(1, 1, 1, 1, NULL, 1, 1),
(2, 2, 1, 2, NULL, 0, 0),
(3, 3, 1, 2, NULL, 0, 0),
(4, 4, 1, 1, NULL, 1, 1),
(5, 5, 1, 1, NULL, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_tests`
--

CREATE TABLE `user_tests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `test_id` bigint(20) UNSIGNED NOT NULL,
  `score` int(11) NOT NULL DEFAULT 0,
  `max_score` int(11) NOT NULL,
  `passed` tinyint(1) NOT NULL DEFAULT 0,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `attempt_number` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_tests`
--

INSERT INTO `user_tests` (`id`, `user_id`, `test_id`, `score`, `max_score`, `passed`, `started_at`, `completed_at`, `attempt_number`) VALUES
(1, 873, 1, 1, 1, 0, '2025-07-19 15:32:57', '2025-07-19 15:32:57', 1),
(2, 873, 1, 0, 1, 0, '2025-07-19 15:33:37', '2025-07-19 15:33:37', 1),
(3, 873, 1, 0, 1, 0, '2025-07-19 15:38:21', '2025-07-19 15:38:21', 1),
(4, 873, 1, 1, 1, 0, '2025-07-19 15:38:29', '2025-07-19 15:38:29', 1),
(5, 873, 1, 1, 1, 0, '2025-07-19 15:39:50', '2025-07-19 15:39:50', 1);

-- --------------------------------------------------------

--
-- Table structure for table `verified_users`
--

CREATE TABLE `verified_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not verified',
  `avatar_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `test_result` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chat_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `verified_users`
--

INSERT INTO `verified_users` (`id`, `name`, `email`, `phone`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `status`, `avatar_url`, `test_result`, `group_link`, `chat_link`) VALUES
(1, 'Иванов Иван', 'ivanov@example.com', '+7(901) 123-45-67', '2025-07-15 18:34:58', 'testpass1', NULL, '2025-07-15 18:34:58', NULL, 'verified', NULL, NULL, NULL, NULL),
(2, 'Петров Пётр', 'petrov@example.com', '+7(902) 234-56-78', '2025-07-15 21:10:26', 'testpass2', NULL, '2025-07-15 21:10:26', NULL, 'verified', NULL, '', NULL, NULL),
(3, 'Сидоров Сидор', 'sidorov@example.com', '+7(903) 345-67-89', NULL, 'testpass3', NULL, NULL, NULL, 'not verified', NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_email_unique` (`email`);

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `answers_question_id_foreign` (`question_id`);

--
-- Indexes for table `assigned_tests`
--
ALTER TABLE `assigned_tests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_name_unique` (`name`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`);

--
-- Indexes for table `directions`
--
ALTER TABLE `directions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `directions_name_unique` (`name`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `documents_category_id_foreign` (`category_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `listeners`
--
ALTER TABLE `listeners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `listeners_direction_id_foreign` (`direction_id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `news_title_unique` (`title`),
  ADD UNIQUE KEY `news_slug_unique` (`slug`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questions_test_id_foreign` (`test_id`);

--
-- Indexes for table `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tests_direction_id_foreign` (`direction_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_answers`
--
ALTER TABLE `user_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_answers_user_test_id_foreign` (`user_test_id`),
  ADD KEY `user_answers_question_id_foreign` (`question_id`),
  ADD KEY `user_answers_answer_id_foreign` (`answer_id`);

--
-- Indexes for table `user_tests`
--
ALTER TABLE `user_tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_tests_user_id_foreign` (`user_id`),
  ADD KEY `user_tests_test_id_foreign` (`test_id`);

--
-- Indexes for table `verified_users`
--
ALTER TABLE `verified_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `assigned_tests`
--
ALTER TABLE `assigned_tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `directions`
--
ALTER TABLE `directions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `listeners`
--
ALTER TABLE `listeners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=874;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_answers`
--
ALTER TABLE `user_answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_tests`
--
ALTER TABLE `user_tests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `verified_users`
--
ALTER TABLE `verified_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `listeners`
--
ALTER TABLE `listeners`
  ADD CONSTRAINT `listeners_direction_id_foreign` FOREIGN KEY (`direction_id`) REFERENCES `directions` (`id`);

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_test_id_foreign` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tests`
--
ALTER TABLE `tests`
  ADD CONSTRAINT `tests_direction_id_foreign` FOREIGN KEY (`direction_id`) REFERENCES `directions` (`id`);

--
-- Constraints for table `user_answers`
--
ALTER TABLE `user_answers`
  ADD CONSTRAINT `user_answers_answer_id_foreign` FOREIGN KEY (`answer_id`) REFERENCES `answers` (`id`),
  ADD CONSTRAINT `user_answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`),
  ADD CONSTRAINT `user_answers_user_test_id_foreign` FOREIGN KEY (`user_test_id`) REFERENCES `user_tests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_tests`
--
ALTER TABLE `user_tests`
  ADD CONSTRAINT `user_tests_test_id_foreign` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`),
  ADD CONSTRAINT `user_tests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `listeners` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
