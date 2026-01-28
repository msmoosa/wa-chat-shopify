<?php
namespace App\Services;

use Osiset\ShopifyApp\Contracts\ShopModel as IShopModel;

class ShopifyService
{
    /**
     * @var IShopModel
     */
    protected $shop;

    public function __construct(IShopModel $shop) {
        $this->shop = $shop;
    }
    public function getCheckouts()
    {
        $response = $this->shop->api()->rest('GET', '/admin/api/2026-01/checkouts.json');
        return $response['body']['checkouts'] ?? [];
    }
}