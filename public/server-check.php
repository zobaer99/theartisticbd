<?php
/**
 * Server Diagnostic Tool
 * Upload this file to your server's public directory
 * Access it via: https://theartisticbd.com/server-check.php
 * DELETE THIS FILE AFTER CHECKING!
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Server Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px; }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .warning { color: #ff9800; font-weight: bold; }
        .section { margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 4px solid #4CAF50; }
        .check { margin: 10px 0; padding: 5px; }
        pre { background: #263238; color: #aed581; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .delete-warning { background: #ffebee; border: 2px solid #f44336; padding: 15px; margin: 20px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Laravel Server Diagnostic Tool</h1>
        
        <div class="delete-warning">
            <strong>⚠️ SECURITY WARNING:</strong> Delete this file immediately after checking!
        </div>

        <?php
        $rootPath = dirname(__FILE__) . '/..';
        
        echo '<div class="section">';
        echo '<h2>1. Environment Check</h2>';
        
        // Check .env file
        $envPath = $rootPath . '/.env';
        if (file_exists($envPath)) {
            echo '<div class="check"><span class="success">✓</span> .env file exists</div>';
            
            $envContent = file_get_contents($envPath);
            preg_match('/APP_ENV=(.*)/', $envContent, $appEnv);
            preg_match('/APP_DEBUG=(.*)/', $envContent, $appDebug);
            preg_match('/APP_URL=(.*)/', $envContent, $appUrl);
            
            echo '<div class="check">APP_ENV: ' . (isset($appEnv[1]) ? $appEnv[1] : '<span class="error">Not set</span>') . '</div>';
            echo '<div class="check">APP_DEBUG: ' . (isset($appDebug[1]) ? $appDebug[1] : '<span class="error">Not set</span>') . '</div>';
            echo '<div class="check">APP_URL: ' . (isset($appUrl[1]) ? $appUrl[1] : '<span class="error">Not set</span>') . '</div>';
        } else {
            echo '<div class="check"><span class="error">✗</span> .env file is missing!</div>';
        }
        echo '</div>';

        echo '<div class="section">';
        echo '<h2>2. PHP Configuration</h2>';
        echo '<div class="check">PHP Version: <strong>' . PHP_VERSION . '</strong></div>';
        echo '<div class="check">Server Software: <strong>' . $_SERVER['SERVER_SOFTWARE'] . '</strong></div>';
        echo '<div class="check">Document Root: <strong>' . $_SERVER['DOCUMENT_ROOT'] . '</strong></div>';
        echo '<div class="check">Script Filename: <strong>' . $_SERVER['SCRIPT_FILENAME'] . '</strong></div>';
        echo '</div>';

        echo '<div class="section">';
        echo '<h2>3. Directory Permissions</h2>';
        
        $directories = [
            'storage' => $rootPath . '/storage',
            'bootstrap/cache' => $rootPath . '/bootstrap/cache',
            'public' => $rootPath . '/public',
        ];
        
        foreach ($directories as $name => $path) {
            if (file_exists($path)) {
                $perms = substr(sprintf('%o', fileperms($path)), -4);
                $writable = is_writable($path) ? '<span class="success">Writable</span>' : '<span class="error">Not Writable</span>';
                echo '<div class="check">' . $name . ': ' . $perms . ' - ' . $writable . '</div>';
            } else {
                echo '<div class="check"><span class="error">✗</span> ' . $name . ' does not exist!</div>';
            }
        }
        echo '</div>';

        echo '<div class="section">';
        echo '<h2>4. Required PHP Extensions</h2>';
        
        $extensions = ['openssl', 'pdo', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'curl', 'fileinfo', 'gd'];
        
        foreach ($extensions as $ext) {
            $loaded = extension_loaded($ext);
            if ($loaded) {
                echo '<div class="check"><span class="success">✓</span> ' . $ext . '</div>';
            } else {
                echo '<div class="check"><span class="error">✗</span> ' . $ext . ' (missing)</div>';
            }
        }
        echo '</div>';

        echo '<div class="section">';
        echo '<h2>5. File Existence Check</h2>';
        
        $files = [
            'artisan' => $rootPath . '/artisan',
            'composer.json' => $rootPath . '/composer.json',
            'public/index.php' => $rootPath . '/public/index.php',
        ];
        
        foreach ($files as $name => $path) {
            if (file_exists($path)) {
                echo '<div class="check"><span class="success">✓</span> ' . $name . ' exists</div>';
            } else {
                echo '<div class="check"><span class="error">✗</span> ' . $name . ' is missing!</div>';
            }
        }
        echo '</div>';

        echo '<div class="section">';
        echo '<h2>6. POST Request Test</h2>';
        echo '<div class="check">Current Request Method: <strong>' . $_SERVER['REQUEST_METHOD'] . '</strong></div>';
        echo '<div class="check">Content Type: <strong>' . (isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : 'Not set') . '</strong></div>';
        echo '</div>';

        echo '<div class="section">';
        echo '<h2>7. Recommended Actions</h2>';
        echo '<pre>';
        echo "# Run these commands on your server:\n\n";
        echo "cd " . $rootPath . "\n";
        echo "chmod -R 775 storage\n";
        echo "chmod -R 775 bootstrap/cache\n";
        echo "php artisan config:clear\n";
        echo "php artisan cache:clear\n";
        echo "php artisan route:clear\n";
        echo "php artisan view:clear\n";
        echo '</pre>';
        echo '</div>';

        echo '<div class="delete-warning">';
        echo '<strong>⚠️ IMPORTANT:</strong> Delete this file (server-check.php) immediately after viewing this page for security reasons!';
        echo '</div>';
        ?>
    </div>
</body>
</html>
