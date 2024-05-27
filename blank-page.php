<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beverage Menu</title>
</head>
<body>

<div class="col" id="product-list">
    <div style="width:100%">
        <h5 style="margin-bottom:20px">Beverage</h5>
        <ul>
            <li style="list-style:none">
                <div class="row" style="margin-left:-40px; margin-top:0; margin-bottom:0; margin-right:10px">
                    <div style="width: 105px">
                        <img src="https://ebisoftware.com.my:9050/UserImages/2b503063-71fe-45bd-b3c7-8edd325e8192/SalesItem/7Up-Ice-Lemon-Mint-355ml-1194893-01.jpg" style="height: 100px; width: 100px; margin-left:0; align-self:flex-start; align-content:flex-start">
                    </div>
                    <div class="col">
                        <div class="row" style="padding: 0; margin-left: 5px; margin-right: 2px">
                            <h5 style="text-align: left; font-size:12px; font-weight: bold;">7Up Ice Lemon Mint 355ml</h5>
                        </div>
                        <div class="row" style="font-size: 14px; font-weight:bold; color:cornflowerblue; margin-left: 15px; margin-right: 2px">4.50</div>
                    </div>
                    <div style="width: 50px; margin-right:5px; justify-content:flex-end">
                        <button type="button" class="btn btn-primary" onclick="handleButtonClick(0)" style="font-size: 12px; font-weight: bold; border-radius: 20%">Add</button>
                    </div>
                </div>
            </li>
        </ul>
        <ul>
            <li style="list-style:none">
                <div class="row" style="margin-left:-40px; margin-top:0; margin-bottom:0; margin-right:10px">
                    <div style="width: 105px">
                        <img src="https://ebisoftware.com.my:9050/UserImages/2b503063-71fe-45bd-b3c7-8edd325e8192/SalesItem/Ceylon-Rinash-Cofee.jpg" style="height: 100px; width: 100px; margin-left:0; align-self:flex-start; align-content:flex-start">
                    </div>
                    <div class="col">
                        <div class="row" style="padding: 0; margin-left: 5px; margin-right: 2px">
                            <h5 style="text-align: left; font-size:12px; font-weight: bold;">Ceylon Rinash Cofee</h5>
                        </div>
                        <div class="row" style="font-size: 14px; font-weight:bold; color:cornflowerblue; margin-left: 15px; margin-right: 2px">8.90</div>
                    </div>
                    <div style="width: 50px; margin-right:5px; justify-content:flex-end">
                        <button type="button" class="btn btn-primary" onclick="handleButtonClick(1)" style="font-size: 12px; font-weight: bold; border-radius: 20%">Add</button>
                    </div>
                </div>
            </li>
        </ul>
        <ul>
            <li style="list-style:none">
                <div class="row" style="margin-left:-40px; margin-top:0; margin-bottom:0; margin-right:10px">
                    <div style="width: 105px">
                        <img src="https://ebisoftware.com.my:9050/UserImages/2b503063-71fe-45bd-b3c7-8edd325e8192/SalesItem/latee.jpg" style="height: 100px; width: 100px; margin-left:0; align-self:flex-start; align-content:flex-start">
                    </div>
                    <div class="col">
                        <div class="row" style="padding: 0; margin-left: 5px; margin-right: 2px">
                            <h5 style="text-align: left; font-size:12px; font-weight: bold;">Latee</h5>
                        </div>
                        <div class="row" style="font-size: 14px; font-weight:bold; color:cornflowerblue; margin-left: 15px; margin-right: 2px">8.90</div>
                    </div>
                    <div style="width: 50px; margin-right:5px; justify-content:flex-end">
                        <button type="button" class="btn btn-primary" onclick="handleButtonClick(2)" style="font-size: 12px; font-weight: bold; border-radius: 20%">Add</button>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>

<script>
    // 存储初始数据
    var originalData = [{
        productName: "7Up Ice Lemon Mint 355ml",
        productPrice: "4.50"
    },{
        productName: "Ceylon Rinash Cofee",
        productPrice: "8.90"
    },{
        productName: "Latee",
        productPrice: "8.90"
    }];

    // 处理按钮点击事件
    function handleButtonClick(index) {
        // 获取点击按钮对应的产品信息
        var product = originalData[index];
        // 输出产品信息到控制台
        console.log('Original Product Name:', product.productName);
        console.log('Original Product Price:', product.productPrice);
    }
</script>

</body>
</html>
