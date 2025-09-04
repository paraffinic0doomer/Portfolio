<?php
// Test upload configuration
echo "<h3>PHP Upload Configuration:</h3>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "<br>";
echo "file_uploads: " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "<br>";
echo "upload_tmp_dir: " . ini_get('upload_tmp_dir') . "<br>";

echo "<h3>Directory Permissions:</h3>";
$upload_dir = 'uploads/';
if (!is_dir($upload_dir)) {
    if (mkdir($upload_dir, 0755, true)) {
        echo "Created uploads directory successfully<br>";
    } else {
        echo "Failed to create uploads directory<br>";
    }
} else {
    echo "Uploads directory already exists<br>";
}

if (is_writable($upload_dir)) {
    echo "Uploads directory is writable<br>";
} else {
    echo "Uploads directory is NOT writable<br>";
}

echo "<h3>Test Upload Form:</h3>";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['test_file'])) {
    echo "<h4>Upload Result:</h4>";
    echo "File name: " . $_FILES['test_file']['name'] . "<br>";
    echo "File size: " . $_FILES['test_file']['size'] . " bytes<br>";
    echo "File error: " . $_FILES['test_file']['error'] . "<br>";
    echo "File type: " . $_FILES['test_file']['type'] . "<br>";
    echo "Temp file: " . $_FILES['test_file']['tmp_name'] . "<br>";
    
    if ($_FILES['test_file']['error'] == 0) {
        $target = $upload_dir . 'test_' . time() . '.jpg';
        if (move_uploaded_file($_FILES['test_file']['tmp_name'], $target)) {
            echo "<span style='color: green;'>File uploaded successfully to: $target</span><br>";
        } else {
            echo "<span style='color: red;'>Failed to move uploaded file</span><br>";
        }
    } else {
        echo "<span style='color: red;'>Upload error code: " . $_FILES['test_file']['error'] . "</span><br>";
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="test_file" accept="image/*">
    <button type="submit">Test Upload</button>
</form>

<p><a href="admin-dashboard.php">Back to Admin Dashboard</a></p>
