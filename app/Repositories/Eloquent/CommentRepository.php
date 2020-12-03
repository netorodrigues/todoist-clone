<?php

namespace App\Repositories\Eloquent;

use App\Models\Comment;
use App\Repositories\CommentRepositoryInterface;

final class CommentRepository implements CommentRepositoryInterface
{

    public function create(
        String $userId,
        ?String $projectId,
        ?String $taskId,
        String $content
    ): array{
        $comment = new Comment;

        $comment->user_id = $userId;
        $comment->project_id = $projectId ?? null;
        $comment->task_id = $taskId ?? null;
        $comment->content = $content;
        $comment->save();

        return $comment->toArray();
    }

    public function getById(String $commentId): array
    {
        $comment = Comment::find($userId);
        return $comment ? $comments->toArray() : [];
    }

    public function getByTask(String $taskId): array
    {
        $comments = Comment::where('task_id', $taskId)->get();
        return $comments->toArray();
    }

    public function getByProject(String $projectId): array
    {
        $comments = Comment::where('project_id', $projectId)->get();
        return $comments->toArray();
    }

    public function getByUser(String $userId): array
    {
        $comments = Comment::where('user_id', $userId)->get();
        return $comments->toArray();
    }

    public function edit(
        String $commentId,
        array $data
    ): bool {
        return false;
    }

    public function delete(String $commentId): bool
    {
        return false;
    }

}
