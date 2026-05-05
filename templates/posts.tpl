{extends file="layouts/main.tpl"}
{block name="content"}
    <h2 class="text-2xl font-bold mb-6">Posts</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {foreach $posts as $post}
            <div class="bg-white p-4 rounded shadow hover:shadow-md transition">
                {if $post->getImage()}
                    <img src="{$post->getImage()}" class="mb-4 rounded" alt="{$post->getTitle()}">
                {/if}
                <a href="/post/{$post->getId()}">
                    <h4 class="font-bold mb-2">{$post->getTitle()}</h4>
                </a>
                <p class="text-sm text-gray-600">{$post->getDescription()|truncate_text:100:"/post/`$post->getId()`" nofilter}</p>
                <div class="mt-4 text-sm text-gray-500">👁 {$post->getViews()} views</div>
                <div class="mt-4 text-sm text-gray-500">Created at: {$post->getCreatedAt()} </div>
            </div>
        {/foreach}
    </div>
    <div class="flex justify-center mt-6 space-x-1">
        {$paginator->links() nofilter}
    </div>
{/block}
