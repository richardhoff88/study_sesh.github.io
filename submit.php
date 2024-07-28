<?php
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "study_tracker";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studyMethod = $_POST['studyMethod'];
    $otherStudyMethod = $_POST['otherStudyMethod'];
    $cameraConsent = $_POST['cameraConsent'];
    $screenConsent = $_POST['screenConsent'];
    $studyDescription = $_POST['studyDescription'];
    $currentState = $_POST['currentState'];
    $studyFrequency = $_POST['studyFrequency'];
    $otherStudyFrequency = $_POST['otherStudyFrequency'];
    $studyDays = $_POST['studyDays'];
    $studyTimes = $_POST['studyTimes'];

    $sql = "INSERT INTO user_input (study_method, other_study_method, camera_consent, screen_consent, study_description, current_state, study_frequency, other_study_frequency, study_days, study_times)
    VALUES ('$studyMethod', '$otherStudyMethod', '$cameraConsent', '$screenConsent', '$studyDescription', '$currentState', '$studyFrequency', '$otherStudyFrequency', '$studyDays', '$studyTimes')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
