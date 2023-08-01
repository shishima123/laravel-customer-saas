<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use App\Repositories\UserRepository;

class SystemController extends Controller
{
    public function __construct(public UserRepository $userRepo)
    {
    }

    public function changeLanguage($lang)
    {
        if (! in_array($lang, ['en', 'ja'])) {
            abort(400);
        }
        return redirect()->back()->withCookie(cookie('locale', $lang));
    }

    public function adminAccountGet()
    {
        $user = auth()->user();
        $subpage = '_account';
        return view('admin-setting.update', compact('user', 'subpage'));
    }

    public function changePasswordPost(ChangePasswordRequest $request, User $user)
    {
        try {
            $this->userRepo->updatePassword($request, $user);
            return redirect()->back()->with('success', __('message.notify.success.update'));
        } catch (\Exception $e) {
            report($e);
            return redirect()->back()->with('error', __('message.notify.error.update'));
        }
    }
}
