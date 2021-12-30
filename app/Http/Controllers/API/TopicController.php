<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Topic;


class TopicController extends Controller
{
    public function index()
    {
        $topics = Topic::all();

        return response()->json([
            'status' => 'success',
            'data' => $topics
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $formRequest = [
            'name' => 'required|min:6',
        ];

        $validate = Validator::make($data, $formRequest);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors()
            ], 400);
        }

        $topics = Topic::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $topics
        ]);
    }

    public function show($id)
    {
        $topics = Topic::find($id);

        if(!$topics) {
            return response()->json([
                'status' => 'error',
                'message' => 'topics not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $topics
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();

        $topics = Topic::find($id);

        if(!$topics) {
            return response()->json([
                'status' => 'error',
                'message' => 'topics not found'
            ], 404);
        }

        $topics->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $topics
        ]);
    }

    public function destroy($id)
    {
        $topics = Topic::find($id);

        if (!$topics) {
            return response()->json([
                'status' => 'error',
                'message' => 'topic not found'
            ], 404);
        }
        
        $topics->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'topic have been deleted'
        ]);
    }
}
