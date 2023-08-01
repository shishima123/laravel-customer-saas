<?php

namespace Modules\Api\App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\ChangePasswordRequest;
use App\Repositories\UserRepository;

class SystemController extends ApiController
{
    public function __construct(public UserRepository $userRepo)
    {
        $this->middleware('role:' . Role::ADMIN->value)->only(['changePasswordPost']);
    }

    public function changePasswordPost(ChangePasswordRequest $request)
    {
        $rs = $this->userRepo->updatePassword($request, auth()->user());
        if ($rs) {
            return $this->successResponse(__('message.notify.success.update'));
        }
        return $this->errorResponse(__('message.notify.error.update'));
    }
}
