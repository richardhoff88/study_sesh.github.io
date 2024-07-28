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

require_once('databaseDef.php');

$dbConnection = openDB();

$userID = mysqli_real_escape_string($dbConnection,$userID);

if ($role=='student') {
   $sqlcmd = "SELECT firstname,lastname,city,county,state,zip,country,distance,description,videoID FROM students WHERE userID = '$userID'";
} else {
   $sqlcmd = "SELECT name,address,city,county,state,zip,country,description,website FROM orgs WHERE userID = '$userID'";
}

$result = mysqli_query($dbConnection,$sqlcmd);
$row = mysqli_fetch_assoc($result);

if (empty($row)) {
   $firstname = ''; $lastname = ''; $name = ''; $address = ''; $city = ''; $county = ''; $state = ''; $zip = ''; $country = 'USA'; $distance = 20; $description = ''; $videoID = ''; $website = '';
} else {
   if ($role=='student') {
      $firstname = $row['firstname'];
      $lastname = $row['lastname'];
      $distance = $row['distance'];
      $videoID = $row['videoID'];
   } else {
      $name = $row['name'];
      $address = $row['address'];
      $website = $row['website'];
   }
   $city = $row['city'];
   $county = $row['county'];
   $state = $row['state'];
   $zip = $row['zip'];
   $country = $row['country'];
   $description = $row['description'];
}

closeDB($dbConnection);
?>
<HTML>
<HEAD>
<TITLE>Pianize</TITLE>
<meta name="author" content="Richard Hoffmann">
<meta name="description" content="Connect student volunteer music performers with event organizers">
<meta name="keywords" content="piano, violine, music, senior home, music events">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<script>
function getVideoID(link) {
   var fields;

   if (link.includes("?")) {
      fields = link.split(/=|&/);
      video_id = fields[1];
   } else {
      fields = link.split("/");
      video_id = fields[fields.length-1];
   }
   return video_id;
}

function upload() {
   var link = document.getElementById('video_link').value;
   var video_id = getVideoID(link);

   if (video_id!="") {
      document.getElementById("video_container").innerHTML = "<iframe class='responsive-iframe' src='https://www.youtube.com/embed/" + video_id + "' frameborder='0' allow='autoplay; encrypted-media' allowfullscreen></iframe>";
   }
}

function selectRegion(region) {
   if (region=='USA') selectUSA();
   if (region=='Germany') selectGermany();	
   if (region=='Taiwan') selectTaiwan();
}

function selectUSA() {
    <?php

    $stateCode = array('AL','AK','AZ','AR','CA','CO','CT','DE','DC','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','PR','RI','SC','SD','TN','TX','UT','VT','VA','VI','WA','WV','WI','WY');

    $stateName = array('Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District of Columbia','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Puerto Rico','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virginia','Virgin Islands','Washington','West Virginia','Wisconsin','Wyoming');

    if ($state=='') echo "var selectedState=false;"; else echo "var selectedState=true;";

    echo "var options='';";
    for ($i=0; $i<count($stateCode); $i++) {
    	echo "options+='<option value=" . '"' . $stateCode[$i] . '"' . "';";
	if ($state==$stateCode[$i]) echo "options+=' selected';";
	echo "options+='>$stateName[$i]</option>';";
    }
    echo "var zip = '$zip';";
    ?>
	

   var selectedNone="";
   if (!selectedState) { 
      selectedNone = "<option value='none' selected disabled hidden>Select state</option>";
   }
   var enterZip="&nbsp;Zip code <input id='zip' value='" + zip + "' type='text' size='5' maxlength='5'>";
   var countyStr = "<input id='county' value='' hidden>";
   document.getElementById("select_region").innerHTML = "State <select name='state' id='state'>" + selectedNone + options + "</select>" + enterZip + countyStr;	 
}

function selectGermany() {
<?php echo "var zip = '$zip';"; ?>	 
   var enterZip="Postleitzahl <input id='zip' value='" + zip + "' type='text' size='5' maxlength='5'>";
   var countyStr = "<input id='county' value='' hidden>";
   var stateStr = "<input id='state' value='' hidden>";
   document.getElementById("select_region").innerHTML = enterZip + countyStr + stateStr;	
}

function selectTaiwan() {
   <?php
   $countyName = array('Changhua','Chiayi','Chiayi City','Hinchu','Hinchu City','Hualien','Kaohsiung','Keelung','Kinmen','Lienchiang','Miaoli','Nantou','New Taipei','Penghu','Pingtung','Taichung','Tainan','Taipei','Taitung','Taoyuan','Yilan','Yunlin');

   if ($county=='') echo "var selectedCounty=false;"; else echo "var selectedCounty=true;";

   echo "var options='';";
   for ($i=0; $i<count($countyName); $i++) {
       echo "options+='<option value=" . '"' . $countyName[$i] . '"' . "';";
       if ($county==$countyName[$i]) echo "options+=' selected';";
       echo "options+='>$countyName[$i]</option>';";
    }
    ?>

    var stateStr = "<input id='state' value='' hidden>";
    var zipStr = "<input id='zip' value='' hidden>";

    var selectedNone="";
    if (!selectedCounty) { 
       selectedNone = "<option value='none' selected disabled hidden>Select county</option>";
    }
   
    document.getElementById("select_region").innerHTML = "County <select name='county' id='county'>" + selectedNone + options + "</select>" + stateStr + zipStr;	 
}

function save() {
<?php
   if ($role=='student') {
      echo "var firstname = document.getElementById('firstname').value;";
      echo "var lastname = document.getElementById('lastname').value;";
      echo "var distance = document.getElementById('distance').value;";
      echo "var link = document.getElementById('video_link').value;";
      echo "var video_id = getVideoID(link);"; 
   } else {
      echo "var name = document.getElementById('name').value;";
      echo "var address = document.getElementById('address').value;";
      echo "var website = document.getElementById('website').value;";
   }
?>
   var city = document.getElementById('city').value;
   var county = document.getElementById('county').value;
   var state = document.getElementById('state').value;
   var zip = document.getElementById('zip').value;
   var country = document.getElementById('country').value;
   var description = document.getElementById('description').value;

   var xhr = new XMLHttpRequest();
	    
   xhr.open('POST', 'saveProfile.php', true);

<?php
   if ($role=='student') {
      echo "var data = {'firstname': firstname, 'lastname': lastname, 'city': city, 'county': county, 'state': state, 'zip': zip, 'country': country, 'distance': distance, 'description': description, 'videoID': video_id};";
      echo "var type = 'student';";
   } else {
      echo "var data = {'name': name, 'address': address, 'city': city, 'county': county, 'state': state, 'zip': zip, 'country': country, 'description': description, 'website': website};";
      echo "var type = 'org';";
   }
?>	     
   
   xhr.setRequestHeader("Content-type","application/json; charset=UTF-8");
   xhr.onreadystatechange = function() {
      if (xhr.readyState == 4 && xhr.status == 200) {
         if (xhr.responseText == "fail") {
            alert("Invalid zip code!");
         } else {
            window.location.href= 'profile.php?ID=<?php echo $userID;?>&type=' + type; 
         }      
      }
   }	    
   xhr.send(JSON.stringify(data)); 
}
</script>
<style>
A:link { COLOR: blue; TEXT-DECORATION: none }
A:visited { COLOR: blue; TEXT-DECORATION: none }
A:active { COLOR: blue; TEXT-DECORATION: none }
A:hover { COLOR: blue; TEXT-DECORATION:underline }

img {width: 100%}

body { font-family: helvetica,arial,sans-serif; font-variant: normal; font-style: normal; font-size: 17px; background-color: #ffffff; color: #000000}

b { font-family: helvetica,arial,sans-serif; font-weight: bold}

h1 { font-family: helvetica,arial,sans-serif; text-align: center}

.responsive-iframe {
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  width: 100%;
  height: 100%;
}

.video_container {
  position: relative;
  overflow: hidden;
  width: 100%;
  padding-top: 56.25%; /* 16:9 Aspect Ratio (divide 9 by 16 = 0.5625) */
}

@media only screen and (min-width: 600px) {
   .video_container {
       position: relative;
       overflow: hidden;
       width: 600px;
       padding-top: 337px;
   }
}
</style>
</HEAD>
<?php if ($role=='org' || $videoID=='') echo "<BODY onload='selectRegion(" . '"' . $country . '"' . ")'>"; else echo "<BODY onload='upload();selectRegion(" . '"' . $country . '"' . ")'>";?>
<p align="right" style="color:black;font-size: 20px;"><a href="homepage.php">Home</a> | <a href="logout.php">Logout</a></p>
<br>
<center><h2>Edit Profile</h2></center>
<br>
<?php
if ($role=='student') {
   echo "First name <input id='firstname' value='$firstname' type='text' size='30' maxlength='255'><br><br>";
   echo "Last name <input id='lastname' value='$lastname' type='text' size='30' maxlength='255'>";
} else {
   echo "Organization name <input id='name' value='$name' type='text' size='30' maxlength='255'>";
}
?>
<br><br>
Country
<select name="country" id="country" onChange="selectRegion(this.value)">
<option value="USA" <?php if ($country=='USA') echo 'selected';?>>USA</option>
<option value="Germany" <?php if ($country=='Germany') echo 'selected';?>>Germany</option>
<option value="Taiwan" <?php if ($country=='Taiwan') echo 'selected';?>>Taiwan</option>
</select><br><br>
<?php
if ($role!='student') {
   echo "Address <input id='address' value='$address' type='text' size='30' maxlength='255'><br><br>";
}
?>
City <input id="city" value="<?php echo $city;?>" type="text" size="30" maxlength="255">
<br><br>
<div class='select_region' id='select_region'></div>
<br>
<?php
if ($role=='student') {
   echo "Distance willing to travel <input id='distance' value='$distance' type='number' min='1' max='1000'>miles<br><br>";
   echo "Describe yourself and highlight your accomplishments";
} else {
   echo "Describe your organization";
}
?>
<br>
<textarea name="description" id="description" cols="54" rows="10"><?php echo $description;?></textarea><br><br>

<?php
if ($role=='student') {
   if ($videoID != '') $link = "https://www.youtube.com/embed/" . $videoID; else $link = "";
   echo "YouTube link <input id='video_link' value='$link' type='text' size='35' maxlength='255'> ";
   echo "<button onclick='upload()'>Upload video</button>";
   echo "<div class='video_container' id='video_container'></div>";
} else {
   echo "Website <input id='website' value='$website' type='text' size='30' maxlength='255'><br>";
}
?>
<br>
<center>
<button style = 'font-size: 18px;' onclick='save()'>Save</button>
</center>
</BODY>
</HTML>











