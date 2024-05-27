<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Order</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<style>
    body {
		font-family: 'Arial', sans-serif;
		background-color: #f8f9fa;
		padding-top: 20px;
    }
    .div-product{

    }
    .card {
		border: none;
		border-radius: 15px;
		background-color: #fff;
		box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
		transition: all 0.3s ease;
		height: 500px;
    }
    .card:hover {
  		transform: translateY(-5px);
    }
    .card img {
		border-top-left-radius: 15px;
		border-top-right-radius: 15px;
		max-width: 100%;
		height: auto;
    }
    .card-body {
		padding: 20px;
    }
    .card-title {
		font-size: 1.5rem;
		color: #333;
		margin-bottom: 15px;
    }
    .card-text {
		color: #666;
		margin-bottom: 20px;
    }
    .price {
		font-size: 1.2rem;
		color: #007bff;
    }
    .quantity-container {
		display: flex;
		align-items: center;
		justify-content: space-between;
    }
    .quantity {
		display: flex;
		align-items: center;
    }
    .quantity input {
		width: 40px;
		text-align: center;
		margin: 0 10px;
		border: 1px solid #ccc;
		border-radius: 5px;
    }
    .order-btn {
		background-color: #007bff;
		border-color: #007bff;
		border-radius: 5px;
    }
    .order-btn:hover {
		background-color: #0056b3;
		border-color: #0056b3;
    }
    .container{
        max-width: 100%;
    }
    /* 媒体查询 */
    @media (min-width: 500px) {
        .div-image{

        }
        .div-product-detial{

        }
        .product-img {
            width: 200px;
        }
    }
    @media (min-width: 768px) {
        .product-img {
            width: 100%;
        }
        .card {
            width: 260px;
        }
    }
</style>
<body>
	<div>
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
	</div>

	<div class="container mt-3">
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
		<span>Order Product</span>
		<div class="div-product">
			<table width="90%" style="margin: 0 auto;">
			    <tr>
			        <?php $count = 0; ?>
			        <?php foreach ($responseData['result'] as $key => $value) { ?>
			            <td align="center" width="25%">
			                <div class="card d-inline-block mb-3">
			                    <img src="https://assets.tmecosys.com/image/upload/t_web767x639/img/recipe/ras/Assets/91ddca01979057807546512dc2e237b8/Derivates/c33960b78e02359c64ae47ac469b0bfb05875d1e.jpg" alt="Malaysian Hotpot" class="product-img card-img-top" height="">
			                    <div class="card-body div-product-detial">
			                        <h3 class="card-title"><?php echo $value['AccountName'];?></h3>
			                        <p class="card-text">Description</p>
			                        <p class="price">$ <?php echo $value['SalesPrice'];?></p>
			                        <div class="quantity-container">
			                            <div class="quantity">
			                                <button class="btn btn-secondary decrement-btn">-</button>
			                                <input type="text" class="form-control quantity-input" value="1">
			                                <button class="btn btn-secondary increment-btn">+</button>
			                            </div>
			                            <button class="btn btn-primary order-btn">Order</button>
			                        </div>
			                    </div>
			                </div>
			            </td>
			            <?php $count++; ?>
			            <?php if ($count % 4 == 0) { ?>
			                </tr><tr>
			            <?php } ?>
			        <?php } ?>
			    </tr>
			</table>
	    </div>
    </div>
	
<script>
document.addEventListener("DOMContentLoaded", function() {
    const incrementBtns = document.querySelectorAll(".increment-btn");
    const decrementBtns = document.querySelectorAll(".decrement-btn");

    incrementBtns.forEach(btn => {
        btn.addEventListener("click", function() {
            const input = btn.parentElement.querySelector(".quantity-input");
            let value = parseInt(input.value);
            value++;
            input.value = value;
        });
    });

    decrementBtns.forEach(btn => {
        btn.addEventListener("click", function() {
            const input = btn.parentElement.querySelector(".quantity-input");
            let value = parseInt(input.value);
            if (value > 1) {
                value--;
                input.value = value;
            }
        });
    });
});
</script>
</body>
</html>