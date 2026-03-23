<?php include 'code.php';?>
<?php include 'includes/header.php';?>

    <div class="container-fluid px-4">
        <br><br>
        <h1 class="mt-4">Add an Admin</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Admins</li>
            <li class="breadcrumb-item active">Add Admin</li>
        </ol>
        
        <div class="card">
            <?php alertMessage(); ?>
            <div class="card-header">
                <h4 class="mb-0">Add Admin
                    <a href="admins.php" class="btn btn-primary float-end">Back</a>
                </h4>
            </div>
            <div class="card-body">
                <form action="" method = "POST">

                <div class="row">
                    <div class="col-md-12 mb-3">   
                        <label for="">Name</label>
                        <input type="text" required name="name" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="">Email</label>
                        <input type="email" required name="email" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="">Password</label>
                        <input type="password" required name="password" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="">Phone Number</label>
                        <input type="text" required name="phone" class="form-control">
                    </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_ban" id="is_ban">
                                <label class="form-check-label" for="is_ban">
                                    Is Ban
                                </label>
                            </div>
                    </div>
                    <div class="col-md-12 mb-3 text-end">
                        <button type="submit" name="saveAdmin" class="btn btn-primary">Save</button>
                    </div>
                </div>
                    
                </form>
                
            </div>
        </div>
    </div>
<?php include 'includes/footer.php';?>