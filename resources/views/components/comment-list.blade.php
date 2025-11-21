@props(['comments', 'canDelete' => false])

@if($comments->count() > 0)
    <div class="mt-8 space-y-6">
        <h3 class="text-2xl font-bold text-gray-900">
            Comments ({{ $comments->count() }})
        </h3>
        
        <div class="space-y-6">
            @foreach($comments as $comment)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-indigo-600 font-semibold text-sm">
                                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        {{ $comment->user->name }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $comment->created_at->format('F j, Y \a\t g:i A') }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="text-gray-700 whitespace-pre-wrap break-words">
                                {{ $comment->content }}
                            </div>
                        </div>
                        
                        @if($canDelete && (auth()->user()->isAdmin() || (auth()->user()->isAuthor() && $comment->post->author_id === auth()->id())))
                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" id="delete-form-{{ $comment->id }}">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button
                                    type="button"
                                    onclick="confirmDelete({{ $comment->id }}, '{{ addslashes(\Illuminate\Support\Str::limit($comment->content, 50)) }}')"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 rounded px-2 py-1"
                                >
                                    Delete
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@else
    <div class="mt-8 bg-gray-50 rounded-lg border border-gray-200 p-6 text-center">
        <p class="text-gray-600">No comments yet. Be the first to comment!</p>
    </div>
@endif

@if($canDelete)
    @push('scripts')
    <script>
        function confirmDelete(commentId, commentPreview) {
            if (confirm(`Are you sure you want to delete this comment?\n\n"${commentPreview}"\n\nThis action cannot be undone.`)) {
                document.getElementById('delete-form-' + commentId).submit();
            }
        }
    </script>
    @endpush
@endif

