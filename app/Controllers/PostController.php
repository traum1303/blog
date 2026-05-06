<?php declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Services\PaginationFactory;
use App\Services\PostService;

class PostController extends AbstractController {
    public function __construct(
        private readonly PostService       $postService,
        private readonly PaginationFactory $paginationFactory,
        \Smarty                            $smarty
    ) {
        parent::__construct($smarty);
    }

    public function index(Request $request): void
    {
        $data = $this->postService->getAllPosts($request);
        $paginator = $this->paginationFactory->make($data['items'], $data['total'], $data['limit'], $data['page']);

        $this->smarty
            ->assign('posts', $paginator->items())
            ->assign('paginator', $paginator)
            ->display('posts.tpl');
    }

    public function show(Request $request, int $id): void
    {
        $post = $this->postService->getPostWithIncrementingViews($id);
        $related = $this->postService->getRelatedPosts($id, 3);
        $this->smarty
            ->assign('post', $post)
            ->assign('related', $related)
            ->display('post.tpl');
    }
}
