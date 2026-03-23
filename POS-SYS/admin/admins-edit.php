<?php include 'code.php';?>
<?php include 'includes/header.php';?>

    <div class="container-fluid px-4">
        <br><br>
        <h1 class="mt-4">Edit Admin</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Admins</li>
            <li class="breadcrumb-item active">Edit Admin</li>
        </ol>
        
        <div class="card">
            <?php alertMessage(); ?>
            <div class="card-header">
                <h4 class="mb-0">Edit Admin
                    <a href="admins.php" class="btn btn-danger float-end">Back</a>
                </h4>
            </div>
            <div class="card-body">
                <form action="" method = "POST">

                <?php
                 if(isset($_GET['id']))
                 {
                    if($_GET['id'])
                    {
                        $adminId = $_GET['id'];
                    }
                    else
                    {
                        echo '<h5>No ID Found</h5>';
                        return false;
                    }
                 }
                 else
                 {
                    echo '<h5>No ID Given In Params</h5>';
                    return false;
                 }

                 $adminData = getById('admins', $adminId);
                 if($adminData)
                {
                    if($adminData['status'] == 200)
                    {
                        ?>
                    <input type="hidden" name="adminId" value="<?= $adminData['data']['id']; ?>">
                    <div class="row">
                    <div class="col-md-12 mb-3">   
                        <label for="">Name</label>
                        <input type="text" name="name" value="<?= $adminData['data']['name']; ?>" required class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="">Email</label>
                        <input type="email" name="email" value="<?= $adminData['data']['email'] ?>" required class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="">Password</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="">Phone Number</label>
                        <input type="text" name="phone" value="<?= $adminData['data']['phone'] ?>" required class="form-control">
                    </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_ban" <?= $adminData['data']['is_ban'] == true ? 'checked' : ''; ?> id="is_ban">
                                <label class="form-check-label" for="is_ban">
                                    Is Ban
                                </label>
                            </div>
                    </div>
                    <div class="col-md-12 mb-3 text-end">
                        <button type="submit" name="updateAdmin" class="btn btn-primary">Update</button>
                    </div>
                </div>
                        <?php
                    }
                    else
                    {
                        echo '<h5>'.$adminData['message'].'</h5>';
                        return false;
                    }
                }
                else
                {
                    echo '<h5>Something went wrong!</h5>';
                    return false;
                }
                 
                 ?>

                    
                </form>
                
            </div>
        </div>
    </div>
<?php include 'includes/footer.php';?>