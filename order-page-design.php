<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Malaysian Hotpot Ingredients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
      font-family: 'Arial', sans-serif;
      background-color: #f8f9fa;
      padding-top: 20px;
    }
    .card {
      border: none;
      border-radius: 15px;
      background-color: #fff;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
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
</head>
<body>

    <div class="container">
        <div class="card">
            <img src="https://assets.tmecosys.com/image/upload/t_web767x639/img/recipe/ras/Assets/91ddca01979057807546512dc2e237b8/Derivates/c33960b78e02359c64ae47ac469b0bfb05875d1e.jpg" alt="Malaysian Hotpot" class="product-img card-img-top" height="">
            <div class="card-body div-product-detial">
                <h3 class="card-title">Hot Pot</h3>
                <p class="card-text">Description</p>
                <p class="price">$ 25.99</p>
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
    </div>

<!-- 引入Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
