<?php

namespace App\Controller;

use App\Entity\ApiKey;
use App\Entity\Product;
use App\Entity\Customer;
use OpenApi\Annotations as OA;
use App\Repository\ApiKeyRepository;
use App\Repository\ProductRepository;
use App\Repository\CustomerRepository;
use OpenApi\Annotations\SecurityScheme;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ApiController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * Consult all bilmo product
     * @Route("/api/auth/get/all/products", name="get-all-products", methods={"GET"})
     * 
     *      * API DOC *
     *
     * Consult all bilmo product.
     *
     * @OA\Tag(name="Products")
     *
     * @OA\Response( response=200, description="Return all product")
     * @OA\Response( response=204, description="No products for moments !")
     * @OA\Response( response=401, description="Expired JWT Token or bilmo api key is not valid")
     */
    public function getAllProducts(ApiKeyRepository $verifapikey, ProductRepository $product, Request $request)
    {

        //1. Verif api key exist 
        $apikey = $request->headers->get("user-api-key");
        $verifapikey = $verifapikey->findOneBy(array('api_key' => $apikey));

        if (!$verifapikey) {
            return new JsonResponse('Your bilmo api key is not valid or is not declared !', 400, ['Content-Type' => 'application/json']);
        }


        //2. Get all product
        $products = $product->findAll();

        if (count($products) === 0) {
            return new JsonResponse('No products for moments !', 204, ['Content-Type' => 'application/json']);
        }


        // For instance, return a Response with encoded Json
        return $this->json($products, 200, [], []);
    }

    /**
     * Consult detail bilmo product
     * @Route("/api/auth/get/detail/{productid}/product", name="get-product-detail", methods={"GET"})
     * 
     *      * API DOC *
     *
     * Consult bilmo product by id.
     *
     * @OA\Tag(name="Products")
     * 
     * @OA\Response( response=200, description="Return defined product by id")
     * @OA\Response( response=404, description="Product dont exist !")
     * @OA\Response( response=401, description="Expired JWT Token or bilmo api key is not valid")
     * 
     */
    public function getDetailProduct($productid, ApiKeyRepository $verifapikey, ProductRepository $product, Request $request): Response
    {
        //1. Verif api key exist 
        $apikey = $request->headers->get("user-api-key");
        $verifapikey = $verifapikey->findOneBy(array('api_key' => $apikey));

        if (!$verifapikey) {
            return new JsonResponse('Your bilmo api key is not valid or is not declared !', 400, ['Content-Type' => 'application/json']);
        }
        //2. Get detailed product
        $product = $product->findBy(array('id' => $productid));

        if (!$product) {
            return new JsonResponse('Product dont exist !', 404, ['Content-Type' => 'application/json']);
        }


        // For instance, return a Response with encoded Json
        return $this->json($product, 200, [], []);
    }

    /**
     * Consult all customer by user
     * @Route("/api/auth/get/all/customers", name="get-all-customer-by-user", methods={"GET"})
     * 
     *      * API DOC *
     *
     * Consult all bilmo customer for defined user.
     *
     * @OA\Tag(name="Customers")
     * 
     * @OA\Response( response=200, description="Return all custom defined by user")
     * @OA\Response( response=404, description="No customers for moments in your account !")
     * @OA\Response( response=401, description="Expired JWT Token or bilmo api key is not valid")
     */
    public function getAllCustomers(ApiKeyRepository $verifapikey, CustomerRepository $customer, Request $request): Response
    {

        //1. Verif api key exist 
        $apikey = $request->headers->get("user-api-key");
        $verifapikey = $verifapikey->findOneBy(array('api_key' => $apikey));

        if (!$verifapikey) {
            return new JsonResponse('Your bilmo api key is not valid or is not declared !', 401, ['Content-Type' => 'application/json']);
        }

        //2. Get all customers
        $user = $verifapikey->getUser();
        $customer = $customer->findAllByUser($user);

        if (count($customer) === 0) {
            return new JsonResponse('No customers for moments in your account !', 200, ['Content-Type' => 'application/json']);
        }

        // For instance, return a Response with encoded Json

        return $this->json($customer, 200, [], ['groups' => 'customer:read']);
    }

    /**
     * Consult customer detail by user
     * @Route("/api/auth/get/{useruniqueid}/customer", name="get-detail-customer-by-user", methods={"GET"})
     * 
     *      * API DOC *
     *
     * Consult bilmo customer by id for defined user.
     *
     * @OA\Tag(name="Customers")
     * 
     * @OA\Response( response=200, description="Return defined customer defined by id")
     * @OA\Response( response=404, description="User dont exist or your are not allowed for take information !")
     * @OA\Response( response=401, description="Expired JWT Token or bilmo api key is not valid")
     */
    public function getDatailCustomer(ApiKeyRepository $verifapikey, CustomerRepository $customer, $useruniqueid, Request $request): Response
    {

        //1. Verif api key exist 
        $apikey = $request->headers->get("user-api-key");
        $verifapikey = $verifapikey->findOneBy(array('api_key' => $apikey));

        if (!$verifapikey) {
            return new JsonResponse('Your bilmo api key is not valid or is not declared !', 401, ['Content-Type' => 'application/json']);
        }


        //2. Get customer details
        $user = $verifapikey->getUser();



        $customer = $customer->findOneByUser($user, $useruniqueid);

        if (count($customer) === 0) {
            return new JsonResponse('User dont exist or your are not allowed for take information !', 200, ['Content-Type' => 'application/json']);
        }

        return $this->json($customer, 200, [], ['groups' => 'customer:read']);
    }

    /**
     * add new customer
     * @Route("/api/auth/post/add/customer", name="post-new-customer" , methods={"POST"})
     * 
     *      * API DOC *
     *
     * Add new bilmo customer for defined user.
     *
     * @OA\Tag(name="Customers")
     * 
     * @OA\Response( response=200, description="Customer is created !")
     * @OA\Response( response=400, description="Bad request !")
     * @OA\Response( response=401, description="Expired JWT Token or bilmo api key is not valid")
     * 
     * @OA\RequestBody(
     *    required=true,
     *    description="Add new customer on your account",
     *    @OA\JsonContent(
     *       required={"email","firstname","lastname","phone","adress","zip_code","city","country"},
     *       @OA\Property(property="email", type="string", format="email", example="contact@bilmo.com"),
     *       @OA\Property(property="firstname", type="string", example="John"),
     *       @OA\Property(property="lastname", type="string", example="Doe"),
     *       @OA\Property(property="phone", type="string", example="+33675901691"),
     *       @OA\Property(property="adress", type="string", example="8, sentier de la carriere"),
     *       @OA\Property(property="city", type="string", example="neuilly sur marne"),
     *       @OA\Property(property="country", type="string", example="France"),
     *    ),
     * ),
     */
    public function postNewCustomer(Request $request, ApiKeyRepository $verifapikey, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        //1. Verif api key exist 
        $apikey = $request->headers->get("user-api-key");
        $verifapikey = $verifapikey->findOneBy(array('api_key' => $apikey));

        if (!$verifapikey) {
            return new JsonResponse('Your bilmo api key is not valid or is not declared !', 400, ['Content-Type' => 'application/json']);
        }

        try {
            //2. Add New customer
            $user = $verifapikey->getUser();
            $data = $request->getContent();

            $newcustomer = $serializer->deserialize($data, Customer::class, 'json');
            $newcustomer->setUser($user);

            $error = $validator->validate($newcustomer);

            if (count($error) > 0) {
                return $this->json($error, 400, ['Content-Type' => 'application/json']);
            }

            $this->entityManager->persist($newcustomer);
            $this->entityManager->flush();

            return new JsonResponse('Customer is created !', 201, ['Content-Type' => 'application/json']);
        } catch (NotEncodableValueException $e) {
            return new JsonResponse(['status' => 400, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * remove customer
     * @Route("/api/auth/delete/{useruniqueid}/customer", name="remove-customer", methods={"DELETE"})
     * 
     *      * API DOC *
     *
     * Remove bilmo customer by id for defined user.
     * 
     * @OA\Tag(name="Customers")
     * 
     * @OA\Response( response=200, description="Customer is removed !!")
     * @OA\Response( response=418, description="You dont allowed for remove this customer !!")
     * @OA\Response( response=401, description="Expired JWT Token or bilmo api key is not valid")
     *
     */
    public function deleteCustomer($apikey, $useruniqueid, ApiKeyRepository $verifapikey, CustomerRepository $customer, Request $request): Response
    {
        //1. Verif api key exist 
        $apikey = $request->headers->get("user-api-key");
        $verifapikey = $verifapikey->findOneBy(array('api_key' => $apikey));

        if (!$verifapikey) {
            return new JsonResponse('Your bilmo api key is not valid or is not declared !', 400, ['Content-Type' => 'application/json']);
        }

        //Delect user
        $user = $verifapikey->getUser();

        $customer = $customer->findOneByUser($user, $useruniqueid);

        if ($customer) {
            $this->entityManager->remove($customer);
            $this->entityManager->flush();
            return new JsonResponse('Customer ' . $useruniqueid . ' is removed !', 200, ['Content-Type' => 'application/json']);
        }

        return new JsonResponse('You dont allowed for remove this customer !', 418, ['Content-Type' => 'application/json']);
    }
}
