<!DOCTYPE html>
<html>
<head>
<title>Food Ordering</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<!-- <link rel="icon" type="image/x-icon" href="favicon.ico"> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?php
  $protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'];
  $uri = $_SERVER['REQUEST_URI'];
  $base_url = "$protocol://$host/ebicloud/";
?>
<link rel="stylesheet" href="<?php echo $base_url; ?>css/style.css">
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include Toastr CSS and JS -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="icon" type="image/png" href="images/cart.png">

</head>
<body>
  <?php 

      //Get Data from URL
      $currentURL = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
      $lastSlashPosition = strrpos($currentURL, "/");
      $strOrderToken = substr($currentURL, $lastSlashPosition + 1);
      $strOrderInfo = base64_decode($strOrderToken);
      $arrOrderInfo = explode("|", $strOrderInfo);
      $strOrderRefNo = $arrOrderInfo[0];
      $obfuscated_strOrderRefNo = base64_encode(json_encode($strOrderRefNo));
      $strCompanyCode = $arrOrderInfo[1];
      $obfuscated_strCompanyCode = base64_encode(json_encode($strCompanyCode));
      $strEndPoint = $arrOrderInfo[2]; 
      $obfuscated_strEndPoint = base64_encode(json_encode($strEndPoint));


      //Get token data from API
      $url = "{$strEndPoint}/api/Order/LoadRecord/{$strOrderRefNo}/{$strCompanyCode}";
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'accept: application/json'
      ));
      $response = curl_exec($ch);
      if(curl_errno($ch)){
          echo 'Curl error: ' . curl_error($ch);
      }
      curl_close($ch);
      // echo "<b>Json<br></b>".$response;
      $tokenresponseData = json_decode($response, true);
      $obfuscated_data = base64_encode($tokenresponseData);

      $token =  $tokenresponseData['result']['token'];

      //Get food category detail from API
      $url = "{$strEndPoint}/api/OrderInventory/LoadItemGroupsAsync/{$strOrderRefNo}/{$strCompanyCode}";
      // echo $url."<br>";
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'accept: application/json',
          'Authorization: '.$token
      ));
      $response = curl_exec($ch);
      if(curl_errno($ch)){
          echo 'Curl error: ' . curl_error($ch);
      }
      curl_close($ch);
      // echo "<b>Json<br></b>".$response;
      $food_category_detail_responseData = json_decode($response, true);
      // print_r($food_category_detail_responseData);

      //Get food item data from API
      $url = "{$strEndPoint}/api/OrderInventory/LoadProxyByItemGroupAsync/{$strOrderRefNo}/{$strCompanyCode}";
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'accept: application/json',
          'Authorization: '.$token
      ));
      $response = curl_exec($ch);
      if(curl_errno($ch)){
          echo 'Curl error: ' . curl_error($ch);
      }
      curl_close($ch);
      // echo "<b>Json<br></b>".$response;
      $food_item_responseData = json_decode($response, true);
      $obfuscated_food_data = base64_encode(json_encode($food_item_responseData));
      
      $condimentGroupIds = [];

      if (isset($food_item_responseData['result']) && is_array($food_item_responseData['result'])) {
         
          foreach ($food_item_responseData['result'] as $groupKey => $groupItems) {
              
              foreach ($groupItems as $itemData) {
                  
                  if (isset($itemData['condimentGroupID']) && !empty($itemData['condimentGroupID'])) {
                      
                      $condiments = explode(',', $itemData['condimentGroupID']);

                      foreach ($condiments as $condiment) {
                          $condiment = trim($condiment); 
                          if (!in_array($condiment, $condimentGroupIds)) {
                              $condimentGroupIds[] = $condiment;
                          }
                      }

                      // echo "Food Condiment for {$itemData['accountName']}: " . implode(",", $condiments) . "<br>"; 
                  } else {
                      // echo "No condiment group ID found for {$itemData['accountName']}<br>"; 
                  }
              }
          }
          
          $combinedCondiments = implode(",", $condimentGroupIds);
          // echo "Combined Food Condiments: " . $combinedCondiments . "<br>"; 
      } else {
          // echo "No data found in the result section.";
      }

      //Get food condiment detail from API
      $url = "{$strEndPoint}/api/OrderCondimentGroup/LoadProxyByParentIDAsync/{$combinedCondiments}/{$strCompanyCode}/{$strOrderRefNo}";
      // echo $url."<br>";

      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'accept: application/json',
          'Authorization: '.$token
      ));
      $response = curl_exec($ch);
      if(curl_errno($ch)){
          // echo 'Curl error: ' . curl_error($ch);
      }
      curl_close($ch);
      // echo "<b>Json<br></b>".$response;
      $food_condiment_detail_responseData = json_decode($response, true);
      $obfuscated_food_condiment_data = base64_encode(json_encode($food_condiment_detail_responseData));

      $currentTime = new DateTime('now', new DateTimeZone('UTC'));
      $serviceTimeFrom =  $currentTime->format('Y-m-d\TH:i:s.u\Z');

  ?>    

        <!-- Navigation Bar -->
        <div class="navbar">
            <div class="hamburger-menu" onclick="toggleMenu()">☰</div>
            <div class="nav-items"> 
                <?php
                if (isset($food_category_detail_responseData['result']) && is_array($food_category_detail_responseData['result'])) {
                    foreach ($food_category_detail_responseData['result'] as $key => $categoryName) {
                        ?>
                        <div class="nav-item">
                            <a class="nav-link" href="#<?php echo htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8'); ?>"
                                data-category-name="<?php echo htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </div>
                        <?php
                    }
                } else {
                    echo "<div class='nav-item'>No categories available</div>";
                }
                ?>
            </div>
        </div>
        
        <!-- Display menu -->
        <div class="main-content" >
            <div class="w3-row-padding" id="food-items-display" style="width: 100%;">
                <?php
                    if (isset($food_item_responseData['result']) && is_array($food_item_responseData['result'])) {
                        $itemGroupNames = array_column(array_map('reset', $food_item_responseData['result']), 'itemGroupName');
                        array_multisort($itemGroupNames, SORT_ASC, $food_item_responseData['result']);
                        $buttonCounter = 1;
                        $usedItemGroupNames = [];
                        $itemCount = 0;

                        echo '<div id="table-container">';
                        foreach ($food_item_responseData['result'] as $key => $valueGroup) {
                            foreach ($valueGroup as $value) {

                                $itemGroupName = htmlspecialchars($value['itemGroupName'], ENT_QUOTES, 'UTF-8');
                                if (!in_array($itemGroupName, $usedItemGroupNames)) {
                                    $usedItemGroupNames[] = $itemGroupName;
                                }

                ?>
                <div class="responsive-item">
                    <table width="100%">
                        <tr>
                            <td width="50%"><img src="https://teamspacedigital.com/ebicloud/images/default.jpg" id="<?php echo $itemGroupName; ?>" alt="Default Image" class="product-img card-img-top" /></td>
                            <td width="50%">
                                <div>
                                    <table class="description-table">
                                        <tr>
                                            <td class="td-1">
                                              <b><span class="title_display_1"><?php echo htmlspecialchars($value['accountName'], ENT_QUOTES, 'UTF-8'); ?></span></b><br>
                                              <span>RM <?php echo htmlspecialchars(number_format($value['salesPrice'], 2), ENT_QUOTES, 'UTF-8'); ?></span>
                                            </td>
                                            <td class="td-2">
                                              <!-- <button class="button_menu2_1 button2 open-new-modal-btn">+</button> -->
                                              <div class="button-container"></div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>   
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
                            $itemCount++;
                        }
                    }

                    echo '</div>';

                    $jsonData = json_encode($itemData);
                    $obfuscated_itemData = base64_encode($jsonData);
                } else {
                    echo "No data found.";
                }
                ?>
            </div>
        </div>

        <!-- Display button cart & checkout -->
        <div id="div-button">
            <table id="table-button">
                <tr>
                    <td class="left-cell">
                        <div id="myBtn" class="left-cell-content">
                            <div class="notification-counter">
                                <i class="fas fa-shopping-cart" style="font-size:24px;"></i>
                                <span class="counter"></span>
                            </div>
                            <span style="margin-left: 10px;">RM</span>
                            <span style="font-size: 24px;margin-left: 13px;" class="total_price"></span>
                        </div>
                    </td>
                    <td id="checkout" class="right-cell">
                        <div id="openCustomModal" class="right-cell-content">
                            <span style="font-size: 22px;margin-left: 13px;">Checkout</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div id="scroll-up-link">
            <div id="scroll-up">
              <i class="fa fa-arrow-up" aria-hidden="true" style="font-size: 15px;"></i>
            </div>
          <p style="text-align: center;">Back to Top</p>
        </div>

        <!-- Modal Cart -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="close">&times;</span>
                    <h4>Food Cart</h4>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>

        <!-- Modal Checkout -->
        <div id="customModal" class="custom-modal">
          <div class="custom-modal-content">
            <div class="custom-modal-header">
              <span class="custom-close">&times;</span>
              <h4>Checkout</h4>
            </div>
            <div class="custom-modal-body">
            </div>
          </div>
        </div>

         <!-- Modal Condiment -->
        <div id="newModal" class="new-modal">
            <div class="new-modal-content">
                <div class="new-modal-header">
                    <span class="new-close">&times;</span>
                    <div class="header-content"><h4>Condiments</h4></div>
                </div>
                <div class="new-modal-body">
                </div>
            </div>
        </div>
        
<script>
    var cartData = {};
    var foodData2 = [];
    var orderRefNo = '';
    var companyCode = '';
    var endPoint = '';
    var token = '';
    let cartItems = [];

    document.addEventListener('DOMContentLoaded', function () {
        const newUrl = window.location.href.replace('order-menu-pages.php', 'getApi.php');
        const buttonContainers = document.querySelectorAll('.button-container');

        fetch(newUrl + '?get_details=true')
          .then(response => {
              if (!response.ok) {
                  throw new Error('Network response was not ok');
              }
              return response.json();
          })
          .then(data => {

              orderRefNo = data.orderRefNo;
              companyCode = data.CompanyCode; 
              endPoint = data.EndPoint;
              token = data.token;

          })
          .catch(error => {
              // Handle any errors that occurred during the fetch
              console.error('Fetch error:', error);
          });
        fetch(newUrl + '?get_menu=true')
            .then(response => response.json())
            .then(menu => {
                if (menu && menu.result) {
                    const menuItems = Object.values(menu.result).flat();
                    foodData2 = menuItems;
                    // console.log(foodData2);
                    menuItems.forEach((item, index) => {
                        const orderMasterAccountID = item.orderMasterAccountID;
                        const buttonContainer = buttonContainers[index % buttonContainers.length];
                        const button = createButton('+', buttonContainer);

                        button.addEventListener('click', function () {
                          cartData = {};
                          cartItems = [];
                            fetch(newUrl + '?index=' + orderMasterAccountID)
                                .then(response => response.json())
                                .then(data => {
                                    renderModalContent(data);
                                    openModal();
                                })
                                .catch(error => console.error('Error fetching data:', error));
                        });
                    });
                } else {
                    console.error('Menu result is not defined:', menu.result);
                }
            })
            .catch(error => {
                console.error('Error fetching menu data:', error);
            });

        function createButton(text, container) {
            const button = document.createElement('button');
            button.textContent = text;
            button.classList.add('menu-button', 'open-new-modal-btn');
            container.appendChild(button);
            return button;
        }

        function renderModalContent(data) {
            const itemId = data.displayCode;
            const itemName = data.accountName;
            const itemPrice = parseFloat(data.salesPrice).toFixed(2);
            const itemImageUrl = "https://assets.tmecosys.com/image/upload/t_web767x639/img/recipe/ras/Assets/91ddca01979057807546512dc2e237b8/Derivates/c33960b78e02359c64ae47ac469b0bfb05875d1e.jpg";
            const condimentGroupID = data.condimentGroupID;

            const modalBody = document.querySelector('.new-modal-body');
            modalBody.innerHTML = `
                <img src="${itemImageUrl}" style="width: 120px; height: 80px; display: block; margin: 0 auto; margin-top:20px;">
                <p style="text-align: center;"><b>${itemName}</b><br>RM ${itemPrice}</p>
            `;

            const addButton = document.createElement('button');
            addButton.textContent = 'Add to Cart';
            addButton.classList.add('button-add-to-cart', 'addcart-btn', 'button1');
            addButton.style.cursor = 'pointer';
            addButton.style.border = 'none';
            addButton.style.backgroundColor = 'red';
            addButton.style.color = 'white';
            addButton.style.padding = '5px 10px';
            addButton.style.borderRadius = '5px';
            addButton.style.width = '100%';
            addButton.style.marginBottom = '30px';

            function addToCart() {
                const selectedCondiments = modalBody.querySelectorAll('input[type="radio"]:checked');
                let condimentIDs = [];
                let formattedPrice = null;

                if (selectedCondiments.length > 0) {
                    formattedPrice = 0;
                    selectedCondiments.forEach(selectedCondiment => {
                        const condimentContainer = selectedCondiment.closest('.condiment-item');
                        const [condimentID, condimentPrice] = selectedCondiment.value.split('_');
                        condimentIDs.push(condimentID);
                        formattedPrice += parseFloat(condimentPrice);
                    });
                }


                 cartData = {
                    itemId: itemId,
                    itemName: itemName,
                    itemPrice: itemPrice,
                    condimentID: condimentIDs.join(','), 
                    condimentPrice: formattedPrice,
                };

                addToCartFunction(cartData);
            }

        addButton.addEventListener('click', addToCart);
            if (condimentGroupID) {
                fetch(newUrl + '?get_condiment=true')
                    .then(response => response.json())
                    .then(condiments => {
                        const condimentGroup = condiments.filter(condiment => condimentGroupID.includes(condiment.condimentGroupID));
                        // Render condiments if available
                        if (condimentGroup.length > 0) {
                            condimentGroup.forEach(category => {
                                // Render each category's condiments
                                const categoryTitle = `<p style="font-weight: bold; text-align: left;">${category.condimentGroupID}</p>`;
                                let condimentRows = category.lstCondiment.map(condiment => {
                                    const formattedPrice = `+ RM ${parseFloat(condiment.condimentAddOnPrice).toFixed(2)}`;
                                    return `
                                        <div id="condiment_${condiment.autoID}" class="condiment-item">
                                          <table width="100%">
                                              <tr>
                                                  <td width="50%">
                                                      <table>
                                                          <tr>
                                                              <td width="10%" style="text-align:left;">
                                                                  <input type="radio" name="condiment_${category.condimentGroupID}" value="${condiment.condimentID}_${condiment.condimentAddOnPrice}" id="condiment_${condiment.autoID}" style="cursor: pointer;">
                                                              </td>
                                                              <td width="70%">
                                                                  <label for="condiment_${condiment.autoID}" style="cursor: pointer;">${condiment.condimentName}</label>
                                                              </td>
                                                          </tr>
                                                      </table>
                                                  </td>
                                                  <td width="50%" style="text-align:right;">
                                                      <label for="condiment_${condiment.autoID}" style="cursor: pointer;">${formattedPrice}</label>
                                                  </td>
                                              </tr>
                                          </table>
                                      </div>

                                    `;
                                }).join('');
                                modalBody.innerHTML += categoryTitle + condimentRows;
                            });
                        }
                        modalBody.appendChild(addButton);
                    })
                    .catch(error => console.error('Error fetching condiments:', error));
            } else {
                modalBody.appendChild(addButton);
            }
        }

        function openModal() {
            const newModal = document.getElementById("newModal");
            if (newModal) {
                newModal.style.display = "block";
            }
        }

        document.addEventListener('click', function (event) {
            const newModal = document.getElementById("newModal");
            const closeButton = document.querySelector(".new-close");
            if (closeButton && event.target === closeButton) {
                newModal.style.display = "none";
            }
        });

        function addToCartFunction(cartData) {
          
            // console.log('Adding to cart:', cartData);
        }

    });

    //Scroll up button for mobile
    document.addEventListener('DOMContentLoaded', function() {
      var scrollUpLink = document.getElementById('scroll-up-link');
      var scrollUpButton = document.getElementById('scroll-up');

      scrollUpLink.addEventListener('click', function() {
        document.body.classList.add('clicked');
        setTimeout(function() {
          document.body.classList.remove('clicked');
        }, 500);
        window.scrollTo({top: 0, behavior: 'smooth'});
      });

      window.addEventListener('scroll', function() {
        if (window.scrollY > 100) {
          scrollUpButton.classList.add('show');
        } else {
          scrollUpButton.classList.remove('show');
        }
      });
    });

    //toggle button for mobile
    function toggleMenu() {
      var navbar = document.querySelector('.navbar');
      navbar.classList.toggle('show');
    }

    //Button add to cart (Modal)
    var customModal = document.getElementById("customModal");
    var openCustomModal = document.getElementById("openCustomModal");
    var customClose = document.getElementsByClassName("custom-close")[0];
    openCustomModal.onclick = function() {
      customModal.style.display = "block";
    }
    customClose.onclick = function() {
      customModal.style.display = "none";
    }
    window.onclick = function(event) {
      if (event.target == customModal) {
        customModal.style.display = "none";
      }
    }

    //Button grey cart (Modal)
    var modal = document.getElementById("myModal");
    var btn = document.getElementById("myBtn");
    var span = document.getElementsByClassName("close")[0];
    btn.onclick = function() {
      modal.style.display = "block";
    }
    span.onclick = function() {
      modal.style.display = "none";
    }
    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }

    // Active the navigation when click
    document.addEventListener('DOMContentLoaded', function() {
        var navItems = document.querySelectorAll('.nav-item');

        // Set the first item as active by default
        navItems[0].style.backgroundColor = '#cc0000';

        navItems.forEach(function(navItem) {
            navItem.addEventListener('click', function() {
                // Reset all backgrounds to #333
                navItems.forEach(function(item) {
                    item.style.backgroundColor = '#333';
                });
                navItem.style.backgroundColor = '#cc0000';
            });
        });
    });

</script>

<script type="text/javascript">
    $(document).ready(function() {

    });

    var itemData2 = '';
    // var orderRefNo = '';
    // var companyCode = '';
    // var endPoint = '';
    // var serviceTimeFrom = '';
    // var token = '';
    var foodCondimentDetail = '';
    var addtocartmenu = [];
    var addtocartmenuArray = [];
    var orderIdCounter = 0;
    var cart_count = 0;
    var total_price = 0;  
    var itemQuantities = {};
    var item = [];
    var allCondiments = [];
    var cartDataMap = {};
    serviceTimeFrom = "<?php echo $serviceTimeFrom; ?>";
    $('.counter').text(cart_count);
    $('.total_price').text(total_price.toFixed(2)); 


    

    function addToCart(cartData) {
        cartItems.push(cartData);
    }

    $(document).on('click', '.addcart-btn', function() {
        addToCart(cartData);

        // console.log('Cart Items:', cartItems);

        for (var i = 0; i < cartItems.length; i++) {
            var cartItem = cartItems[i];
            var totalPrice = parseFloat(cartItem.itemPrice) + parseFloat(cartItem.condimentPrice);
            var addtocartmenu = {
                order_id: orderIdCounter++,
                id_item: cartItem.itemId,
                price: parseFloat(cartItem.itemPrice), 
                condiment: cartItem.condimentID,
                condimentAddOnPrices: parseFloat(cartItem.condimentPrice), 
                quantity: 1,
                totalPrice: totalPrice,
            };

            addtocartmenuArray.push(addtocartmenu);
        }

        // console.log('addtocartmenuArray:', addtocartmenuArray);
        $('#newModal').hide();
        var total_price = addtocartmenuArray.reduce((sum, item) => sum + item.totalPrice, 0);
        var total_quantity = addtocartmenuArray.reduce((sum, item) => sum + item.quantity, 0);

        $('.counter').text(total_quantity); 
        $('.total_price').text(total_price.toFixed(2)); 

        toastr.success('Item successfully added!', 'Success', {closeButton: false,progressBar: true,positionClass: 'toast-top-center',timeOut: 800,showMethod: 'fadeIn',hideMethod: 'fadeOut',});
    });

 
  //Open Modal add to cart
  $(document).on('click', '#myBtn', function() {
        
      $('.modal-body').empty();

      if (addtocartmenuArray.length === 0) {
          $('.modal-body').append('<p>No Item in Cart</p>'); 
      } else {
          var quantity = 0;
          var button_checkout = $('<button>').addClass('my-button-class').text('Checkout').css({'background-color': 'lightblue', 'border': '1px solid black', 'padding': '10px','cursor': 'pointer'});
          var table = $('<table>').addClass('cart-table').css({'margin': 'auto', 'border-collapse': 'separate', 'border-spacing': '5px','margin-top':'10px'});
          var thead = $('<thead>');
          var tbody = $('<tbody>');
          var headerRow = $('<tr>');
          headerRow.append($('<th>').text('').addClass('image_cart_title'));
          headerRow.append($('<th>').text('Name'));
          headerRow.append($('<th>').text('Quantity'));
          headerRow.append($('<th>').text('Total'));
          headerRow.append($('<th>').text(''));
          // thead.append(headerRow);
          var foodItemsArray = foodData2;
          var totalSum = 0;
          // console.log(foodData2);
          console.log(addtocartmenuArray);
          addtocartmenuArray.forEach(item => {
              console.log(item.id_item);
              console.log(foodItemsArray);
              var matchingItem = foodItemsArray.find(matchingItem => matchingItem.displayCode === item.id_item);
              // console.log(matchingItem);

              var row = $('<tr>');
              var img = $('<img>').attr('src', 'https://assets.tmecosys.com/image/upload/t_web767x639/img/recipe/ras/Assets/91ddca01979057807546512dc2e237b8/Derivates/c33960b78e02359c64ae47ac469b0bfb05875d1e.jpg').css({'width': '30px','height': '30px'});
              var imageCell = $('<td>').append(img).css({'padding': '3px','margin': '3px'}).addClass('image_cart_cell');
              row.append(imageCell);

              var nameCell = $('<td>').css({'padding': '5px', 'margin': '3px'});
              nameCell.append(matchingItem.accountName);  

              if (item.condiment) {
                  var splitCondiments = item.condiment.split(','); 
                  
                  var extractedValues = [];
                  for (var i = 0; i < splitCondiments.length; i++) {
                      var keyValue = splitCondiments[i].split('-');
                      extractedValues.push(keyValue[1]);
                  }
                  var concatenatedValues = extractedValues.join(', ');
                  // console.log(concatenatedValues);
                  nameCell.append($('<br>')); 
                  nameCell.append("<i>*" + concatenatedValues + "</i>"); 

              }

              // if (item.remark) {
              //     nameCell.append($('<br>')); 
              //     nameCell.append("<i>*"+item.remark+"</i>"); 
              // }

              row.append(nameCell);

              var quantityControl = $('<div>').addClass('quantity-control').css({'display': 'flex','align-items': 'center','gap': '5px' });

              var minusButton = $('<button>').addClass('minus').text(' - ').css({ 'padding-left': '5px', 'padding-right': '5px' , 'cursor': 'pointer' }).data({
                  id: item.order_id,
                  price: item.price,
                  name: item.name,
                  condiments: item.condiments || null,
                  // remark: item.remark || null
              });


              var plusButton = $('<button>').addClass('plus').text(' + ').css({ 'padding-left': '3px', 'padding-right': '3px' , 'cursor': 'pointer'}).data({
                  id: item.order_id,
                  price: item.price,
                  name: item.name,
                  condiments: item.condiment || null,
                  // remark: item.remark || null
              });

              quantityControl.append(minusButton);
              quantityControl.append($('<span>').text(item.quantity));
              quantityControl.append(plusButton);

              row.append($('<td>').append(quantityControl).css({'padding': '5px','margin': '3px'}));

              row.append($('<td>').text(item.totalPrice.toFixed(2)).css({'padding': '5px','margin': '3px'}));

              var trashButton = $('<button>').addClass('trash').text('🗑️').css({'padding': '5px', 'background-color': 'transparent', 'border': 'none', 'outline': 'none', 'cursor': 'pointer', }).data('itemId', item.order_id); 

              row.append($('<td>').append(trashButton).css({'padding': '5px','margin': '3px'}));

              totalSum += item.totalPrice;

              tbody.append(row);
          });

          table.append(thead).append(tbody);

          var totalRow = $('<tr>').css({'font-weight': 'bold'});
          
          totalRow.append($('<td>').attr('colspan', 3).text('Total:'));
          totalRow.append($('<td>').attr('colspan', 2).text('RM '+totalSum.toFixed(2)));

          tbody.append(totalRow);

          $('.modal-body').append(table); 

         var checkout_button = $('<button>')  
            .addClass('checkout') 
            .css({   
                'text-align': 'center',
                'cursor': 'pointer', 
                'height': '30px', 
                'width': '120px', 
                'display': 'inline-block' 
            })
            .text('Go to Checkout');

            var buttonContainer = $('<div>')  
            .css({  
                'display': 'flex', 
                'justify-content': 'center', 
                'align-items': 'center', 
                'margin': '10px 0' 
            });

            buttonContainer.append(checkout_button);  

          $('.modal-body').append(buttonContainer); 

          

          $('.close, .custom-close').click(function() {
              $(this).closest('.modal, .custom-modal').hide();
          });
      }
  });
  
  //Plus quantity
  $(document).on('click', '.plus', function() {
      var order_id = $(this).data('id'); 
      var item = addtocartmenuArray.find(it => it.order_id === order_id);
      if (item) {
          total_price_per_unit = item.price+item.condimentAddOnPrices;
          item.quantity += 1;
          item.totalPrice = item.totalPrice + total_price_per_unit;
          $(this).siblings('span').text(item.quantity); 
          $(this).closest('tr').find('td').eq(3).text(item.totalPrice.toFixed(2));
      }
      recalculateCartTotal(); 
  });

  //Minus quantity
  $(document).on('click', '.minus', function() {
      var order_id = $(this).data('id');
      var item = addtocartmenuArray.find(it => it.order_id === order_id);

      if (item && item.quantity > 1) { 
          total_price_per_unit = item.price+item.condimentAddOnPrices;
          item.quantity -= 1;
          item.totalPrice = item.totalPrice - total_price_per_unit;
          $(this).siblings('span').text(item.quantity); 
          $(this).closest('tr').find('td').eq(3).text(item.totalPrice.toFixed(2)); 
      }
      recalculateCartTotal(); 
  });

  //remove row
  $(document).on('click', '.trash', function() {
      var order_id = $(this).data('itemId'); 
      addtocartmenuArray = addtocartmenuArray.filter(it => it.order_id !== order_id);
      $(this).closest('tr').remove();
      recalculateCartTotal(); 
  });

  function recalculateCartTotal() {
      var totalSum = addtocartmenuArray.reduce((sum, item) => sum + item.totalPrice, 0);
      var totalQuantity = addtocartmenuArray.reduce((total, item) => total + item.quantity, 0);
      $('.counter').text(totalQuantity);
      if (totalSum === 0) {
          $('.modal-body').empty();
          $('.modal-body').append('<p>No Item in Cart</p>'); 
          $('.total_price').text(totalSum.toFixed(2)); 
      } else {
          $('.cart-table tbody tr:last-child td').eq(1).text('RM '+totalSum.toFixed(2)); 
          $('.total_price').text(totalSum.toFixed(2));
      }
  }

  $(document).on('click', '.checkout', function() {
      $('#myModal').hide();
      $('#openCustomModal').trigger('click');
  });

  //Open Modal Submit
  $(document).on('click', '#openCustomModal', function() {
      var totalSum = addtocartmenuArray.reduce((sum, item) => sum + item.totalPrice, 0);

      $('.custom-modal-body').empty();
      cart_count = addtocartmenuArray.length;

      if (cart_count === 0) {
          $('.custom-modal-body').append('<div class="modal-content-padding">No Item in Cart</div>');
      } else {
          var formattedTotalSum = totalSum.toFixed(2);
          $('.custom-modal-body').append('<div class="modal-content-padding">Your Order Total is <b>RM' + formattedTotalSum + '</b></div>');
          $('.custom-modal-body').append('<div class="modal-content-padding"><button id="submit_order">Place Order</button></div>');
      }
  });


  let totalCondimentPrice = 0;
  $(document).on('click', '#submit_order', function() {

      const companyName = "ABC Company";
      const now = new Date();
      const options = {
          weekday: 'long',
          day: 'numeric',
          month: 'long',
          year: 'numeric',
      };
      const formatter = new Intl.DateTimeFormat('en-GB', options);
      const formattedDate = formatter.format(now);
      const orderTime = now.toLocaleTimeString();
      
      // Ensure `addtocartmenu` is defined
      if (typeof addtocartmenuArray === 'undefined' || addtocartmenuArray.length === 0) {
          console.warn("Cart is empty.");
          return;
      }

      var foodItemsArray = foodData2;

      Swal.fire({
          title: 'Order Submitted!',
          text: 'Your order has been submitted successfully. Click OK to view the receipt.',
          icon: 'success',
          confirmButtonText: 'OK'
      }).then(() => {
          const orderDetails = 
          addtocartmenuArray.map(item => {
                  
                  let memo = ''; 
                  if (item.condiment && item.condiment.trim() !== '') {
                      const condimentParts = item.condiment.split(',');
                      const extractCondiment = part => {
                          const parts = part.split('-');
                          return parts.length > 1 ? parts[1].trim() : ''; 
                      };
                      memo = condimentParts.map(extractCondiment).join(',');
                  }

                  const itemTotalCondimentPrice = item.condimentAddOnPrices;

                  totalCondimentPrice += itemTotalCondimentPrice;

                  // console.log(totalCondimentPrice);
                  var matchingItem = foodItemsArray.find(matchingItem => matchingItem.displayCode === item.id_item);

                  if (!matchingItem) {
                      console.error(`No matching item found for display code: ${item.id_item}`);
                      return null;
                  }

                  return {
                      isLoading: false,
                      documentLineID: 0,
                      orderRefNo: orderRefNo,
                      CompanyCode: companyCode,
                      branchID: '',
                      lineItemID: matchingItem.masterAccountID,
                      lineItemDisplayCode: matchingItem.displayCode,
                      description: matchingItem.salesDescription,
                      quantity: item.quantity,
                      unitPrice: item.price,
                      subTotal: item.totalPrice,
                      memo: memo,
                      orderStatus: 'Pending',
                      paymentStatus: 'OrderOnly',
                      serviceTimeFrom: serviceTimeFrom,
                      condimentAddOnPrice: item.totalCondimentCharge,
                      skuName: matchingItem.unitOfMeasureName,
                      skuQuantity: 0,
                      matrix: '',
                      inventoryTypeID: matchingItem.inventoryTypeID,
                      saveAction: 1,
                      isDirty: true,
                      lstPackageItems: [],
                  };
              });

          $.ajax({
              url: 'https://teamspacedigital.com/ebicloud/post-data-script.php',
              method: 'POST',
              contentType: 'application/json',
              data: JSON.stringify({
                  orderDetails: orderDetails,
                  orderRefNo: orderRefNo,
                  companyCode: companyCode,
                  token: token,
                  endPoint: endPoint,
                  totalCondimentPrice: totalCondimentPrice,
              }),
              dataType: 'json',
              success: function(data) {
                  console.log('Response from server:', data);

                  if (data.status === 'success') {
                      console.log('Success');
                      window.open('https://teamspacedigital.com/ebicloud/post-data-script.php', '_blank');
                  }
              },
              error: function(jqXHR, textStatus, errorThrown) {
                  console.error('Error sending data:', textStatus, errorThrown);
              }
          });

      })
      .catch(error => {
          console.error("An error occurred:", error);
      });
  });

  // all close button modal
  $(document).on('click', '.custom-close, .close', function() {
      itemQuantities = {};
      cartItems = [];
      item = [];
  });
</script>
</body>
</html>
