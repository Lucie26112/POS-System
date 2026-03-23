<?php 
session_start();

require 'dbcon.php';

//validates input field
function validate($data){
    
    global $conn;
    $validatedData = mysqli_real_escape_string($conn, $data);
    return trim($validatedData);
}

// Redirects from one page to another page with the message (status)

function redirect($url, $status){

    $_SESSION['status'] = $status;
    header('Location: ' . $url);
    exit(0);
}
    
// Display Messages/Status after any process

function alertMessage(){
    if(isset($_SESSION['status'])){
        $status = $_SESSION['status'];
        echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
  <strong>' . $status . '</strong>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>';
        unset($_SESSION['status']);
    }
}

// Record using this function
function insert($tableName, $data)
{
    global $conn;

    $table = validate($tableName);

    $columns = array_keys($data);
    $values = array_values($data);

    $finalColumn = implode(',', $columns);
    $finalValues = "'". implode("','", $values) . "'";

    $query = "INSERT INTO $table ($finalColumn) VALUES ($finalValues)";
    
    $result = mysqli_query($conn, $query);
    
    return $result;
}

//Update data using this func
function update($tableName, $id, $data)
{
    global $conn;

    $table = validate($tableName);
    $id = validate($id);

    $updateDataString = "";

    foreach($data as $column => $value){
        $updateDataString .= $column . '=' . "'$value',";
    }
    
    $finalUpdateData = substr(trim($updateDataString),0,-1);

    $query = "UPDATE $table SET $finalUpdateData WHERE id = '$id'";
    $result = mysqli_query($conn, $query);
    
    return $result;
}

function getAll($tableName, $status = NULL){

    global $conn;

    $table = validate($tableName);
    $status = validate($status);

    if($status == 'status'){
        $query = "SELECT * FROM $table WHERE status = '1'";
        try {
            $result = mysqli_query($conn, $query);
            return $result;
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }
    else
        {
            $query = "SELECT * FROM $table";
        }

    try {
        return mysqli_query($conn, $query);
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}

function getById($tableName, $id){
    global $conn;
    
    $table = validate($tableName);
    $id = validate($id);
    
    $query = "SELECT * FROM $table WHERE id = '$id' LIMIT 1";
    try {
        $result = mysqli_query($conn, $query);
    } catch (mysqli_sql_exception $e) {
        $response = [

            'status' => 500,
            'message' => 'Something went wrong'
        ];
        return $response;
    }
    if($result){

        if(mysqli_num_rows($result) == 1){

            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $response = [
                'status' => 200,
                'message' => 'Success',
                'data' => $row
            ];
            return $response;

        }else{
            $response = [
                'status' => 404,
                'message' => 'No Data found'
            ];
            return $response;
        }
        
    }else{
        $response = [

            'status' => 500,
            'message' => 'Something went wrong'
        ];
        return $response;
    }
}

// Delete Data from the database using id

function delete($tableName, $id){
    global $conn;
    
    $table = validate($tableName);
    $id = validate($id);
    
    $query = "DELETE FROM $table WHERE id = '$id' LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    return $result;
}

function checkParamId($type){

    if(isset($_GET[$type])){
        if($_GET[$type] != ''){
            return $_GET[$type];
        }else{
            return '<h5>No ID Found</h5>';
        }

    }else{
        return '<h5>No ID Given</h5>';
    }


}

function logoutSession(){
    unset($_SESSION['loggedIn']);
    unset($_SESSION['loggedInUser']);
}

function jsonResponse($status, $status_type, $message)
{
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    $response = [
        'status' => $status,
        'status_type' => $status_type,
        'message' => $message,
    ];
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit(0);
}


?>