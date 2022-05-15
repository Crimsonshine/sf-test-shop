<?php

namespace App\Form\Handler;

use App\Entity\Product;
use App\Utils\File\FileSaver;
use App\Utils\Manager\ProductManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;

class ProductFormHandler
{
    private FileSaver $fileSaver;
    private ProductManager $productManager;

    public function __construct(ProductManager $productManager, FileSaver $fileSaver)
    {
        $this->fileSaver = $fileSaver;
        $this->productManager = $productManager;
    }

    public function processEditForm(Product $product, FormInterface $form)
    {
        $this->productManager->save($product);

        $newImageFile = $form->get('newImage')->getData();

        $tempImageFilename = $newImageFile ?
            $this->fileSaver->saveUploadedFileIntoTemp($newImageFile)
            : null;

        $this->productManager->updateProductImagesDir($product, $tempImageFilename);
        // TODO: Добавление картинки различного размера товару
        // 1. Сохранение изменения товара (+)
        // 2. Сохранить загруженный файл в временной папке (+)

        // 3. Добавить изображение товару addProductImage & ProductImage
        // 3.1. Получение пути папки с картинками товара (+)

        // 3.2. Работа с ProductImage
        // 3.2.1 Изменение размера и сохранение картинки в папке товара (BIG, MIDDLE, SMALL) (+)
        // 3.2.2 Создаем ProductImage & return to product (+)

        // 3.3 Сохранить Product с new ProductImage (+)

        $this->productManager->save($product);
        //dd($product);

        return $product;
    }
}