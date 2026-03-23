<?php include 'code.php';?>
<?php include 'includes/header.php';?>

    <div class="container-fluid px-4">
        <br><br>
        <h1 class="mt-4">Edit Category</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Categories</li>
            <li class="breadcrumb-item active">Edit Category</li>
        </ol>
        
        <div class="card">
            <?php alertMessage(); ?>
            <div class="card-header">
                <h4 class="mb-0">Edit Category
                    <a href="categories.php" class="btn btn-primary float-end">Back</a>
                </h4>
            </div>
            <div class="card-body">
                <?php
                $parmValue = checkParamId('id');
                if(!is_numeric($parmValue)){
                    echo '<h5>'.$parmValue.'</h5>';
                    return false;
                }

                $category = getById('categories',$parmValue);
                if($category['status'] == 200){
                ?>

                <form action="" method="POST">
                <input type="hidden" name="categoryId" value="<?= $category['data']['id']; ?>">

                <div class="row">
                    <div class="col-md-12 mb-3">   
                        <label for="">Name</label>
                        <input type="text" required name="name" value="<?= $category['data']['name']; ?>" class="form-control">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?= $category['data']['description']; ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label>Status (Unchecked = Visible, Checked = Hidden)</label>
                        <br/>
                        <input type="checkbox" Style="width: 30px; height: 30px;" name="status" <?= ($category['data']['status'] == true ? 'checked':'') ? 'checked' : ''; ?> >
                    </div>
                    <div class="col-md-12 mb-3 text-end">
                        <br/>
                        <button type="submit" name="updateCategory" class="btn btn-primary">Update</button>
                    </div>
                </div>
                    
                </form>
                <?php
                }
                else
                {
                    echo '<h5>No Record Found</h5>';
                }
                ?>
            </div>
        </div>
    </div>
<?php include 'includes/footer.php';?>