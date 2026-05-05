{extends file="layouts/main.tpl"}
{block name="content"}
<article class="bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">{$post->getTitle()}</h1>
    {if $post->getImage()}
        <img src="{$post->getImage()}" class="mb-4 rounded" alt="{$post->getTitle()}">
    {/if}
    <p class="text-gray-600 mb-4">{$post->getDescription()}</p>
    <div>{$post->getContent()}</div>

    <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
        <div>
            <div class="mt-4 text-sm text-gray-500">👁 {$post->getViews()} views</div>
            <div class="mt-4 text-sm text-gray-500">📅 {$post->getCreatedAt()}</div>
        </div>
        {if $post->getCategories()}
        <div class="text-sm font-medium">
            Categories:
            {foreach $post->getCategories() as $category}
                <a href="/category/{$category->getId()}" class="text-gray-600 hover:text-black hover:underline">
                   #{$category->getName()}
                </a>
            {/foreach}
        </div>
        {/if}
    </div>

</article>
<hr class="my-8">
<h3 class="text-xl font-bold mb-4">Related posts</h3>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    {foreach $related as $item}
        <div class="bg-white p-4 rounded shadow">
            <a href="/post/{$item->getId()}">{$item->getTitle()}</a>
        </div>
    {/foreach}
</div>
{/block}
