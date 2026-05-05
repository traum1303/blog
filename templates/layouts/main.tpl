<!DOCTYPE html>
<html lang="eng">
    <head>
        <meta charset="UTF-8">
        <title>My Blog</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100 text-gray-800">
        <header class="bg-white shadow-sm border-b">
            <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
                <!-- Logo -->
                <a href="/" class="text-xl font-bold text-gray-900 tracking-tight">
                    MyBlog
                </a>
                <!-- Menu -->
                <nav class="flex items-center gap-6 text-sm font-medium">
                    <a href="/" class="{is_active path='/' exact=true}">
                        Categories
                    </a>
                    <a href="/posts" class="{is_active path='/posts'}">
                        Posts
                    </a>
                    <a href="/about" class="{is_active path='/about'}">
                        About
                    </a>
                    <a href="/contact"  class="{is_active path='/contact'}">
                        Contact
                    </a>
                    <!-- CTA button -->
                    <a href="post/create" class="ml-2 px-4 py-1.5 rounded-full bg-black text-white text-sm hover:bg-gray-800 transition">
                        New Post
                    </a>
                </nav>
            </div>
        </header>
        <main class="max-w-5xl mx-auto p-6">
        {block name="content"}{/block}
        </main>
    </body>
</html>
