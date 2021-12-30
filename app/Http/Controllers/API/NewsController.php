<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use App\Models\Topic;
use App\Models\News;
use App\Models\Tag;
use URL;
use Log;
use File;

class NewsController extends Controller
{
    public function index(Request $request)
    {        
        $topic_id   = $request->query('topic_id');
        $status = $request->query('status');

        $newsCache = Redis::get('all news : ');
        
        if (isset($newsCache)) {
            $news_ = json_decode($newsCache, FALSE);

            return response()->json([
                'status' => 'success',
                'message' => 'data from redis',
                'data' => $news_
            ]); 
        } 

        $news = News::query();

        $news->when($topic_id, function($query) use ($topic_id) {
            return $query->where("topic_id", '=', $topic_id);    
        });

        $news->when($status, function($query) use ($status) {
            return $query->where("status", '=', $status);
        });

        Redis::set('all news : ', json_encode($news->paginate(10)));

        return response()->json([
            'status' => 'success',
            'message' => 'data from db',
            'data' => $news->paginate(100)
        ]);        
    }

    public function store(Request $request) 
    {
        $data = $request->all();
        
        $formRequest = [
            'topic_id' => 'required|integer',
            'title' => 'required|string|max:255',            
            'thumbnail' => 'required|image:jpeg,png,jpg',
            'content' => 'required',
            'created_by' => 'required',
        ];

        $validate = Validator::make($data, $formRequest);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors()
            ], 400);
        }

        $topic_id = $request->input('topic_id');

        $topic = Topic::find($topic_id);

        if(!$topic) {
            return response()->json([
                'status' => 'error',
                'message' => 'topic not found'
            ], 404);
        }
        
        $thumbnail = $request->file('thumbnail');        
        $fileName = $thumbnail->getClientOriginalName();
        $path = public_path().'/images';
        $thumbnail->move($path, $fileName);
                        
        $uploadedImageResponse = [
            "image_name" => $fileName,
            "image_url" =>  '/images/' . $fileName,
            "mime" => $thumbnail->getClientMimeType()
        ];

        $data['thumbnail'] = $uploadedImageResponse['image_url'];    

        $news = News::create($data);

        $news['thumbnail'] = URL::to('/') . $uploadedImageResponse['image_url'];

        return response()->json([
            'status' => 'success',
            'data' => $news
        ]);
    }

    public function show($id) 
    {            
        $newsCache = Redis::get('news_' . $id);
        
        if (isset($newsCache)) {
            $news_ = json_decode($newsCache, FALSE);
            
            return response()->json([
                'status' => 'success',
                'message' => 'fetched from redis',
                'data' => $news_
            ]);
        }

        $news = News::with('tags')->find($id);
         
        if(!$news) {
            return response()->json([
                'status' => 'error',
                'message' => 'news not found'
            ], 404);
        }

        Redis::set('news_' . $id, $news);

        $news['thumbnail'] = URL::to('/') . '/' . $news->thumbnail;
        
        return response()->json([
            'status' => 'success',
            'message' => 'fetched from db',
            'data' => $news
        ]);
    }

    public function update(Request $request, $id) 
    {
        $data = $request->all();                

        $formRequest = [
            'topic_id' => 'integer',
            'title' => 'string|max:255',   
            /* 'thumbnail' => 'image:jpeg,png,jpg' */                 
            'status' => 'in:publish,deleted,draft'
        ];

        $validate = Validator::make($data, $formRequest);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors()
            ], 400);
        }

        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'status' => 'error',
                'message' => 'news not found'
            ], 404);
        }
        
        $topic_id = $request->input('topic_id');
        
        $topic = Topic::find($topic_id);
        
        if(!$topic) {
            return response()->json([
                'status' => 'error',
                'message' => 'topic not found'
            ], 404);
        }        
        
        // *! PLEASE READ THIS COMMENT
        // ** The comment code is to check if user want to update thumbnail field
        // *! Assume for this case, the thumbnail isn't updated.
        // *! It because PUT method does not support to get request from (form-data) in Laravel
        // *! But we can do this thing (comment code below) if we want to update the thumbnail
        // TODO: 1. Change the POSTMAN method to POST then add query ?_method=PUT end the end of the endpoint
        // TODO: 2. Un-comment the code below
        // *? example => http://localhost:8000/api/news/1?_method=PUT
        // ** NOTE: issue resource from https://stackoverflow.com/questions/50691938/patch-and-put-request-does-not-working-with-form-data
        // ** NB: The reason for adjust thumbnail isnt updated to show recruter the funtion for HTTP method (PUT)

        /* if ($data['thumbnail']) {
            if (File::exists(public_path($news->thumbnail))){
                unlink($news->thumbnail); 
                
                $thumbnail = $request->file('thumbnail');        
                $fileName = $thumbnail->getClientOriginalName();
                $path = public_path().'/images';
                $thumbnail->move($path, $fileName);                            
            }
        } 

        $uploadedImageResponse = [
            "image_name" => $fileName,
            "image_url" =>  '/images/' . $fileName,
            "mime" => $thumbnail->getClientMimeType()
        ];

        $data['thumbnail'] = $uploadedImageResponse['image_url']; */   
        
        $news->update($data);

        /* $news['thumbnail'] = URL::to('/') . $uploadedImageResponse['image_url']; */

        Redis::del('news_' . $id);        

        return response()->json([
            'status' => 'success',
            'data' => $news
        ]);
    }

    public function destroy($id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'status' => 'error',
                'message' => 'news not found'
            ], 404);
        }
        
        unlink($news->thumbnail);

        Redis::del('news_' . $id);

        $news->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'news have been deleted'
        ]);
    }
}
