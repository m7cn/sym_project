<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture {
	public function load(ObjectManager $manager) {
		// create 20 products! Bam!
		for ($i = 0; $i < 20; $i++) {
			$product = new Product();
			$product->setName('product '.$i);
			$product->setPrice(mt_rand(10, 100));
			$product->setDescription(file_get_contents('http://loripsum.net/api'));
			$manager->persist($product);
		}

		$manager->flush();
	}
}
