<?php
$menu = [
    ['name' => 'Home', 'url' => '/home'],
    ['name' => 'About', 'url' => '/about'],
    ['name' => 'Contact', 'url' => '/contact'],
];

// Handle AJAX requests
if (isset($_GET['index']) || isset($_GET['get_menu'])) {
    header('Content-Type: application/json');

    if (isset($_GET['index'])) {
        $index = intval($_GET['index']);
        if (isset($menu[$index])) {
            echo json_encode($menu[$index]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Menu item not found']);
        }
    } else if (isset($_GET['get_menu'])) {
        echo json_encode($menu);
    }
    exit; // Ensure the rest of the page doesn't render when responding to an AJAX request
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Buttons</title>
</head>
<body>
    <div id="buttons-container"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('buttons-container');

            fetch(window.location.href + '?get_menu=true')
                .then(response => response.json())
                .then(menu => {
                    menu.forEach((item, index) => {
                        const button = document.createElement('button');
                        button.textContent = item.name;
                        button.classList.add('menu-button');
                        button.addEventListener('click', function () {
                            fetch(window.location.href + '?index=' + index)
                                .then(response => response.json())
                                .then(data => {
                                    console.log(data);
                                })
                                .catch(error => {
                                    console.error('Error fetching menu data:', error);
                                });
                        });
                        container.appendChild(button);
                    });
                })
                .catch(error => {
                    console.error('Error fetching menu data:', error);
                });
        });
    </script>
</body>
</html>
