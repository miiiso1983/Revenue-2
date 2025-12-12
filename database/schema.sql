-- Revenue Management System Database Schema
-- MySQL 8.0+ / MariaDB 10.3+

CREATE DATABASE IF NOT EXISTS revenue_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE revenue_management;

-- Users Table
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'guest') NOT NULL DEFAULT 'guest',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contracts Table
CREATE TABLE contracts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    app_name VARCHAR(255) NOT NULL,
    client_name VARCHAR(255) NOT NULL,
    invoice_number VARCHAR(255) NOT NULL UNIQUE,
    invoice_date DATE NOT NULL,
    duration_months INT NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    currency ENUM('USD', 'IQD') NOT NULL,
    installment_frequency ENUM('monthly', 'quarterly', 'yearly') NOT NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_client_name (client_name),
    INDEX idx_invoice_date (invoice_date),
    INDEX idx_currency (currency),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Monthly Allocations Table
CREATE TABLE monthly_allocations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    contract_id BIGINT UNSIGNED NOT NULL,
    month_date DATE NOT NULL,
    allocated_amount DECIMAL(15, 2) NOT NULL,
    currency ENUM('USD', 'IQD') NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_contract_month (contract_id, month_date),
    INDEX idx_month_date (month_date),
    FOREIGN KEY (contract_id) REFERENCES contracts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Installments Table
CREATE TABLE installments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    contract_id BIGINT UNSIGNED NOT NULL,
    due_date DATE NOT NULL,
    installment_amount DECIMAL(15, 2) NOT NULL,
    currency ENUM('USD', 'IQD') NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_contract_due (contract_id, due_date),
    INDEX idx_due_date (due_date),
    FOREIGN KEY (contract_id) REFERENCES contracts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit Logs Table
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(255) NOT NULL,
    table_name VARCHAR(255) NOT NULL,
    record_id BIGINT UNSIGNED NOT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    created_at TIMESTAMP NOT NULL,
    INDEX idx_table_record (table_name, record_id),
    INDEX idx_created_at (created_at),
    INDEX idx_user_id (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Default Users
INSERT INTO users (username, email, password, role, created_at, updated_at) VALUES
('admin', 'admin@example.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW(), NOW()),
('guest', 'guest@example.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'guest', NOW(), NOW());

-- Note: Default password for both users is "password"

