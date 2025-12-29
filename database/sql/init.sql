-- Database initialization script for StockFlowPOS
-- Creates user and sets up basic permissions

-- Create application database
CREATE DATABASE IF NOT EXISTS stockflowpos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create application user
CREATE USER IF NOT EXISTS 'stockflowpos'@'%' IDENTIFIED BY 'password';

-- Grant permissions
GRANT ALL PRIVILEGES ON stockflowpos.* TO 'stockflowpos'@'%';
FLUSH PRIVILEGES;

-- Set default database
USE stockflowpos;