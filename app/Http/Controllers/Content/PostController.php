<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\NewPostResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\ProjectPostResource;
use App\Http\Resources\QuestionPostkResource;
use App\Http\Resources\TeamPostResource;
use App\Http\Resources\WorkPostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    // ------------- Resource Message -----------------
    private function resourceResponse($resource = null , $post = null, string $message)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            // 'data' => new $resource($post),
            'data' => $resource && $post ? new $resource($post) : null,
        ], 201);
    }

    // ----------------- Search Function Functions --------------------
    private function searchPost($query, Request $request)
    {
        if(!$request->filled('search')) {
            return ;
        }
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
          ->orWhere('content', 'like', "%{$search}%");

        });
    }


    // ----------------- Store Functions --------------------


    private function uploadFile(Request $request): ?string
    {
        if (! $request->hasFile('file')) {
            return null;
        }

        return $request->file('file')->store('posts', 'public');
    }

    private function createPost(array $data): Post
    {
        return Post::create([
            'title'          => $data['title'],
            'content'        => $data['content'] ?? null,
            'file'           => $data['file'] ?? null,
            'skill'          => $data['skill'] ?? null,
            'primary_link'   => $data['primary_link'] ?? null,
            'secondary_link' => $data['secondary_link'] ?? null,
            'type'           => $data['type'],
            'user_id'        => auth()->id(),
        ]);
    }

    private function storeQuestion(array $data)
    {
        $post = $this->createPost($data);

        return $this->resourceResponse(
            QuestionPostkResource::class,
            $post,
            'Question created successfully'
        );
    }

    
    private function storeNews(array $data, StorePostRequest $request)
    {
        $data['file'] = $this->uploadFile($request);

        $post = $this->createPost($data);

        return $this->resourceResponse(
            NewPostResource::class,
            $post,
            'News created successfully'
        );
    }


    private function storeProject(array $data, StorePostRequest $request)
    {
        $data['file'] = $this->uploadFile($request);

        $post = $this->createPost($data);

        return $this->resourceResponse(
            ProjectPostResource::class,
            $post,
            'Project created successfully'
        );
    }

    

    private function storeTeam(array $data)
    {
        $post = $this->createPost($data);

        return $this->resourceResponse(
            TeamPostResource::class,
            $post,
            'Team created successfully'
        );
    }

    private function storeWork(array $data)
    {
        $post = $this->createPost($data);

        $post->work()->create([
            'location'      => $data['location'],
            'salary_range'  => $data['salary_range'],
            'work_place'    => $data['work_place'],
            'contact'       => $data['contact'],
            'hours'         => $data['hours'],
        ]);

        $post->load('work');

        return $this->resourceResponse(
            WorkPostResource::class,
            $post,
            'Work created successfully'
        );
    }

    // ----------------- Update Functions --------------------

       private function updateFile(Post $post,  array $data)
{
       if (array_key_exists('file', $data)) {
        $post->file = $data['file'];
    }
        $post->save();
}


    private function updateQuestion(Post $post, array $data)
{
    return $this->resourceResponse(
        QuestionPostkResource::class,
        $post,
        'Question updated successfully'
    );
}

private function updateWork(Post $post, array $data)
{
    $post->work()->updateOrCreate(
        ['post_id' => $post->id],
        [
            'location'      => $data['location'],
            'salary_range'  => $data['salary_range'],
            'work_place'    => $data['work_place'],
            'contact'       => $data['contact'],
            'hours'         => $data['hours'],
        ]
    );

    $post->load('work');

    return $this->resourceResponse(
        WorkPostResource::class,
        $post,
        'Work updated successfully'
    );
}

    private function updateNews(Post $post, array $data)
{
        $this->updateFile($post, $data);

    return $this->resourceResponse(
        NewPostResource::class,
        $post,
        'News updated successfully'
    );
}

private function updateProject(Post $post, array $data)
{
        $this->updateFile($post, $data);

    return $this->resourceResponse(
        ProjectPostResource::class,
        $post,
        'Project updated successfully'
    );
}

private function updateTeam(Post $post, array $data)
{
    return $this->resourceResponse(
        TeamPostResource::class,
        $post,
        'Team updated successfully'
    );
}


    public function index(Request $request)
    {
        $query = Post::with(['work','user']) ->withCount([
        'votes as upvotes' => fn ($q) => $q->where('custom', 'upvote'),
        'votes as downvotes' => fn ($q) => $q->where('custom', 'downvote'),
        'votes as ai_votes' => fn ($q) => $q->where('custom', 'ai'),])->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $this->searchPost($query,$request);

        $posts = $query->paginate(10);

        return PostResource::collection($posts);
    }

    public function store(StorePostRequest $request)
    {
        $data = $request->validated();

        return match ($data['type']) {
            'question' => $this->storeQuestion($data),
            'new'      => $this->storeNews($data, $request),
            'project'  => $this->storeProject($data, $request),
            'team'     => $this->storeTeam($data),
            'work'     => $this->storeWork($data),
        };
    }

    public function show(string $id)
    {
        //
    }

    public function update(UpdatePostRequest $request, string $id)
    {
    
        $post = Post::with('work')->findOrFail($id);
        
        $data = $request->validated();
        $type = $data['type'] ?? $post->type;

        
        // $post->save();
    

        $post->update([
        'title'          => $data['title'] ?? $post->title,
        'content'        => $data['content'] ?? $post->content,
        'skill'          => $data['skill'] ?? $post->skill,
        'primary_link'   => $data['primary_link'] ?? $post->primary_link,
        'secondary_link' => $data['secondary_link'] ?? $post->secondary_link,
        'type'           => $type,
    ]);


    
    if ($type !== 'work') {
    $post->work()->delete();
}
        
        $post->load('work');

    return match ($type) {
        'question' => $this->updateQuestion($post, $data),
        'new'      => $this->updateNews($post, $data),
        'project'  => $this->updateProject($post, $data),
        'team'     => $this->updateTeam($post, $data),
        'work'     => $this->updateWork($post, $data),
    };
    }

    public function destroy(string $id)
    {
          $post = Post::with('work')->findOrFail($id);

    if ($post->work) {
        $post->work()->delete();
    }

    if ($post->file) {
        Storage::disk('public')->delete($post->file);
    }

    $post->delete();

     return $this->resourceResponse(
        null,
        null,
        'Post deleted successfully'
    );
    }
}