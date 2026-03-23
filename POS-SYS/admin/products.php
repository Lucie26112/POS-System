<?php include 'includes/header.php';?>

                    <div class="container-fluid px-4">
                        <br><br>
                        <h1 class="mt-4">Products</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Products</li>
                        </ol>
                        
                        <div class="card">
                            <?php alertMessage(); ?>
                            <div class="card-header">
                                <h4 class="mb-0">Products
                                    <a href="products-create.php" class="btn btn-primary float-end">Add Product</a>
                                </h4>
                            </div>
                            <div class="card-body">
                            <?php alertMessage(); ?>
                            <?php $products = getAll('products');
                            if($products === false){
                                echo '<h4>Something went wrong!</h4>';
                                }
                                if($products && mysqli_num_rows($products) > 0)
                                {

                                ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Id</th>
                                                <th>Image</th>
                                                <th>Name</th>
                                                <th>Category</th>
                                                <th>Price</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                            <?php 
                                            $productData = [];
                                            while($row = mysqli_fetch_assoc($products)) {
                                                $productData[] = $row;
                                            }
                                            $categoryMap = [];
                                            $categories = getAll('categories');
                                            if ($categories && mysqli_num_rows($categories) > 0) {
                                                while ($cat = mysqli_fetch_assoc($categories)) {
                                                    $categoryMap[(string)$cat['id']] = $cat['name'];
                                                }
                                            }
                                            foreach($productData as $item) : ?>
                                            <tr>
                                                <td><?=  $item['id'] ?></td>
                                                <td><img src="../<?= $item['image'] ?>" alt="<?= $item['name'] ?>" width="50" height="50"></td>
                                                <td><?=  $item['name'] ?></td>
                                                <td><?= isset($categoryMap[(string)$item['category_id']]) ? htmlspecialchars($categoryMap[(string)$item['category_id']], ENT_QUOTES, 'UTF-8') : 'Unknown'; ?></td>
                                                <td><?=  $item['price'] ?></td>
                                                <td><?=  $item['quantity'] ?></td>
                                                <td><?=  $item['status'] == 0 ? 'Visible' : 'Hidden' ?></td>
                                                <td>
                                                    <a href="products-edit.php?id=<?= $item['id'] ?>" class="btn btn-success btn-sm">Edit</a>
                                                    <a href="products-delete.php?id=<?= $item['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            
                                        </tbody>
                                    </table>
                                </div>
                                <?php
                                    }
                                    else
                                    {
                                    ?>
                                        <h4 class="mb-0">No Record Found</h4>
                                    <?php
                                    }
                                    ?>
                            </div>
                        </div>
                    </div>

<?php include 'includes/footer.php';?>