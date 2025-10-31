<?php

namespace App\Http\Controllers\Back;

use App\Models\VideoStories;
use Illuminate\Http\Request;
use App\{
    Http\Controllers\Controller
};


class VedioStorieController extends Controller
{
    public function index()
    {
        $stories = VideoStories::latest()->paginate(10);
        return view('back.video_stories.index', compact('stories'));
    }

    public function create()
    {
        return view('back.video_stories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'nullable|string|max:255',
            'description'  => 'nullable|string',
            'youtube_url'  => [
                'required',
                'url',
                'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/i'
            ]
        ]);

        VideoStories::create($request->only(['title', 'description', 'youtube_url']));

        return redirect()->route('back.video-stories.index')->with('success', 'Video story added.');
    }

    public function show(VideoStories $videoStory)
    {
        return view('back.video_stories.show', compact('videoStory'));
    }

    public function edit(VideoStories $videoStory)
    {
        return view('back.video_stories.edit', compact('videoStory'));
    }

    public function update(Request $request, VideoStories $videoStory)
    {
        $request->validate([
            'title'        => 'nullable|string|max:255',
            'description'  => 'nullable|string',
 'youtube_url'  => [
                'required',
                'url',
                'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/i'
            ]        ]);

        $videoStory->update($request->only(['title', 'description', 'youtube_url']));

        return redirect()->route('back.video-stories.index')->with('success', 'Video story updated.');
    }

    public function destroy(VideoStories $videoStory)
    {
        $videoStory->delete();
        return redirect()->route('back.video-stories.index')->with('success', 'Video story deleted.');
    }
}
