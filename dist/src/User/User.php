<?php

namespace User;

class User
{
  private $username;
  private $permissions;
  private $isAuthenticated;

  public function __construct()
  {
    $this->username = null;
    /* standard permissions for anon users */
    $this->permissions = $this->anonPermissions();
    $this->isAuthenticated = false;
  }

/**
 * 
 */
  static function getFromSession($container, Session $session)
  {
    $user = new User();
    $username = $session->get('username');
    if($username)
    {
      $userModel = new \User\UserModel($container['db']);
      $userData = $userModel->getUser($username);
      if($userData)
      {
        $user->username = $username;
        $user->isAuthenticated = true;
        $user->permissions = $userModel->getPermissions($username);
      }
    }
    return $user;
  }

}