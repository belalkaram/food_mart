CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`) VALUES
(1, 'admin', 'admin@example.com', '$2y$10$w9eC2s5fQz/1hJ4Y7k.gA.xW/8Z.9T.c.2p.R/6y.eF.0hJ/9K', 'المدير العام', 'admin');

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255),
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `products` (`name`, `price`, `image`, `description`) VALUES
('Sunstar Fresh Melon Juice', 18.00, 'images/thumb-melon.png', 'Melon Juice'),
('Spicy Mint Lollipop', 22.00, 'images/thumb-lollipop.png', 'Mint Lollipop'),
('Fresh Green Cucumber', 12.00, 'images/thumb-cucumber.png', 'Green Cucumber'),
('Natural Almond Milk', 34.00, 'images/thumb-milk.png', 'Almond Milk'),
('Organic Banana', 18.00, 'images/thumb-bananas.png', 'Organic Banana'),
('Sweetie Jelly Puffs', 15.00, 'images/thumb-biscuits.png', 'Jelly Puffs'),
('Green Vegetable Pack', 19.00, 'images/thumb-cucumber.png', 'Vegetable Pack'),
('Fresh Original Milk', 29.00, 'images/thumb-milk.png', 'Original Milk'),
('Juicy Baby Bananas', 18.00, 'images/thumb-bananas.png', 'Baby Bananas'),
('Chocolate Chip Biscuits', 24.00, 'images/thumb-biscuits.png', 'Chip Biscuits');

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

