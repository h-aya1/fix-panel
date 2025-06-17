-- Initial database setup
CREATE DATABASE IF NOT EXISTS ems_korea CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user for application (if not exists)
-- Note: The main user is created via environment variables in docker-compose

-- Set timezone
SET GLOBAL time_zone = '+09:00';

-- Create additional databases if needed
-- CREATE DATABASE IF NOT EXISTS ems_korea_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
