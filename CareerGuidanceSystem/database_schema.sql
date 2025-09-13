-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 06, 2025 at 08:16 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `career_guidance_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `careers`
--

CREATE TABLE `careers` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `careers`
--

INSERT INTO `careers` (`id`, `title`, `description`) VALUES
(1, 'Web Developer', 'Designs, develops, and maintains websites and web applications. html css'),
(2, 'Data Scientist', 'Analyzes complex data to extract insights and predict future trends.'),
(3, 'Cybersecurity Analyst', 'Protects computer systems and networks from threats and attacks.'),
(4, 'Digital Marketing Specialist', 'Plans and executes digital marketing campaigns.'),
(5, 'Graphic Designer', 'Creates visual concepts using computer software or by hand, to communicate ideas.'),
(6, 'Network Administrator', 'Manages computer networks, ensuring smooth operation.'),
(7, 'Project Manager', 'Plans, executes, and closes projects, ensuring they meet deadlines and budgets.'),
(8, 'DevOps', 'intrested in cloud');

-- --------------------------------------------------------

--
-- Table structure for table `recommendations`
--

CREATE TABLE `recommendations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `career_id` int(11) NOT NULL,
  `success_score` decimal(5,2) NOT NULL,
  `recommended_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recommendations`
--

INSERT INTO `recommendations` (`id`, `user_id`, `career_id`, `success_score`, `recommended_at`) VALUES
(39, 3, 7, 30.98, '2025-07-06 18:06:26'),
(40, 3, 4, 52.20, '2025-07-06 18:06:26'),
(41, 3, 2, 26.53, '2025-07-06 18:06:26'),
(42, 3, 5, 39.74, '2025-07-06 18:06:26'),
(43, 3, 8, 22.44, '2025-07-06 18:06:26'),
(44, 3, 1, 22.50, '2025-07-06 18:06:26'),
(45, 3, 3, 14.60, '2025-07-06 18:06:26'),
(46, 3, 6, 19.37, '2025-07-06 18:06:26');

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`id`, `name`) VALUES
(167, '3D Modeling'),
(107, 'Agile Methodologies'),
(181, 'Agile Project Management'),
(143, 'Android Development'),
(42, 'Angular'),
(168, 'Animation'),
(92, 'Ansible'),
(136, 'Apache Spark'),
(184, 'API Integration'),
(151, 'Application Security'),
(198, 'AR/VR Development'),
(62, 'ASP.NET Core'),
(33, 'Assembly'),
(193, 'Automated Testing'),
(86, 'AWS (Amazon Web Services)'),
(87, 'Azure (Microsoft Azure)'),
(95, 'Azure DevOps'),
(50, 'Babel'),
(135, 'Big Data'),
(104, 'Bitbucket'),
(195, 'Blockchain'),
(46, 'Bootstrap'),
(140, 'Business Intelligence (BI)'),
(21, 'C#'),
(162, 'C# (Game Dev)'),
(20, 'C++'),
(163, 'C++ (Game Dev)'),
(79, 'Cassandra'),
(110, 'CI/CD Pipelines'),
(199, 'Cloud Architecture'),
(99, 'Cloud Migration'),
(97, 'Cloud Security'),
(35, 'Cobol'),
(6, 'Communication'),
(180, 'Communication Skills'),
(159, 'Compliance (GDPR, HIPAA)'),
(131, 'Computer Vision'),
(106, 'Confluence'),
(111, 'Containerization'),
(14, 'Content Writing'),
(5, 'Critical Thinking'),
(156, 'Cryptography'),
(40, 'CSS3'),
(12, 'Cybersecurity'),
(32, 'Dart'),
(9, 'Data Analysis'),
(126, 'Data Cleaning'),
(200, 'Data Governance'),
(85, 'Data Modeling'),
(138, 'Data Warehousing'),
(82, 'Database Design'),
(3, 'Database Management (MySQL)'),
(183, 'Debugging'),
(132, 'Deep Learning'),
(201, 'DevSecOps'),
(175, 'DHCP'),
(59, 'Django'),
(174, 'DNS'),
(89, 'Docker'),
(80, 'Elasticsearch'),
(116, 'ELK Stack'),
(152, 'Endpoint Security'),
(139, 'ETL'),
(58, 'Express.js'),
(66, 'FastAPI'),
(127, 'Feature Engineering'),
(176, 'Firewalls'),
(60, 'Flask'),
(146, 'Flutter'),
(34, 'Fortran'),
(164, 'Game Design'),
(101, 'Git'),
(102, 'GitHub'),
(103, 'GitLab'),
(94, 'GitLab CI/CD'),
(24, 'Go'),
(96, 'Google Cloud Build'),
(88, 'Google Cloud Platform (GCP)'),
(115, 'Grafana'),
(13, 'Graphic Design'),
(68, 'GraphQL'),
(137, 'Hadoop'),
(36, 'Haskell'),
(39, 'HTML5'),
(158, 'Identity and Access Management (IAM)'),
(153, 'Incident Response'),
(100, 'Infrastructure as Code (IaC)'),
(144, 'iOS Development'),
(196, 'IoT (Internet of Things)'),
(18, 'Java'),
(19, 'JavaScript'),
(93, 'Jenkins'),
(105, 'Jira'),
(45, 'jQuery'),
(109, 'Kanban'),
(122, 'Keras'),
(26, 'Kotlin'),
(90, 'Kubernetes'),
(64, 'Laravel'),
(190, 'Leadership'),
(37, 'Lisp'),
(177, 'Load Balancing'),
(10, 'Machine Learning'),
(194, 'Manual Testing'),
(15, 'Marketing'),
(124, 'Matplotlib'),
(189, 'Mentoring'),
(69, 'Microservices'),
(129, 'Model Evaluation'),
(128, 'Model Training'),
(74, 'MongoDB'),
(113, 'Monitoring & Logging'),
(72, 'MySQL'),
(149, 'NativeScript'),
(130, 'Natural Language Processing (NLP)'),
(188, 'Negotiation'),
(81, 'Neo4j'),
(150, 'Network Security'),
(11, 'Networking'),
(172, 'Networking Fundamentals'),
(57, 'Node.js'),
(84, 'NoSQL Databases'),
(119, 'NumPy'),
(169, 'Operating Systems (Linux/Unix)'),
(77, 'Oracle Database'),
(112, 'Orchestration'),
(118, 'Pandas'),
(155, 'Penetration Testing'),
(30, 'Perl'),
(22, 'PHP'),
(165, 'Physics Engines'),
(73, 'PostgreSQL'),
(142, 'Power BI'),
(4, 'Problem Solving'),
(1, 'Programming (PHP)'),
(55, 'Progressive Web Apps (PWAs)'),
(8, 'Project Management'),
(38, 'Prolog'),
(114, 'Prometheus'),
(187, 'Public Speaking'),
(17, 'Python'),
(123, 'PyTorch'),
(191, 'Quality Assurance (QA)'),
(197, 'Quantum Computing Basics'),
(31, 'R'),
(145, 'React Native'),
(41, 'React.js'),
(75, 'Redis'),
(133, 'Reinforcement Learning'),
(186, 'Research Skills'),
(52, 'Responsive Design'),
(67, 'RESTful APIs'),
(23, 'Ruby'),
(61, 'Ruby on Rails'),
(28, 'Rust'),
(48, 'Sass/Less'),
(29, 'Scala'),
(120, 'Scikit-learn'),
(108, 'Scrum'),
(125, 'Seaborn'),
(157, 'Security Information and Event Management (SIEM)'),
(70, 'Serverless Architecture'),
(98, 'Serverless Computing'),
(166, 'Shader Programming'),
(56, 'Single-Page Applications (SPAs)'),
(202, 'Site Reliability Engineering (SRE)'),
(192, 'Software Testing'),
(117, 'Splunk'),
(63, 'Spring Boot'),
(83, 'SQL Query Optimization'),
(76, 'SQL Server'),
(78, 'SQLite'),
(134, 'Statistical Analysis'),
(44, 'Svelte'),
(25, 'Swift'),
(147, 'SwiftUI'),
(65, 'Symfony'),
(178, 'System Administration'),
(141, 'Tableau'),
(47, 'Tailwind CSS'),
(173, 'TCP/IP'),
(7, 'Teamwork'),
(179, 'Technical Support'),
(185, 'Technical Writing'),
(121, 'TensorFlow'),
(91, 'Terraform'),
(27, 'TypeScript'),
(51, 'TypeScript (Frontend)'),
(53, 'UI/UX Design Principles'),
(160, 'Unity 3D'),
(161, 'Unreal Engine'),
(182, 'Version Control'),
(171, 'Virtualization'),
(43, 'Vue.js'),
(154, 'Vulnerability Assessment'),
(54, 'Web Accessibility'),
(2, 'Web Design (HTML/CSS)'),
(49, 'Webpack'),
(71, 'WebSockets'),
(170, 'Windows Server'),
(148, 'Xamarin');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `major` varchar(255) DEFAULT NULL,
  `gpa` decimal(3,2) DEFAULT NULL,
  `experience_summary` text DEFAULT NULL,
  `projects_summary` text DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `summary_text` text DEFAULT NULL,
  `university_name` varchar(255) DEFAULT NULL,
  `graduation_year` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `major`, `gpa`, `experience_summary`, `projects_summary`, `phone_number`, `linkedin_url`, `summary_text`, `university_name`, `graduation_year`) VALUES
(2, 'admin', 'admin@gmail.com', '$2y$10$2x885dh5RFrKyUvmWM9v/Ofp1fwp555R4wUzwplwJded0YQb7B3My', 'admin', '2025-07-06 16:18:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Bishal Ranjitkar', 'bishalranjit2002@gmai.com', '$2y$10$9y2I5/1a5VbNy6D4ydcRyOvG3bvjLQrfSX3kbh0URL169pO4DZDqC', 'user', '2025-07-06 16:53:48', 'computer application', 3.40, '', 'built a web', '9812241818', 'https://www.linkedin.com/in/bishalranjit0606/', 'Motivated and adaptable professional with a strong foundation in problem-solving and cross-functional collaboration. Known for quick learning, attention to detail, and the ability to thrive in fast-paced environments. Eager to contribute to team success through hard work, creativity, and a growth-oriented mindset.', 'Tribhuvan university', '2025');

-- --------------------------------------------------------

--
-- Table structure for table `user_answers`
--

CREATE TABLE `user_answers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_answers`
--

INSERT INTO `user_answers` (`id`, `user_id`, `question`, `answer`) VALUES
(59, 3, 'What is your ideal work environment?', 'Highly collaborative team-based environment with constant interaction'),
(60, 3, 'How do you prefer to approach problem-solving?', 'Brainstorming creative and unconventional solutions'),
(61, 3, 'What kind of tasks do you find most engaging?', 'Writing and debugging code||Interacting directly with clients or users'),
(62, 3, 'How comfortable are you with continuous learning and adapting to new tools?', 'Somewhat comfortable, I prefer stability but can adapt'),
(63, 3, 'Which of the following areas interests you most?', 'Data Analysis & Business Intelligence'),
(64, 3, 'Are you comfortable with abstract concepts and theoretical frameworks?', 'Somewhat comfortable, I prefer practical applications'),
(65, 3, 'How much do you enjoy working with mathematical or statistical concepts?', 'Moderately enjoy, I can apply them when needed'),
(66, 3, 'How would you describe your communication style?', 'Detailed and precise'),
(67, 3, 'Are you more of a detail-oriented person or a big-picture thinker?', 'Mostly detail-oriented, but can see the big picture'),
(68, 3, 'How do you handle repetitive tasks?', 'Somewhat comfortable, if they are necessary for a larger goal'),
(69, 3, 'How important is innovation and creating new things to you?', 'Very important, I enjoy contributing to new ideas'),
(70, 3, 'What drives you most in a career?', 'Opportunities for continuous learning and growth||Work-life balance and flexibility');

-- --------------------------------------------------------

--
-- Table structure for table `user_skills`
--

CREATE TABLE `user_skills` (
  `user_id` int(11) NOT NULL,
  `skill_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_skills`
--

INSERT INTO `user_skills` (`user_id`, `skill_id`) VALUES
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(3, 6),
(3, 8),
(3, 9),
(3, 11),
(3, 13),
(3, 14),
(3, 89),
(3, 103),
(3, 121),
(3, 171),
(3, 189);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `careers`
--
ALTER TABLE `careers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `title` (`title`);

--
-- Indexes for table `recommendations`
--
ALTER TABLE `recommendations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `career_id` (`career_id`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_answers`
--
ALTER TABLE `user_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_skills`
--
ALTER TABLE `user_skills`
  ADD PRIMARY KEY (`user_id`,`skill_id`),
  ADD KEY `skill_id` (`skill_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `careers`
--
ALTER TABLE `careers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `recommendations`
--
ALTER TABLE `recommendations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=207;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_answers`
--
ALTER TABLE `user_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `recommendations`
--
ALTER TABLE `recommendations`
  ADD CONSTRAINT `recommendations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recommendations_ibfk_2` FOREIGN KEY (`career_id`) REFERENCES `careers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_answers`
--
ALTER TABLE `user_answers`
  ADD CONSTRAINT `user_answers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_skills`
--
ALTER TABLE `user_skills`
  ADD CONSTRAINT `user_skills_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_skills_ibfk_2` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
