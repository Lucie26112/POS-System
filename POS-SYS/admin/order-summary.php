<?php
require_once '../config/function.php';
require_once 'authentication.php';
if(!isset($_SESSION['cart_items'])){
    echo '<script>window.location.href = "order-create.php";</script>';
}
if (empty($_SESSION['invoice_no'])) {
    header('Location: order-create.php');
    exit(0);
}

$invoiceNo = $_SESSION['invoice_no'];
$customerPhone = $_SESSION['customerPhone'] ?? '';
$paymentMode = $_SESSION['paymentMode'] ?? '';

include 'includes/header.php';
?>
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="mb-0">Order Summary
                        <a href="order-create.php" class="btn btn-primary float-end">Back</a>
                    </h4>
                </div>
                <div class="card-body">
                    <?php alertMessage(); ?>

                    <table id="orderSummary" style="width:100%; border-collapse:collapse;">
                        <?php 
                        if(isset($_SESSION['customerPhone']))
                        {
                            $phone = validate($_SESSION['customerPhone']);
                            $invoiceNo = validate($_SESSION['invoice_no']);
                            $customerQuery = mysqli_query($conn, "SELECT * FROM customers WHERE phone='$phone' LIMIT 1");
                            if($customerQuery)
                            {
                                if(mysqli_num_rows($customerQuery) > 0)
                                {
                                    $customerRow = mysqli_fetch_assoc($customerQuery);
                                    ?>
                                    <tr>
                                        <td colspan="3" style="text-align:center;">
                                            <h4 style="font-size: 23px; line-height: 30px; margin:2px; padding:0;">Arellano University Juan Sumulong Campus</h4>
                                            <p style="font-size: 13px; line-height: 20px; margin:2px; padding:0;">2600 Legarda St., Sampaloc, Manila, 1008 Metro Manila</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="vertical-align:top; padding-top: 8px;">
                                            <h5 style="font-size: 18px; line-height: 25px; margin:2px; padding:0;">Order Items</h5>
                                            <?php
                                            $sessionProducts = (isset($_SESSION['cart_items']) && is_array($_SESSION['cart_items'])) ? $_SESSION['cart_items'] : [];
                                            if (count($sessionProducts) > 0) {
                                                $grandTotal = 0;
                                            ?>
                                                <table style="width: 100%; border-collapse: collapse;" cellpadding="6">
                                                    <thead>
                                                        <tr>
                                                            <th align="left" style="border-bottom: 1px solid #ccc;" width="6%">ID</th>
                                                            <th align="left" style="border-bottom: 1px solid #ccc;">Product Name</th>
                                                            <th align="left" style="border-bottom: 1px solid #ccc;" width="10%">Price</th>
                                                            <th align="left" style="border-bottom: 1px solid #ccc;" width="10%">Quantity</th>
                                                            <th align="left" style="border-bottom: 1px solid #ccc;" width="15%">Total Price</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($sessionProducts as $index => $item):
                                                            $price = isset($item['price']) ? (float)$item['price'] : 0;
                                                            $qty = isset($item['quantity']) ? (int)$item['quantity'] : 0;
                                                            $lineTotal = $price * $qty;
                                                            $grandTotal += $lineTotal;
                                                        ?>
                                                            <tr>
                                                                <td><?= (int)$index + 1; ?></td>
                                                                <td><?= htmlspecialchars($item['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                                <td><?= number_format($price, 0); ?></td>
                                                                <td><?= $qty; ?></td>
                                                                <td><strong><?= number_format($lineTotal, 0); ?></strong></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                        <tr>
                                                            <td colspan="4" align="right" style="padding-top: 8px; border-top: 1px solid #ccc;">
                                                                <strong>Grand Total:</strong>
                                                            </td>
                                                            <td style="padding-top: 8px; border-top: 1px solid #ccc;">
                                                                <strong><?= number_format($grandTotal, 0); ?></strong>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <?php if(isset($_SESSION['cart_items'])) : ?>
                                                <div class="mt-3 text-end">
                                                    <button type="button" class="btn btn-primary px-4 mx-1" id="saveOrder">Save</button>
                                                </div>
                                                <?php endif; ?>
                                            <?php
                                            } else {
                                                echo '<div class="alert alert-warning">No items in cart</div>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width:50%; vertical-align:top;">
                                            <h5 style="font-size: 18px; line-height: 25px; margin:2px; padding:0;">Customer Details</h5>
                                            <p style="font-size: 13px; line-height: 20px; margin:2px; padding:0;">Name: <?= $customerRow['name']; ?></p>
                                            <p style="font-size: 13px; line-height: 20px; margin:2px; padding:0;">Email: <?= $customerRow['email']; ?></p>
                                            <p style="font-size: 13px; line-height: 20px; margin:2px; padding:0;">Phone: <?= $customerRow['phone']; ?></p>
                                        </td>
                                        <td style="width:50%; vertical-align:top; text-align:right;">
                                            <h5 style="font-size: 18px; line-height: 25px; margin:2px; padding:0;">Order Details</h5>
                                            <p style="font-size: 13px; line-height: 20px; margin:2px; padding:0;">Invoice No: <?= $invoiceNo; ?></p>
                                            <p style="font-size: 13px; line-height: 20px; margin:2px; padding:0;">Date: <?= date('Y-m-d'); ?></p>
                                            <p style="font-size: 13px; line-height: 20px; margin:2px; padding:0;">Customer Phone: <?= $customerRow['phone']; ?></p>
                                            <p style="font-size: 13px; line-height: 20px; margin:2px; padding:0;">Payment Mode: <?= $paymentMode; ?></p>
                                        </td>
                                    </tr>
                                    <?php
                                }else{
                                    echo '<tr><td colspan="2"><div class="alert alert-warning">Customer not found</div></td></tr>';
                                }
                            }
                        }
                        ?>
                </table>
                </div>
            </div>
        </div>
    </div>
<?php include 'includes/footer.php'; ?>

<script>
    // Expose session data to custom.js for the #saveOrder handler
    window.orderSessionData = {
        customerPhone: <?= json_encode($_SESSION['customerPhone'] ?? ''); ?>,
        paymentMode: <?= json_encode($_SESSION['paymentMode'] ?? ''); ?>,
        invoice_no: <?= json_encode($_SESSION['invoice_no'] ?? ''); ?>
    };
    // Native fallback for #saveOrder in case jQuery handler fails
    document.addEventListener('DOMContentLoaded', function() {
        var saveBtn = document.getElementById('saveOrder');
        if (saveBtn) {
            saveBtn.addEventListener('click', function(e) {
                console.log('saveOrder clicked (native fallback)');
                var customerPhone = window.orderSessionData.customerPhone || '';
                var paymentMode = window.orderSessionData.paymentMode || '';
                var invoice_no = window.orderSessionData.invoice_no || '';
                console.log('saveOrder data (native):', { customerPhone, paymentMode, invoice_no });
                var formData = new FormData();
                formData.append('saveOrder', 'true');
                formData.append('customerPhone', customerPhone);
                formData.append('paymentMode', paymentMode);
                formData.append('invoice_no', invoice_no);
                fetch('orders-code.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    console.log('saveOrder response (native):', data);
                    if (data.status == 200) {
                        if (typeof swal === 'function') {
                            swal(data.message, data.message, data.status_type)
                                .then(function() { window.location.href = 'orders.php'; });
                        } else {
                            alert(data.message);
                            window.location.href = 'orders.php';
                        }
                    } else {
                        if (typeof swal === 'function') {
                            swal(data.message, data.message, data.status_type);
                        } else {
                            alert(data.message);
                        }
                    }
                })
                .catch(function(err) {
                    console.error('saveOrder error (native):', err);
                    if (typeof swal === 'function') {
                        swal('Error', 'Could not save order. Please try again.', 'error');
                    } else {
                        alert('Could not save order. Please try again.');
                    }
                });
            });
        }
    });
</script>