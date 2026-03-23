<?php include 'code.php';?>
<?php include 'includes/header.php';?>

    <div class="container-fluid px-4">
        <br><br>
        <h1 class="mt-4">Edit Customer</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Categories</li>
            <li class="breadcrumb-item active">Edit Category</li>
        </ol>
        
        <div class="card">
            <?php alertMessage(); ?>
            <div class="card-header">
                <h4 class="mb-0">Edit Customer
                    <a href="customers.php" class="btn btn-primary float-end">Back</a>
                </h4>
            </div>
            <div class="card-body">
                <?php
                $parmValue = checkParamId('id');
                if(!is_numeric($parmValue)){
                    echo "<h5>$parmValue</h5>";
                    return false;
                }

                $customer = getById('customers',$parmValue);
                if($customer['status'] == 200){
                ?>

                <form action="" method="POST">
                <input type="hidden" name="customerId" value="<?= $customer['data']['id']; ?>">

                <div class="row">
                    <div class="col-md-12 mb-3">   
                        <label for="">Name</label>
                        <input type="text" required name="name" value="<?= $customer['data']['name']; ?>" class="form-control">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="">Email</label>
                        <input type="email" required name="email" value="<?= $customer['data']['email']; ?>" class="form-control">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="">Phone</label>
                        <input type="text" required name="phone" value="<?= $customer['data']['phone']; ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Status (Unchecked = Active, Checked = Inactive)</label>
                        <br/>
                        <input type="checkbox" Style="width: 30px; height: 30px;" name="status" <?= ($customer['data']['status'] == 1) ? 'checked' : ''; ?> >
                    </div>
                    <div class="col-md-12 mb-3 text-end">
                        <br/>
                        <button type="submit" name="updateCustomer" class="btn btn-primary">Update</button>
                    </div>
                </div>
                    
                </form>
                <?php
                }
                else
                {
                    echo "<h5>No Record Found</h5>";
                }
                ?>
            </div>
        </div>
    </div>
<?php include 'includes/footer.php';?>