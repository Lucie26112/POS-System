<?php include 'code.php';?>
<?php include 'includes/header.php';?>
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="mb-0">Order Summary
                        <a href="orders.php" class="btn btn-primary float-end">Back</a>
                    </h4>
                    <div>
                        <div class="card-body">
                            <?php alertMessage()?>

                            <?php
                            if(isset($_GET['track']))
                                {
                                    $trackingNo = validate($_GET['track']);

                                    $query = "SELECT o.*, c.* FROM orders o, customers c WHERE c.id = o.customer_id AND tracking_no='$trackingNo' ORDER BY o.id DESC";
                                    $orders = mysqli_query($conn, $query);

                                    if($orders)
                                    {
                                        if(mysqli_num_rows($orders) > 0)
                                        {
                                            $orderData = mysqli_fetch_assoc($orders);
                                            $orderId = $orderData['id'];

                                            ?>
                                            <div class="card card-body shadow border-1 mb-4">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h4>Order Details</h4>
                                                        <label class="mb-1">
                                                            Tracking No: <span class="fw-bold"><?= $orderData['tracking_no'] ?></span>
                                                        </label>
                                                        <br/>
                                                        <label class="mb-1">
                                                            Order Date: <span class="fw-bold"><?= $orderData['order_date'] ?></span>
                                                        </label>
                                                        <br/>
                                                        <label class="mb-1">
                                                            Order Status: <span class="fw-bold"><?= $orderData['order_status'] ?></span>
                                                        </label>
                                                        <br/>
                                                        <label class="mb-1">
                                                            Payment Mode: <span class="fw-bold"><?= $orderData['paymentMode'] ?></span>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h4>Customer Details</h4>
                                                        <label class="mb-1">
                                                            Customer Name: <span class="fw-bold"><?= $orderData['name'] ?></span>
                                                        </label>
                                                        <br/>
                                                        <label class="mb-1">
                                                            Customer Email: <span class="fw-bold"><?= $orderData['email'] ?></span>
                                                        </label>
                                                        <br/>
                                                        <label class="mb-1">
                                                            Customer Phone: <span class="fw-bold"><?= $orderData['phone'] ?></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            
                                            <?php

                                            $orderItemQuery = "SELECT o.*, oi.*, p.* FROM orders AS o, order_items AS oi, products AS p WHERE oi.order_id = o.id AND p.id = oi.product_id AND o.tracking_no = '$trackingNo'";

                                            $orderItemsRes = mysqli_query($conn, $orderItemQuery);
                                            if($orderItemsRes)
                                                {
                                                    if(mysqli_num_rows($orderItemsRes) > 0)
                                                    {
                                                        ?>
                                                            <h4 class="my-3">Order Item Details</h4>
                                                            <table class="table table-bordered table-striped">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Product</th>
                                                                        <th>Price</th>
                                                                        <th>Quantity</th>
                                                                        <th>Total</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach($orderItemsRes as $orderItemRow): ?>
                                                                        <tr>
                                                                          <td>
                                                                              <?php $imgSrc = ($orderItemRow['image'] != '' ? '../'.$orderItemRow['image'] : '../assets/images/no-img.jpg'); ?>
                                                                              <a href="#" class="js-orderItemImage" data-bs-toggle="modal" data-bs-target="#orderItemImageModal" data-full-src="<?= htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8'); ?>" style="display:inline-block; margin-right:8px;">
                                                                                  <img src="<?= $imgSrc ?>" style="width:50px; height:50px; object-fit:cover; border-radius:4px;" alt="Img">
                                                                              </a>
                                                                              <?= $orderItemRow['name'] ?>
                                                                          </td>
                                                                            <td><?= $orderItemRow['price'] ?></td>
                                                                            <td><?= $orderItemRow['quantity'] ?></td>
                                                                            <td><?= $orderItemRow['price'] * $orderItemRow['quantity'] ?></td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                        <tr>
                                                                            <td colspan="3" class="text-end">Total:</td>
                                                                            <td><?= number_format($orderItemRow['total_amount'],0) ?></td>
                                                                        </tr>
                                                                </tbody>
                                                            </table>
                                                        <?php
                                                    }
                                                    else
                                                    {
                                                        echo "<h5>No order items found.</h5>";
                                                    }
                                                }
                                                else
                                                {
                                                    echo "<h5>Something went wrong.</h5>";
                                                    return false;
                                                }
                                            ?>
                                            
                                            
                                            
                                            
                                            
                                            <?php
                                        }
                                        else
                                        {
                                            echo "<h5>No order found.</h5>";
                                        }
                                    }
                                    else
                                    {
                                        echo "<h5>Something went wrong.</h5>";
                                    }
                                }
                                else
                                {
                                    ?>
                                    <div class="text-center py-5">
                                        <h5>No order ID provided.</h5>
                                        <a href="orders.php" class="btn btn-primary">Back to Orders</a>
                                    </div>
                                    <?php
                                }
                            
                            ?>
                            
                        </div>
                    </div>



            </div>

<div class="modal fade" id="orderItemImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="orderItemImageModalImg" src="" alt="" style="max-width:100%; height:auto;">
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('click', function(e) {
    var a = e.target && e.target.closest ? e.target.closest('.js-orderItemImage') : null;
    if (!a) { return; }
    e.preventDefault();
    var src = a.getAttribute('data-full-src') || '';
    var img = document.getElementById('orderItemImageModalImg');
    if (img) { img.src = src; }
});
</script>

<?php include 'includes/footer.php';?>