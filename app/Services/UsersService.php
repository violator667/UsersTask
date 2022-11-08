<?php
namespace App\Services;

use User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UsersService
{
    private $userNameLenght = '10';
    private $checkKeys = ['name','login','email','password'];

    private function checkIfAllDataWasSent(array $user, $usedMethod = 'store'): bool
    {
        if($usedMethod!='store')
        {
            array_push($this->checkKeys, 'id');
        }
        foreach($this->checkKeys as $key)
        {
            if (array_key_exists($key, $user) && !empty($user[$key])) {
                return true;
            }
        }
        return false;
    }

    private function checkNameLenght(array $user): bool
    {
        if(strlen($user['name'] >= $this->userNameLenght))
        {
            return true;
        }
        return false;
    }

    private function checkEmail(array $user): bool
    {
        if(filter_var($user['email'], FILTER_VALIDATE_EMAIL))
        {
            return true;
        }
        return false;
    }

    public function checkUserDetails(array $user, $usedMethod = 'store'): bool
    {
      if($this->checkIfAllDataWasSent($user, $usedMethod))
      {
          if($this->checkNameLenght($user))
          {
            if($this->checkEmail($user))
            {
                return true;
            }
          }
      }
      return false;
    }

    public function updateUsers(array $users)
    {
        foreach($users as $user)
        {
            if($this->checkUserDetails($user, 'update'))
            {
                User::where('id',$user['id'])->update([
                    'name' => $user['name'],
                    'login' => $user['login'],
                    'email' => $user['email'],
                    'password' => Hash::make($user['password'])
                ]);
                return true;
            }
            return false;
        }
    }

    public function storeUsers(array $users): bool
    {
        foreach($users as $user)
        {
            if($this->checkUserDetails($user, 'store')) {
                if(User::where('email', '=', $user['email'])->count() == 0)
                {
                    $newUser = new User;
                    $newUser->name = $user['name'];
                    $newUser->login = $user['login'];
                    $newUser->email = $user['email'];
                    $newUser->password = Hash::make($user['password']);
                    $newUser->save();
                    return true;
                }
            }
            return false;
        }
    }

    public function sendMail($users): bool
    {
        foreach($users as $user)
        {
            if($this->checkUserDetails($user, 'mail'))
            {
                $details['email'] = $user['email'];
                //more user details if needed by the view
                dispatch(new App\Jobs\SendWelcomeEmailJob($details));
                return true;
            }
        }
        return false;
    }
}