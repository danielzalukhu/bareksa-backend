<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Tag;

class TagController extends Controller
{
    public function index() 
    {
        $tags = Tag::all(['id', 'news_id', 'hastag']);

        return response()->json([
            'status' => 'success',
            'data' => $tags
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();   
        
        $formRequest = [
            'news_id' => 'required|integer',
            'hastag' => 'required|max:20'
        ];

        $validate = Validator::make($data, $formRequest);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors()
            ], 400);
        }
                
        $news_id = $request->input('news_id');

        $news = News::find($news_id);

        if(!$news) {
            return response()->json([
                'status' => 'error',
                'message' => 'news not found'
            ], 404);
        }

        foreach($data["hastag"] as $hastag) {
            $tags[] = Tag::create([
                "news_id" => $news_id,
                "hastag"  => $hastag
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $tags
        ]);
    }

    public function show($id)
    {
        $tags = Tag::find($id);

        if(!$tags) {
            return response()->json([
                'status' => 'error',
                'message' => 'tags not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $tags
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();

        $tags = Tag::find($id);

        if(!$tags) {
            return response()->json([
                'status' => 'error',
                'message' => 'tags not found'
            ], 404);
        }

        $news_id = $request->input('news_id');

        if ($news_id) {
            $news = News::find($news_id);

            if(!$news) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'news not found'
                ], 404);
            }
        }

        $tags->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $tags
        ]);
    
    }

    public function destroy($id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json([
                'status' => 'error',
                'message' => 'tags not found'
            ], 404);
        }
        
        $tag->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'tags have been deleted'
        ]);
    }
}
