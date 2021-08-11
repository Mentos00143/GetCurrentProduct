<?php
namespace Perspective\GetCurrentProduct\ViewModel;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\SessionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class GetCurrentProduct implements ArgumentInterface
{


    private SessionFactory $catalogSessionFactory;
    private ProductRepositoryInterface $productRepository;
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(
        SessionFactory $catalogSessionFactory,
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository
    )
    {
        $this->catalogSessionFactory = $catalogSessionFactory;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return ProductInterface
     * @throws NoSuchEntityException
     */
    public function getCurrentProduct(): ProductInterface
    {
        $productID = $this->catalogSessionFactory->create()->getData('last_viewed_product_id');
        return $this->productRepository->getById($productID);
    }

    /**
     * @return CategoryInterface|null
     * @throws NoSuchEntityException
     */
    public function getCategory(): ?CategoryInterface
    {
        $categoryID = $this->catalogSessionFactory->create()->getData('last_viewed_category_id');
        $category = $categoryID ? $this->categoryRepository->get($categoryID) : null;
        return $category && in_array($category->getId(), $this->getCurrentProduct()->getCategoryIds())
            ? $category : null;
    }
}
