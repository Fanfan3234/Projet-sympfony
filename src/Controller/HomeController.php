<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $repoArticle;
    private $repoCategory;

    public function __construct(ArticleRepository $repoArticle, CategoryRepository $repoCategory)
    {
        $this->repoArticle = $repoArticle;
        $this->repoCategory = $repoCategory;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(CategoryRepository $repoCategory): Response
    {
        $categories = $this->repoCategory->findAll();
        $articles = $this->repoArticle->findAll();
        return $this->render('home/index.html.twig', [
            "articles" => $articles,
            "categories" => $categories,
        ]);
    }

    /**
     * @Route("/show/{id}", name="show")
     */
    public function show(Article $article): Response
    {
        if (!$article) {
            $this->redirectToRoute("home");
        }
        return $this->render('home/show.html.twig', [
            "article" => $article,
        ]);
    }
    /**
     * @Route("/showArticles/{id}", name="show_articles")
     */
    public function showArticles(?Category $category): Response
    {
        if ($category) {
            $articles = $category->getArticles()->getValues();
        } else {
            return $this->redirectToRoute("home");
        }
        
        return $this->render('home/index.html.twig', [
            "articles" => $articles,
            "categories" => $this->repoCategory->findAll()
        ]);
    }
}
