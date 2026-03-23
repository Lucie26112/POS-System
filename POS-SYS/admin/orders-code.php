<?php

include ('../config/function.php');

if(!isset($_SESSION['cart_items']))
{
    $_SESSION['cart_items'] = [];
}
if(!isset($_SESSION['cart_items_ids']))
{
    $_SESSION['cart_items_ids'] = [];
}

function send_order_create_cart_json($conn, $success, $message)
{
    header('Content-Type: application/json; charset=utf-8');

    $items = [];
    $grand = 0.0;
    if (!empty($_SESSION['cart_items']) && is_array($_SESSION['cart_items'])) {
        foreach ($_SESSION['cart_items'] as $key => $item) {
            $price = isset($item['price']) ? (float)$item['price'] : 0.0;
            $qty = isset($item['quantity']) ? (int)$item['quantity'] : 0;
            $grand += $price * $qty;

            $maxQty = '';
            if (!empty($item['product_id'])) {
                $pid = (int)$item['product_id'];
                $stockRes = mysqli_query($conn, "SELECT quantity FROM products WHERE id='{$pid}' LIMIT 1");
                if ($stockRes && mysqli_num_rows($stockRes) > 0) {
                    $stockRow = mysqli_fetch_assoc($stockRes);
                    $maxQty = (string)(int)$stockRow['quantity'];
                }
            }

            $items[] = [
                'key' => (string)$key,
                'name' => isset($item['name']) ? (string)$item['name'] : '',
                'price' => isset($item['price']) ? $item['price'] : '0',
                'quantity' => $qty,
                'line_total' => number_format($price * $qty, 2),
                'max_qty' => $maxQty,
            ];
        }
    }

    echo json_encode([
        'success' => $success,
        'message' => $message,
        'items' => $items,
        'grand_total' => number_format($grand, 2),
        'is_empty' => count($items) === 0,
    ], JSON_UNESCAPED_UNICODE);
    exit(0);
}

if(isset($_POST['addItem'])) 
{
    // Sync cart_items_ids with actual cart_items to prevent mismatches
    $_SESSION['cart_items_ids'] = array_values(array_unique(array_column($_SESSION['cart_items'], 'product_id')));
    $productId = validate($_POST['product_id']);
    $quantity = validate($_POST['quantity']);

    if($productId == '' || !is_numeric($productId))
    {
        redirect('order-create.php', 'Please select a valid product.');
    }

    if($quantity == '' || !is_numeric($quantity) || (int)$quantity <= 0)
    {
        redirect('order-create.php', 'Please enter a valid quantity.');
    }

    $quantity = (int)$quantity;

    $checkProduct = mysqli_query($conn, "SELECT * FROM products WHERE id='$productId' LIMIT 1");
    
    if($checkProduct)
    {
        if(mysqli_num_rows($checkProduct) > 0)
        {
            $row = mysqli_fetch_assoc($checkProduct);

            $existingQuantity = 0;
            foreach($_SESSION['cart_items'] as $prodSessionItem)
            {
                if($prodSessionItem['product_id'] == $row['id'])
                {
                    $existingQuantity = (int)$prodSessionItem['quantity'];
                    break;
                }
            }

            $requestedTotal = $existingQuantity + $quantity;

            if((int)$row['quantity'] < $requestedTotal) {
                $remaining = (int)$row['quantity'] - $existingQuantity;
                if($remaining < 0) { $remaining = 0; }
                redirect('order-create.php', 'Only ' . $remaining . ' items available.');
            }
        $productData = [
            'product_id' => $row['id'],
            'name' => $row['name'],
            'image' => $row['image'],
            'price' => $row['price'],
            'quantity' => $quantity,
        ];
        if(!in_array($row['id'], $_SESSION['cart_items_ids'])) {
            array_push($_SESSION['cart_items_ids'], $row['id']);
            array_push($_SESSION['cart_items'], $productData);
        }else{
            $found = false;
            foreach($_SESSION['cart_items'] as $key => $prodSessionItem) {
                if($prodSessionItem['product_id'] == $row['id']) {
                    $newQuantity = $prodSessionItem['quantity'] + $quantity;

                    $productData = [
                        'product_id' => $row['id'],
                        'name' => $row['name'],
                        'image' => $row['image'],
                        'price' => $row['price'],
                        'quantity' => $newQuantity,
                    ];
                    $_SESSION['cart_items'][$key] = $productData;
                    $found = true;
                }
            }
            if (!$found) {
                // Product ID was in cart_items_ids but not in cart_items; add it as new
                array_push($_SESSION['cart_items'], $productData);
            }
        }

        redirect('order-create.php', $row['name'] . ' added to cart.');
    }else{
        redirect('order-create.php', 'Product not found.');
    }
}else{
    redirect('order-create.php', 'Something went wrong.');
}
}

if(isset($_POST['increaseQty']) || isset($_POST['decreaseQty']))
{
    $isAjaxCartQty = !empty($_POST['ajax_cart_qty']);
    $cartKey = validate($_POST['cart_key']);

    if($cartKey === '' || !isset($_SESSION['cart_items'][$cartKey]))
    {
        if ($isAjaxCartQty) {
            send_order_create_cart_json($conn, false, 'Cart item not found.');
        }
        redirect('order-create.php?open_cart=1', 'Cart item not found.');
    }

    $item = $_SESSION['cart_items'][$cartKey];

    $productId = isset($item['product_id']) ? (int)$item['product_id'] : 0;
    if($productId <= 0)
    {
        if ($isAjaxCartQty) {
            send_order_create_cart_json($conn, false, 'Product not found.');
        }
        redirect('order-create.php?open_cart=1', 'Product not found.');
    }

    $currentQty = isset($item['quantity']) ? (int)$item['quantity'] : 0;
    $newQty = $currentQty;

    if(isset($_POST['increaseQty']))
    {
        $newQty = $currentQty + 1;
    }
    else
    {
        $newQty = $currentQty - 1;
    }

    if($newQty <= 0)
    {
        unset($_SESSION['cart_items'][$cartKey]);

        $index = array_search($productId, $_SESSION['cart_items_ids']);
        if($index !== false)
        {
            unset($_SESSION['cart_items_ids'][$index]);
            $_SESSION['cart_items_ids'] = array_values($_SESSION['cart_items_ids']);
        }

        $_SESSION['cart_items'] = array_values($_SESSION['cart_items']);
        if ($isAjaxCartQty) {
            send_order_create_cart_json($conn, true, 'Item removed from cart.');
        }
        redirect('order-create.php?open_cart=1', 'Item removed from cart.');
    }

    $checkProduct = mysqli_query($conn, "SELECT quantity FROM products WHERE id='$productId' LIMIT 1");
    if(!$checkProduct || mysqli_num_rows($checkProduct) == 0)
    {
        if ($isAjaxCartQty) {
            send_order_create_cart_json($conn, false, 'Product not found.');
        }
        redirect('order-create.php?open_cart=1', 'Product not found.');
    }

    $productRow = mysqli_fetch_assoc($checkProduct);
    $availableStock = (int)$productRow['quantity'];

    $existingOther = 0;
    foreach($_SESSION['cart_items'] as $k => $cartItem)
    {
        if((string)$k === (string)$cartKey) { continue; }
        if(isset($cartItem['product_id']) && (int)$cartItem['product_id'] === $productId)
        {
            $existingOther += (int)$cartItem['quantity'];
        }
    }

    if($availableStock < ($existingOther + $newQty))
    {
        $remaining = $availableStock - $existingOther - $currentQty;
        if($remaining < 0) { $remaining = 0; }
        if ($isAjaxCartQty) {
            send_order_create_cart_json($conn, false, 'Only ' . $remaining . ' items available.');
        }
        redirect('order-create.php?open_cart=1', 'Only ' . $remaining . ' items available.');
    }

    $_SESSION['cart_items'][$cartKey]['quantity'] = $newQty;
    if ($isAjaxCartQty) {
        send_order_create_cart_json($conn, true, 'Cart updated.');
    }
    redirect('order-create.php?open_cart=1', 'Cart updated.');
}

if(isset($_GET['removeItem']))
{
    $cartKey = validate($_GET['removeItem']);
    if($cartKey !== '' && isset($_SESSION['cart_items'][$cartKey]))
    {
        $productId = isset($_SESSION['cart_items'][$cartKey]['product_id']) ? (int)$_SESSION['cart_items'][$cartKey]['product_id'] : 0;
        unset($_SESSION['cart_items'][$cartKey]);
        $_SESSION['cart_items'] = array_values($_SESSION['cart_items']);

        if($productId > 0)
        {
            $index = array_search($productId, $_SESSION['cart_items_ids']);
            if($index !== false)
            {
                unset($_SESSION['cart_items_ids'][$index]);
                $_SESSION['cart_items_ids'] = array_values($_SESSION['cart_items_ids']);
            }
        }
        redirect('order-create.php?open_cart=1', 'Item removed from cart.');
    }

    redirect('order-create.php?open_cart=1', 'Cart item not found.');
}

if(isset($_POST['proceedToPlace']))
{
    $phone = validate($_POST['customerPhone'] ?? '');
    $paymentMode = validate($_POST['paymentMode'] ?? '');


    //Checking For Customers
    $checkCustomer = mysqli_query($conn, "SELECT * FROM customers WHERE phone='$phone' LIMIT 1");
    if($checkCustomer){
        if(mysqli_num_rows($checkCustomer) > 0)
        {
            $_SESSION['invoice_no'] = "INV-".rand(111111, 999999);
            $_SESSION['customerPhone'] = $phone;
            $_SESSION['paymentMode'] = $paymentMode;
            jsonResponse(200, 'success', 'Customer Found');
        }
        else
        {
            $_SESSION['customerPhone'] = $phone;
            jsonResponse(404, 'warning', 'Customer Not Found.');
        }
    }
    else
    {
        jsonResponse(500, 'error', 'Something Went Wrong.');
    }
}

if(isset($_POST['saveOrder']))
{
    $phone = validate($_POST['customerPhone']);
    $paymentMode = validate($_POST['paymentMode']);
    $invoice_no = validate($_POST['invoice_no']);
    $order_placed_by_id = $_SESSION['loggedInUser']['user_id'];

    $checkCustomer = mysqli_query($conn, "SELECT * FROM customers WHERE phone='$phone' LIMIT 1");
    if(!$checkCustomer){
        jsonResponse(500, 'error', 'Something Went Wrong.');
    }

    if(mysqli_num_rows($checkCustomer) > 0)
    {
        $customerData = mysqli_fetch_assoc($checkCustomer); 

        if(!isset($_SESSION['cart_items'])){
            jsonResponse(404, 'error', 'No items found in Cart.');
        }

        $sessionProducts = $_SESSION['cart_items'];
        $totalAmount = 0;
        foreach($sessionProducts as $amtItem){
           $totalAmount += $amtItem['price'] * $amtItem['quantity'];
        }
        $data = [
            'customer_id' => $customerData['id'],
            'tracking_no' => rand(11111, 99999),
            'invoice_no' => $invoice_no,
            'total_amount' => $totalAmount,
            'order_date' => date('Y-m-d'),
            'order_status' => 'Booked',
            'paymentMode' => $paymentMode,
            'order_placed_by_id' => $order_placed_by_id
        ];
        $result = insert('orders', $data);
        $lastOrderId = mysqli_insert_id($conn);

        foreach($sessionProducts as $prodItem){
           $productId = $prodItem['product_id'];
           $price = $prodItem['price'];
           $quantity = $prodItem['quantity'];

           //Inserting order items

           $dataOrderItem = [
            'order_id' => $lastOrderId,
            'product_id' => $productId,
            'price' => $price,
            'quantity' => $quantity
           ];
           $resultOrderItem = insert('order_items', $dataOrderItem);

           //Check product quantity
           $checkProductQuantityQuery = mysqli_query($conn, "SELECT * FROM products WHERE id='$productId'");
           $productQtyData = mysqli_fetch_assoc($checkProductQuantityQuery);
           $TotalProductQuantity = $productQtyData['quantity'] - $quantity;
           
           $dataUpdate = [
            'quantity' => $TotalProductQuantity
           ];
           $updateProductQty = update('products', $productId, $dataUpdate);
        }

        unset($_SESSION['productItemIds']);
        unset($_SESSION['cart_items']);
        unset($_SESSION['customerPhone']);
        unset($_SESSION['paymentMode']);
        unset($_SESSION['invoice_no']);

        jsonResponse(200, 'success', 'Order Placed Successfully.');

    

    }
    else
    {
        jsonResponse(404, 'warning', 'Customer not found.');
    }

}
?>