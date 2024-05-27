<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ebi get data</title>
</head>
<body>
	<div>
		<?php
			function displayArray($data, $prefix = '') {
			    foreach ($data as $key => $value) {
			    	if (is_bool($value)) {
			            $value = $value ? 'true' : 'false';
			        }
			        if (is_array($value)) {
			            displayArray($value, $prefix . $key . '.');
			        } else {
			            echo '<tr>';
			            echo '<td valign="top">' . $prefix . $key . '</td>';
			            echo '<td valign="top" align="center">:</td>';
			            echo '<td valign="top">' . $value . '</td>';
			            echo '</tr>';
			        }
			    }
			}
		?>
		<?php
			$url = 'https://ebisoftware.com.my:5000/api/Account/Login';
			$data = array(
			    'email' => 'tqteoh@yslesolutions.com', // Replace with actual email
			    'password' => '1234',
			    'rememberMe' => true,
			    'returnUrl' => 'string' // Replace with actual return URL
			);

			$payload = json_encode($data);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$result = curl_exec($ch);

			curl_close($ch);

			// echo $result;

			$responseData = json_decode($result, true);
			$accessToken = $responseData['result']['AuthResponse']['AccessToken'];
		?>
		<span>Login Authorisation</span>
		<table style="table-layout: fixed; width: 95%; word-wrap: break-word; text-align: justify;">
			<tr>
				<th width="230">Field</th>
				<th width="10"></th>
				<th>Return Data</th>
			</tr>
			<?php displayArray($responseData); ?>
		</table>
	</div>

	<div style="margin-top:1rem;">
		<?php
			$url = 'https://ebisoftware.com.my:5000/api/Inventory/LoadProxyByActive';
			$data = array(
			    'id' => 'string' // Replace 'string' with the actual ID value
			);
			$headers = array(
			    'Content-Type: application/json-patch+json',
			    'Authorization: Bearer '.$accessToken.'' // Replace 'YourAccessTokenHere' with your actual access token
			);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$response = curl_exec($ch);
			curl_close($ch);

			$responseData = json_decode($response, true);
		?>
		<span>Get Stock</span>
		<table style="table-layout: fixed; width: 95%; word-wrap: break-word; text-align: justify;">
			<tr>
				<th width="230">Field</th>
				<th width="10"></th>
				<th>Return Data</th>
			</tr>
			<?php displayArray($responseData); ?>
		</table>
	</div>

</body>
</html>