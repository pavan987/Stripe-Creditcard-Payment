<?php
define("STRIPE_SECRET_KEY","sk_test_hJm3UGJuw3GjhA58Ni33dL26");
define("STRIPE_TOKEN_ENDPOINT","https://api.stripe.com/v1/tokens");
define("STRIPE_CHARGE_ENDPOINT","https://api.stripe.com/v1/charges");

$header = ["Authorization: Bearer " . STRIPE_SECRET_KEY];
$token_data = [
	'card' => [
		'name' => $_POST['full-name'],
		'number' => $_POST['card-num'],
		'exp_month' => $_POST['exp-month'],
		"exp_year" => $_POST['exp-year'],
		"cvc" => $_POST['cvc'],
	]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, STRIPE_TOKEN_ENDPOINT);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_data));

$token_response = curl_exec($ch);

$token_response= json_decode($token_response, true);
// var_dump($token_response);
if (!isset($token_response['error'])){
	// Token Generated
	$charge_data = [
		'amount' => ($_POST['amount'] * 100),
		"currency" => "usd",
		"source" => $token_response['id'],
	];
	curl_setopt($ch, CURLOPT_URL, STRIPE_CHARGE_ENDPOINT);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($charge_data));
	$charge_response = curl_exec($ch);

	$charge_response= json_decode($charge_response, true);
	// var_dump($charge_response);
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Confirmation | USC Donations</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css">
</head>
<body>

	<div class="container">
		<div class="row">
			<h1 class="col-12 mt-4">Confirmation</h1>
		</div> <!-- .row -->
	</div> <!-- .container -->

	<div class="container">

		<div class="row mt-4">
			<div class="col-12">

				<?php
				if(isset($token_response['error']) && !empty($token_response['error'])) {
					echo $token_response['error']['message'];
				}
				elseif (isset($charge_response['error']) && !empty($charge_response['error'])) {
					echo $charge_response['error']['message'];
				}
				else {
					echo "Payment successful. Confirmation Number " . $charge_response['id'];
				}

				 ?>

			</div> <!-- .col -->
		</div> <!-- .row -->

		<div class="row mt-4 mb-4">
			<div class="col-12">
				<a href="donation_form.php" role="button" class="btn btn-primary">Submit Another Donation</a>
			</div> <!-- .col -->
		</div> <!-- .row -->

	</div> <!-- .container -->

</body>
</html>
