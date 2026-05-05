<?php declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Repositories\CategoryRepository;
use App\Services\CategoryService;
use App\Services\PaginationFactory;
use Smarty;

class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly CategoryRepository $categoryRepository,
        private readonly PaginationFactory $paginationFactory,
        Smarty $smarty
    ) {
        parent::__construct($smarty);
    }

    /**
     * @throws \SmartyException
     */
    public function show(Request $request, int $id): void
    {
        $data = $this->categoryService->getPostsByCategory($request, $id);
        $category = $this->categoryRepository->find($id);
        $paginator = $this->paginationFactory->make($data['items'], $data['total'], $data['limit'], $data['page']);

        $this->smarty
            ->assign('posts', $paginator->items())
            ->assign('paginator', $paginator)
            ->assign('category', $category)
            ->display('category.tpl');
    }
}