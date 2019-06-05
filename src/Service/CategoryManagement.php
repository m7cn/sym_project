<?php
/**
 * Created by PhpStorm.
 * User: machour
 * Date: 04.06.19
 * Time: 13:17
 */

namespace App\Service;

use App\Repository\CategoryRepository;

class CategoryManagement
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getCategories(){
        return $this->categoryRepository->findAll();
    }

}