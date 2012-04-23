<?php

namespace Auth\classes;

use furnace\core\Config;
use furnace\core\Furnace;
use furnace\connections\Connections;
use furnace\controller\Controller;
use furnace\controller\Input;

class Auth {

  public function login($method,$data) {
    switch (strtolower($method)) {
      case 'password':
        return $this->password_auth($data);
      default:
        throw new \Exception("Authentication method '{$method}' not supported");
    }
  }

  public function register($data,$ignore = array()) {
    $method = Config::Get('Auth.registration.use');
    switch (strtolower($method)) {
      case 'password':
        return $this->password_register($data,$ignore);
      default:
        throw new \Exception("Registration method '{$method}' not supported");
    }
  }

  public function logout() {
    unset($_SESSION['_auth']);
    return true;
  }

  public static function test() {
    return isset($_SESSION['_auth']);
  }

  public function force($failureRedirect = null) {
    if ($failureRedirect === null)
      $failureRedirect = Config::Get('app.url.login');

    $route = Furnace::GetRequest()->route();
    
    if (!self::test()) {
      debug($_SESSION);
      Furnace::Redirect($failureRedirect . '?after=' . urlencode('/' . ltrim($route->raw,'/')));
    } else {
      return true;
    }
  }

  public static function user() {
    return (self::test())
      ? $_SESSION['_auth']
      : false;
  }

  public static function identity() {
    return (self::test())
      ? $_SESSION['_auth']['identity']
      : false;
  }

  /*=================================================================
   * PASSWORD (SQL table) AUTHENTICATION/REGISTRATION
  **===============================================================*/
  protected function password_auth($data) {
    $un = $data[Config::Get('Auth.password.identity')];
    $pw = $data[Config::Get('Auth.password.credential')];
    $pw_encrypt = false;

    $conn  = Config::Get('Auth.password.connection');
    $table = Config::Get('Auth.password.table');
    $identity   = Config::Get('Auth.password.identity');
    $credential = Config::Get('Auth.password.credential');
    $mail       = Config::Get('Auth.password.email');
    $hash       = Config::Get('Auth.password.hash');
    $salt       = Config::Get('Auth.password.salt');

    // Encrypt the provided password
    switch (strtoupper($hash)) {
      case 'MD5':
        $pw_encrypt = md5( $salt . $pw );
        break;
      default:
        $pw_encrypt = $pw;
    }

    $sql = "SELECT * FROM `{$table}` "
          ."WHERE `{$identity}`=:i AND `{$credential}`=:c "; 

    $conn = Connections::Get($conn);

    $stmt = $conn->prepare($sql);
    $stmt->execute(array(
      'i' => $un,
      'c' => $pw_encrypt
    ));

    $user = $stmt->fetch();
    
    if ($user) {
      $authdata = array(
         'method'    => 'password'
        ,'timestamp' => time()
        ,'identity'  => $un
        ,'email'     => $user[$mail]
        ,'data'      => $user);

      $_SESSION['_auth'] = $authdata; 
      
      return true;
    } else {
      throw new \Exception("Invalid credentials supplied");
    }
  }

  protected function password_register($data, $ignore = array()) {
    $conn  = Config::Get('Auth.password.connection');
    $table = Config::Get('Auth.password.table');
    $hash  = Config::Get('Auth.password.hash');
    $salt  = Config::Get('Auth.password.salt');
    $credential = Config::Get('Auth.password.credential');

    // Encrypt the provided password in place
    switch (strtoupper($hash)) {
      case 'MD5':
        $data[$credential] = md5( $salt . $data[$credential] );
      default:
        $data[$credential] = $data[$credential];
    }

    // Prepare the provided data
    $filtered = $data;
    foreach ($ignore as $i) {
      unset($filtered[$i]);
    }
    $keys  = array_keys($filtered);
    $stubs = array();
    foreach ($keys as $k) {
      $stubs[] = ":{$k}";
    }

    // Build the insert sql
    $sql = "INSERT INTO `{$table}` (`"
         . implode('`,`',$keys) . "`) VALUES("
         . implode(',', $stubs) . ") ";

    $conn = Connections::Get($conn);
    $stmt = $conn->prepare($sql);
    $stmt->execute($filtered);
  }
}
