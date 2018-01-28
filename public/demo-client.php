<?php

$clientId = '6a537ae3700736c27bae67a8a702dba07612014365457c94b127f531383575e860b0d2d3118a66b4';
$clientSecret = 'cz';

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
