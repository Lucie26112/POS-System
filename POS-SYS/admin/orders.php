<?php include 'includes/header.php';?>

    <div class="container-fluid px-4">
        <br><br>
        <h1 class="mt-4">Orders</h1>
        <ol class="breadcrumb mb-4">
        </ol>
        
        <div class="card">
            <div class="card-header">
                <h4>Orders</h4>
            </div>
            <div class="card-body">
                <?php
                $query = "SELECT o.*, c.* FROM orders o, customers c WHERE c.id = o.customer_id ORDER BY o.id DESC";
                $orders = mysqli_query($conn, $query);
                if($orders){

                if(mysqli_num_rows($orders) > 0){
                ?>    
                <table class="table table-bordered table-striped align-items-center justify-content-center">
                <thead>
                        <tr>
                            <th>Tracking No.</th>
                            <th>Customer Name</th>
                            <th>Customer Phone</th>
                            <th>Order Date</th>
                            <th>Order Status</th>
                            <th>Payment Mode</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $orderItem) : ?>
                        <tr>
                            <td><?= $orderItem['tracking_no']; ?></td>
                            <td><?= $orderItem['name']; ?></td>
                            <td><?= $orderItem['phone']; ?></td>
                            <td><?= date('d, M, Y', strtotime($orderItem['order_date'])); ?></td>
                            <td><?= $orderItem['order_status']; ?></td>
                            <td><?= $orderItem['paymentMode']; ?></td>
                            <td>
                                <a href="orders-view.php?track=<?= $orderItem['tracking_no']; ?>" class="btn btn-primary">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php } else { ?>
                    <p>No orders found</p>
                <?php }
                }else{
                    echo "<h5>Something Went Wrong!</h5>";
                } ?>
            </div>
        </div>
    </div>
<?php include 'includes/footer.php';?>