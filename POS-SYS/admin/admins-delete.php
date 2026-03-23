<?php 

require '../config/function.php';

$paraResult = checkParamId('id');
if(is_numeric($paraResult)){
    
    $adminId = validate($paraResult);
    $adminData = getById('admins', $adminId);
    if($adminData['status'] == 200){
        $adminDeleteRes = delete('admins', $adminId);
        if($adminDeleteRes){
            redirect('admins.php','Admin Deleted Successfully!');
        }
        else{
            redirect('admins.php','Something Went Wrong!');
        }
    }
    else{
        redirect('admins.php',$adminData['message']);
    }

}else{
    redirect('admins.php','Something Went Wrong!');
}


?>