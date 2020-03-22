<?php

namespace App\Controller;

use App\Entity\Region;
use App\Entity\Category;
use Symfony\Component\Mime\Email;
use App\Repository\TypeRepository;
use App\Repository\AnimalRepository;
use App\Repository\ArticleRepository;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->repository = $articleRepository;

    }
    /**
     * @Route("/", name="homepage")
     */
    public function homePage( AnimalRepository $animalRepository, TypeRepository $typeRepository )
    {
        
        $animalsAlert = $animalRepository->findLastLostFindPets();
        $animalsToAdopt =  $animalRepository->findLastAdoptionPets();
        
        $articles = $this->repository->findLastArticles();

        $typesForWidget = $typeRepository-> findWidgetCategories();
     
        return $this->render('main/homepage.html.twig', [
            "animalsAlert" => $animalsAlert,
            "animalsToAdopt" => $animalsToAdopt,
            "articles" => $articles,
            "typesForWidget" => $typesForWidget
        ]);
    }

    /**
     * @Route("/blog", name="blog")
     */
    public function blogPage (TypeRepository $typeRepository)
    {
        $articles = $this->repository->findLastArticles();

        $categoriesRepository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $categoriesRepository->findAll();

        $typesForWidget = $typeRepository-> findWidgetCategories();

        return $this->render(
            "blog/blog.html.twig",
            [
                "articles" => $articles,
                "categories" => $categories,
                "typesForWidget" => $typesForWidget
            ]
        );
    }

    /**
     * @Route("/adoption", name="adoption")
     */
    public function animalAdoption(AnimalRepository $animalRepository, TypeRepository $typeRepository)
    {
 
        $animalsToAdopt =  $animalRepository->findLastAdoptionPets();

        $types =  $typeRepository->findAll();
        $typesForWidget = $typeRepository-> findWidgetCategories();

        return $this->render("adoption/adoption.html.twig", [
            'animalsToAdopt' => $animalsToAdopt,
            'types' => $types,
            "typesForWidget" => $typesForWidget
        ]);
    }

    /**
     * @Route("/map", name="map")
     */
    public function map(TypeRepository $typeRepository)
    {
        $regionRepository = $this->getDoctrine()->getRepository(Region::class);
        $regions = $regionRepository->findAll();
        
        $typesForWidget = $typeRepository-> findWidgetCategories();

        return $this->render("map/map.html.twig", [
            "regions" => $regions,
            "typesForWidget" => $typesForWidget
        ]);
    }

    /**
     * @Route("/contact_us", name="contact")
     */
    public function contactPage(TypeRepository $typeRepository, MailerInterface $mailer, FlashyNotifier $flashy)
    {
        if(isset($_POST) && isset($_POST['submit'])) {
            
            $secretKey = "6LcMPuIUAAAAADXLFEViVBBeeUEtPx2Qlh9U3Glf";
            $token = $_POST["gtoken"];
            $ip = $_SERVER["REMOTE_ADDR"];            
            $url = "https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$token."&remoteip=".$ip;
            $request = file_get_contents($url);
            $response = json_decode($request);

            if($response->success)
            {                
                $email = (new Email())
                    ->from($_POST['email'])
                    ->to('espoirsurpattes@gmail.com')
                    ->subject('Espoir sur Pattes : message de ' .$_POST['name'])
                    ->html('<p>'.$_POST['message'].'</p>');                
                $mailer->send($email);
                
                $flashy->success("Votre mail a bien été envoyé");
                return $this->redirectToRoute('contact');
                
            }
            else
            {                
                $flashy->error("Votre mail n'a pas pu être envoyé");
                return $this->redirectToRoute('homepage');
            }
        }  

        $typesForWidget = $typeRepository-> findWidgetCategories();

        return $this->render("contact/contact.html.twig",[
            "typesForWidget" => $typesForWidget,

        ]);
    }

    /**
     * @Route("/about_us", name="about")
     */
    public function aboutPage(TypeRepository $typeRepository)
    {

        $typesForWidget = $typeRepository-> findWidgetCategories();

        return $this->render("about_us/about_us.html.twig",[
            "typesForWidget" => $typesForWidget,
        ]);
    }

    /**
     * @Route("/association", name="association")
     */
    public function association (TypeRepository $typeRepository)
    {

        $typesForWidget = $typeRepository-> findWidgetCategories();

        return $this->render("association/association.html.twig",[
            "typesForWidget" => $typesForWidget,
        ]);
    }
}