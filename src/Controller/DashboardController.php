<?php

namespace App\Controller;

use App\Form\DashboardType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
     * @Route("/dashboard", name="dashboard")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
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

        return $this->render('dashboard/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
