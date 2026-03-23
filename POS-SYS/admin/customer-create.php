<?php include 'code.php';?>
<?php include 'includes/header.php';?>

<?php
$prefillPhone = '';
if (isset($_GET['phone']) && is_string($_GET['phone'])) {
    $prefillPhone = trim($_GET['phone']);
}
?>

    <div class="container-fluid px-4">
        <br><br>
        <h1 class="mt-4">Add a Customer</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Customers</li>
            <li class="breadcrumb-item active">Add Customer</li>
        </ol>
        
        <div class="card">
            <?php alertMessage(); ?>
            <div class="card-header">
                <h4 class="mb-0">Add Customer
                    <a href="customers.php" class="btn btn-primary float-end">Back</a>
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
                        <label for="">Email</label>
                        <input type="email" required name="email" class="form-control">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="customerPhoneField">Phone</label>
                        <input type="text" required name="phone" id="customerPhoneField" class="form-control" value="<?= htmlspecialchars($prefillPhone, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-6">
                        <label>Status (Unchecked = Visible, Checked = Hidden)</label>
                        <br/>
                        <input type="checkbox" Style="width: 30px; height: 30px;" name="status">
                    </div>
                    <div class="col-md-12 mb-3 text-end">
                        <br/>
                        <button type="submit" name="saveCustomer" class="btn btn-primary">Save</button>
                    </div>
                </div>
                    
                </form>
                
            </div>
        </div>
    </div>
<?php include 'includes/footer.php';?>