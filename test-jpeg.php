<?php
echo "<h2>JPEG Support Test</h2>";

// Check GD extension
if (extension_loaded('gd')) {
    echo "✅ GD extension is loaded<br>";
    $info = gd_info();
    echo "JPEG Support: " . ($info['JPEG Support'] ? "✅ Yes" : "❌ No") . "<br>";
    echo "PNG Support: " . ($info['PNG Support'] ? "✅ Yes" : "❌ No") . "<br>";
    echo "GIF Support: " . ($info['GIF Create Support'] ? "✅ Yes" : "❌ No") . "<br>";
    echo "WebP Support: " . ($info['WebP Support'] ? "✅ Yes" : "❌ No") . "<br>";
} else {
    echo "❌ GD extension is not loaded<br>";
}

// Check upload configuration
echo "<h3>Upload Configuration:</h3>";
echo "file_uploads: " . (ini_get('file_uploads') ? "✅ Enabled" : "❌ Disabled") . "<br>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "<br>";

// Check upload directory
echo "<h3>Upload Directory Status:</h3>";
$upload_dir = 'uploads/';
echo "Directory exists: " . (is_dir($upload_dir) ? "✅ Yes" : "❌ No") . "<br>";
echo "Directory writable: " . (is_writable($upload_dir) ? "✅ Yes" : "❌ No") . "<br>";
echo "Directory permissions: " . substr(sprintf('%o', fileperms($upload_dir)), -4) . "<br>";

// List supported MIME types
echo "<h3>Supported File Types:</h3>";
$allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$allowed_mimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

echo "Extensions: " . implode(', ', $allowed_types) . "<br>";
echo "MIME types: " . implode(', ', $allowed_mimes) . "<br>";

echo "<br><a href='admin-dashboard.php'>← Back to Admin Dashboard</a>";
?>
