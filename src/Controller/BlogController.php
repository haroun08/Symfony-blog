<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityManagerInterface;

use App\Form\ArticleType;
use App\Entity\Comment;
use App\Form\CommentType;




class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo): Response
    {

        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }
    /**
    * @route("/",name="home")
    */
    public function home() {
        return $this->render('blog/home.html.twig',[
            'title' => "Hello here",
            'age' => 31
        ]);
    }

/**
 * @route("/blog/new", name="blog_create")
 * @route("/blog/{id}/edit",name="blog_edit")
 */
public function form(Article $article = null, Request $request, EntityManagerInterface $manager)
{
    if (!$article) {
        $article = new Article();
    }

    $form = $this->createForm(ArticleType::class, $article);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        if (!$article->getId()) {
            $article->setCreatedAt(new \DateTimeImmutable());
        }

        $manager->persist($article);
        $manager->flush();

        return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
    }

    return $this->render('blog/create.html.twig', [
        'formArticle' => $form->createView(),
        'editMode' => $article->getId() !== null
    ]);
}


    /**
     * @route("/blog/{id}",name="blog_show")
     */
    public function show(Article $article, Request $request, EntityManagerInterface $manager){
        $comment = new Comment();

        $form = $this->createForm(CommentType::class,$comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAT(new \DateTime())
                    ->setArticle($article);
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('blog_show',['id' => $article->getId()]);
        }

        return $this->render('blog/show.html.twig',[
            'article'=>$article,
            'commentForm' => $form->createView()
        ]);
    }

}
