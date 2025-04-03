CREATE TABLE IF NOT EXISTS `employees` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `company_id` int(11) NOT NULL,
    `name` varchar(100) NOT NULL,
    `email` varchar(100) NOT NULL,
    `phone` varchar(20),
    `position` varchar(50),
    `department` varchar(50),
    `salary` decimal(10,2),
    `hire_date` date,
    `status` enum('Aktif','Pasif') DEFAULT 'Aktif',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `attendance` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `employee_id` int(11) NOT NULL,
    `date` date NOT NULL,
    `check_in` time,
    `check_out` time,
    `status` enum('Tam Gün','Yarım Gün','İzinli','Yok') DEFAULT 'Tam Gün',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `email` varchar(100) NOT NULL,
    `password` varchar(100) NOT NULL,
    `role` enum('admin','ik','puantor','yonetici') NOT NULL,
    `company_id` int(11),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
