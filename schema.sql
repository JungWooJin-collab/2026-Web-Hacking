-- 데이터베이스 생성
CREATE DATABASE IF NOT EXISTS board_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE board_db;

-- Users 테이블 (사용자 계정 및 관리자 권한)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Posts 테이블 (게시글 및 첨부파일 정보)
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    file_name VARCHAR(255) NULL,
    file_path VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- (예시) 초기 관리자 계정 생성 (비밀번호: admin123, 비밀번호 해시는 PHP password_hash 로 나중에 변경하거나 삽입 필요)
-- INSERT INTO users (username, password_hash, role) VALUES ('admin', '$2y$10$YourHashedPasswordHere', 'admin');
