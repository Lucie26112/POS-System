<?php include 'code.php';?>
<?php include 'includes/header.php';?>

    <div class="container-fluid px-4">
        <br><br>
        <h1 class="mt-4">Add a Category</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Categories</li>
            <li class="breadcrumb-item active">Add Category</li>
        </ol>
        
        <div class="card">
            <?php alertMessage(); ?>
            <div class="card-header">
                <h4 class="mb-0">Add Category
                    <a href="categories.php" class="btn btn-primary float-end">Back</a>
                </h4>
            </div>
            <div class="card-body">
                <form action="" method = "POST">

                <div class="row">
                    <div class="col-md-12 mb-3">   
                        <label for="">Name</label>
                        <input type="text" required name="name" class="form-control">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label>Status (Unchecked = Visible, Checked = Hidden)</label>
                        <br/>
                        <input type="checkbox" Style="width: 30px; height: 30px;" name="status">
                    </div>
                    <div class="col-md-12 mb-3 text-end">
                        <br/>
                        <button type="submit" name="saveCategory" class="btn btn-primary">Save</button>
                    </div>
                </div>
                    
                </form>
                
            </div>
        </div>
    </div>
<?php include 'includes/footer.php';?>