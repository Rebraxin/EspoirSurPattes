<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Form\UserType;
use App\Form\NewPasswordType;
use App\Form\ResetPasswordType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


class SecurityController extends AbstractController
{

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, FlashyNotifier $flashy): Response
    {
        if ($this->getUser())
        {
            
            $flashy->success("Connexion réussie");
            return $this->redirectToRoute('homepage');

        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);    
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/nouveau-mdp", name="app_forgotten_password")
     */
    public function forgottenPassword(Request $request, UserRepository $userRepo, MailerInterface $mailer, FlashyNotifier $flashy, TokenGeneratorInterface $tokenGenerator)
    {
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $datas = $form->getData();

            $user = $userRepo->findOneByEmail($datas['email']);
            if(!$user)
            {
                $flashy->error("Cette adresse n'existe pas");
                return $this->redirectToRoute('app_login');
            }

            $token = $tokenGenerator->generateToken();

            try
            {
                $user->setResetToken($token);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($user);
                $manager->flush();
            } catch(\Exception $e)
            {
                $flashy->error("Une erreure est survenue : ".$e->getMessage());
                return $this->redirectToRoute('app_login');
            }

            // Generate URL password
            $url = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
            $email = (new Email())
                    ->from('espoirsurpattes@gmail.com')
                    ->to($user->getEmail())
                    ->subject('EspoirSurPattes - Activation de votre compte')
                    ->html('<p>Bonjour,</p><p>Demande de réinitialisation de mot de passe</p><p>Cliquez sur le lien : '.$url.'</p>', 'text/html'
                    );
                    $mailer->send($email);

            $flashy->success("Un email de réinitialisation a été envoyé");
            return $this->redirectToRoute('app_login');

        }

        return $this->render('security/forgotten_password.html.twig', [
            'emailForm' => $form->createView()
        ]);
    }

    /**
     *@Route("/reset-password/{token}", name="app_reset_password") 
     */
    public function resetPassword(Request $request, UserPasswordEncoderInterface $encoder, FlashyNotifier $flashy, UserRepository $userRepo, $token)
    {
        $user = $userRepo->findOneBy(['resetToken' => $token]);
        $form = $this->createForm(NewPasswordType::class, $user);
        $form->handleRequest($request);
        
        

        if ($form->isSubmitted() && $form->isValid())
        {
            if(!$user)
            {
                $flashy->error("Identification non reconnu");
                return $this->redirectToRoute('app_login');
            }            

            if($request->isMethod('POST'))
            {  
                
                $user->setResetToken(null);
                $plainPassword = $form->get('plain_password')->getData();
                $encodedPassword = $encoder->encodePassword($user, $plainPassword);
                $user->setPassword($encodedPassword);

                
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($user);
                $manager->flush();

                $flashy->success("Mot de passe modifié avec succès");
                return $this->redirectToRoute('app_login');
            }
            else
            {
                return $this->render('security/reset_password.html.twig', ['token' => $token]);
            }
        }

        return $this->render(
            "security/reset_password.html.twig", [
                "newPasswordForm" => $form->createView()
            ]
        );        
    }


    /**
     * @Route ("/create_account", name="create_account")
     */
    public function createUser(Request $request, UserPasswordEncoderInterface $encoder, FlashyNotifier $flashy, MailerInterface $mailer)
    {
        $newUser = new User();
        $form = $this->createForm(UserType::class, $newUser);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid())
        {
            $secretKey = "6LcMPuIUAAAAADXLFEViVBBeeUEtPx2Qlh9U3Glf";
            $token = $_POST["gtoken"];
            $ip = $_SERVER["REMOTE_ADDR"];            
            $url = "https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$token."&remoteip=".$ip;
            $request = file_get_contents($url);
            $response = json_decode($request);

            $plainPassword = $form->get('plain_password')->getData();
            $encodedPassword = $encoder->encodePassword($newUser, $plainPassword);

            $newUser->setPassword($encodedPassword);
            $newUser->setCreatedAt(new DateTime());

            $newUser->setActivationToken(md5(uniqid()));

            if($response->success)
            {
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($newUser);
                $manager->flush();

                $email = (new Email())
                        ->from('espoirsurpattes@gmail.com')
                        ->to($newUser->getEmail())
                        ->subject('EspoirSurPattes - Activation de votre compte')
                        ->html($this->renderView(
                            'emails/activation.html.twig', [
                                'token' => $newUser->getActivationToken()
                            ]),'text/html'
                        );
                        $mailer->send($email);

                $flashy->success("Compte créé avec succès. Pensez a valider votre email");
                return $this->redirectToRoute('app_login');
            }
            else
            {
                $flashy->error("La création du compte a échoué");
                return $this->redirectToRoute('homepage');
            }

            
        }

        return $this->render(
            "user/add.html.twig", [
                "formAddUser" => $form->createView()
            ]
        );
    }

    /**
     * @Route("/activation/{token}", name="activation")
     */
    public function activation(UserRepository $userRepo, FlashyNotifier $flashy, $token)
    {
        $user = $userRepo->findOneBy(['activationToken' => $token]);

        if(!$user)
        {
            $flashy->error("Cet utilisateur ,'existe pas");
        }

        $user->setActivationToken(null);
        $user->setRoles(['ROLE_USER']);
        $manager =$this->getDoctrine()->getManager();
        $manager->persist($user);
        $manager->flush();

        $flashy->success("Votre compte a bien été activé");
        return $this->redirectToRoute('homepage');
    }
}
