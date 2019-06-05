<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture {
	public function load(ObjectManager $manager) {
		// create 20 products! Bam!
		for ($i = 1; $i <= 20; $i++) {
		    if($i%5==0 || $i == 1){
                $category = new Category();
                $name = "category ".$i/5;
                $category->setName($name);
            }


			$product = new Product();
			$name = $this->generateRandomString();
			$product->setName($name);
			$product->setPrice(mt_rand(10, 100));
			$product->setDescription(file_get_contents('http://loripsum.net/api'));
            $product->setCategory($category);
            if($i%5==0 || $i == 1) {
                $manager->persist($category);
            }
			$manager->persist($product);
		}

		$manager->flush();
	}

	private function generateRandomString($length = 10) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
