<?php

namespace App\Http\Controllers;

use App\Services\UsersService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $usersService;

    public function __construct()
    {
        $this->usersService = new UsersService();
    }

    public function updateUsers(Request $request)
    {
        foreach ($request as $user)
        {
            if($this->usersService->updateUsers($user))
            {
                return Redirect::back()->with(['success', __('Aktualizacja zakończona powodzeniem!')]);
            }
            return Redirect::back()->withErrors(['error', __('Wystąił błąd podczas aktualizaji użytkownika')]);
        }

    }

    public function storeUsers(Request $request)
    {
        foreach ($request as $user)
        {
            if($this->usersService->storeUsers($user))
            {
                return Redirect::back()->with(['success', __('Wszyscy użytkownicy zostali pomyślnie stworzeni')]);
            }
            return Redirect::back()->withErrors(['error', __('Wystąił błąd podczas tworzenia użytkownika')]);
        }

    }

}