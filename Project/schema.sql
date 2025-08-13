CREATE DATABASE IF NOT EXISTS condo_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE condo_booking;

CREATE TABLE facilities (
  facility_id   INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  description   VARCHAR(255),
  open_time     TIME NOT NULL DEFAULT '08:00:00',
  close_time    TIME NOT NULL DEFAULT '22:00:00',
  slot_minutes  INT NOT NULL DEFAULT 60,
  is_active     TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE residents (
  resident_id   INT AUTO_INCREMENT PRIMARY KEY,
  unit          VARCHAR(20) NOT NULL,
  name          VARCHAR(100) NOT NULL,
  email         VARCHAR(120) NOT NULL,
  contact       VARCHAR(30) NOT NULL,
  UNIQUE KEY uk_unit_email_contact (unit, email, contact)
);

CREATE TABLE bookings (
  booking_id    INT AUTO_INCREMENT PRIMARY KEY,
  resident_id   INT NOT NULL,
  facility_id   INT NOT NULL,
  booking_date  DATE NOT NULL,
  slot_start    TIME NOT NULL,
  slot_end      TIME NOT NULL,
  status        ENUM('CONFIRMED','CANCELLED') NOT NULL DEFAULT 'CONFIRMED',
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_b_resident FOREIGN KEY (resident_id) REFERENCES residents(resident_id) ON DELETE CASCADE,
  CONSTRAINT fk_b_facility  FOREIGN KEY (facility_id) REFERENCES facilities(facility_id) ON DELETE CASCADE,
  UNIQUE KEY uk_active_slot (facility_id, booking_date, slot_start)
);

CREATE TABLE management_users (
  user_id       INT AUTO_INCREMENT PRIMARY KEY,
  email         VARCHAR(120) NOT NULL UNIQUE,
  user_name     VARCHAR(60)  NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('ADMIN','STAFF') NOT NULL DEFAULT 'ADMIN',
  is_active     TINYINT(1) NOT NULL DEFAULT 1
);
