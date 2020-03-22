<?php

namespace App\Controller;

use DateTime;
use App\Entity\Animal;
use App\Entity\Region;
use App\Entity\Department;
use App\Form\AnimalFormType;
use App\Services\ImageUploader;
use App\Repository\TypeRepository;
use App\Repository\AnimalRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/animal")
 */
class AnimalController extends AbstractController
{
    public function __construct(AnimalRepository $animalRepository)
    {
        $this->repository = $animalRepository;

    }
    /**
     * @Route("/list/{id}/type/", name="animal_list_by_type")
     */
    public function animalListByType($id, PaginatorInterface $paginator, Request $request, TypeRepository $typeRepository)
    {

        $typesForWidget = $typeRepository-> findWidgetCategories();

        $type = $typeRepository->find($id);
        $typeId = $type->getId();

        $animals =$paginator->paginate(
            $this->repository->findAnimalsByType($typeId),
            $request->query->getInt('page',1),
            12
        );
        
        return $this->render(
            "adoption/animal_list_by_type.html.twig",
            [
                "animals" => $animals,
                "type" => $type,
                "typesForWidget" => $typesForWidget
            ]
        );
    }

    /**
     * @Route("/{id}/view", name="animal_view")
     */
    public function animalById(Animal $animal, TypeRepository $typeRepository)
    {
        $typesForWidget = $typeRepository-> findWidgetCategories();

        return $this->render("animal/animal_view.html.twig", [
            "animal" => $animal,
            "typesForWidget" => $typesForWidget
        ]);
    }

    /**
     * @Route("/create", name="animal_create")
     * @IsGranted("ROLE_USER")
     */
    public function CreateAnimal(Request $request, ImageUploader $imageUploader, FlashyNotifier $flashy)
    {
        $animal = new Animal();

        $formAnimal = $this->createForm(AnimalFormType::class, $animal);
        $formAnimal->handleRequest($request);


        if ($formAnimal->isSubmitted() && $formAnimal->isValid()) {
            
              //Captcha 
            $secretKey = "6LcMPuIUAAAAADXLFEViVBBeeUEtPx2Qlh9U3Glf";
            $token = $_POST["gtoken"];
            $ip = $_SERVER["REMOTE_ADDR"];            
            $url = "https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$token."&remoteip=".$ip;
            $request = file_get_contents($url);
            $response = json_decode($request);
            
            
            $manager = $this->getDoctrine()->getManager();
            $file = $formAnimal['image']->getData();

            $file = $formAnimal['image']->getData();
            
            //Animal Picture :
            if (!empty($file)) {

                $fileName = $imageUploader->moveAndRename($file);
                $animal->setImage($fileName);
                
            } else {
                $animal->setImage("help.png");
            }

            //Set the date automatically
            $animal->setCreatedAt(new DateTime());

            //Set the User:
            $user = $this->getUser();
            $animal->setUser($user);

            //Test if Captcha is OK
            if($response->success) {

                $manager->persist($animal);
                $manager->flush();

               $animalId = $animal->getId();


                $flashy->success("L'animal a bien été ajouté");
                return $this->redirectToRoute('animal_view', ['id' => $animalId]);
            }
            else {
            
                $flashy->error("Votre animal n'a pas pu être publié");
                return $this->redirectToRoute('animal_create');
            }

        }
        
        return $this->render('animal/layout/_form_animal.html.twig', [
            'formAnimal' => $formAnimal->createView(),
        ]);
    }

    /**
     * @Route("/department/{id}/list", name="pet_list_by_department")
     */
    public function listByDepartement($id, Department $department, PaginatorInterface $paginator, Request $request, TypeRepository $typeRepository)
    {
        $typesForWidget = $typeRepository-> findWidgetCategories();

        $animalsByDepartment = $paginator->paginate(
            $this->repository->findPetsByDepartment($id),
            $request->query->getInt('page', 1),
            12
        );
        return $this->render("map/animal_by_department.html.twig", [
            "animalsByDepartment" => $animalsByDepartment,
            "department" => $department,
            "typesForWidget" => $typesForWidget
        ]);
    }

    /**
     * @Route("/region/{id}/list", name="pet_list_by_region")
     */
    public function listByRegion($id, Region $region, PaginatorInterface $paginator, Request $request, TypeRepository $typeRepository)
    {
        $typesForWidget = $typeRepository-> findWidgetCategories();

        $animalsByRegion = $paginator->paginate(
            $this->repository->findPetsByRegion($id),
            $request->query->getInt('page', 1),
            12
        );

        return $this->render("map/animal_by_region.html.twig", [
            "animalsByRegion" => $animalsByRegion,
            "region" => $region,
            "typesForWidget" => $typesForWidget

        ]);
    }

    /**
     * @Route ("/updade/{id}",name ="update_animal")
     * @IsGranted("ROLE_USER")
     */
    public function updateAnimal(ImageUploader $imageUploader,Request $request, Animal $animal, FlashyNotifier $flashy)
    {

        $this->denyAccessUnlessGranted('update', $animal);

        $animalId = $animal->getId();

        $formAnimal = $this->createForm(AnimalFormType::class, $animal);
        $formAnimal->handleRequest($request);

        if ( $formAnimal->isSubmitted() &&  $formAnimal->isValid()) {

            $file = $formAnimal['image']->getData();
            
            //Animal Picture :
            if (!empty($file)) {

                $fileName = $imageUploader->moveAndRename($file);
                $animal->setImage($fileName);
                
            } else {
                $animal->setImage("help.png");
            }

            $manager = $this->getDoctrine()->getManager();
            $manager->flush();

            $flashy->success("L'animal a bien été modifié");

            return $this->redirectToRoute('animal_view', ['id' => $animalId]);
        }

        return $this->render('animal/layout/_form_animal.html.twig', 
        [
            'formAnimal' => $formAnimal->createView(),
        ]);
    }

    /**
     * @Route ("/delete/{id}",name ="delete_animal")
     * @IsGranted("ROLE_USER")
     */
    public function deleteArticle(Animal $animal, FlashyNotifier $flashy)
    {
        $this->denyAccessUnlessGranted('delete', $animal);

        $this->getDoctrine()->getManager()->remove($animal);
        $this->getDoctrine()->getManager()->flush();

        $flashy->success("L'annonce de l'animal a bien été supprimée");

        return $this->redirectToRoute('map');
    }
}
