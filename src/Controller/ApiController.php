<?php

namespace App\Controller;

use App\Entity\ApiKey;
use App\Entity\Customer;
use App\Entity\Product;
use App\Repository\ApiKeyRepository;
use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     */
    public function getAllProducts(ApiKeyRepository $verifapikey, ProductRepository $product, Request $request): Response
    {

        //1. Verif api key exist 
        $apikey = $request->headers->get("user-api-key");
        $verifapikey = $verifapikey->findOneBy(array('api_key' => $apikey));

        if (!$verifapikey) {
            return new JsonResponse('Your api key dont match !', 400, ['Content-Type' => 'application/json']);
        }

        //2. Get all product
        $products = $product->findAll();

        if (count($products) === 0) {
            return new JsonResponse('No products for moments !', 200, ['Content-Type' => 'application/json']);
        }


        // For instance, return a Response with encoded Json
        return $this->json($products, 200, [], []);
    }

    /**
     * Consult detail bilmo product
     * @Route("/api/auth/get/detail/{productid}/product", name="get-product-detail", methods={"GET"})
     */
    public function getDetailProduct($productid, ApiKeyRepository $verifapikey, ProductRepository $product, Request $request): Response
    {
        //1. Verif api key exist 
        $apikey = $request->headers->get("user-api-key");
        $verifapikey = $verifapikey->findOneBy(array('api_key' => $apikey));

        if (!$verifapikey) {
            return new JsonResponse('Your api key dont match !', 400, ['Content-Type' => 'application/json']);
        }
        //2. Get detailed product
        $product = $product->findBy(array('id' => $productid));

        if (!$product) {
            return new JsonResponse('Product dont exist !', 400, ['Content-Type' => 'application/json']);
        }


        // For instance, return a Response with encoded Json
        return $this->json($product, 200, [], []);
    }

    /**
     * Consult all customer by user
     * @Route("/api/auth/get/all/customers", name="get-all-customer-by-user", methods={"GET"})
     */
    public function getAllCustomers(ApiKeyRepository $verifapikey, CustomerRepository $customer, Request $request): Response
    {

        //1. Verif api key exist 
        $apikey = $request->headers->get("user-api-key");
        $verifapikey = $verifapikey->findOneBy(array('api_key' => $apikey));

        if (!$verifapikey) {
            return new JsonResponse('Your api key dont match !', 400, ['Content-Type' => 'application/json']);
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
     */
    public function getDatailCustomer(ApiKeyRepository $verifapikey, CustomerRepository $customer, $useruniqueid, Request $request): Response
    {

        //1. Verif api key exist 
        $apikey = $request->headers->get("user-api-key");
        $verifapikey = $verifapikey->findOneBy(array('api_key' => $apikey));

        if (!$verifapikey) {
            return new JsonResponse('Your api key dont match !', 400, ['Content-Type' => 'application/json']);
        }


        //2. Get customer details
        $user = $verifapikey->getUser();



        $customer = $customer->findOneByUser($user, $useruniqueid);

        if (count($customer) === 0) {
            return new JsonResponse('user dont exist !', 200, ['Content-Type' => 'application/json']);
        }

        return $this->json($customer, 200, [], ['groups' => 'customer:read']);
    }

    /**
     * add new customer
     * @Route("/api/auth/post/add/customer", name="post-new-customer" , methods={"POST"})
     */
    public function postNewCustomer(Request $request, ApiKeyRepository $verifapikey, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        //1. Verif api key exist 
        $apikey = $request->headers->get("user-api-key");
        $verifapikey = $verifapikey->findOneBy(array('api_key' => $apikey));

        if (!$verifapikey) {
            return new JsonResponse('Your api key dont match !', 400, ['Content-Type' => 'application/json']);
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
     */
    public function deleteCustomer($apikey, $useruniqueid, ApiKeyRepository $verifapikey, CustomerRepository $customer, Request $request): Response
    {
        //1. Verif api key exist 
        $apikey = $request->headers->get("user-api-key");
        $verifapikey = $verifapikey->findOneBy(array('api_key' => $apikey));

        if (!$verifapikey) {
            return new JsonResponse('Your api key dont match !', 400, ['Content-Type' => 'application/json']);
        }

        //Delect user
        $user = $verifapikey->getUser();

        $customer = $customer->findOneByUser($user, $useruniqueid);

        if ($customer) {
            $this->entityManager->remove($customer);
            $this->entityManager->flush();
            return new JsonResponse('Customer ' . $useruniqueid . ' is removed !', 200, ['Content-Type' => 'application/json']);
        }

        return new JsonResponse('You dont allowed for remove this customer !', 200, ['Content-Type' => 'application/json']);
    }
}
