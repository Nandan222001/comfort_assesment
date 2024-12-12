<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Services\UserService;
use App\Models\User;
use Illuminate\Http\Request;
use ReflectionFunctionAbstract;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $allUsers = User::query()->select(['id', 'name','username', 'email'])->latest('id')->get();
        if ($request->ajax()) {
            $data = DataTables::of($allUsers)
            ->addIndexColumn()
            ->addColumn('action', function ($user) {
                $editRoute = route('user.edit', ['id' => $user->id]);
                $deleteRoute = route('user.destroy', ['id' => $user->id]);
                $csrfToken = csrf_token(); 
                return <<<HTML
                <a href="{$editRoute}" class="btn btn-sm btn-dark" title="Edit">Edit</a>
                <form action="{$deleteRoute}" method="POST" style="display:inline-block;">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="_token" value="{$csrfToken}">
                    <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this user?')">
                        Delete
                    </button>
                </form>
                HTML;
            })
            
            ->rawColumns(['action','cover'])
            ->toJson();
            return $data;
        }

        return view('user.index');

    }

    public function create(Request $request)
    {
        return view('user.create');
    }

    public function store(UserCreateRequest $request)
    {
        $userData = $request->validated();
        $user = $this->userService->save($userData);
        if(empty($user)) return back()->with('error', 'Failed to create User');
        return back()->with('success', 'User created successfully');
    }

    public function edit(Request $request, $userId)
    {
        $user = User::where('id', $userId)->with('details')->first();
        if(empty($user)) return to_route('user.index')->with('error', 'User not found');
        $additional = [];
        foreach($user->details as $detail) {
            $additional[$detail->key] = $detail->value;
        }
        $additional['firstname'] = $user->name;
        $additional['middlename'] = $user->middle;
        $additional['lastname'] = $user->last;
        $additional['prefix'] = $additional['gender'] === 'Male' ? 'Mr.' : 'Mrs.';

        return view('user.update', compact('user','additional'));
    }

    public function update(UserUpdateRequest $request, $userId)
    {
        $userData = $request->validated();
        $user = $this->userService->save($userData);
        if(empty($user)) return back()->with('error', 'Failed to update User');
        return back()->with('success', 'User updated successfully');
    }

    public function destroy(Request $request, $userId)
    {
        $result = $this->userService->delete($userId);

        if (!$result) {
            return back()->with('error', 'User not found or failed to delete');
        }
        return back()->with('success', 'User deleted successfully');
    }


}
