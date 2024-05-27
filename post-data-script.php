<?php
session_start(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawData = file_get_contents('php://input');
    $decodedData = json_decode($rawData, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        $_SESSION['posted_data'] = $decodedData;

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Data received successfully']);
    } else {

        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: text/html'); 

    if (isset($_SESSION['posted_data'])) {
        if (json_last_error() === JSON_ERROR_NONE) {
            // Separate into 3 variables
            $orderDetails = $_SESSION['posted_data']['orderDetails']; echo "<br>";
            echo $orderRefNo = $_SESSION['posted_data']['orderRefNo'];  echo "<br>";
            echo $companyCode = $_SESSION['posted_data']['companyCode'];  echo "<br>";
            echo $token = $_SESSION['posted_data']['token'];  echo "<br>";
            echo $endPoint = $_SESSION['posted_data']['endPoint'];  echo "<br>";
            // $totalCondimentPrice = $_SESSION['posted_data']['totalCondimentPrice'];

            $postDataJson = json_encode($orderDetails, JSON_PRETTY_PRINT);
            echo "<pre>{$postDataJson}</pre>";

            $url = "{$endPoint}/api/OrderLine/SaveAll/{$companyCode}";
            // echo $url."<br>";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'accept: application/json',
                'Authorization: '.$token,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($postDataJson)
            ));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataJson);

            $response = curl_exec($ch);
            if(curl_errno($ch)){
                echo 'Curl error: ' . curl_error($ch);
            }
            curl_close($ch);
            echo "<b>Json<br></b>".$response;
            $responseData = json_decode($response, true);

            date_default_timezone_set('UTC'); 

            $currentDate = new DateTime();
            $dayName = $currentDate->format('l'); 
            $day = $currentDate->format('j'); 
            $monthName = $currentDate->format('F'); 
            $year = $currentDate->format('Y'); 

            $formattedDate = "{$dayName}, {$day} {$monthName} {$year}";
            $currentTime = $currentDate->format('g:ia'); 


    ?>

    <html>
    <head>
      <title>Receipt Preview</title>
      <style>
          body { 
              font-family: 'Monaco', 'Courier New', 'Lucida Console', monospace;
              padding: 10px; 
          }
          .receipt-container { 
              margin-top: 40px;
              width: 400px; 
              height: auto; 
              box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2); /* Box shadow */
              background-color: white; 
          }
          h2 { text-align: center; }
          p { margin: 5px 0; }
          hr { margin: 10px 0; }
          table { 
              width: 90%; 
              border-collapse: collapse; 
            
          }
          th, td { 
              padding: 5px; 
              text-align: left; 
          }
          th { 
              font-weight: bold; 
          }
          .total { 
              font-weight: bold; 
          }
          @media print {
              body { 
                  margin: 0; 
                  padding: 0; 
              }
              .receipt-container { 
                  border: none; 
              }
              .print-button { 
                  display: none; 
              }
          }
          #content {
              margin: 10px;
          }
      </style>
  </head>
  <body>
      <center>
          <div class="receipt-container">
              <div style="width: 100%; background: linear-gradient(to bottom, orange, white); text-align: center; padding-top:30px; padding-bottom:40px;">
                  <center><b><span style="font-size:40px;">Thank you</span></b><br><b><span style="font-size:20px">for your order</span></b></center>
              </div>
              <div id="content">
                  <h2>Invoice</h2>
                  <p style="color:grey;"><?php echo $formattedDate; ?> at <?php echo $currentTime; ?></p>
                  <hr/>
                  <table>
                      <tr>
                          <th>Item</th>
                          <th>Price</th>
                          <th>Qty</th>
                          <th>Total</th>
                      </tr>

                      <?php
                          $items = json_decode($postDataJson, true);
                          $grandTotal = 0; 
                          foreach ($items as $item) {
                              $unitPrice = number_format($item['unitPrice'], 2); 
                              $itemTotal = number_format($item['subTotal'], 2); 
                              $grandTotal += (float) $itemTotal; 

                              echo "<tr>";
                              echo "<td>";
                              echo htmlspecialchars($item['description']);

                              if (!empty($item['memo'])) { // Only output memo if it's not empty
                                  echo "<br><i>*" . htmlspecialchars($item['memo']) . "</i>";
                              }

                              echo "</td>";
                              echo "<td>" . htmlspecialchars($unitPrice) . "</td>";
                              echo "<td>" . htmlspecialchars($item['quantity']) . "</td>";
                              echo "<td>" . htmlspecialchars($itemTotal) . "</td>";
                              echo "</tr>";
                          }

                          $grandTotalFormatted = number_format($grandTotal, 2);
                      ?>

                      <tr class="total">
                          <td colspan="3">Grand Total</td>
                          <td><?php echo htmlspecialchars($grandTotalFormatted); ?></td>
                      </tr>
                  </table>
                  <br><br>
                  <p style="margin-bottom: 30px; color:grey;"><i>******** Customer Copy ********</i></p><br><br>
              </div>
          </div>
          <button class="print-button" style="margin-top: 30px;" onclick="window.print();">Print</button>
      </center>
  </body>
</html>

<?php
        } else {
            echo "Invalid JSON data";
        }

    } else {
        echo "No stored data found.";
    }
} else {
    // Handle other HTTP methods
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>

