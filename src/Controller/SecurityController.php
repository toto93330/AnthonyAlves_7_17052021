<?php

namespace App\Controller;

use Exception;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Provider\Google;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{

    private $entityManager;
    private $encoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/connect/google", name="connect_google_start")
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {

        $client = $clientRegistry->getClient('google');
        return $client->redirect(['https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile']);

        try {

            $user = $client->fetchUser();
            var_dump($user);
            die;
        } catch (IdentityProviderException $e) {
            var_dump($e->getMessage());
            die;
        }
    }


    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/oauth/check/google", name="verif_google_auth")
     */
    public function google(MailerInterface $mailer)
    {

        session_start(); // Remove if session.auto_start=1 in php.ini

        $provider = new Google([
            'clientId'     => '208419920199-g385p6nrsjhlriauu2i9ftp3tbl2b06g.apps.googleusercontent.com',
            'clientSecret' => 'oiEI5jXdaA31PoQRTzBMSWr2',
            'redirectUri'  => 'https://127.0.0.1:8000/oauth/check/google',
        ]);


        // Try to get an access token (using the authorization code grant)
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        // Optional: Now you have a token you can look up a users profile data
        try {

            // We got an access token, let's now get the owner details
            $ownerDetails = $provider->getResourceOwner($token);

            // verif email is verified
            if (!$ownerDetails->toArray()['email_verified']) {
                $this->addFlash('notify_error', 'Please validate your google email and retry!');
                return $this->redirectToRoute('home');
            }
            //echo $ownerDetails->getEmail();

            $emailexist = $this->entityManager->getRepository(User::class)->findBy(["email" => $ownerDetails->toArray()['email']]);

            if (!$emailexist) {
                // if email dont existe on database create user and send email whith password

                function generateRandomString($length = 10)
                {
                    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
                }

                // CREATE USER
                $password = generateRandomString();
                $user = new User();
                $user->setEmail($ownerDetails->getEmail());
                $user->setRoles(array("ROLE_USER"));
                $user->setPassword($this->encoder->encodePassword($user, $password));
                $user->setName($ownerDetails->getFirstName());
                $this->entityManager->persist($user);

                $email = (new TemplatedEmail())
                    ->from('bilmo666666@gmail.com')
                    ->to($ownerDetails->getEmail())
                    ->subject('www.bilmo.com | Thanks for signing up to bilmo API!')
                    ->text('The snow must go on!')
                    // path of the Twig template to render
                    ->htmlTemplate('email/signup.html.twig')

                    // pass variables (name => value) to the template
                    ->context([
                        'name' => $ownerDetails->getFirstName(),
                        'password' => $password,
                        'useremail' => $ownerDetails->getEmail(),
                    ]);

                // actually executes the queries (i.e. the INSERT query)
                $this->entityManager->flush();
                $mailer->send($email);

                $this->addFlash('notify', 'Thank you for registration, an email has been sent to your inbox!');
                return $this->redirectToRoute('home');
            } else {
                $this->addFlash('notify_error', 'Your are already registered, please use login form!');
                return $this->redirectToRoute('home');
            }
        } catch (Exception $e) {

            // Failed to get user details
            exit('Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
