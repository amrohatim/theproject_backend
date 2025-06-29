-- SQL Queries to Verify Branch Coordinates Fix
-- Run these queries to verify that latitude and longitude coordinates are being saved correctly

-- 1. Check the structure of the branches table to confirm lat/lng columns exist
DESCRIBE branches;

-- 2. Check all branches with their coordinates
SELECT 
    id,
    name,
    address,
    lat,
    lng,
    status,
    created_at
FROM branches 
ORDER BY created_at DESC;

-- 3. Check for branches with NULL coordinates (should be none after the fix)
SELECT 
    id,
    name,
    address,
    lat,
    lng,
    status
FROM branches 
WHERE lat IS NULL OR lng IS NULL;

-- 4. Check for branches with invalid coordinates (outside valid ranges)
SELECT 
    id,
    name,
    address,
    lat,
    lng,
    status,
    CASE 
        WHEN lat < -90 OR lat > 90 THEN 'Invalid Latitude'
        WHEN lng < -180 OR lng > 180 THEN 'Invalid Longitude'
        ELSE 'Valid'
    END as coordinate_status
FROM branches 
WHERE lat < -90 OR lat > 90 OR lng < -180 OR lng > 180;

-- 5. Get statistics about coordinate data
SELECT 
    COUNT(*) as total_branches,
    COUNT(lat) as branches_with_lat,
    COUNT(lng) as branches_with_lng,
    COUNT(CASE WHEN lat IS NOT NULL AND lng IS NOT NULL THEN 1 END) as branches_with_both_coordinates,
    MIN(lat) as min_latitude,
    MAX(lat) as max_latitude,
    MIN(lng) as min_longitude,
    MAX(lng) as max_longitude,
    AVG(lat) as avg_latitude,
    AVG(lng) as avg_longitude
FROM branches;

-- 6. Check branches created by vendor users (to verify the fix specifically for vendor routes)
SELECT 
    b.id,
    b.name,
    b.address,
    b.lat,
    b.lng,
    b.status,
    u.name as vendor_name,
    u.email as vendor_email,
    b.created_at
FROM branches b
JOIN users u ON b.user_id = u.id
WHERE u.role = 'vendor'
ORDER BY b.created_at DESC;

-- 7. Test query to verify coordinate precision (should show decimal places)
SELECT 
    id,
    name,
    CAST(lat AS CHAR) as lat_string,
    CAST(lng AS CHAR) as lng_string,
    LENGTH(SUBSTRING_INDEX(CAST(lat AS CHAR), '.', -1)) as lat_decimal_places,
    LENGTH(SUBSTRING_INDEX(CAST(lng AS CHAR), '.', -1)) as lng_decimal_places
FROM branches 
WHERE lat IS NOT NULL AND lng IS NOT NULL
LIMIT 10;

-- 8. Check for duplicate coordinates (might indicate data issues)
SELECT 
    lat,
    lng,
    COUNT(*) as branch_count,
    GROUP_CONCAT(name SEPARATOR ', ') as branch_names
FROM branches 
WHERE lat IS NOT NULL AND lng IS NOT NULL
GROUP BY lat, lng
HAVING COUNT(*) > 1;

-- 9. Verify that coordinates are within UAE bounds (for UAE-based branches)
-- UAE approximate bounds: lat 22-26, lng 51-57
SELECT 
    id,
    name,
    address,
    lat,
    lng,
    CASE 
        WHEN lat BETWEEN 22 AND 26 AND lng BETWEEN 51 AND 57 THEN 'Likely UAE'
        ELSE 'Outside UAE'
    END as location_check
FROM branches 
WHERE lat IS NOT NULL AND lng IS NOT NULL;

-- 10. Recent branches to verify the fix is working for new entries
SELECT 
    id,
    name,
    address,
    lat,
    lng,
    status,
    created_at
FROM branches 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY created_at DESC;
