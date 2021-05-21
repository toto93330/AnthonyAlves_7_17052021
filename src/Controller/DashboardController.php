<?php

namespace App\Controller;

use App\Entity\ApiKey;
use App\Form\DashboardType;
use App\Repository\ApiKeyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DashboardController extends AbstractController
{
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/dashboard/update/apikey", name="updatekey")
     */
    public function updateApiKey(): Response
    {
        function generateRandomString($length = 10)
        {
            return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
        }

        // GENERATE NEW API KEY AND FLUSH
        $newapikey = (generateRandomString() . generateRandomString());
        $user = $this->getUser();
        $ApiKey = $this->entityManager->getRepository(ApiKey::class)->findOneBy(array('User' => $user));
        $ApiKey->setApiKey($newapikey);
        $this->entityManager->persist($ApiKey);
        $this->entityManager->flush();

        // ENCODE FOR SENDING JSON
        $encoders = [new JsonEncoder()]; // If no need for XmlEncoder
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        // Serialize your object in Json
        $jsonObject = $serializer->serialize($newapikey, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        // For instance, return a Response with encoded Json
        return new Response($jsonObject, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder, ApiKeyRepository $apikey): Response
    {


        // USER MANAGER
        $user = $this->getUser();
        $form = $this->createForm(DashboardType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('passwordold')->getData();

            if ($encoder->isPasswordValid($user, $password)) {
                if (!empty($form->get('newpassword')->getData())) {

                    $newpassword = $encoder->encodePassword($user, $form->get('newpassword')->getData());
                    $user->setPassword($newpassword);
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                    $this->addFlash('notify', 'Your password is updated!');
                    return $this->redirectToRoute('dashboard');
                }
            } else {
                $this->addFlash('notify_error', 'Your Actual password is not correct!');
                return $this->redirectToRoute('dashboard');
            }
        }
        // API MANAGER


        $userapikey = $apikey->findOneBy(array('User' => $user));;
        $userapikey = $userapikey->getApiKey();

        return $this->render('dashboard/index.html.twig', [
            'user_api_key' => $userapikey,
            'form' => $form->createView(),
        ]);
    }
}
