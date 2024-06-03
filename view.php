<style>
    body {
        background-image: url("https://source.unsplash.com/1600x900/?nature");
        background-size: cover;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 20px;
        font-size: 16px;
        color: white;
    }

    th, td {
        border: 1px solid white;
        padding: 10px;
    }

    th {
        background-color: #333;
        text-align: left;
        font-weight: bold;
    }

    tr:nth-child(even) {
        background-color: #222;
    }

    tr:hover {
        background-color: #444;
        cursor: pointer;
    }

    img {
        width: 40px;
        height: 40px;
        vertical-align: middle;
    }
</style>

<?php
$host = "sql209.epizy.com";
$user = "epiz_34168126";
$password = "0aaddWyvNSaN";
$database = "epiz_34168126_rabi";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
<button style="float:right;"><a href="Rabi_Dhaubanjar_2329457.php" style="text-decoration:none;">Home</a></button>
<center>
    <h1>Weather Table</h1>
    
    <?php
    $sql = "SELECT * FROM weather";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo "<table style='border: 1px solid white; border-collapse: collapse;'>";
        echo "<tr style='background-color: #333; color: white;'><th style='border: 1px solid white; padding: 8px;'>Date and Time</th><th style='border: 1px solid white; padding: 8px;'>City</th><th style='border: 1px solid white; padding: 8px;'>Temperature</th><th style='border: 1px solid white; padding: 8px;'>Weather</th><th style='border: 1px solid white; padding: 8px;'>Icon</th><th style='border: 1px solid white; padding: 8px;'>Windspeed</th><th style='border: 1px solid white; padding: 8px;'>Humidity</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            $icon_url = "https://openweathermap.org/img/w/" . $row["icon"] . ".png";
            echo "<tr style='border: 1px solid white;background-color:#333;'><td style='border: 1px solid white; padding: 8px; color: white;'>" . $row["date"] . "</td><td style='border: 1px solid white; padding: 8px; color: white;'>" .$row["city_name"] . "</td><td style='border: 1px solid white; padding: 8px; color: white;'>". $row["temperature"] . " °C</td><td style='border: 1px solid white; padding: 8px; color: white;'>" . $row["weather"] . "</td><td style='border: 1px solid white; padding: 8
px;'><img src='$icon_url' alt='" . $row["weather"] . "'></td><td style='border: 1px solid white; padding: 8px; color: white;'>" . $row["windspeed"]."km/hr" . "</td><td style='border: 1px solid white; padding: 8px; color: white;'>" . $row["humidity"]."%" . "</td></tr>";
}
echo "</table>";
} else {
echo "No data found";
}
?>

</center>