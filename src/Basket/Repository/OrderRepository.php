<?php

namespace App\Basket\Repository;

use App\Auth\Model\User;
use App\Basket\Basket;
use App\Basket\BasketRow;
use App\Basket\Model\Order;
use App\Basket\Repository\OrderRowRepository;
use App\Shop\Model\Product;
use Framework\Database\AbstractRepository;
use Framework\Database\QueryBuilder;
use Traversable;

class OrderRepository extends AbstractRepository
{

    protected string $table = 'orders';

    protected string $entity = Order::class;

    private $orderRowRepository;


    /**
     * @var OrderRowTable
     */
    protected $orderRowTable;


    public function findForUser(User $user): QueryBuilder
    {
        return $this->makeQuery()->where("user_id = {$user->getId()}");
    }

    public function findRows($orders)
    {
        $ordersId = [];
        foreach ($orders as $order) {
            $ordersId[] = $order->getId();
        }
        $rows = $this->getOrderRowRepository()->makeQuery()
            ->join('products as p', 'p.id = o.product_id')
            ->where('o.order_id IN (' . implode(',', $ordersId) . ')')
            ->select('o.*', 'p.image as productImage', 'p.title as productTitle', 'p.slug as productSlug')
            ->fetchAll();
        /** @var OrderRow $row */
        foreach ($rows as $row) {
            foreach ($orders as $order) {
                if ($row->getOrderId() === $order->getId()) {
                    $product = new Product();
                    $product->setId($row->getProductId());
                    $product->setTitle($row->productTitle);
                    $product->setImage($row->productImage);
                    $product->setSlug($row->productSlug);
                    $row->setProduct($product);
                    $order->addRow($row);
                    break;
                }
            }
        }
        return $rows;
    }


    public function createFromBasket(Basket $basket, array $params = [])
    {
        $params['price'] = $basket->getTotal();
        $params['created_at'] = date('Y-m-d H:i:s');

        $this->pdo->beginTransaction();
        $this->insert($params);
        $orderId = $this->getPdo()->lastInsertId();
        /** @var BasketRow */
        foreach ($basket->getRows() as $row) {
            $this->getOrderRowRepository()->insert([
                'order_id' => $orderId,
                'price' => $row->getProduct()->getPrice(),
                'product_id' => $row->getProductId(),
                'quantity' => $row->getQuantity()
            ]);
        }
        $this->pdo->commit();
    }


    public function getOrderRowRepository(): OrderRowRepository
    {
        if ($this->orderRowRepository === null) {
            $this->orderRowRepository = new OrderRowRepository($this->pdo);
        }
        return $this->orderRowRepository;
    }
}
