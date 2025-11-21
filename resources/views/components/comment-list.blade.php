@props(['comments', 'canDelete' => false])

@if($comments->count() > 0)
    <div class="mt-8 space-y-6">
        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 bg-gradient-to-r from-purple-600 to-pink-500 bg-clip-text text-transparent">
            Comments ({{ $comments->count() }})
        </h3>
        
        <div class="space-y-6">
            @foreach($comments as $comment)
                <div class="card-gradient p-6 group">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="flex-shrink-0">
                                    <div class="avatar-gradient">
                                        <div>
                                            <span class="text-purple-600 dark:text-purple-400 font-semibold text-sm">
                                                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                                        {{ $comment->user->name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $comment->created_at->format('F j, Y \a\t g:i A') }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap break-words leading-relaxed">
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
                                    class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 rounded px-2 py-1 transition-colors duration-300"
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
    <div class="mt-8 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-lg border border-purple-200 dark:border-purple-800 p-8 text-center">
        <p class="text-gray-600 dark:text-gray-400 text-lg">No comments yet. Be the first to comment!</p>
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

