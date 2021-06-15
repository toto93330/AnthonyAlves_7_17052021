<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\Customer;
use OpenApi\Annotations as OA;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\CustomerRepository;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ApiController extends AbstractController
{
    private $entityManager;
    private $user;
    private $serializer;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack, UserRepository $users, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;

        $this->serializer = $serializer;

        $request = $requestStack->getCurrentRequest();
        $headers = $request->headers->all();
        if (isset($headers['authorization']['0'])) {
            $token = explode(" ", $headers['authorization']['0']);
            $token = explode(".", $token[1]);
            $user = json_decode(base64_decode($token[1]));
            $this->user = $users->findBy(['email' => $user->username]);
        }
    }

    /**
     * Generate JWT Bearer Token  *IMPORTANT*
     * @Route("/api/v1/login_check", name="jwt-token", methods={"POST"})
     * 
     *      * API DOC *
     *  
     * Generate JWT Bearer Token
     *
     * @OA\Tag(name="Jwt Token")
     * 
     * @OA\Response( response=200, description="Return JWT token")
     * @OA\Response( response=400, description="Bad request.")
     * @OA\Response( response=401, description="Invalid credentials.")
     *
     * @OA\RequestBody(
     *    required=true,
     *    description="Generate JWT Bearer Token with your user login informations",
     *    @OA\JsonContent(
     *       required={"username","password"},
     *       @OA\Property(property="username", type="string", format="email", example="test@bilmo.com"),
     *       @OA\Property(property="password", type="string", example="test"),
     *    ),
     * ),
     */
    public function JWTGenToken()
    {
    }

    /**
     * Consult all bilmo product
     * @Route("/api/v1/products/{pages}", name="get-all-products", methods={"GET"})
     * 
     *      * API DOC *
     *
     * Consult all bilmo product.
     *
     * @OA\Tag(name="Products")
     *
     * @OA\Response( response=200, description="Return all product")
     * @OA\Response( response=204, description="No products for moments !")
     * @OA\Response( response=401, description="Expired JWT Token")
     */
    public function getAllProducts($pages, ProductRepository $product, Request $request)
    {
        // TAKE PRODUCT WITH PAGINATION
        $products = $product->findByMaxResult($pages);

        // OUT OF RANGE
        if (count($products) === 0) {
            return new JsonResponse(['status' => 204, 'message' => 'No more products for moments !'], 204, ['Content-Type' => 'application/json']);
        }

        $products = $this->serializer->toArray($products);

        // API auto discoverable
        $json = [];

        foreach ($products as $key => $value) {
            $json +=  [$key => ['@uri' => '/api/v1/products/' . $products[$key]['id'], '@method' => '[GET]', '@content' => $value]];
        }


        // For instance, return a Response with encoded Json
        return $this->json($json, 200, [], []);
    }

    /**
     * Consult detail bilmo product
     * @Route("/api/v1/product/{productid}", name="get-product-detail", methods={"GET"})
     * 
     *      * API DOC *
     *
     * Consult bilmo product by id.
     *
     * @OA\Tag(name="Products")
     * 
     * @OA\Response( response=200, description="Return defined product by id")
     * @OA\Response( response=404, description="Product dont exist !")
     * @OA\Response( response=401, description="Expired JWT Token")
     * 
     */
    public function getDetailProduct($productid, ProductRepository $product, Request $request): Response
    {

        $product = $product->findBy(array('id' => $productid));

        if (!$product) {
            return new JsonResponse(['status' => 404, 'message' => 'Product dont exist !'], 404, ['Content-Type' => 'application/json']);
        }

        // For instance, return a Response with encoded Json
        return $this->json($product, 200, [], []);
    }

    /**
     * Consult all customer by user
     * @Route("/api/v1/customers/{page}", name="get-all-customer-by-user", methods={"GET"})
     * 
     *      * API DOC *
     *
     * Consult all bilmo customer for defined user.
     *
     * @OA\Tag(name="Customers")
     * 
     * @OA\Response( response=200, description="Return all custom defined by user")
     * @OA\Response( response=204, description="No customers for moments in your account !")
     * @OA\Response( response=401, description="Expired JWT Token")
     */
    public function getAllCustomers($page, CustomerRepository $customer, Request $request): Response
    {

        // TAKE PRODUCT WITH PAGINATION
        $customer = $customer->findByMaxResult($this->user[0], $page);

        // OUT OF RANGE
        if (count($customer) === 0) {
            return new JsonResponse(['status' => 204, 'message' => 'No customers more for moments in your account !'], 204, ['Content-Type' => 'application/json']);
        }

        // API auto discoverable
        $json = [];
        foreach ($customer as $key => $value) {
            $json +=  [$key => ['@uri' => '/api/v1/customer/' . $customer[$key]['id'], '@method' => '[GET]', '@content' => $value]];
        }

        // For instance, return a Response with encoded Json

        return $this->json($json, 200, [], []);
    }

    /**
     * Consult customer detail by user
     * @Route("/api/v1/customer/{useruniqueid}", name="get-detail-customer-by-user", methods={"GET"})
     * 
     *      * API DOC *
     *
     * Consult bilmo customer by id for defined user.
     *
     * @OA\Tag(name="Customers")
     * 
     * @OA\Response( response=200, description="Return defined customer defined by id")
     * @OA\Response( response=404, description="User dont exist or your are not allowed for take information !")
     * @OA\Response( response=401, description="Expired JWT Token")
     */
    public function getDetailCustomer($useruniqueid, CustomerRepository $customerRepository, Request $request): Response
    {

        $customer = $customerRepository->findOneByUser($this->user[0], $useruniqueid);

        if (count($customer) === 0) {
            return new JsonResponse(['status' => 401, 'message' => 'User dont exist or your are not allowed for take information !'], 200, ['Content-Type' => 'application/json']);
        }

        $json = [];

        foreach ($customer as $key => $value) {
            $json +=  [$key => ['@uri' => '/api/customer/' . $customer[$key]['id'], '@method' => '[GET]', '@content' => $value]];
        }

        return $this->json($json, 200, [], ['groups' => 'customer:read']);
    }

    /**
     * add new customer
     * @Route("/api/v1/customer", name="post-new-customer" , methods={"POST"})
     * 
     *      * API DOC *
     *
     * Add new bilmo customer for defined user.
     *
     * @OA\Tag(name="Customers")
     * 
     * @OA\Response( response=200, description="Customer is created !")
     * @OA\Response( response=400, description="Bad request !")
     * @OA\Response( response=401, description="Expired JWT Token")
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
     *       @OA\Property(property="zipcode", type="integer", example=93330),
     *       @OA\Property(property="country", type="string", example="France"),
     *    ),
     * ),
     */
    public function postNewCustomer(Request $request, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {

        try {
            $data = $request->getContent();

            // INSTENTIATE CUSTOMER OBJET AND VERIF ERROR
            $newcustomer = $serializer->deserialize($data, Customer::class, 'json');

            // VERIF EMAIL
            $emailConstraint = new Assert\Email();
            $emailConstraint->message = 'Invalid email address';

            $email = $newcustomer->getEmail();
            $error = $validator->validate($email, $emailConstraint);

            if (count($error) > 0) {
                return $this->json($error[0]->getMessage(), 400, ['Content-Type' => 'application/json']);
            }

            //VERIF ERROR
            $newcustomer->setUser($this->user[0]);
            $error = $validator->validate($newcustomer);

            if (count($error) > 0) {
                return $this->json($error[0]->getMessage(), 400, ['Content-Type' => 'application/json']);
            }

            // IF CUSTOMER IS ALLREADY REGISTERED
            $customer = json_decode($data);
            $customerexist = $this->entityManager->getRepository(Customer::class)->findBy(["email" => $customer->email, "user" => $this->user[0]]);;
            if (!empty($customerexist)) {
                return new JsonResponse(['status' => 401, 'message' => 'This customer is allready registered !'], 401, ['Content-Type' => 'application/json']);
            }

            // IF ALL INFORMATION IS OK PERSIST CUSTOMER
            $this->entityManager->persist($newcustomer);
            $this->entityManager->flush();

            return new JsonResponse(['status' => 201, 'message' => 'Customer is created !'], 201, ['Content-Type' => 'application/json']);
        } catch (NotEncodableValueException $e) {
            return new JsonResponse(['status' => 400, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * remove customer
     * @Route("/api/v1/customer/{useruniqueid}", name="remove-customer", methods={"DELETE"})
     * 
     *      * API DOC *
     *
     * Remove bilmo customer by id for defined user.
     * 
     * @OA\Tag(name="Customers")
     * 
     * @OA\Response( response=204, description="")
     * @OA\Response( response=418, description="You dont allowed for remove this customer !!")
     * @OA\Response( response=401, description="Expired JWT Token")
     *
     */
    public function deleteCustomer($useruniqueid, CustomerRepository $customerRepository): Response
    {
        $customer = $this->entityManager->getRepository(Customer::class)->findOneBy(["user" => $this->user[0], "id" => $useruniqueid]);

        if ($customer) {
            $this->entityManager->remove($customer);
            $this->entityManager->flush();
            return new JsonResponse([], 204, ['Content-Type' => 'application/json']);
        }

        return new JsonResponse(['status' => 418, 'message' => 'You dont allowed for remove this customer !'], 418, ['Content-Type' => 'application/json']);
    }



    /**
     * Modifier customer
     * @Route("/api/v1/customer/{useruniqueid}", name="update-customer", methods={"PATCH"})
     * 
     *      * API DOC *
     *
     * Update bilmo customer by id for defined user.
     * 
     * @OA\Tag(name="Customers")
     * 
     * @OA\Response( response=200, description="Customer is updated !!")
     * @OA\Response( response=418, description="You dont allowed for update this customer !!")
     * @OA\Response( response=401, description="Expired JWT Token")
     * 
     * @OA\RequestBody(
     *    required=true,
     *    description="modifier customer on your account",
     *    @OA\JsonContent(
     *       required={"email","firstname","lastname","phone","adress","zip_code","city","country"},
     *       @OA\Property(property="email", type="string", format="email", example="contact@bilmo.com"),
     *       @OA\Property(property="firstname", type="string", example="John"),
     *       @OA\Property(property="lastname", type="string", example="Doe"),
     *       @OA\Property(property="phone", type="string", example="+33675901691"),
     *       @OA\Property(property="adress", type="string", example="8, sentier de la carriere"),
     *       @OA\Property(property="city", type="string", example="neuilly sur marne"),
     *       @OA\Property(property="zipcode", type="integer", example=93330),
     *       @OA\Property(property="country", type="string", example="France"),
     *    ),
     * ),
     *
     */
    public function updateCustomer($useruniqueid, CustomerRepository $customer, Request $request, SerializerInterface $serializer): Response
    {


        $customer = $customer->findOneBy(["user" => $this->user[0], "id" => $useruniqueid]);



        // IF CUSTOMER DONT EXIST OR USER USER NOT ALLOWED FOR UPDATE CUSTOMER
        if (empty($customer)) {
            return new JsonResponse(['status' => 418, 'message' => 'You dont allowed for update this customer !'], 418, ['Content-Type' => 'application/json']);
        }

        // IF ITS OK
        if (!empty($customer)) {

            $data = json_decode($request->getContent());

            $customer->setEmail($data->email);
            $customer->setFirstname($data->firstname);
            $customer->setLastname($data->lastname);
            $customer->setPhone($data->phone);
            $customer->setAdress($data->adress);
            $customer->setCity($data->city);
            $customer->setZipCode($data->zipcode);
            $customer->setCountry($data->country);

            $this->entityManager->persist($customer);
            $this->entityManager->flush();

            return new JsonResponse(['status' => 200, 'message' => 'Customer ' . $useruniqueid . ' is updated !'], 200, ['Content-Type' => 'application/json']);
        }
    }
}
