<?php require 'config.php'; ?>

<!DOCTYPE html>
<html>
<body>


<form method="POST" action="<?php echo POST_DATA_URL;?>">
  <label for="apikey">Api Key:</label><br>
  <input type="text" id="api_key" name="api_key" value="<?php echo PROJECT_API_KEY;?>"><br>
  <label for="mq7">mq7:</label><br>
  <input type="text" id="mq7" name="mq7" value=""><br>
  <label for="mq135">mq135:</label><br>
  <input type="text" id="mq135" name="mq135" value=""><br><br>
  <label for="sharp">sharp:</label><br>
  <input type="text" id="sharp" name="sharp" value=""><br><br>
  <input type="submit" value="Submit">
</form> 

</body>
</html>