<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/product")
 *
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index", methods={"GET"})
     *
     */
    public function index(ProductRepository $productRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $prods = $productRepository->findAll();
        // Paginate the results of the query
        $prods = $paginator->paginate(
        // Doctrine Query, not results
            $prods,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );
        return $this->render('product/index.html.twig', [
            'products' => $prods,
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET","POST"})
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     * IsGranted("ROLE_USER")
     */
    public function new(Request $request, Product $product = null): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $view = 'product/edit.html.twig';
        if(!$product){
            $product = new Product();
            $view = 'product/new.html.twig';
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render($view, [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

//    /**
//     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
//     */
//    public function edit(Request $request, Product $product): Response
//    {
//        $form = $this->createForm(ProductType::class, $product);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $this->getDoctrine()->getManager()->flush();
//
//            return $this->redirectToRoute('product_index', [
//                'id' => $product->getId(),
//            ]);
//        }
//
//        return $this->render('product/edit.html.twig', [
//            'product' => $product,
//            'form' => $form->createView(),
//        ]);
//    }

    /**
     * @Route("/{id}", name="product_delete", methods={"DELETE"})
     * IsGranted("ROLE_USER")
     */
    public function delete(Request $request, Product $product): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index');
    }
}
