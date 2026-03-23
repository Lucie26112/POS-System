<?php include 'includes/header.php';?>

                    <div class="container-fluid px-4">
                        <br><br>
                        <h1 class="mt-4">Customers</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Customers</li>
                        </ol>
                        
                        <div class="card">
                            <?php alertMessage(); ?>
                            <div class="card-header">
                                <h4 class="mb-0">Customers
                                    <a href="customer-create.php" class="btn btn-primary float-end">Add Customer</a>
                                </h4>
                            </div>
                            <div class="card-body">
                            <?php alertMessage(); ?>
                            <?php $customers = getAll('customers');
                            if($customers === false){
                                echo '<h4>Something went wrong!</h4>';
                                }
                                if($customers && mysqli_num_rows($customers) > 0)
                                {

                                ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Id</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                            <?php 
                                            $customerData = [];
                                            while($row = mysqli_fetch_assoc($customers)) {
                                                $customerData[] = $row;
                                            }
                                            foreach($customerData as $item) : ?>
                                            <tr>
                                                <td><?=  $item['id'] ?></td>
                                                <td><?=  $item['name'] ?></td>
                                                <td><?=  $item['email'] ?></td>
                                                <td><?=  $item['phone'] ?></td>
                                                <td><?=  $item['status'] == 0 ? 'Active' : 'Inactive' ?></td>
                                                <td>
                                                    <a href="customer-edit.php?id=<?= $item['id'] ?>" class="btn btn-success btn-sm">Edit</a>
                                                    <a href="customer-delete.php?id=<?= $item['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this customer?')">Delete</a>
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