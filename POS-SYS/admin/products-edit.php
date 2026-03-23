<?php include 'code.php';?>
<?php include 'includes/header.php';?>

    <div class="container-fluid px-4">
        <br><br>
        <h1 class="mt-4">Edit Product</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Products</li>
            <li class="breadcrumb-item active">Edit Product</li>
        </ol>
        
        <div class="card">
            <?php alertMessage(); ?>
            <div class="card-header">
                <h4 class="mb-0">Edit Product
                    <a href="products.php" class="btn btn-primary float-end">Back</a>
                </h4>
            </div>
            <div class="card-body">
                <form action="" method = "POST" enctype="multipart/form-data">

                <?php 
                    $parmValue = checkParamId('id');
                    if(!is_numeric($parmValue)){
                        echo '<h5>Id Is Not an Integer</h5>';
                        return false;
                    }

                    $product = getById('products', $parmValue);
                    if($product)
                    {
                        if($product['status'] == 200)
                        {

                            ?>
                            <input type="hidden" name="productId" value="<?= $product['data']['id']; ?>">

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label>Select Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">Select Category</option>
                            <?php 
                            $categories = getAll('categories');
                            if($categories){
                                if(mysqli_num_rows($categories) > 0){
                                    foreach($categories as $cateItem){
                                        ?>
                                        <option value="<?=$cateItem['id'];?>"
                                        <?= $product['data']['category_id'] == $cateItem['id'] ? 'selected' : '';?>
                                        >
                                            <?=$cateItem['name'];?>
                                        </option>
                                        <?php
                                    }

                                }else{
                                    ?>
                                    <option value="">No Category Found</option>
                                    <?php
                                }

                            }else{
                                echo '<option value="">Something Went Wrong!</option>';
                            }
                            ?>
                        </select>

                    </div>
                    <div class="col-md-12 mb-3">   
                        <label for="">Product Name</label>
                        <input type="text" required name="name" value ="<?= $product['data']['name']; ?>"class="form-control">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="">Product Description</label>
                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['data']['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="">Product Price</label>
                        <input type="number" required name="price" value ="<?= $product['data']['price']; ?>" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="">Product Quantity</label>
                        <input type="number" required name="quantity" value ="<?= $product['data']['quantity']; ?>" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="">Product Image</label>
                        <input type="file" name="image" class="form-control">
                        <img src="../<?= $product['data']['image']; ?>" alt="product image" style="width: 100px; height: 100px;">
                    </div>
                    <div class="col-md-6">
                        <label>Status (Unchecked = Visible, Checked = Hidden)</label>
                        <br/>
                        <input type="checkbox" Style="width: 30px; height: 30px;" name="status" <?= $product['data']['status'] == true ? 'checked' : ''; ?>>
                    </div>
                    <div class="col-md-12 mb-3 text-end">
                        <br/>
                        <button type="submit" name="updateProduct" class="btn btn-primary">Update</button>
                    </div>
                </div>
                    <?php
                    }
                        else
                        {
                        echo '<h5>'.$product['status'].'</h5>';
                        return false;
                        }
                    }
                    else
                    {
                        echo '<h5>Something Went Wrong.</h5>';
                        return false;
                    }
                ?>
                </form>
                
            </div>
        </div>
    </div>
<?php include 'includes/footer.php';?>