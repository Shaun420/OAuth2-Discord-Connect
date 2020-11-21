<?php
/*
Project: OAuth2-Discord-Connect
File: index.php
Author: Shaun420

Copyright 2020 Shaun420

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with the License.
You may obtain a copy of the License at

	http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and limitations under the License.
*/

// Keep variables ready for further requests.
// You can obtain your client id and client secret from https://discord.com/developers/applications
$client_id = "[YOUR_CLIENT_ID]";
$client_secret = "[YOUR_CLIENT_SECRET]";

// Use your website page as the value. It can be this same page or a different one.
// For example: "https://example.com/oauth"
$redirect_uri = "[YOUR_WEBSITE]";

// LOOKUP #1 -
// POST data to be sent along with HTTP request.
$data = "client_id={$client_id}&client_secret={$client_secret}&grant_type=authorization_code&code={$_GET["code"]}&redirect_uri={$redirect_uri}&scope=identify";

// This is required as per RFC 6749 (https://tools.ietf.org/html/rfc6749)
$header = array('Content-Type: application/x-www-form-urlencoded');

// Get the access token from code. The script obtains the code from GET parameter after discord user is shown authorization form.
$tokenrequest = curl_init();
curl_setopt($tokenrequest, CURLOPT_URL, "https://discord.com/api/oauth2/token");

// Receive returned information and errors if any.
curl_setopt($tokenrequest, CURLOPT_RETURNTRANSFER, true);

// Attach data and header to curl lookup.
curl_setopt($tokenrequest, CURLOPT_POSTFIELDS, $data);
curl_setopt($tokenrequest, CURLOPT_HTTPHEADER, $header);

// Save the result return from curl request.
$tokenResult = curl_exec($tokenrequest);
curl_close($tokenrequest);

// Parse the result in to an array.
$tokenData = json_decode($tokenResult);

// LOOKUP #2 -
// Prepare user lookup headers (with access token from previous lookup)
$userLookupHeader = array(
    'Content-Type: application/x-www-form-urlencoded',
    "Authorization: Bearer " . $tokenData->access_token,
);

// Initialize curl user lookup with discord API.
$userLookup = curl_init();
curl_setopt($userLookup, CURLOPT_URL, "https://discord.com/api/users/@me");
curl_setopt($userLookup, CURLOPT_HTTPHEADER, $userLookupHeader);
curl_setopt($userLookup, CURLOPT_RETURNTRANSFER, true);

// Execute the curl lookup and save the result.
$userLookupResult = curl_exec($userLookup);
curl_close($userLookup);

// Parse the result in to an array. This contains the user object of the authorized user.
// For more info. on user object - https://discord.com/developers/docs/resources/user#user-object
$userData = json_decode($userLookupResult);
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/php; charset=utf-8">
	<title>Discord Connect</title>
</head>
<body>
<h1>Welcome <?php echo $userData->username;?>!</h1>
<h3>ID: <?php echo $userData->id;?></h3>
</body>
</html>
