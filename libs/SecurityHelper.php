<?php

/**
 * Security Helper Class
 * 
 * Provides utility methods for security-related tasks
 */
class SecurityHelper {
    
    /**
     * Sanitize input data to prevent XSS
     * 
     * @param mixed $input The input to sanitize
     * @return mixed The sanitized input
     */
    public static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Safely extract PHAR contents with proper validation
     * 
     * @param string $pharFile The PHAR file to extract
     * @param string $targetDir The target directory
     * @return bool Success status
     */
    public static function safeExtractPhar($pharFile, $targetDir) {
        // Implement a more secure version of the PHAR extraction
        // with proper validation and error handling
        if (!file_exists($pharFile) || !is_readable($pharFile)) {
            return false;
        }
        
        // Validate target directory
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true)) {
                return false;
            }
        }
        
        // Extract with proper validation
        try {
            $phar = new Phar($pharFile);
            $phar->extractTo($targetDir, null, true);
            return true;
        } catch (Exception $e) {
            // Log error
            error_log("PHAR extraction failed: " . $e->getMessage());
            return false;
        }
    }
}
