<?php

namespace App\Controller;

use DateTime;
use App\Entity\Media;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Services\Slugger;
use App\Services\ImageUploader;
use Symfony\Component\Mime\Email;
use App\Repository\TypeRepository;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->repository = $articleRepository;
    }

    /**
     * @Route("/list", name="article_list")
     */
    public function articleList(PaginatorInterface $paginator, Request $request, TypeRepository $typeRepository)
    {

        $typesForWidget = $typeRepository->findWidgetCategories();

        $articles = $paginator->paginate(
            $this->repository->findAllArticles(),
            $request->query->getInt('page', 1),
            6
        );

        return $this->render("blog/all_articles.html.twig", [
            "articles" => $articles,
            "typesForWidget" => $typesForWidget
        ]);
    }

    /**
     * @Route("/list/{id}/category/", name="article_list_by_category")
     */
    public function articleListByCategory(PaginatorInterface $paginator, Request $request, $id, TypeRepository $typeRepository)
    {
        $categoriesRepository = $this->getDoctrine()->getRepository(Category::class);
        $category = $categoriesRepository->find($id);
        $categoryId = $category->getId();

        $typesForWidget = $typeRepository->findWidgetCategories();


        $articles = $paginator->paginate(
            $this->repository->findArticlesByCategory($categoryId),
            $request->query->getInt('page', 1),
            6
        );

        return $this->render("blog/article_list_by_category.html.twig", [
            "articles" => $articles,
            "category" => $category,
            "typesForWidget" => $typesForWidget
        ]);
    }

    //Create and read comments are include into Article view 
    /**
     * @Route("/{slug}/view", name="article_view")
     */
    public function articleByslug(Article $article, Request $request, TypeRepository $typeRepository)
    {
        $typesForWidget = $typeRepository->findWidgetCategories();

        $slug = $article->getSlug();
        $comments = $article->getComments();

        $newComment = new Comment();
        $newComment->setArticle($article);

        $formComment = $this->createForm(CommentType::class, $newComment);
        $formComment->handleRequest($request);

        if ($formComment->isSubmitted() && $formComment->isValid()) {

            $manager = $this->getDoctrine()->getManager();

            //Set the date 
            $newComment->setCreatedAt(new DateTime());

            //Set the User:
            $user = $this->getUser();
            $newComment->setUser($user);

            $manager->persist($newComment);
            $manager->flush();

            $this->addFlash("success", "Le commentaire a bien été ajouté");

            return $this->redirectToRoute('article_view', ['slug' => $slug]);
        }

        return $this->render(
            "article/article.html.twig",
            [
                "article" => $article,
                "comments" => $comments,
                "formComment" => $formComment->createView(),
                "typesForWidget" => $typesForWidget
            ]
        );
    }

    /**
     * @Route("/create", name="create_article")
     * @IsGranted("ROLE_USER")
     */
    public function createArticle(Request $request, Slugger $slugger, ImageUploader $imageUploader, FlashyNotifier $flashy)
    {
        $article = new Article();

        

        $formArticle = $this->createForm(ArticleType::class, $article);
        $formArticle->handleRequest($request);
      
        if ($formArticle->isSubmitted() && $formArticle->isValid()) {

            //Captcha 
            $secretKey = "6LcMPuIUAAAAADXLFEViVBBeeUEtPx2Qlh9U3Glf";
            $token = $_POST["gtoken"];
            $ip = $_SERVER["REMOTE_ADDR"];            
            $url = "https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$token."&remoteip=".$ip;
            $request = file_get_contents($url);
            $response = json_decode($request);

            
            $manager = $this->getDoctrine()->getManager();
            
            $file = $formArticle['media']->getData();
            
            // Picture from the article 
            if (!empty($file)) {
                
                $fileName = $imageUploader->moveAndRename($file);
                $media = new Media();
                $media->setImgLink($fileName);
                $article->setMedia($media);
            } else {
                $media = new Media();
                $media->setImgLink("article.jpg");
                $article->setMedia($media);
            }
            
            
            //Slugify the Title for the URL
            $slug = $slugger->slugify($article->getTitle());
            $article->setSlug($slug);
            
            //Set the date automatically
            $article->setCreatedAt(new DateTime());
            
            //Set the User:
            $user = $this->getUser();
            $article->setUser($user);
            
            //Test if Captcha is OK
            if($response->success) {
            
                $manager->persist($media);
                $manager->persist($article);
                $manager->flush();

                $flashy->success("L'article a bien été ajouté");
                return $this->redirectToRoute('article_view', ['slug' => $slug]);
            }

            else {
            
            $flashy->error("Votre article n'a pas pu être publié");
            return $this->redirectToRoute('blog');
            }
 
        }
        
        return $this->render(
            "article/layout/_form_article.html.twig",[
                'formArticle' => $formArticle->createView(),
            ]
        );
    }

    /**
     * @Route ("/updade/{id}",name ="update_article")
     * @IsGranted("ROLE_USER")
     */
    public function updateArticle(ImageUploader $imageUploader,Request $request, $id, Article $article, FlashyNotifier $flashy)
    {
        $slug = $article->getSlug();

        $this->denyAccessUnlessGranted('update', $article);

        $formArticle = $this->createForm(ArticleType::class, $article);
        $formArticle->handleRequest($request);

        if ($formArticle->isSubmitted() && $formArticle->isValid()) {

            $file = $formArticle['media']->getData();
            
            // Picture from the article 
            if (!empty($file)) {
                
                $fileName = $imageUploader->moveAndRename($file);
                $media = new Media();
                $media->setImgLink($fileName);
                $article->setMedia($media);
            } else {
                $media = new Media();
                $media->setImgLink("article.jpg");
                $article->setMedia($media);
            }

            $manager = $this->getDoctrine()->getManager();

            $manager->flush();

            $flashy->success("L'article a bien été modifié");

            return $this->redirectToRoute('article_view', ['slug' => $slug]);
        }

        return $this->render(
            "article/layout/_form_article.html.twig",
            [
                'formArticle' => $formArticle->createView(),
            ]
);
    }

    /**
     * @Route ("/delete/{id}",name ="delete_article")
     * @IsGranted("ROLE_USER")
     */
    public function deleteArticle(Article $article, FlashyNotifier $flashy)
    {
        $this->denyAccessUnlessGranted('delete', $article);

        $this->getDoctrine()->getManager()->remove($article);
        $this->getDoctrine()->getManager()->flush();

        $flashy->success("L'article a bien été supprimé");

        return $this->redirectToRoute('blog');
    }

    /**
     * @Route ("/signal/{id}",name ="signal_article")
     * @IsGranted("ROLE_USER")
     */
    public function signalArticle( Article $article, MailerInterface $mailer, FlashyNotifier $flashy)
    {
        $slug = $article->getSlug();
       
        $articleContent = $article->getContent();
        $userWhoWroteArticle = $article->getUser()->getUserName();
        $user = $this->getUser();

        $email = (new Email())
            ->from('espoirsurpattes@gmail.com')
            ->to('espoirsurpattes@gmail.com')
            ->subject('Important Espoir sur Pattes Article signalé')
            ->html(
                '<h2>Contenu de l\'article signalé :</h2><p>' . $articleContent . '</p>
                <p>Article écrit par : ' . $userWhoWroteArticle .  '</p>
                <p> Signalé par  : ' . $user . '</p>'

            );

        $mailer->send($email);

        $flashy->success("L'article a bien été signalé");
        return $this->redirectToRoute('article_view', ['slug' => $slug]);
    }
}
