<?php

namespace App\Utils\Manager;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class ProductManager
{
    private EntityManagerInterface $entityManager;
    private string $productImagesDir;
    private ProductImageManager $productImageManager;

    public function __construct(EntityManagerInterface $entityManager, ProductImageManager $productImageManager, string $productImagesDir)
    {
        $this->entityManager = $entityManager;
        $this->productImagesDir = $productImagesDir;
        $this->productImageManager = $productImageManager;
    }

    public function getRepository() : ObjectRepository
    {
        return $this->entityManager->getRepository(Product::class);
    }

    public function save(Product $product)
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    public function remove(Product $product)
    {
        $product->setIsDeleted(true);
        $this->save($product);
    }

    public function getProductImagesDir(Product $product)
    {
        return $filename = sprintf('%s\%s', $this->productImagesDir, $product->getId());
    }

    public function updateProductImagesDir(Product $product, string $tempImageFilename = null): Product
    {
        if (!$tempImageFilename){
            return $product;
        }

        $productDir = $this->getProductImagesDir($product);
        $productImage = $this->productImageManager->saveImageForProduct($productDir, $tempImageFilename);
        $productImage->setProduct($product);
        $product->addProductImage($productImage);

        return $product;
    }
}