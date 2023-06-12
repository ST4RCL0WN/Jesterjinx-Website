<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Gallery\GallerySubmission;
use Auth;

class PermalinkController extends Controller {
    /**
     * returns replies recursively.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getComment($id) {
        $comments = Comment::withTrashed()->get();
        //$comments = $comments->sortByDesc('created_at');
        $comment = $comments->find($id);

        if (!$comment) {
            abort(404);
        }
        if (!$comment->commentable) {
            abort(404);
        }

        if (isset($comment->commentable->is_visible) && !$comment->commentable->is_visible) {
            abort(404);
        }

        // Check if the comment can be viewed
        switch ($comment->type) {
            case 'Staff-User':
                if (!Auth::check()) {
                    abort(404);
                }
                $submission = GallerySubmission::find($comment->commentable_id);
                $isMod = Auth::user()->hasPower('manage_submissions');
                $isOwner = ($submission->user_id == Auth::user()->id);
                $isCollaborator = $submission->collaborators->where('user_id', Auth::user()->id)->first() != null ? true : false;
                if (!$isMod && !$isOwner && !$isCollaborator) {
                    abort(404);
                }
                break;
            case 'Staff-Staff':
                if (!Auth::check()) {
                    abort(404);
                }
                if (!Auth::user()->hasPower('manage_submissions')) {
                    abort(404);
                }
                break;
            default:
                break;
        }

        if ($comment->commentable_type == 'App\Models\User\UserProfile') {
            $comment->location = $comment->commentable->user->url;
        } else {
            $comment->location = $comment->commentable->url;
        }

        return view('comments._perma_layout', [
            'comment' => $comment,
        ]);
    }
}
