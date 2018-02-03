<?php

$clientId = '0646c401d36a3ea2bdf1b2e4625fca47139e37813c9ccefc426b65ec7cfa8aff4d177efb0a0d57be';
$clientSecret = 'chesszebra';

if (empty($_GET['code']) && empty($_GET['error'])) {
    header('Location: http://oauth.lichess.org.docker/oauth/authorize?state=12345&response_type=code&client_id=' . $clientId . '&scope=test%20test2%20read%20write');
    exit;
}

echo '<p><a href="/demo-client.php">AGAIN</a></p>';

if (!empty($_GET['error'])) {
    var_dump($_GET);
    exit;
}

$postFields = [
    'grant_type' => 'authorization_code',
    'client_id' => $clientId,
    'code' => $_GET['code'],
];

if ($clientSecret) {
    $postFields['client_secret'] = $clientSecret;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://oauth.lichess.org/oauth');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
$json = json_decode($result, true);

curl_close($ch);

echo $result;

var_dump($json);
exit;
