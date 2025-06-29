<?php

namespace App\Helpers;

class UnsplashImageHelper
{
    /**
     * Get a random Unsplash image URL with the given search term and dimensions.
     *
     * @param string $searchTerm
     * @param int $width
     * @param int $height
     * @return string
     */
    public static function getRandomImageUrl(string $searchTerm, int $width = 800, int $height = 600): string
    {
        // Format the search term for URL
        $formattedSearchTerm = str_replace(' ', '+', strtolower($searchTerm));

        // Create the Unsplash source URL
        return "https://source.unsplash.com/random/{$width}x{$height}?{$formattedSearchTerm}";
    }

    /**
     * Download an image from Unsplash and save it to the specified path.
     *
     * @param string $searchTerm
     * @param string $savePath
     * @param int $width
     * @param int $height
     * @return string The path where the image was saved
     */
    public static function downloadAndSaveImage(string $searchTerm, string $savePath, int $width = 800, int $height = 600): string
    {
        try {
            // Get the image URL
            $imageUrl = self::getRandomImageUrl($searchTerm, $width, $height);

            // Create directory if it doesn't exist
            $directory = dirname($savePath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Set a timeout for the request
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30 // 30 seconds timeout
                ]
            ]);

            // Download and save the image
            $imageContent = @file_get_contents($imageUrl, false, $context);

            // Check if download was successful
            if ($imageContent === false) {
                // If download fails, use a placeholder image
                return '/images/placeholder.jpg';
            }

            // Save the image
            file_put_contents($savePath, $imageContent);

            // Return the path relative to public directory
            return str_replace('public/', '/', $savePath);
        } catch (\Exception $e) {
            // If any error occurs, return a placeholder image path
            return '/images/placeholder.jpg';
        }
    }
}
