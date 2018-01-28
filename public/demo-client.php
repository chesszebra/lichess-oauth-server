<?php

$clientId = 'b4e072e609b5754cd51d88c70cbc83d974f198cc56f8c78519431a06e4876abda481470e8e6608bb';
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
