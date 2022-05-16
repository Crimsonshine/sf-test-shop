<?php

namespace App\Form\DTO;

use App\Entity\Product;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class EditProductModel
{
    /**
     * @var int
     */

    public $id;

    /**
     * @var string
     */
    #[Assert\NotBlank(message: 'Пожалуйста введите название')]
    public $title;

    /**
     * @var string
     */
    #[Assert\NotBlank(message: 'Пожалуйста введите цену')]
    #[Assert\GreaterThanOrEqual(value: '0')]
    public $price;

    /**
     * @var UploadedFile|null
     */
    #[Assert\File(maxSize: '10240K', mimeTypes: ['image/jpeg', 'image/png'], mimeTypesMessage: 'Пожалуйста загрузите корректное изображение (png/jpg и <=10MB)')]
    public $newImage;

    /**
     * @var int
     */
    #[Assert\NotBlank(message: 'Пожалуйста укажите количество')]
    public $quantity;

    /**
     * @var string
     */
    public $description;

    /**
     * @var bool
     */
    public $isPublished;

    /**
     * @var bool
     */
    public $isDeleted;

    public static function makeFromProduct(?Product $product): self
    {
        $model = new self();
        if (!$product) {
            return $model;
        }

        $model->id          = $product->getId();
        $model->title       = $product->getTitle();
        $model->price       = $product->getPrice();
        $model->quantity    = $product->getQuantity();
        $model->description = $product->getDescription();
        $model->isPublished = $product->isIsPublished();
        $model->isDeleted   = $product->isIsDeleted();

        return $model;
    }
}