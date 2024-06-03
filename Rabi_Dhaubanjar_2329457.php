<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body{
	display: flex;
  flex-direction: row-reverse;
	justify-content: center;
	align-items: center;
	height: 100vh;
	margin: 0;
	background: #222;
font-size: 120%;
background-size: cover;
background-image: url("https://source.unsplash.com/1600x900/?nature");

}
.card {
background: #000000d0;
color: white;
padding: 2em;
border-radius: 30px;
width: 100%;
max-width: 420px;
margin: 1em;
margin-top: 50px;
}
.search{
	display: flex;
	align-items: center;
	justify-content: center;


}
button{
	margin:0.5em;
	border-radius: 50%;
	border: none;
	height:44px;
	width: 44px;
	outline: none;
	background: #7c7c7c2b;
	color: white;
	transition:0.2s ease-in-out ;
}
button:hover{
	background: #fffefe6b;
}
input.search-bar{
	border: none;
	outline: none;
	padding: 0.4em 1em;
	border-radius: 24px;
	background-color: #7c7c7c2b;
	color: white;
	font-family: inherit;
	font-size: 105%;
width:calc(100%-80px);
}
h1.temp{
	margin: 0;
	margin-bottom: 0.4em;
}
.description{
	text-transform: capitalize;
	margin-left: 8px;
}
.icon{
	display: flex;
	align-items: center;
}


</style>

  <title>Weather app</title>
</head>

<body>
  
  <?php
  header("Access-Control-Allow-Origin: *");
  header("Access-Control-Allow-Header: *");
$api_key = "e58a1ac74eccc30d15d02689344dd3a0";
if(isset($_POST['search-button'])){
  $city = $_POST['search-button'];
}else{
  $city = "Bassetlaw";
}




// Connect to MySQL database
$host = "sql209.epizy.com";
$user = "epiz_34168126";
$password = "0aaddWyvNSaN";
$database = "epiz_34168126_rabi";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


// Retrieve weather data for the past 7 days
$sql_check_city = "SELECT * FROM weather WHERE city_name = '$city' ORDER BY date DESC LIMIT 1";
$result_check_city = mysqli_query($conn, $sql_check_city);

if (mysqli_num_rows($result_check_city) > 0) {
    // City already exists, update the weather data
    $row = mysqli_fetch_assoc($result_check_city);
    $last_date = $row['date'];
    $date_to_update = date("Y-m-d H:i:s", strtotime('-1 day', strtotime($last_date)));
    

    // Retrieve weather data for the past day
    $url = "https://api.openweathermap.org/data/2.5/forecast?q=$city&appid=$api_key&units=metric&cnt=8";
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (isset($data['city']) && isset($data['city']['name'])) {
        // Update the weather table with the new data
        for ($i = 0; $i < 7; $i++) {
            $day = $data["list"][$i];
            $num_days = $i + 1;
            $date = date("Y-m-d H:i:s", strtotime("-$num_days day", $day["dt"]));
            
            // Check if the date is within the range to update
            if ($date >= $date_to_update) {
                $temp = $day["main"]["temp"];
                $weather = $day["weather"][0]["description"];
                $icon = $day["weather"][0]["icon"];
                $windspeed = $day["wind"]["speed"];
                $humidity = $day["main"]["humidity"];
                
                $sql = "UPDATE weather SET temperature = $temp, weather = '$weather', icon = '$icon', windspeed = $windspeed, humidity = $humidity WHERE city_name = '$city' AND date = '$date'";

                
                mysqli_query($conn, $sql);
            }
        }
    } 
 }else {
    // City doesn't exist, insert the weather data
    $url = "https://api.openweathermap.org/data/2.5/forecast?q=$city&appid=$api_key&units=metric";
$response = file_get_contents($url);
$data = json_decode($response, true);
if (isset($data['city']) && isset($data['city']['name'])) {
    // Insert the weather data for the city
    for ($i = 0; $i < 7; $i++) {
        $day = $data["list"][$i];
        $num_days = $i + 1;
        $date = date("Y-m-d H:i:s", strtotime("-$num_days day", $day["dt"]));
        $temp = $day["main"]["temp"];
        $weather = $day["weather"][0]["description"];
        $icon = $day["weather"][0]["icon"];
        $windspeed = $day["wind"]["speed"];
        $humidity = $day["main"]["humidity"];
        
        $sql = "INSERT INTO weather (city_name, date, temperature, weather, icon, windspeed, humidity) VALUES ('$city', '$date', $temp, '$weather', '$icon', $windspeed, $humidity)";
        
        mysqli_query($conn, $sql);
    }
} else {
    // City not found
    die("<h1>No city found.</h1>");
}
}



$sql = "SELECT * FROM weather WHERE city_name='$city'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  echo "<table style=
    border: 1px solid white;
    border-collapse: collapse;
    max-height: 400px;
    ";
  echo "<tr style='background-color: #333; color: white;'><th style='border: 1px solid white; padding: 8px;'>Date and Time</th><th style='border: 1px solid white; padding: 8px;'>Temperature</th><th style='border: 1px solid white; padding: 8px;'>Weather</th><th style='border: 1px solid white; padding: 8px;'>Icon</th><th style='border: 1px solid white; padding: 8px;'>Windspeed</th><th style='border: 1px solid white; padding: 8px;'>Humidity</th></tr>";
  while ($row = mysqli_fetch_assoc($result)) {
      $icon_url = "https://openweathermap.org/img/w/" . $row["icon"] . ".png";
      echo "<tr style='border: 1px solid white;background-color:#333;'><td style='border: 1px solid white; padding: 8px; color: white;'>" . $row["date"] . "</td><td style='border: 1px solid white; padding: 8px; color: white;'>" . $row["temperature"] . " °C</td><td style='border: 1px solid white; padding: 8px; color: white;'>" . $row["weather"] . "</td><td style='border: 1px solid white; padding: 8px;'><img src='$icon_url' alt='" . $row["weather"] . "'></td><td style='border: 1px solid white; padding: 8px; color: white;'>" . $row["windspeed"]."km/hr" . "</td><td style='border: 1px solid white; padding: 8px; color: white;'>" . $row["humidity"]."%" . "</td></tr>";
  }
  echo "</table>";
} else {
  echo "No data found";
}
mysqli_close($conn);    
  ?>


  
 <div class="card ">
  <div class="search">
  <form method="POST">
    <input type="text" class="search-bar" placeholder="Search" name="search-button">
    <button type="submit"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 1024 1024" height="1.5em" width="1.5em" xmlns="http://www.w3.org/2000/svg"><path d="M909.6 854.5L649.9 594.8C690.2 542.7 712 479 712 412c0-80.2-31.3-155.4-87.9-212.1-56.6-56.7-132-87.9-212.1-87.9s-155.5 31.3-212.1 87.9C143.2 256.5 112 331.8 112 412c0 80.1 31.3 155.5 87.9 212.1C256.5 680.8 331.8 712 412 712c67 0 130.6-21.8 182.7-62l259.7 259.6a8.2 8.2 0 0 0 11.6 0l43.6-43.5a8.2 8.2 0 0 0 0-11.6zM570.4 570.4C528 612.7 471.8 636 412 636s-116-23.3-158.4-65.6C211.3 528 188 471.8 188 412s23.3-116.1 65.6-158.4C296 211.3 352.2 188 412 188s116.1 23.2 158.4 65.6S636 352.2 636 412s-23.3 116.1-65.6 158.4z"></path></svg></button>
  </form>
</div>
  

<div class="weather loading">
  <h1 class="city">Weather in <?php echo $city ?></h1>
  <div class="temp"><?php echo "Temperature: $temp ℃" ?></div>
  <img src="https://openweathermap.org/img/wn/<?php echo $icon ?>.png" alt="Weather icon" class="icon"/>
  <div class="description"><?php echo $weather ?></div>
  <div class="humidity"><?php echo "Humidity: " . $humidity ."%"?></div>
  <div class="wind"><?php echo "Wind speed: " . $windspeed. "km/h"?></div>
  <div class="rain"><?php echo "Rain: no data availabe" . $rain ?></div>
  <p>Date and Time: <span id="datetime"><?php echo date("F j, Y, g:i a"); ?></span></p>

  <div id="txt"></div>
</div>

  </div>
  <button style="background-color: #4CAF50;">
  <a href="view.php" style="text-decoration:none;color:white;">View all data</a>
</button>



</body>
<script>
const cityForm = document.querySelector('form');
const dataDiv = document.querySelector('.weather');

const apiKey = 'e58a1ac74eccc30d15d02689344dd3a0';
const city = '<?php echo $city; ?>';
const url = `https://api.openweathermap.org/data/2.5/forecast?q=${city}&appid=${apiKey}&units=metric`;

// Check if weather data for the searched city exists in local storage
const weatherData = getWeatherDataFromLocalStorage(city);

if (weatherData) {
  console.log('Retrieving weather data from local storage and database...');
  displayWeatherData(weatherData);
} else if (navigator.onLine) {
  console.log('Retrieving weather data from API...');
  fetchWeatherData(url, city);
} else {
  dataDiv.innerHTML = 'No internet connection and no stored weather data available.';
}

function fetchWeatherData(url, city) {
  console.log('Fetching weather data from API...');
  fetch(url)
    .then(response => response.json())
    .then(data => {
      const weatherData = {
        city: data.city.name,
        temperature: data.list[0].main.temp,
        weather: data.list[0].weather[0].description,
        icon: data.list[0].weather[0].icon,
        windSpeed: data.list[0].wind.speed,
        humidity: data.list[0].main.humidity
      };
      const timestamp = new Date().getTime();
      const weatherDataString = JSON.stringify(weatherData);
      localStorage.setItem(`weatherData_${city}`, weatherDataString);

      displayWeatherData(weatherData);
    })
    .catch(error => {
      console.error(error);
      const weatherData = getWeatherDataFromLocalStorage(city);
      if (weatherData) {
        console.log('Retrieving weather data from local storage and database...');
        displayWeatherData(weatherData);
      } else {
        dataDiv.innerHTML = 'An error occurred while fetching the weather data. Please try again or check your internet connection.';
      }
    });
}

function getWeatherDataFromLocalStorage(city) {
  const key = `weatherData_${city}`;
  const weatherDataString = localStorage.getItem(key);
  return weatherDataString ? JSON.parse(weatherDataString) : null;
}

function displayWeatherData(weatherData) {
  dataDiv.innerHTML = `
    <div>
      <h1 class="city">Weather in ${weatherData.city}</h1>
      <div class="temp">Temperature: ${weatherData.temperature} ℃</div>
      <img src="https://openweathermap.org/img/wn/${weatherData.icon}.png" alt="Weather icon" class="icon"/>
      <div class="description">${weatherData.weather}</div>
      <div class="humidity">Humidity: ${weatherData.humidity}%</div>
      <div class="wind">Wind speed: ${weatherData.windSpeed} km/h</div>
      <div class="rain">Rain: no data available</div>
      <p>Date and Time: <span id="datetime">${new Date().toLocaleString()}</span></p>
    </div>
  `;
}

function appendWeatherData(city) {
  dataDiv.innerHTML = '';

  for (let i = 0; i < localStorage.length; i++) {
    const key = localStorage.key(i);
    if (key.startsWith('weatherData_')) {
      const weatherDataString = localStorage.getItem(key);
      const weatherData = JSON.parse(weatherDataString);

      if (weatherData.city.toLowerCase() === city.toLowerCase()) {
        displayWeatherData(weatherData);
      }
    }
  }
}


</script>
</html>