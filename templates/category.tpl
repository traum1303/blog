{extends file="layouts/main.tpl"}
{block name="content"}
    <div class="mb-6">
        <h2 class="text-2xl font-bold">Category: {$category->getName()}</h2>
        <p class="text-gray-600">{$category->getDescription()}</p>
    </div>
    <div class="mb-4 space-x-4">
        <a href="?sort=date" class="px-3 py-1 bg-gray-200 rounded">Date</a>
        <a href="?sort=views" class="px-3 py-1 bg-gray-200 rounded">Views</a>
    </div>
    <div class="space-y-4">
        {foreach $posts as $post}
            <div class="bg-white p-4 rounded shadow">
                <a href="/post/{$post->getId()}">
                    <h3 class="text-lg font-semibold mb-2">{$post->getTitle()}</h3>
                </a>
                <p class="text-gray-600">{$post->getDescription()}</p>
                <div class="text-sm text-gray-500 mt-2">👁 {$post->getViews()}</div>
                <div class="text-sm text-gray-500 mt-2">📆 {$post->getCreatedAt()}</div>
            </div>
        {/foreach}
    </div>
    <div class="flex justify-center mt-6 space-x-1">
        {$paginator->links() nofilter}
    </div>
{/block}
