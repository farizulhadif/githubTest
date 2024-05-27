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

// Function to make a cURL request
function makeCurlRequest($url, $token = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = array('accept: application/json');
    if ($token) {
        $headers[] = 'Authorization: ' . $token;
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
    }
    curl_close($ch);
    return json_decode($response, true);
}

// Get token data from API
$url = "{$strEndPoint}/api/Order/LoadRecord/{$strOrderRefNo}/{$strCompanyCode}";
$tokenResponseData = makeCurlRequest($url);

$token = $tokenResponseData['result']['token'];

// Get food item data from API
$url = "{$strEndPoint}/api/OrderInventory/LoadProxyByItemGroupAsync/{$strOrderRefNo}/{$strCompanyCode}";
$menu = makeCurlRequest($url, $token);

// Check for condiment group IDs
$condimentGroupIds = [];

if (isset($menu['result']) && is_array($menu['result'])) {
    foreach ($menu['result'] as $groupItems) {
        foreach ($groupItems as $itemData) {
            if (isset($itemData['condimentGroupID']) && !empty($itemData['condimentGroupID'])) {
                $condiments = explode(',', $itemData['condimentGroupID']);
                foreach ($condiments as $condiment) {
                    $condiment = trim($condiment); 
                    if (!in_array($condiment, $condimentGroupIds)) {
                        $condimentGroupIds[] = $condiment;
                    }
                }
            }
        }
    }

    $combinedCondiments = implode(",", $condimentGroupIds);

    // Get food condiment detail from API
    $url = "{$strEndPoint}/api/OrderCondimentGroup/LoadProxyByParentIDAsync/{$combinedCondiments}/{$strCompanyCode}/{$strOrderRefNo}";
    $condiments = makeCurlRequest($url, $token);
}

if (isset($_GET['index']) || isset($_GET['get_menu']) || isset($_GET['get_condiment']) || isset($_GET['get_details'])) {
    header('Content-Type: application/json');
    
    if (isset($_GET['index'])) {
        $index = intval($_GET['index']);
        foreach ($menu['result'] as $key => $items) {
            foreach ($items as $item) {
                if ($item['orderMasterAccountID'] == $index) {
                    echo json_encode($item);
                    exit;
                }
            }
        }
        http_response_code(404);
        echo json_encode(['error' => 'Menu item not found']);
    } elseif (isset($_GET['get_menu'])) {
        echo json_encode($menu);
    } elseif (isset($_GET['get_condiment'])) {
        echo json_encode($condiments['result']);
    } elseif (isset($_GET['get_details'])) {
        $response = [
            'token' => $token,
            'orderRefNo' => $strOrderRefNo,
            'CompanyCode' => $strCompanyCode,
            'EndPoint' => $strEndPoint,
        ];
        echo json_encode($response);
    }
    exit;
}



?>
