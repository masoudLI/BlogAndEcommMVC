<?php

namespace App\Shop\Actions;

use App\Shop\ProductUploadImage;
use App\Shop\Model\Shop;
use App\Shop\PdfUpload;
use App\Shop\Repository\ShopRepository;
use Framework\Router;
use Framework\Actions\CrudAction;
use Framework\Session\FlashService;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class ShopCrudAction extends CrudAction
{

    protected $viewPath = "@shop/admin/products";

    protected $routePrefix = "shop_admin_products";

    private $productUploadImage;

    public $acceptedParams = ['title', 'slug', 'description', 'price', 'created_at', 'published'];

    private $repository;

    private $pdfUpload;

    public function __construct(
        RendererInterface $renderer,
        ShopRepository $repository,
        Router $router,
        FlashService $flash,
        ProductUploadImage $productUploadImage,
        PdfUpload $pdfUpload
    ) {
        parent::__construct($renderer, $repository, $router, $flash);
        $this->productUploadImage = $productUploadImage;
        $this->repository = $repository;
        $this->pdfUpload = $pdfUpload;
    }

    protected function getNewEntity()
    {
        $product = new Shop();
        $product->setCreatedAt(new \DateTime());
        return $product;
    }

    protected function delete(Request $request)
    {
        $product = $this->repository->find($request->getAttribute('id'));
        /** @var Shop $product */
        $this->productUploadImage->delete($product->getImage());
        return parent::delete($request);
    }

    /**
     * @param Request $request
     * @param Product $item
     * @return array
     */
    protected function prePersist(Request $request, $item): array
    {
        $params = array_merge($request->getParsedBody(), $request->getUploadedFiles());
        $image = $this->productUploadImage->upload($params['image'], $item->getImage());
        if ($image) {
            $params['image'] = $image;
            $this->acceptedParams[] = 'image';
        }
        $params = array_filter($params, function ($key) {
            return in_array($key, $this->acceptedParams);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($params, ['updated_at' => date('Y-m-d H:i:s')]);
    }


    protected function postPersist(Request $request, $item): void
    {
        $file = $request->getUploadedFiles()['pdf'];
        $productId = $item->getId() ?: $this->repository->getPdo()->lastInsertId();
        $this->pdfUpload->upload($file, "$productId.pdf", "$productId.pdf");
        parent::postPersist($request, $item);
    }


    protected function getValidator(Request $request)
    {
        $validator = parent::getValidator($request)
            ->required('title', 'slug', 'description', 'created_at', 'image')
            ->length('title', 5)
            ->length('slug', 5)
            ->slug('slug')
            ->unique('slug', $this->repository, null, $request->getAttribute('id'))
            ->length('description', 5)
            //->float('price')
            ->dateTime('created_at')
            ->extension('image', ['jpg', 'png', 'jpeg'])
            ->extension('pdf', ['pdf']);
        if (is_null($request->getAttribute('id'))) {
            $validator->uploaded('image');
            $validator->uploaded('pdf');
        }
        return $validator;
    }
}
