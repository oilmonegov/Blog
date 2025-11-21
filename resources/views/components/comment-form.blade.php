@props(['post'])

@auth
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-colors duration-300">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 bg-gradient-to-r from-purple-600 to-pink-500 bg-clip-text text-transparent">
            Leave a Comment
        </h3>
        
        <form action="{{ route('comments.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="post_id" value="{{ $post->id }}">
            
            <div>
                <x-input-label for="content" value="Your Comment" class="text-gray-700 dark:text-gray-300" />
                <x-textarea
                    id="content"
                    name="content"
                    rows="5"
                    class="mt-1 block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 focus:ring-purple-500 focus:border-purple-500"
                    placeholder="Write your comment here..."
                    required
                >{{ old('content') }}</x-textarea>
                <x-input-error :messages="$errors->get('content')" class="mt-2" />
            </div>
            
            <div class="flex items-center justify-end">
                <button type="submit" class="btn-gradient">
                    Post Comment
                </button>
            </div>
        </form>
    </div>
@else
    <div class="mt-8 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-lg border border-purple-200 dark:border-purple-800 p-6 text-center">
        <p class="text-gray-700 dark:text-gray-300 mb-4">
            Please <a href="{{ route('login') }}" class="text-purple-600 dark:text-purple-400 hover:text-pink-600 dark:hover:text-pink-400 font-medium transition-colors duration-300">log in</a> to leave a comment.
        </p>
    </div>
@endauth

