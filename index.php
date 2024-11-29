<?php
include "config/db.php";

header("Content-Type: application/json");

//HTTP Method
$requestMethod = $_SERVER["REQUEST_METHOD"];

$request = isset($_GET["request"]) ? explode("/", trim($_GET["request"], "/")) : [];

$requestMethod;

// $taskID = isset($request[1]) ? intval($request[1]) : null;
$taskID = isset($_GET["id"]) ? trim($_GET["id"],"/") : null;

switch ($requestMethod) {
    case "POST":
        if ($taskID) {
            createTasks($taskID);
        }else {
            createTask();
        }
        break;
    case "GET":
        if ($taskID) {
            getTask($taskID);
        }else {
            getTasks();
        }
        break;
    case "DELETE":
        if ($taskID) {
            delete($taskID);
        }else {
            deleteAll();
        }
        break;
    case "PUT":
        if ($taskID) {
            updateTask($taskID);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Task ID is Required"]);
        }
        break;
    case "PATCH":
        if ($taskID) {
            patchTask($taskID);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Task ID is Required"]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Method NOT Existing"]);
        break;
}

mysqli_close($conn);
?>


<?php
function createTask()
{
    global $conn;

    $data = json_decode(file_get_contents("php://input"), true);
    $title = $data["title"];
    $description = $data["description"];

    if (!empty($title)) {
        $sql = "INSERT INTO task (title, description) VALUES ('$title', '$description')";
        if (mysqli_query($conn, $sql)) {
            http_response_code(201);
            echo json_encode(["message" => "Task Created Successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "SERVER ERRAWR::Creating Tasks"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Title is Required Bongak"]);
    }
}

function createTasks($param){
    global $conn;

    $sql = "INSERT INTO task (title, description)
    VALUES 
    ('task 1', 'description 1'), 
    ('task 2', 'description 2'),
    ('task 3', 'description 3'),
    ('task 4', 'description 4'),
    ('task 5', 'description 5'),
    ('task 6', 'description 6'),
    ('task 7', 'description 7'),
    ('task 8', 'description 8'),
    ('task 9', 'description 9'),
    ('task 10', 'description 10')
    ";
    if (mysqli_query($conn, $sql)) {
        http_response_code(201);
        echo json_encode(["message" => "Tasks Created Successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "SERVER ERRAWR::Creating Tasks"]);
    }
}

function getTasks()
{
    global $conn;

    $sql = "SELECT * from task";
    $result = mysqli_query($conn, $sql);
    $task = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($task);
}

function getTask($param)
{
    global $conn;

    $sql = "SELECT * from task WHERE id = $param";
    $result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        echo json_encode($row);
    }else{
        echo json_encode(["message"=> "Task not Found"]);
    }
}

function deleteAll()
{
    global $conn;

    $sql = "DELETE FROM task";
    $result = mysqli_query($conn, $sql);
    
    if ($result){
        if (mysqli_affected_rows($conn) > 0) {
            echo json_encode(["message" => "All tasks deleted successfully"]);
        }else {
            echo json_encode(["message" => "No tasks to delete"]);
        }
    }else{
        echo json_encode(["message" => "Error deleting tasks"]);
    } 
}

function delete($param)
{
    global $conn;

    $sql = "DELETE FROM task WHERE id = $param";
    $result = mysqli_query($conn, $sql);

    if ($result){
        if (mysqli_affected_rows($conn) > 0) {
            echo json_encode(["message" => "Task [$param] succesfully deleted"]);
        }else {
            echo json_encode(["message" => "Task [$param] does not exist"]);
        }
    }else{
        echo json_encode(["message" => "Error deleting a task"]);
    } 
}

function updateTask($param){

    global $conn;

    $data = json_decode(file_get_contents("php://input"), true);
    $title = isset($data["title"])? $data["title"] : null;
    $description = isset($data["description"])? $data["description"] : null;
    
    if ($title && $description) {
        $sql = "UPDATE task SET title = '$title', description = '$description' WHERE id = $param";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(["message" => "Task [$param] updated successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "SERVER ERRAWR::Updating Task"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Title and Description is both required"]);
    }
}

function patchTask($param){
    global $conn;
    
    $data = json_decode(file_get_contents("php://input"), true);
    $title = isset($data["title"])? $data["title"] : null;
    $description = isset($data["description"])? $data["description"] : null;
    
    if ($title && $description) {
        $sql = "UPDATE task SET title = '$title', description = '$description' WHERE id = $param";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(["message" => "Updated successfully, but  u should've used PUT -_- smh"]);
        }else {
            http_response_code(500);
            echo json_encode(["message" => "SERVER ERRAWR::Updating Task"]);
        }
    }elseif ($title || $description){
        if ($title){
            $sql = "UPDATE task SET title = '$title' WHERE id = $param";
            if (mysqli_query($conn, $sql)) {
                echo json_encode(["message" => "Task [$param]'s title updated successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "SERVER ERRAWR::Updating Title of Task"]);
            }
        }elseif ($description){
            $sql = "UPDATE task SET description = '$description' WHERE id = $param";
            if (mysqli_query($conn, $sql)) {
                echo json_encode(["message" => "Task [$param]'s description updated successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "SERVER ERRAWR::Updating Description of Task"]);
            }
        }
    }else {
        http_response_code(400);
        echo json_encode(["message" => "Title or Description is required"]);
    }
}
?>