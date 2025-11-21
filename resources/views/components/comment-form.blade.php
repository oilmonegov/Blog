@props(['post'])

@auth
    <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Leave a Comment</h3>
        
        <form action="{{ route('comments.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="post_id" value="{{ $post->id }}">
            
            <div>
                <x-input-label for="content" value="Your Comment" />
                <x-textarea
                    id="content"
                    name="content"
                    rows="5"
                    class="mt-1 block w-full"
                    placeholder="Write your comment here..."
                    required
                >{{ old('content') }}</x-textarea>
                <x-input-error :messages="$errors->get('content')" class="mt-2" />
            </div>
            
            <div class="flex items-center justify-end">
                <x-primary-button type="submit">
                    Post Comment
                </x-primary-button>
            </div>
        </form>
    </div>
@else
    <div class="mt-8 bg-gray-50 rounded-lg border border-gray-200 p-6 text-center">
        <p class="text-gray-700 mb-4">
            Please <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-500 font-medium">log in</a> to leave a comment.
        </p>
    </div>
@endauth

