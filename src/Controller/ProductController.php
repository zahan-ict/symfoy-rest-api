<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\Product;
use App\Form\ProductType;
/**
 * Product controller.
 * @Route("/api", name="api_")
 */
class ProductController extends AbstractFOSRestController
{
  /**
   * Lists all Product.
   * @Rest\Get("/products")
   *
   * @return Response
   */
  public function getProductAction()
  {
    $repository = $this->getDoctrine()->getRepository(Product::class);
    $products = $repository->findall();
    return $this->handleView($this->view($products));
  }
  /**
   * Add Product.
   * @Rest\Post("/addproduct")
   *
   * @return Response
   */
  public function postProductAction(Request $request)
  {
    $movie = new Product();
    $form = $this->createForm(ProductType::class, $movie);
    $data = json_decode($request->getContent(), true);
   
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($movie);
      $em->flush();
      return $this->handleView($this->view(['status' => 'ok'], Response::HTTP_CREATED));
    }
    return $this->handleView($this->view($form->getErrors()));
  }
}