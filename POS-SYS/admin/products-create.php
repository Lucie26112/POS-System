<?php include 'code.php';?>
<?php include 'includes/header.php';?>

    <div class="container-fluid px-4">
        <br><br>
        <h1 class="mt-4">Add a Product</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Products</li>
            <li class="breadcrumb-item active">Add Product</li>
        </ol>
        
        <div class="card">
            <?php alertMessage(); ?>
            <div class="card-header">
                <h4 class="mb-0">Add Product
                    <a href="products.php" class="btn btn-primary float-end">Back</a>
                </h4>
            </div>
            <div class="card-body">
                <form action="" method = "POST" enctype="multipart/form-data">

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
                                        echo '<option value="'.$cateItem['id'].'">'.$cateItem['name'].'</option>';
                                    }

                                }else{
                                    echo '<option value="">No Category Found</option>';
                                }

                            }else{
                                echo '<option value="">Something Went Wrong!</option>';
                            }
                            ?>
                        </select>

                    </div>
                    <div class="col-md-12 mb-3">   
                        <label for="">Product Name</label>
                        <input type="text" required name="name" class="form-control">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="">Product Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="">Product Price</label>
                        <input type="number" required name="price" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="">Product Quantity</label>
                        <input type="number" required name="quantity" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="">Product Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Status (Unchecked = Visible, Checked = Hidden)</label>
                        <br/>
                        <input type="checkbox" Style="width: 30px; height: 30px;" name="status">
                    </div>
                    <div class="col-md-12 mb-3 text-end">
                        <br/>
                        <button type="submit" name="saveProduct" class="btn btn-primary">Save</button>
                    </div>
                </div>
                    
                </form>
                
            </div>
        </div>
    </div>
<?php include 'includes/footer.php';?>