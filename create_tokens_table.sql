-- SQL script to create the personal_access_tokens table

-- Create the personal_access_tokens table if it doesn't exist
CREATE TABLE IF NOT EXISTS personal_access_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE INDEX personal_access_tokens_token_unique (token),
    INDEX personal_access_tokens_tokenable_type_tokenable_id_index (tokenable_type, tokenable_id)
);

-- Add migration record if it doesn't exist
INSERT IGNORE INTO migrations (migration, batch) 
SELECT '2019_12_14_000001_create_personal_access_tokens_table', COALESCE(MAX(batch), 0) + 1 
FROM migrations;
