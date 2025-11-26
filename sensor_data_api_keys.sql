-- Futtasd pl. mysql kliensből vagy phpMyAdmin-ból.
-- Létrehozza az api_keys táblát a sensor_data adatbázisban.

CREATE DATABASE IF NOT EXISTS sensor_data
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_hungarian_ci;

USE sensor_data;

-- API kulcsok táblája
CREATE TABLE IF NOT EXISTS api_keys (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  api_key       CHAR(64) NOT NULL UNIQUE,
  is_active     TINYINT(1) NOT NULL DEFAULT 1,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_used_at  TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB;

-- Példa API kulcs (CSERÉLD KI egy biztonságos értékre!)
-- Javasolt generálás:  openssl rand -hex 32   (64 hex karakter)
INSERT INTO api_keys (api_key, is_active)
VALUES ('REPLACE_WITH_A_SECURE_RANDOM_64CHAR_KEY', 1);
