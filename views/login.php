<?php
use furnace\core\Config;
?>

<form method="POST" action="<?php echo href(Config::Get('Auth.module.url.loginPostTarget'))?>">
  <input type="hidden" name="after" value="<?php echo $_data['afterLogin']?>"/>
  <label for="username">Username:</label>
  <input type="text" id="username" name="username"/>
  <label for="password">Password:</label>
  <input type="password" id="password" name="password"/>
  <input type="submit" value="Login"/>
</form>
