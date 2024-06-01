<?php
if (isset($_POST["dates"])) {
  $dates = $_POST["dates"];
  echo "Selected dates:<br>";
  foreach ($dates as $date) {
    echo "{$date}<br>";
  }
} else {
  echo "No dates selected.";
}
?>

