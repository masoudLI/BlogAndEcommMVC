<?php

namespace Framework\Actions;

use Framework\Router;
use Framework\Validator;
use Framework\Session\FlashService;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Database\AbstractRepository;
use Framework\Database\Hydrator;
use Psr\Http\Message\ServerRequestInterface as Request;

class CrudAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var AbstractRepository
     */
    private $abstractRepository;

    /**
     * @var FlashService
     */
    private FlashService $flash;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var string
     */
    protected $viewPath;

    /**
     * @var string
     */
    protected $routePrefix;

    /**
     * @var string[]
     */
    protected $acceptedParams = [];

    /**
     * @var string
     */
    protected $messages = [
        'create' => "L'élément a bien été créé",
        'edit'   => "L'élément a bien été modifié",
        'supprimer'   => "L'élément a bien été supprimé",
    ];

    use RouterAwareAction;

    public function __construct(
        RendererInterface $renderer,
        AbstractRepository $abstractRepository,
        Router $router,
        FlashService $flash
    ) {
        $this->renderer = $renderer;
        $this->abstractRepository = $abstractRepository;
        $this->router = $router;
        $this->flash = $flash;
    }

    public function __invoke(Request $request)
    {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);

        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }
        if (substr((string)$request->getUri(), -6) === 'create') {
            return $this->create($request);
        }
        return $this->index($request);
    }


    public function index(Request $request)
    {
        $params = $request->getQueryParams();
        $items = $this->abstractRepository->findAll()->paginate(12, $params['p'] ?? 1);
        return $this->renderer->render($this->viewPath . '/index', [
            'items' => $items
        ]);
    }

    public function create(Request $request)
    {
        $item = $this->getNewEntity();
        if ($request->getMethod() === 'POST') {
            $params = $this->prePersist($request, $item);
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->abstractRepository->insert($params);
                $this->flash->addFlash('success', $this->messages['create']);
                return $this->redirect($this->routePrefix . '_index');
            }
            Hydrator::hydrate($request->getParsedBody(), $item);
            $errors = $validator->getErrors();
        }
        return $this->renderer->render(
            $this->viewPath . '/create',
            $this->formParams([
                'errors' => $errors ?? '',
                'item' => $item
            ])
        );
    }

    public function edit(Request $request)
    {
        $id = (int)$request->getAttribute('id');
        $item = $this->abstractRepository->find($id);
        if ($request->getMethod() === 'POST') {
            $params = $this->prePersist($request, $item);
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->abstractRepository->update($id, $params);
                $this->flash->addFlash('success', $this->messages['edit']);
                return $this->redirect($this->routePrefix . '_index');
            }
            Hydrator::hydrate($request->getParsedBody(), $item);
            $errors = $validator->getErrors();
        }
        return $this->renderer->render(
            $this->viewPath . '/edit',
            $this->formParams([
                'errors' => $errors ?? '',
                'item' => $item
            ])
        );
    }

    protected function delete(Request $request)
    {
        $this->abstractRepository->delete($request->getAttribute('id'));
        $this->flash->addFlash('error', $this->messages['supprimer']);
        return $this->redirect($this->routePrefix . '_index');
    }

    /**
     * getNewEntity
     *
     * @return object
     */
    protected function getNewEntity()
    {
        $entity = $this->abstractRepository->getEntity();
        return new $entity();
    }

    /**
     * Filtre les paramètres reçu par la requête
     *
     * @param  Request $request
     * @return array
     */
    protected function prePersist(Request $request, $item): array
    {
        return array_filter((array)array_merge($request->getParsedBody(), $request->getUploadedFiles()), function ($key) {
            return in_array($key, []);
        }, ARRAY_FILTER_USE_KEY);
    }


    /**
     * Génère le validateur pour valider les données
     *
     * @param  mixed $request
     * @return Validator
     */
    protected function getValidator(Request $request)
    {
        return new Validator(array_merge($request->getParsedBody(), $request->getUploadedFiles()));
    }

    /**
     * Permet de traiter les paramètres à envoyer à la vue
     *
     * @param $params
     * @return array
     */
    protected function formParams(array $params): array
    {
        return $params;
    }
}
