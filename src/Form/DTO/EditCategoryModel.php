<?php

namespace App\Form\DTO;

use App\Entity\Category;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

class EditCategoryModel
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    #[Assert\NotBlank(message: 'Пожалуйста введите название')]
    public $title;

    /**
     * @param Category|null $category
     * @return static
     */
    public static function makeFromCategory(?Category $category): self
    {
        $model = new self();

        if (!$category) {
            return $model;
        }

        $model->id = $category->getId();
        $model->title = $category->getTitle();

        return $model;
    }
}