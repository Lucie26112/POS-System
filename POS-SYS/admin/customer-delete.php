<?php 

require '../config/function.php';

$paraResult = checkParamId('id');
if(is_numeric($paraResult)){
    
    $CustomerId = validate($paraResult);
    $customer = getById('customers', $CustomerId);
    if($customer['status'] == 200){
        $response = delete('customers', $CustomerId);
        if($response){
            redirect('customers.php','Customer Deleted Successfully!');
        }
        else{
            redirect('customers.php','Something Went Wrong!');
        }
    }
    else{
        redirect('customers.php',$customer['message']);
    }

}else{
    redirect('customers.php','Something Went Wrong!');
}


?>