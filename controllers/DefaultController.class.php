<?php
use furnace\core\Config;
use furnace\core\Furnace;
use furnace\controller\Controller;
use furnace\controller\Input;

class DefaultController extends Controller {

  public function __construct($request, $response) {
    parent::__construct($request, $response);
    $this->load('\Auth\classes\Auth');
  }
  
  public function login() {
    if ($this->request->method == HTTP_POST) {
      try {
        $this->Auth->login('password', Input::post());
        $this->response->redirect(Input::Post('after','/'));
      } catch (\Exception $e) {
        $this->flash("Invalid credentials provided...","error");
      }
    }
    $this->set('afterLogin', Input::Get('after', '/'));
  }

  public function logout() {
    $this->Auth->logout();
    $this->response->redirect('/');
  }

  public function register($afterRegistration = '/') {
    if ($this->request->method == HTTP_POST) {
      try {
        $this->Auth->register(Input::post(),array('password2'));
        $this->flash("Registration successful!");
        $this->response->redirect($afterRegistration);
      } catch (\Exception $e) {
        $this->flash("There was a problem with your registration: " 
          . $e->getMessage());
      }
    }
  }
}

