<?php

include "connect.php";

function DissableWebAccess()
{
    # Preflight Check
    if (isset($_SERVER['HTTPS_ORIGIN'])) {
        header("Access-Control-Allow-Origin: *"); # Allow all external connections
        header("Access-Control-Max-Age: 60"); # Keep connections open for 1 minute

        # Check if a site is requesting access to the site:
        if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
            header("Access-Control-Allow-Methods: POST, OPTIONS"); # Only allow these kinds of connections
            header("Access-Control-Allow-Headers: Authorization, Content-Type, Accept, Origin, cache-control");
            http_response_code(200); # Report that they are good to make their request now
            die; # Quit here until they send a followup!
        }
    }

    # Let's prevent anything other than POST requests to go past this point:
    if ($_SERVER['REQUEST_METHOD'] !== "POST") {
        http_response_code(405); # Report that they were denied access
        die; # End things here.
    }
}

function ProcessRequest()
{
    # Make sure our command is sent properly:
    if (!isset($_REQUEST['command']) or $_REQUEST['command'] === null) {
        echo "{\"error\" : \"missing_data\", \"response\" : {}}";
        die;
    }

    # Make sure our data is sent, even if empty:
    if (!isset($_REQUEST['data']) or $_REQUEST['data'] === null) {
        echo "missing_data";
        die;
    }
}

// DissableWebAccess();
ProcessRequest();

# Convert our Godot json string into a dictionary:
$json = json_decode($_REQUEST['data'], true);

# Check that the json was valid:
if ($json === null) {
    echo "invalid_json";
    die;
}

# --- Execute Godot commands: ---
switch ($_REQUEST['command']) {
    // ----------------- User Account -----------------
    case "get_user_account":
        GetStudentAccount($conn);
        die;
    case "add_user_account":
        AddStudentAccount($conn, $json);
        die;
    case "delete_user_account":
        DeleteStudentAccount($conn, $json);
        die;

    // ----------------- Scoring -----------------
    case "get_score":
        GetScore($conn);
        die;
    case "add_score":
        AddScore($conn, $json);
        die;
    case "delete_score":
        DeleteScore($conn, $json);
        die;

    // ----------------- Quiz -----------------
    case "get_quiz":
        GetQuiz($conn);
        die;
    case "add_quiz":
        AddQuiz($conn, $json);
        die;
    case "delete_quiz":
        DeleteQuiz($conn, $json);
        die;

    // ----------------- Player Data -----------------
    case "get_player_data":
        GetPlayerData($conn);
        die;
    case "add_player_data":
        AddPlayerData($conn, $json);
        die;

    default:
        echo "invalid_command";
        die;
}

//  ----------------- User Account -----------------
function GetStudentAccount($conn)
{
    $query = "SELECT * FROM `user`";
    $result = mysqli_query($conn, $query);

    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    echo json_encode($data);
}
function AddStudentAccount($conn, $json)
{
    $UserID = $json['UserID'];
    $FirstName = $json['FirstName'];
    $LastName = $json['LastName'];
    $UserName = $json['UserName'];
    $Password = $json['Password'];
    $Role = $json['Role'];

    $query = "INSERT INTO `user` (UserID, FirstName, LastName, UserName, Password, Role) 
                VALUES ('$UserID', '$FirstName', '$LastName', '$UserName', '$Password', '$Role') 
                ON DUPLICATE KEY UPDATE 
                    UserID = VALUES(UserID), 
                    FirstName = VALUES(FirstName), 
                    LastName = VALUES(LastName), 
                    UserName = VALUES(UserName), 
                    Password = VALUES(Password), 
                    Role = VALUES(Role)";

    mysqli_query($conn, $query);
}
function DeleteStudentAccount($conn, $json)
{
    $UserID = $json['UserID'];
    $query = "DELETE FROM `user` WHERE UserID = '$UserID'";

    mysqli_query($conn, $query);
}

// ----------------- Scoring -----------------
function GetScore($conn)
{
    $query = "SELECT * FROM `scoring`";
    $result = mysqli_query($conn, $query);

    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    echo json_encode($data);
}
function AddScore($conn, $json)
{
    $ScoreID = $json['ScoreID'];
    $PlayerID = $json['PlayerID'];
    $ChapterID = $json['ChapterID'];
    $Score = $json['Score'];

    $query = "INSERT INTO `scoring` (ScoreID, PlayerID, ChapterID, Score)
                VALUES ('$ScoreID', '$PlayerID', '$ChapterID', '$Score') 
                ON DUPLICATE KEY UPDATE 
                    ScoreID = VALUES(ScoreID),
                    PlayerID = VALUES(PlayerID), 
                    ChapterID = VALUES(ChapterID), 
                    Score = VALUES(Score)";

    mysqli_query($conn, $query);
}
function DeleteScore($conn, $json)
{
    $UserID = $json['UserID'];
    $query = "DELETE FROM `scoring` WHERE UserID = '$UserID'";

    mysqli_query($conn, $query);
}

// ----------------- Quiz -----------------
function GetQuiz($conn)
{
    $query = "SELECT * FROM `quiz`";
    $result = mysqli_query(mysql: $conn, query: $query);

    $data = mysqli_fetch_all(result: $result, mode: MYSQLI_ASSOC);

    echo json_encode($data);
}
function AddQuiz($conn, $json)
{
    $ID = $json['ID'];
    $Question = $json['Question'];
    $ChoiceA = $json['ChoiceA'];
    $ChoiceB = $json['ChoiceB'];
    $ChoiceC = $json['ChoiceC'];
    $ChoiceD = $json['ChoiceD'];
    $CorrectAnswer = $json['CorrectAnswer'];

    $query = "INSERT INTO `quiz` (ID, Question, ChoiceA, ChoiceB, ChoiceC, ChoiceD, CorrectAnswer)
        VALUES ('$ID', '$Question', '$ChoiceA', '$ChoiceB', '$ChoiceC', '$ChoiceD', '$CorrectAnswer') 
        ON DUPLICATE KEY UPDATE 
            ID = VALUES(ID), 
            Question = VALUES(Question), 
            ChoiceA = VALUES(ChoiceA), 
            ChoiceB = VALUES(ChoiceB), 
            ChoiceC = VALUES(ChoiceC), 
            ChoiceD = VALUES(ChoiceD), 
            CorrectAnswer = VALUES(CorrectAnswer)";

    mysqli_query($conn, $query);
}
function DeleteQuiz($conn, $json)
{
    $ID = $json['ID'];
    $query = "DELETE FROM `quiz` WHERE ID = '$ID'";

    mysqli_query($conn, $query);
}

// ----------------- Player Data -----------------
function GetPlayerData($conn)
{
    $query = "SELECT * FROM `playerdata`";
    $result = mysqli_query($conn, $query);

    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    echo json_encode($data);
}
function AddPlayerData($conn, $json)
{
    $PlayerID = $json['PlayerID'];
    $PlayerSavePoint = $json['PlayerSavePoint'];
    $ChapterNumber = $json['ChapterNumber'];
    $LessonNumber = $json['LessonNumber'];

    $query = "INSERT INTO `playerdata` (PlayerID, PlayerSavePoint, ChapterNumber, LessonNumber)
        VALUES ('$PlayerID', '$PlayerSavePoint', '$ChapterNumber', '$LessonNumber') 
        ON DUPLICATE KEY UPDATE 
            PlayerID = VALUES(PlayerID), 
            PlayerSavePoint = VALUES(PlayerSavePoint), 
            ChapterNumber = VALUES(ChapterNumber), 
            LessonNumber = VALUES(LessonNumber)";

    mysqli_query($conn, $query);
}
function DeletePlayerData($conn, $json)
{
    $UserID = $json['UserID'];
    $query = "DELETE FROM `playerdata` WHERE UserID = '$UserID'";

    mysqli_query($conn, $query);
}
