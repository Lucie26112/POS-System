<?php 

require_once '../config/function.php';

if(isset($_POST['saveAdmin'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $is_ban = isset($_POST['is_ban']) ? 1 : 0;

    if($name == '' || $email == '' || $password == ''){
        redirect('admins-create.php','All fields are required');
    }else{
        $emailCheck = mysqli_query($conn, "SELECT * FROM admins WHERE email = '$email'");
        if($emailCheck){
            if(mysqli_num_rows($emailCheck) > 0){
                redirect('admins-create.php','Email already exists');
            }
        }
        
        $bcrypt_password = password_hash($password, PASSWORD_BCRYPT);
        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $bcrypt_password,
            'phone' => $phone,
            'is_ban' => $is_ban
        ];
        $result = insert('admins', $data);
        if($result){
            redirect('admins.php','Admin added successfully!');
        }else{
            redirect('admins-create.php','Failed to add admin!');
        }
    }
}

if(isset($_POST['updateAdmin']))
{
    $adminId = validate($_POST['adminId']);
    
    $adminData = getById('admins', $adminId);
    if($adminData['status'] != 200)
    {
        redirect('admins-edit.php?id='.$adminId ,'Something went wrong!');
    }
    
    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);
    $phone = validate($_POST['phone']);
    $is_ban = isset($_POST['is_ban']) == true ? 1 : 0;

    $EmailCheckQuery = "SELECT * FROM admins WHERE email = '$email' AND id != '$adminId'";
    $checkResult = mysqli_query($conn, $EmailCheckQuery);
    if($checkResult){
        if(mysqli_num_rows($checkResult) > 0){
            redirect('admins-edit.php?id='.$adminId,'Email already exists');
        }
    }

    if($password != '')
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    }
    else
    {
        $hashedPassword = $adminData['data']['password'];
    }

    if($name != '' && $email != '')
    {
        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'phone' => $phone,
            'is_ban' => $is_ban
        ];
        $result = update('admins', $adminId, $data);
        if($result){
            redirect('admins.php?id='.$adminId,'Admin updated successfully!');
        }else{
            redirect('admins-edit.php?id='.$adminId,'Failed to update!');
        }

    }
    else
    {
        redirect('admins-edit.php?id='.$_POST['adminId'],'All fields are required');
    }

}

if(isset($_POST['saveCategory']))
{
    $name = validate($_POST['name']);
    $description = validate($_POST['description']);
    $status = isset($_POST['status']) == true ? 1 : 0;
    
    $data = [
        'name' => $name,
        'description' => $description,
        'status' => $status
    ];
    
    $result = insert('categories', $data);
    if($result){
        redirect('categories.php','Category added successfully!');
    }else{
        redirect('categories-create.php','Failed to add category!');
    }
}

if(isset($_POST['updateCategory']))
{
    $categoryId = validate($_POST['categoryId']);

    $categoryData = getById('categories', $categoryId);
    if(!$categoryData || $categoryData['status'] != 200)
    {
        redirect('categories-edit.php?id='.$categoryId, 'Something went wrong!');
    }

    $name = validate($_POST['name']);
    $description = validate($_POST['description']);
    $status = isset($_POST['status']) == true ? 1 : 0;

    if($name != '')
    {
        $data = [
            'name' => $name,
            'description' => $description,
            'status' => $status
        ];

        $result = update('categories', $categoryId, $data);
        if($result){
            redirect('categories.php', 'Category updated successfully!');
        }else{
            redirect('categories-edit.php?id='.$categoryId, 'Failed to update category!');
        }
    }
    else
    {
        redirect('categories-edit.php?id='.$categoryId, 'Name is required');
    }
}

if(isset($_POST['saveProduct']))
{
    $category_id = validate($_POST['category_id']);
    $name = validate($_POST['name']);
    $description = validate($_POST['description']);
    $price = validate($_POST['price']);
    $quantity = validate($_POST['quantity']);
    $status = isset($_POST['status']) == true ? 1 : 0;
    
    if($_FILES['image']['size'] > 0)
    {
        $path = "../assets/uploads/products";
        $image_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = time().".".$image_ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $path."/".$filename);
        $finalImage = "assets/uploads/products/".$filename;
    }
    else
    {
        $finalImage = '';
    }
    
    $data = [
        'name' => $name,
        'category_id' => $category_id,
        'description' => $description,
        'price' => $price,
        'quantity' => $quantity,
        'image' => $finalImage,
        'status' => $status
    ];
    
    $result = insert('products', $data);
    if($result){
        redirect('products.php','Product added successfully!');
    }else{
        redirect('products-create.php','Failed to add product!');
    }
}


if(isset($_POST['updateProduct']))
{
    $productId = validate($_POST['productId']);

    $productData = getById('products', $productId);
    if(!$productData || $productData['status'] != 200)
    {
        redirect('products-edit.php?id='.$productId, 'Something went wrong!');
    }

    $category_id = validate($_POST['category_id']);
    $name = validate($_POST['name']);
    $description = validate($_POST['description']);
    $price = validate($_POST['price']);
    $quantity = validate($_POST['quantity']);
    $status = isset($_POST['status']) == true ? 1 : 0;

    if($_FILES['image']['size'] > 0)
    {
        $path = "../assets/uploads/products";
        $image_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = time().".".$image_ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $path."/".$filename);
        $finalImage = "assets/uploads/products/".$filename;

        $deleteImage = "../".$productData['data']['image'];
        if(file_exists($deleteImage)) {
            unlink($deleteImage);
        }
    }
    else
    {
        $finalImage = $productData['data']['image'];
    }

    $data = [
        'name' => $name,
        'category_id' => $category_id,
        'description' => $description,
        'price' => $price,
        'quantity' => $quantity,
        'image' => $finalImage,
        'status' => $status
    ];

    $result = update('products', $productId, $data);
    if($result){
        redirect('products.php', 'Product updated successfully!');
    }else{
        redirect('products-edit.php?id='.$productId, 'Failed to update product!');
    }
}

if(isset($_POST['saveCustomer']))
{
    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $phone = validate($_POST['phone']);
    $status = isset($_POST['status']) ? 1 : 0;

    if($name == '' || $email == '' || $phone == '')
    {
        redirect('customer-create.php','All fields are required');
    }

    $emailCheck = mysqli_query($conn, "SELECT * FROM customers WHERE email = '$email'");
    if($emailCheck)
    {
        if(mysqli_num_rows($emailCheck) > 0)
        {
            redirect('customer-create.php','Email already exists!');
        }
    }

    $data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'status' => $status
    ];

    $result = insert('customers', $data);
    if($result){
        redirect('customers.php','Customer added successfully!');
    }else{
        redirect('customer-create.php','Failed to add customer!');
    }
}

if(isset($_POST['updateCustomer']))
{
    $customerId = validate($_POST['customerId']);
    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $phone = validate($_POST['phone']);
    $status = isset($_POST['status']) ? 1 : 0;

    if($name == '' || $email == '' || $phone == '')
    {
        redirect('customer-edit.php?id='.$customerId,'All fields are required');
    }

    $emailCheck = mysqli_query($conn, "SELECT * FROM customers WHERE email = '$email' AND id!='$customerId'");
    if($emailCheck)
    {
        if(mysqli_num_rows($emailCheck) > 0)
        {
            redirect('customer-edit.php?id='.$customerId,'Email already exists!');
        }
    }

    $data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'status' => $status
    ];

    $result = update('customers', $customerId, $data);
    if($result){
        redirect('customers.php','Customer updated successfully!');
    }else{
        redirect('customer-edit.php?id='.$customerId,'Failed to update customer!');
    }
}
?>