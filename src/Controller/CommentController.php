<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Component\Mime\Email;
use App\Repository\TypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{

    //Create and Read comments are include into Article Controller

    /**
     * @Route ("/update/{id}",name ="update_comment")
     * @IsGranted("ROLE_USER")
     */
    public function updateComment(Request $request, $id, Comment $comment, TypeRepository $typeRepository, FlashyNotifier $flashy)
    {
        $typesForWidget = $typeRepository->findWidgetCategories();

        // Voters to deny update access in the URL 
        $this->denyAccessUnlessGranted('delete', $comment);

        $article = $comment->getArticle();
        $slug = $article->getSlug();
        $comments = $article->getComments();

        $formComment = $this->createForm(CommentType::class, $comment);
        $formComment->handleRequest($request);

        if ($formComment->isSubmitted() && $formComment->isValid()) {

            $manager = $this->getDoctrine()->getManager();

            $manager->flush();

            $flashy->success("Le commentaire a bien été modifié");

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
     * @Route ("/delete/{id}",name ="delete_comment")
     * @IsGranted("ROLE_USER")
     */
    public function deleteComment($id, Comment $comment, FlashyNotifier $flashy)
    {
        // Voters to deny delete access in the URL 
        $this->denyAccessUnlessGranted('delete', $comment);

        $slug = $comment->getArticle()->getSlug();

        $this->getDoctrine()->getManager()->remove($comment);
        $this->getDoctrine()->getManager()->flush();

        $flashy->success("Le commentaire a bien été supprimé");

        return $this->redirectToRoute('article_view', ['slug' => $slug]);
    }

    /**
     * @Route ("/signal/{id}",name ="signal_comment")
     * @IsGranted("ROLE_USER")
     */
    public function signalComment($id, Comment $comment,MailerInterface $mailer, FlashyNotifier $flashy)
    {
            $slug = $comment->getArticle()->getSlug();

            $commentContent= $comment->getContent();
            $userWhoComment =$comment->getUser()->getUserName();
            $user = $this->getUser();

            $email = (new Email())
                    ->from('espoirsurpattes@gmail.com')
                    ->to('espoirsurpattes@gmail.com')
                    ->subject('Important Espoir sur Pattes Commentaire signalé')
                    ->html('<p>' . $commentContent . '</p>
                            <p>Commentaire écrit par : ' . $userWhoComment .  '</p>
                            <p> Signalé par  : ' . $user . '</p>'
                            );

            $mailer->send($email);

            $flashy->success("Le commentaire a bien été signalé");
            return $this->redirectToRoute('article_view', ['slug' => $slug]);
        
    }
}
