Getting started
===============

Prerequisites
-------------

This api requires Symfony 5.1+ and assume composer is installed.

Installation
------------

Step 1:
Download symfony skeleton project

    composer create-project symfony/skeleton symfony-rest-api
		
		
Step 2:
Install this dependency using composer

``` bash
composer require serializer
composer require friendsofsymfony/rest-bundle
composer require symfony/maker-bundle --dev
composer require sensio/framework-extra-bundle
composer require symfony/validator
composer require symfony/form
composer require symfony/orm-pack
```

Step 3:
Download and install symfony cli. https://symfony.com/download. Go to root of the project and write

     symfony server:start

Step 4: Go to \config\packages\fos_rest.yaml  and add this
     
	 fos_rest:
		format_listener:
			rules:
			- { path: "^/", priorities: ["json"], fallback_format: json }
		exception:
			enabled: true
		view:
		 view_response_listener: "force"
		 formats:
			json: true
					
Step 5:	Go to config\services.yaml  and add this

	sensio_framework_extra.view.listener:
					alias: Sensio\Bundle\FrameworkExtraBundle\EventListener\TemplateListener
					
	
	
Step 6: Create Entity  `bin/console make:entity Product`: and add this
		
	namespace App\Entity;
	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Validator\Constraints as Assert;
	/**
	 * @ORM\Entity
	 * @ORM\Table(name="product")
	 */
	class Product
	{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	/**
	 * @ORM\Column(type="string", length=100)
	 * @Assert\NotBlank()
	 *
	 */
	private $name;
	/**
	 * @ORM\Column(type="text")
	 * @Assert\NotBlank()
	 */
	private $description;

	/**
	 * @ORM\Column(type="text")
	 * @Assert\NotBlank()
	 */
	private $price;

	/**
	 * @ORM\Column(type="text")
	 * @Assert\NotBlank()
	 */
	private $category;

	/**
	 * @return mixed
	 */

	public function getId()
	{
	return $this->id;
	}
	/**
	 * @param mixed $id
	 */
	public function setId($id)
	{
	$this->id = $id;
	}
	/**
	 * @return mixed
	 */
	public function getName()
	{
	return $this->name;
	}
	/**
	 * @param mixed $name
	 */
	public function setName($name)
	{
	$this->name = $name;
	}
	/**
	 * @return mixed
	 */
	public function getDescription()
	{
	return $this->description;
	}
	/**
	 * @param mixed $description
	 */
	public function setDescription($description)
	{
	$this->description = $description;
	}
	/**
	 * @return mixed
	 */
	public function getPrice()
	{
	return $this->price;
	}
	/**
	 * @param mixed $price
	 */
	public function setPrice($price)
	{
	$this->price = $price;
	}

	/**
	 * @return mixed
	 */
	public function getCategory()
	{
	return $this->category;
	}
	/**
	 * @param mixed $category
	 */
	public function setCategory($category)
	{
	$this->category = $category;
	}

	}


Step 7: Create Controller Register bundle into `bin/console make:controller ProductController`: and add this
 
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
Step 8: Add validation using form 

	namespace App\Form;
	use Symfony\Component\Form\AbstractType;
	use Symfony\Component\Form\FormBuilderInterface;
	use Symfony\Component\Form\Extension\Core\Type\SubmitType;
	use Symfony\Component\OptionsResolver\OptionsResolver;
	use App\Entity\Product;

	class ProductType extends AbstractType
	{
		public function buildForm(FormBuilderInterface $builder, array $options)
		{
			$builder
				->add('name')
				->add('description')
				->add('price')
				->add('category')
				->add('save', SubmitType::class)
			;
		}

		public function configureOptions(OptionsResolver $resolver)
		{
			$resolver->setDefaults(array(
				'data_class' => Product::class,
				'csrf_protection' => false
			));
		}
	}
	
Step 9: Excute entity
	
	php bin/console doctrine:schema:update --force 
	
Step 10: Configure .env for database setting. Add this line to .env file
	
	DATABASE_URL=mysql://root:@127.0.0.1:3306/symfony_rest_api?serverVersion=5.7
	
#### Finish time to test: Open Postman

