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
        return [];
    }

    public function getByUser(String $userId): array
    {
        return [];
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
