{extends file="layouts/main.tpl"}
{block name="content"}
    <h2 class="text-2xl font-bold mb-6">Categories</h2>
    {foreach $categories as $item}
        <div class="mb-10">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">{$item->getName()}</h3>
                <a href="/category/{$item->getId()}" class="text-blue-600 hover:underline">All →</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {foreach $item->getPosts() as $post}
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
        </div>
    {/foreach}

    <div class="flex justify-center mt-6 space-x-1">
        {$paginator->links() nofilter}
    </div>
{/block}
