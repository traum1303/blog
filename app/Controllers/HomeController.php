<?php declare(strict_types=1);

namespace App\Controllers;
use App\Core\Request;
use App\Services\CategoryService;
use App\Services\PaginationFactory;

class HomeController extends AbstractController {
    public function __construct(
        private readonly PaginationFactory $paginationFactory,
        private readonly CategoryService $categoryService,
        \Smarty $smarty
    ) {
        parent::__construct($smarty);
    }

    public function index(Request $request): void
    {
        $data = $this->categoryService->getWithPostsPaginated($request);
        $paginator = $this->paginationFactory->make($data['items'], $data['total'], $data['limit'], $data['page']);

        $this->smarty
            ->assign('categories', $paginator->items())
            ->assign('paginator', $paginator)
            ->display('home.tpl');
    }
}
