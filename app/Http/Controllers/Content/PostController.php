<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\NewPostResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\ProjectPostResource;
use App\Http\Resources\QuestionPostResource;
use App\Http\Resources\TeamPostResource;
use App\Http\Resources\WorkPostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class PostController extends Controller
{


    private const SIMPLE_TYPES = [
        'question' => QuestionPostResource::class,
        'team'     => TeamPostResource::class,
    ];


    private const FILE_TYPES = [
        'new'     => NewPostResource::class,
        'project' => ProjectPostResource::class,
    ];

    // ------------- Response Helpers -----------------

    private function resourceResponse($resource = null, $post = null, string $message = 'نجح', int $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $resource && $post ? new $resource($post) : null,
        ], $code);
    }

    private function errorResponse(string $message = 'خطأ', int $code = 403)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $code);
    }

    // ----------------- Search --------------------

    private function searchPost($query, Request $request)
    {
        if (! $request->filled('search')) {
            return;
        }

        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('content', 'like', "%{$search}%");
        });
    }

    // ----------------- File Helpers --------------------

    private function uploadFile(Request $request): ?string
    {
        if (! $request->hasFile('file')) {
            return null;
        }

        return $request->file('file')->store('posts', 'public');
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    // ----------------- Post Creation Helper --------------------

    private function createPost(array $data): Post
    {
        return Post::create([
            'title'          => $data['title'],
            'content'        => $data['content'] ?? null,
            'file'           => $data['file'] ?? null,
            'skill' => $data['skill'] ?? null,
            'primary_link'   => $data['primary_link'] ?? null,
            'secondary_link' => $data['secondary_link'] ?? null,
            'type'           => $data['type'],
            'user_id'        => auth()->id(),
        ]);
    }

    private function saveWorkDetails(Post $post, array $data): void
    {
        $post->work()->updateOrCreate(
            ['post_id' => $post->id],
            [
                'location'     => $data['location'] ?? $post->work?->location,
                'salary_range' => $data['salary_range'] ?? $post->work?->salary_range,
                'work_place'   => $data['work_place'] ?? $post->work?->work_place,
                'contact'      => $data['contact'] ?? $post->work?->contact,
                'hours'        => $data['hours'] ?? $post->work?->hours,
            ]
        );

        $post->load('work');
    }

    // ----------------- Controller Actions --------------------

    public function index(Request $request)
    {
        $query = Post::with(['work', 'user'])
            ->withCount([
                'votes as upvotes'   => fn($q) => $q->where('custom', 'upvote'),
                'votes as downvotes' => fn($q) => $q->where('custom', 'downvote'),
                'votes as ai_votes'  => fn($q) => $q->where('custom', 'ai'),
            ])
            ->orderByRaw('(upvotes - downvotes - (ai_votes * 2)) DESC'); // الترتيب حسب صافي النقاط تنازلياً


        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // فلترة حسب صاحب المنشور — تُستخدم لعرض منشورات مستخدم معيّن بصفحة بروفايله
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $this->searchPost($query, $request);

        $posts = $query->paginate(10);

        return PostResource::collection($posts);
    }


    public function store(StorePostRequest $request)
    {
        $data = $request->validated();
        $type = $data['type'];

        // الأنواع التي تحتوي على ملف: نرفع الملف قبل الإنشاء
        if (array_key_exists($type, self::FILE_TYPES)) {
            $data['file'] = $this->uploadFile($request);
        }

        $post = $this->createPost($data);

        // نوع "العمل" يحتاج جدولاً إضافياً
        if ($type === 'work') {
            $this->saveWorkDetails($post, $data);

            return $this->resourceResponse(
                WorkPostResource::class,
                $post,
                'تم إنشاء العمل بنجاح',
                201
            );
        }

        // الأنواع البسيطة + أنواع الملفات، كلها تمر من هنا الآن
        $resourceClass = self::SIMPLE_TYPES[$type] ?? self::FILE_TYPES[$type];

        return $this->resourceResponse(
            $resourceClass,
            $post,
            'تم إنشاء المنشور بنجاح',
            201
        );
    }

    public function show(Post $post)
    {
        $post->load(['work', 'user']);

        return $this->resourceResponse(
            PostResource::class,
            $post,
            'تم جلب المنشور بنجاح',
            200
        );
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            return $this->errorResponse('لست مخولاً لتحديث هذا المنشور.');
        }

        $post->load('work', 'user');

        $data = $request->validated();
        $type = $data['type'] ?? $post->type;

        // معالجة الملف: لو أُرسل ملف جديد، احذف القديم وارفع الجديد
        if ($request->hasFile('file')) {
            $this->deleteFile($post->file);
            $data['file'] = $this->uploadFile($request);
        }

        $post->update([
            'title'          => $data['title'] ?? $post->title,
            'content'        => $data['content'] ?? $post->content,
            'file'           => $data['file'] ?? $post->file,
            'skill' => $data['skill'] ?? $post->skills,
            'primary_link'   => $data['primary_link'] ?? $post->primary_link,
            'secondary_link' => $data['secondary_link'] ?? $post->secondary_link,
            'type'           => $type,
        ]);

        if ($type === 'work') {
            $this->saveWorkDetails($post, $data);

            return $this->resourceResponse(
                WorkPostResource::class,
                $post,
                'تم تحديث العمل بنجاح',
                200
            );
        }

        // لو تغيّر النوع من "عمل" إلى نوع آخر، احذف بيانات العمل القديمة
        if ($post->work) {
            $post->work()->delete();
        }

        $post->load('work');

        $resourceClass = self::SIMPLE_TYPES[$type] ?? self::FILE_TYPES[$type];

        return $this->resourceResponse(
            $resourceClass,
            $post,
            'تم تحديث المنشور بنجاح',
            200
        );
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            return $this->errorResponse('لست مخولاً لحذف هذا المنشور.');
        }

        if ($post->work) {
            $post->work()->delete();
        }

        $this->deleteFile($post->file);

        $post->delete();

        return $this->resourceResponse(
            null,
            null,
            'تم حذف المنشور بنجاح',
            200
        );
    }


    public function toggleSave(Post $post)
    {
        $result = $post->savedByUsers()->toggle(auth()->id());

        $saved = count($result['attached']) > 0;

        return response()->json([
            'status'  => 'success',
            'message' => $saved ? 'تم حفظ المنشور بنجاح' : 'تم إلغاء حفظ المنشور بنجاح',
            'data'    => ['saved' => $saved],
        ], 200);
    }


    public function savedPosts(Request $request)
    {
        $posts = Post::with(['work', 'user'])
            ->withCount([
                'votes as upvotes'   => fn($q) => $q->where('custom', 'upvote'),
                'votes as downvotes' => fn($q) => $q->where('custom', 'downvote'),
                'votes as ai_votes'  => fn($q) => $q->where('custom', 'ai'),
            ])
            ->whereHas('savedByUsers', fn($q) => $q->where('user_id', auth()->id()))
            ->latest()
            ->paginate(10);

        return PostResource::collection($posts);
    }
}
