<?php
require_once('session.php');

if ($userID=='') {
   $location = 'Location: signin.php';
   header($location);
   exit;
}

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL & ~E_NOTICE);

// get POST data:
$data = json_decode(file_get_contents('php://input'), true);

if ($role=='student') {				      
      $firstname = $data['firstname'];		      
      $lastname = $data['lastname'];
      $distance = $data['distance'];
      $videoID = $data['videoID'];
} else {
      $name = $data['name'];
      $address = $data['address'];
      $website = $data['website'];
}
$city = $data['city'];
$county = $data['county'];
$state = $data['state'];
$zip = $data['zip'];
$country = $data['country'];
$description = $data['description'];

require_once('databaseDef.php');
$dbConnection = openDB();
  
$userID = mysqli_real_escape_string($dbConnection,$userID);
$city = mysqli_real_escape_string($dbConnection,$city);
$state = mysqli_real_escape_string($dbConnection,$state);
$zip = mysqli_real_escape_string($dbConnection,$zip);
$description = mysqli_real_escape_string($dbConnection,$description);

if ($country=='USA') {
   // look up location based on zip:
   $sqlcmd = "SELECT latitude, longitude from zip where zip='$zip'";
} elseif ($country=='Germany') {
  // look up location based on PLZ:
   $sqlcmd = "SELECT latitude, longitude from PLZ where PLZ='$zip'";
} else { // Taiwan
   // look up location based on county
   $sqlcmd = "SELECT latitude, longitude from Taiwan_counties where county='$county'";
}
$result = mysqli_query($dbConnection,$sqlcmd);
$row = mysqli_fetch_assoc($result);

if (empty($row)) {
   echo "fail";
   closeDB($dbConnection);
   exit;
}

$latitude = $row['latitude'];
$longitude = $row['longitude'];

if ($role=='student') {				      
      $firstname = mysqli_real_escape_string($dbConnection,$firstname);	      
      $lastname = mysqli_real_escape_string($dbConnection,$lastname);
      $distance = mysqli_real_escape_string($dbConnection,$distance);
      $videoID = mysqli_real_escape_string($dbConnection,$videoID);
      $sqlcmd = "INSERT INTO students (userID, firstname, lastname, city, county, state, zip, country, latitude, longitude, distance, description, videoID) values ('$userID', '$firstname', '$lastname', '$city', '$county', '$state', '$zip', '$country', '$latitude', '$longitude', '$distance', '$description', '$videoID') ON DUPLICATE KEY UPDATE firstname = VALUES(firstname), lastname = VALUES(lastname), city = VALUES(city), county = VALUES(county), state = VALUES(state), zip = VALUES(zip), country = VALUES(country), latitude = VALUES(latitude), longitude = VALUES(longitude), distance = VALUES(distance), description = VALUES(description), videoID = VALUES(videoID)";
} else {
      $name = mysqli_real_escape_string($dbConnection,$name);
      $address = mysqli_real_escape_string($dbConnection,$address);
      $website = mysqli_real_escape_string($dbConnection,$website);
      $sqlcmd = "INSERT INTO orgs (userID, name, address, city, county, state, zip, country, latitude, longitude, description, website) values ('$userID', '$name', '$address', '$city', '$county', '$state', '$zip', '$country', '$latitude', '$longitude', '$description', '$website') ON DUPLICATE KEY UPDATE name = VALUES(name), address = VALUES(address), city = VALUES(city), county = VALUES(county), state = VALUES(state), zip = VALUES(zip), country = VALUES(country), latitude = VALUES(latitude), longitude = VALUES(longitude), description = VALUES(description), website = VALUES(website)";
}

mysqli_query($dbConnection,$sqlcmd);

closeDB($dbConnection);
?> 