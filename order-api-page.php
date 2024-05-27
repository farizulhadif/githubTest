<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Menu</title>
</head>
<body>
    <?php
        function displayArray($data, $prefix = '') {
            foreach ($data as $key => $value) {
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }else if($value === null){
                    $value = 'null';
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
    <div>
        <h3>QR Table with Token</h3>
        <?php 

            // Get the current URL
            $currentURL = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

            // Remove the query string from the URL if present
            $urlWithoutQueryString = strtok($currentURL, '?');

            // Find the position of the last "/"
            $lastSlashPosition = strrpos($urlWithoutQueryString, "/");

            // Extract the substring after the last "/"
            $strOrderToken = substr($urlWithoutQueryString, $lastSlashPosition + 1);

            // Decode base64 string
            $strOrderInfo = base64_decode($strOrderToken);

            // Split the decoded string into an array
            $arrOrderInfo = explode("|", $strOrderInfo);

            // Extract individual elements
            $strOrderRefNo = $arrOrderInfo[0];
            $strCompanyCode = $arrOrderInfo[1];
            $strEndPoint = $arrOrderInfo[2];

            // Output the results
            echo "OrderRefNo: " . $strOrderRefNo . "<br>";
            echo "CompanyCode: " . $strCompanyCode . "<br>";
            echo "EndPoint: " . $strEndPoint . "<br>";

        ?>  
    </div>

    <div>
        <h3>Token detail</h3>
        <?php 
            $url = "{$strEndPoint}/api/Order/LoadRecord/{$strOrderRefNo}/{$strCompanyCode}";
            echo $url."<br>";
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
            echo "<b>Json<br></b>".$response;
            $responseData = json_decode($response, true);

            $token =  $responseData['result']['token'];
        ?>
        <div style="margin-top:1rem;"></div>
        <table>
            <?php displayArray($responseData); ?>
        </table>
    </div>

    <div>
        <h3>Food Category detail</h3>
        <?php 
            $url = "{$strEndPoint}/api/OrderInventory/LoadItemGroupsAsync/{$strOrderRefNo}/{$strCompanyCode}";
            echo $url."<br>";
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
            echo "<b>Json<br></b>".$response;
            $responseData = json_decode($response, true);
        ?>
        <div style="margin-top:1rem;"></div>
        <table>
            <?php displayArray($responseData); ?>
        </table>
    </div>

    <div>
        <h3>Food Item detail</h3>
        <?php 
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
            echo "<b>Json<br></b>".$response;
            $responseData = json_decode($response, true);
            
            // $Foodcondiment = $responseData['result']['itemGroupID '][0]['condimentGroupID'];
            $condimentGroupIds = [];

            if (isset($responseData['result']) && is_array($responseData['result'])) {
               
                foreach ($responseData['result'] as $groupKey => $groupItems) {
                    
                    foreach ($groupItems as $itemData) {
                        
                        if (isset($itemData['condimentGroupID']) && !empty($itemData['condimentGroupID'])) {
                            
                            $condiments = explode(',', $itemData['condimentGroupID']);

                            foreach ($condiments as $condiment) {
                                $condiment = trim($condiment); 
                                if (!in_array($condiment, $condimentGroupIds)) {
                                    $condimentGroupIds[] = $condiment;
                                }
                            }

                            echo "Food Condiment for {$itemData['accountName']}: " . implode(",", $condiments) . "<br>"; 
                        } else {
                            echo "No condiment group ID found for {$itemData['accountName']}<br>"; 
                        }
                    }
                }
                
                $combinedCondiments = implode(",", $condimentGroupIds);
                echo "Combined Food Condiments: " . $combinedCondiments . "<br>"; // Display the combined value
            } else {
                echo "No data found in the result section.";
            }

        ?>
        <div style="margin-top:1rem;"></div>
        <table>
            <?php displayArray($responseData); ?>
        </table>
    </div>

    <div>
        <h3>Food Condiment detail</h3>
        <?php 
            $url = "{$strEndPoint}/api/OrderCondimentGroup/LoadProxyByParentIDAsync/{$combinedCondiments}/{$strCompanyCode}/{$strOrderRefNo}";
            echo $url."<br>";
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
            echo "<b>Json<br></b>".$response;
            $responseData = json_decode($response, true);
        ?>
        <div style="margin-top:1rem;"></div>
        <table>
            <?php displayArray($responseData); ?>
        </table>
    </div>

    <!-- <div>
        <h3>Order submit detail</h3>

        <?php 
            $currentTime = new DateTime('now', new DateTimeZone('UTC'));
            $serviceTimeFrom =  $currentTime->format('Y-m-d\TH:i:s.u\Z');
            $postData = [
                [
                    "isLoading" => false,
                    "documentLineID" => 0,
                    "orderRefNo" => $strOrderRefNo,
                    "branchID" => "",
                    "lineItemID" => "HQ0000000000003",
                    "lineItemDisplayCode" => "HQSTK0000000002",
                    "description" => "Milo",
                    "quantity" => 2,
                    "unitPrice" => 2,
                    "subTotal" => 4,
                    "memo" => "Hot,Large",
                    "orderStatus" => "Pending",
                    "paymentStatus" => "OrderOnly",
                    "serviceTimeFrom" => $serviceTimeFrom,
                    "condimentAddOnPrice" => 0,
                    "skuName" => "UNIT",
                    "skuQuantity" => 0,
                    "matrix" => "",
                    "companyCode" => $strCompanyCode,
                    "inventoryTypeID" => 1,
                    "saveAction" => 1,
                    "isDirty" => true,
                    "lstPackageItems" => []
                ],
                [
                    "isLoading" => false,
                    "documentLineID" => 0,
                    "orderRefNo" => $strOrderRefNo,
                    "branchID" => "",
                    "lineItemID" => "HQ0000000000001",
                    "lineItemDisplayCode" => "HQSTK0000000001",
                    "description" => "Lemon Ice",
                    "quantity" => 1,
                    "unitPrice" => 1.5,
                    "subTotal" => 3,
                    "memo" => "Hot,Large",
                    "orderStatus" => "Pending",
                    "paymentStatus" => "OrderOnly",
                    "serviceTimeFrom" => $serviceTimeFrom,
                    "condimentAddOnPrice" => 0,
                    "skuName" => "UNIT",
                    "skuQuantity" => 0,
                    "matrix" => "",
                    "companyCode" => $strCompanyCode,
                    "inventoryTypeID" => 1,
                    "saveAction" => 1,
                    "isDirty" => true,
                    "lstPackageItems" => []
                ]
            ];

            $postDataJson = json_encode($postData);
            echo $postDataJson;

            $url = "{$strEndPoint}/api/OrderLine/SaveAll/{$strCompanyCode}";
            echo $url."<br>";
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
        ?>
        <div style="margin-top:1rem;"></div>
        <table>
            <?php displayArray($responseData); ?>
        </table>
    </div> -->
</body>
</html>