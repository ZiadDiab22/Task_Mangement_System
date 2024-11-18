<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\task;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'email|required',
            'password' => 'required',
            'phone_no' => 'required',
        ]);

        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'status' => false,
                'message' => "email is taken"
            ], 200);
        }

        if (User::where('phone_no', $request->phone_no)->exists()) {
            return response()->json([
                'status' => false,
                'message' => "phone_no is taken"
            ], 200);
        }

        $validatedData['password'] = bcrypt($request->password);

        $user = User::create($validatedData);

        $accessToken = $user->createToken('authToken')->accessToken;

        $user_data = User::where('id', $user->id)->first();

        return response()->json([
            'status' => true,
            'access_token' => $accessToken,
            'user_data' => $user_data
        ]);
    }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'password' => 'required',
            'name' => 'required'
        ]);

        if (!Auth::guard('web')->attempt(['password' => $loginData['password'], 'name' => $loginData['name']])) {
            return response()->json(['status' => false, 'message' => 'Invalid User'], 404);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        $user_data = auth()->user();

        return response()->json([
            'status' => true,
            'access_token' => $accessToken,
            'user_data' => $user_data
        ]);
    }

    public function addTask(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'due_date' => 'date',
        ]);

        $validatedData['user_id'] = auth()->user()->id;

        if ($request->has('description')) {
            $validatedData['description'] = $request->description;
        }

        task::create($validatedData);

        $data = task::where('user_id', auth()->user()->id)->get();

        return response([
            'status' => true,
            'message' => "done successfully",
            'tasks' => $data,
        ], 200);
    }

    public function showTasks()
    {
        $data = task::where('user_id', auth()->user()->id)->get();

        return response([
            'status' => true,
            'message' => "done successfully",
            'tasks' => $data,
        ], 200);
    }

    public function deleteTask($id)
    {
        if (!(task::where('id', $id)->exists())) {
            return response([
                'status' => false,
                'message' => 'not found, wrong id'
            ], 200);
        }

        if (!(task::where('id', $id)->where('user_id', auth()->user()->id)->exists())) {
            return response([
                'status' => false,
                'message' => 'this task for another user , you cant access to it'
            ], 200);
        }

        task::where('id', $id)->delete();
        $data = task::where('user_id', auth()->user()->id)->get();

        return response([
            'status' => true,
            'message' => "done successfully",
            'tasks' => $data,
        ], 200);
    }

    public function completeTask($id)
    {
        if (!(task::where('id', $id)->exists())) {
            return response([
                'status' => false,
                'message' => 'not found, wrong id'
            ], 200);
        }

        if (!(task::where('id', $id)->where('user_id', auth()->user()->id)->exists())) {
            return response([
                'status' => false,
                'message' => 'this task for another user , you cant access to it'
            ], 200);
        }

        $task = task::find($id);
        $task->completed = 1;
        $task->save();

        $data = task::where('user_id', auth()->user()->id)->get();

        return response([
            'status' => true,
            'message' => "done successfully",
            'tasks' => $data,
        ], 200);
    }

    public function editTask(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'date' => 'date',
        ]);

        if (!(task::where('id', $request->id)->exists())) {
            return response()->json([
                'status' => false,
                'message' => "Wrong ID , not exist"
            ], 200);
        }


        if (!(task::where('id', $request->id)->where('user_id', auth()->user()->id)->exists())) {
            return response([
                'status' => false,
                'message' => 'this task for another user , you cant access to it'
            ], 200);
        }

        $task = task::find($request->id);

        $input = $request->all();

        foreach ($input as $key => $value) {
            if (in_array($key, ['description', 'due_date', 'title'])) {
                $task->$key = $value;
            }
        }

        $task->save();

        $data = task::where('user_id', auth()->user()->id)->get();

        return response([
            'status' => true,
            'message' => "done successfully",
            'tasks' => $data,
        ], 200);
    }

    public function filterTasks(Request $request)
    {
        $query = task::where('user_id', auth()->user()->id);

        if ($request->has('completed')) {
            if ($request->input('completed') == 1)
                $query->where('completed', 1);
            else if ($request->input('completed') == 0)
                $query->where('completed', 0);
        }
        $query->orderByRaw("COALESCE(due_date, '9999-12-31') ASC");

        $tasks = $query->get();

        return response()->json([
            'status' => true,
            'data' => $tasks,
        ]);
    }
}
