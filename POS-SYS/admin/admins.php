<?php include 'includes/header.php';?>

                    <div class="container-fluid px-4">
                        <br><br>
                        <h1 class="mt-4">Admins</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Admins</li>
                        </ol>
                        
                        <div class="card">
                            <?php alertMessage(); ?>
                            <div class="card-header">
                                <h4 class="mb-0">Admins
                                    <a href="admins-create.php" class="btn btn-primary float-end">Add Admin</a>
                                </h4>
                            </div>
                            <div class="card-body">
                            <?php alertMessage(); ?>
                            <?php $admins = getAll('admins');
                            if($admins === false){
                                echo '<h4>Something went wrong!</h4>';
                                }
                                if($admins && mysqli_num_rows($admins) > 0)
                                {

                                ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Id</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                            <?php while($adminItem = mysqli_fetch_assoc($admins)) : ?>
                                            <tr>
                                                <td><?=  $adminItem['id'] ?></td>
                                                <td><?=  $adminItem['name'] ?></td>
                                                <td><?=  $adminItem['email'] ?></td>
                                                <td>
                                                    <a href="admins-edit.php?id=<?= $adminItem['id'] ?>" class="btn btn-success btn-sm">Edit</a>
                                                    <a href="admins-delete.php?id=<?= $adminItem['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                            
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