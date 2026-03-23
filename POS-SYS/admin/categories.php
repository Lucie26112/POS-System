<?php include 'includes/header.php';?>

                    <div class="container-fluid px-4">
                        <br><br>
                        <h1 class="mt-4">Categories</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Categories</li>
                        </ol>
                        
                        <div class="card">
                            <?php alertMessage(); ?>
                            <div class="card-header">
                                <h4 class="mb-0">Categories
                                    <a href="categories-create.php" class="btn btn-primary float-end">Add Category</a>
                                </h4>
                            </div>
                            <div class="card-body">
                            <?php alertMessage(); ?>
                            <?php $categories = getAll('categories');
                            if($categories === false){
                                echo '<h4>Something went wrong!</h4>';
                                }
                                if($categories && mysqli_num_rows($categories) > 0)
                                {

                                ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Id</th>
                                                <th>Name</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                            <?php 
                                            $categoryData = [];
                                            while($row = mysqli_fetch_assoc($categories)) {
                                                $categoryData[] = $row;
                                            }
                                            foreach($categoryData as $item) : ?>
                                            <tr>
                                                <td><?=  $item['id'] ?></td>
                                                <td><?=  $item['name'] ?></td>
                                                <td><?=  $item['status'] == 0 ? 'Visible' : 'Hidden' ?></td>
                                                <td>
                                                    <a href="categories-edit.php?id=<?= $item['id'] ?>" class="btn btn-success btn-sm">Edit</a>
                                                    <a href="categories-delete.php?id=<?= $item['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
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