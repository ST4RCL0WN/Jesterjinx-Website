@inject('markdown', 'Parsedown')
@php
    $markdown->setSafeMode(true);
@endphp

@if($comment->deleted_at == null)
@if(isset($reply) && $reply === true)
  <div id="comment-{{ $comment->getKey() }}" class="comment_replies border-left col-12 column mw-100 pr-0 pt-4" style="flex-basis: 100%;">
@else
  <div id="comment-{{ $comment->getKey() }}" class="pt-4" style="flex-basis: 100%;">
@endif
<div class="media-body row mw-100 mx-0" style="flex:1;flex-wrap:wrap;">
    <div class="d-none d-md-block">
        <img class="mr-3 mt-2" src="/images/avatars/{{ $comment->commenter->avatar }}" style="width:70px; height:70px; border-radius:50%;" alt="{{ $comment->commenter->name }} Avatar">
    </div>
    <div class="d-block" style="flex:1">
        <div class="row mx-0 px-0 align-items-md-end">
            <h5 class="mt-0 mb-1 col mx-0 px-0">
                {!! $comment->commenter->commentDisplayName !!} @if($comment->commenter->isStaff == true)<small class="text-muted">Staff Member</small>@endif
            </h5>
            @if($comment->is_featured)<div class="ml-1 text-muted text-right col-6 mx-0 pr-1"><small class="text-success">Featured by Owner</small></div> @endif
        </div>
        <div class="comment border p-3 rounded {{ $limit == 0 ? 'shadow-sm border-info' : '' }} {{ ($comment->is_featured && ($limit != 0)) ? 'border-success' : '' }} {{ $comment->likes()->where('is_like', 1)->count() - $comment->likes()->where('is_like', 0)->count() < 0 ? 'bg-light bg-gradient' : '' }}"><p>{!! nl2br($markdown->line($comment->comment)) !!} </p>
        <p class="border-top pt-1 text-right mb-0">
            <small class="text-muted">{!! $comment->created_at !!}
            @if($comment->created_at != $comment->updated_at)
                <span class="text-muted border-left mx-1 px-1">(Edited {!! ($comment->updated_at) !!})</span>
            @endif
            </small>
            <a href="{{ url('comment/').'/'.$comment->id }}"><i class="fas fa-link ml-1" style="opacity: 50%;"></i></a>
            <a href="{{ url('reports/new?url=') . $comment->url }}"><i class="fas fa-exclamation-triangle" data-toggle="tooltip" title="Click here to report this comment." style="opacity: 50%;"></i></a>
        </p>
    </div>
    @if(Auth::check())
        <div class="my-1 row justify-content-between no-gutters">
            <div class="col-auto">
                @can('reply-to-comment', $comment)
                    <button data-toggle="modal" data-target="#reply-modal-{{ $comment->getKey() }}" class="btn btn-sm px-3 py-2 px-sm-2 py-sm-1  btn-faded text-uppercase"><i class="fas fa-comment"></i><span class="ml-2 d-none d-sm-inline-block">Reply</span></button>
                @endcan
                @can('edit-comment', $comment)
                    <button data-toggle="modal" data-target="#comment-modal-{{ $comment->getKey() }}" class="btn btn-sm px-3 py-2 px-sm-2 py-sm-1  btn-faded text-uppercase"><i class="fas fa-edit"></i><span class="ml-2 d-none d-sm-inline-block">Edit</span></button>
                @endcan
                @if((Auth::user()->id == $comment->commentable_id) || Auth::user()->isStaff)
                    <button data-toggle="modal" data-target="#feature-modal-{{ $comment->getKey() }}" class="btn btn-sm px-3 py-2 px-sm-2 py-sm-1  btn-faded text-success text-uppercase"><i class="fas fa-star"></i><span class="ml-2 d-none d-sm-inline-block">{{$comment->is_featured ? 'Unf' : 'F' }}eature Comment</span></button>
                @endif
                @can('delete-comment', $comment)
                    <button data-toggle="modal" data-target="#delete-modal-{{ $comment->getKey() }}" class="btn btn-sm px-3 py-2 px-sm-2 py-sm-1 btn-outline-danger text-uppercase"><i class="fas fa-minus-circle"></i><span class="ml-2 d-none d-sm-inline-block">Delete</span></button>
                @endcan
            </div>
            <div class="col-auto text-right">
                {{-- Likes Section --}}
                <a href="#" data-toggle="modal" data-target="#show-likes-{{$comment->id}}">
                    <button href="#" data-toggle="tooltip" title="Click to View" class="btn btn-sm px-3 py-2 px-sm-2 py-sm-1 btn-faded">
                        {{ $comment->likes()->where('is_like', 1)->count() - $comment->likes()->where('is_like', 0)->count() }}
                        Like
                        @if($comment->likes()->where('is_like', 1)->count() - $comment->likes()->where('is_like', 0)->count() != 1)
                            s
                        @endif
                    </button>
                </a>
                {!! Form::open(['url' => 'comments/'.$comment->id.'/like/1','class' => 'd-inline-block']) !!}
                    {!! Form::button('<i class="fas fa-thumbs-up"></i>', ['type' => 'submit', 'class' => 'btn btn-sm px-3 py-2 px-sm-2 py-sm-1 '. ($comment->likes()->where('user_id', Auth::user()->id)->where('is_like', 1)->exists() ? 'btn-success' : 'btn-outline-success').' text-uppercase']) !!}
                {!! Form::close() !!}
                @if(Settings::get('comment_dislikes_enabled') || (isset($allow_dislikes) && $allow_dislikes))
                    {!! Form::open(['url' => 'comments/'.$comment->id.'/like/0','class' => 'd-inline-block']) !!}
                        {!! Form::button('<i class="fas fa-thumbs-down"></i>', ['type' => 'submit', 'class' => 'btn btn-sm px-3 py-2 px-sm-2 py-sm-1 '. ($comment->likes()->where('user_id', Auth::user()->id)->where('is_like', 0)->exists() ? 'btn-danger' : 'btn-outline-danger') .' text-uppercase']) !!}
                    {!! Form::close() !!}
                @endif
            </div>
        </div>
    @endif

        <div class="modal fade" id="show-likes-{{$comment->id}}" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Likes</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if(count($comment->likes) > 0)
                            <div class="mb-4 logs-table">
                                <div class="logs-table-header">
                                    <div class="row">
                                        <div class="col-4 col-md-3"><div class="logs-table-cell">User</div></div>
                                        <div class="col-12 col-md-4"><div class="logs-table-cell"></div></div>
                                        <div class="col-4 col-md-3"><div class="logs-table-cell"></div></div>
                                    </div>
                                </div>
                                <div class="logs-table-body">
                                    @foreach($comment->likes as $like)
                                        <div class="logs-table-row">
                                            <div class="row flex-wrap">
                                                <div class="col-4 col-md-3"><div class="logs-table-cell"><img style="max-height: 2em;" src="/images/avatars/{{ $like->user->avatar }}" /></div></div>
                                                <div class="col-12 col-md-4"><div class="logs-table-cell">{!! $like->user->displayName !!}</div></div>
                                                <div class="col-4 col-md-4 text-right"><div class="logs-table-cell">{!! $like->is_like ? '<i class="fas fa-thumbs-up"></i>' : '<i class="fas fa-thumbs-down"></i>' !!}</div></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">No likes yet.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @can('edit-comment', $comment)
            <div class="modal fade" id="comment-modal-{{ $comment->getKey() }}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        {!! Form::open(array('route' => array('comments.update', $comment->getKey()))) !!}
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Comment</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    {!! Form::label('message', 'Update your message here:') !!}
                                    {!! Form::textarea('message', $comment->comment, ['class' => 'form-control', 'rows' => 3, 'required']) !!}
                                    <small class="form-text text-muted"><a target="_blank" href="https://help.github.com/articles/basic-writing-and-formatting-syntax">Markdown</a> cheatsheet.</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-outline-secondary text-uppercase" data-dismiss="modal">Cancel</button>
                                {!! Form::submit('Update', ['class' => 'btn btn-sm btn-outline-success text-uppercase']) !!}
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        @endcan

        @can('reply-to-comment', $comment)
            <div class="modal fade" id="reply-modal-{{ $comment->getKey() }}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        {!! Form::open(['url' => 'comments/'.$comment->getKey()]) !!}
                            <div class="modal-header">
                                <h5 class="modal-title">Reply to Comment</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    {!! Form::label('message', 'Enter your message here:') !!}
                                    {!! Form::textarea('message', null, ['class' => 'form-control', 'rows' => 3, 'required']) !!}
                                    <small class="form-text text-muted"><a target="_blank" href="https://help.github.com/articles/basic-writing-and-formatting-syntax">Markdown</a> cheatsheet.</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-outline-secondary text-uppercase" data-dismiss="modal">Cancel</button>
                                {!! Form::submit('Reply', ['class' => 'btn btn-sm btn-outline-success text-uppercase']) !!}
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        @endcan

        @can('delete-comment', $comment)
            <div class="modal fade" id="delete-modal-{{ $comment->getKey() }}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Delete Comment</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                                </button>
                                    </div>
                            <div class="modal-body">
                                <div class="form-group">Are you sure you want to delete this comment?</div></div>
                                <div class="alert alert-warning"><strong>Comments can be restored in the database.</strong> <br> Deleting a comment does not delete the comment record.</div>
                                <a href="{{ route('comments.destroy', $comment->getKey()) }}" onclick="event.preventDefault();document.getElementById('comment-delete-form-{{ $comment->getKey() }}').submit();" class="btn btn-danger text-uppercase">Delete</a>
                        <form id="comment-delete-form-{{ $comment->getKey() }}" action="{{ route('comments.destroy', $comment->getKey()) }}" method="POST" style="display: none;">
                            @method('DELETE')
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        @endcan

    <div class="modal fade" id="feature-modal-{{ $comment->getKey() }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $comment->is_featured ? 'Unf' : 'F' }}eature Comment</h5>
                    <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">Are you sure you want to {{ $comment->is_featured ? 'un' : '' }}feature this comment?</div>
                </div>
                <div class="alert alert-warning">Comments can be unfeatured.</div>
                {!! Form::open(['url' => 'comments/'.$comment->id.'/feature']) !!}
                    @if(!$comment->is_featured) {!! Form::submit('Feature', ['class' => 'btn btn-primary w-100 mb-0 mx-0']) !!}
                    @else {!! Form::submit('Unfeature', ['class' => 'btn btn-primary w-100 mb-0 mx-0']) !!}
                    @endif
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>


{{-- add a limit check so if limit is reached but replies are still presnt to display a button with current amount of replies
use child function
url should be equal to the last replies permalink (e.g reply 5)--}}


        {{-- Recursion for children --}}
        <div class="w-100 mw-100">
            @php $children = $depth == 0 ? $comment->children->sortByDesc('created_at')->paginate(5) : $comment->children->sortByDesc('created_at') @endphp
            @foreach($children as $reply)
                @php $limit++; @endphp

                @if($limit >= 5 && $depth >= 1)
                    <a href="{{ url('comment/').'/'.$comment->id }}"><span class="btn btn-secondary w-100">See More Replies</span></a>
                    @break
                @endif

                @include('comments._perma_comments', [
                    'comment' => $reply,
                    'reply' => true,
                    'limit' => $limit,
                    'depth' => $depth+1
                ])
                @endforeach
            @if($depth == 0) {!! $children->render() !!} @endif
        </div>
    </div>
</div>
@else
    @if(isset($reply) && $reply === true)
    <div id="comment-{{ $comment->getKey() }}" class="comment_replies border-left col-12 column mw-100 pr-0 pt-4" style="flex-basis: 100%;">
    @else
    <div id="comment-{{ $comment->getKey() }}"  class="pt-4" style="flex-basis: 100%;">
    @endif
        <div class="media-body row mw-100 mx-0" style="flex:1;flex-wrap:wrap;">
            <div class="d-none d-md-block">
                <img class="mr-3 mt-2" src="/images/avatars/default.jpg" style="width:70px; height:70px; border-radius:50%;" alt="Default Avatar">
            </div>
            <div class="d-block bg-light" style="flex:1">
                <div class="border p-3 rounded {{ $limit == 0 ? 'shadow-sm border-info' : '' }}"><p>Comment deleted </p>
                <p class="border-top pt-1 text-right mb-0">
                    <small class="text-muted">{!! $comment->created_at !!}
                    @if($comment->created_at != $comment->deleted_at)
                        <span class="text-muted border-left mx-1 px-1">(Deleted {!! ($comment->deleted_at) !!})</span>
                    @endif
                    </small>
                    <a href="{{ url('comment/').'/'.$comment->id }}"><i class="fas fa-link ml-1" style="opacity: 50%;"></i></a>
                </p>
            </div>
        </div>

        {{-- Recursion for children --}}
        <div class="w-100 mw-100">
            @php $children = $depth == 0 ? $comment->children->sortByDesc('created_at')->paginate(5) : $comment->children->sortByDesc('created_at') @endphp
            @foreach($children as $reply)
                @php $limit++; @endphp

                @if($limit >= 5 && $depth >= 1)
                    <a href="{{ url('comment/').'/'.$comment->id }}"><span class="btn btn-secondary w-100">See More Replies</span></a>
                    @break
                @endif

                @include('comments._perma_comments', [
                    'comment' => $reply,
                    'reply' => true,
                    'limit' => $limit,
                    'depth' => $depth+1
                ])
                @endforeach
            @if($depth == 0) {!! $children->render() !!} @endif
        </div>
    </div>
</div>
@endif
