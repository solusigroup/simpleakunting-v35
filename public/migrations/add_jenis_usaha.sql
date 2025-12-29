-- Migration: Add jenis_usaha column to perusahaan table
-- Run this SQL to enable service company (Jasa) COGS exemption feature

ALTER TABLE perusahaan 
ADD COLUMN jenis_usaha ENUM('dagang', 'jasa', 'manufaktur') DEFAULT 'dagang' 
AFTER nama_perusahaan;

-- Update existing record to default 'dagang'
UPDATE perusahaan SET jenis_usaha = 'dagang' WHERE id = 1;
