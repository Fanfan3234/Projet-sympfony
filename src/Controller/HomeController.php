<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $categories = $this->repoCategory->findAll();
        $articles = $this->repoArticle->findAll();

        $articlesPag = $paginator->paginate(
            $articles, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6// Nombre de résultats par page
        );

        return $this->render('home/index.html.twig', [
            "articles" => $articlesPag,
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
    public function showArticles(?Category $category, Request $request, PaginatorInterface $paginator): Response
    {
        if ($category) {
            $articles = $category->getArticles()->getValues();
            $articlesPag = $paginator->paginate(
                $articles, // Requête contenant les données à paginer (ici nos articles)
                $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                6// Nombre de résultats par page
            );
        } else {
            return $this->redirectToRoute("home");
        }
        return $this->render('home/index.html.twig', [
            "articles" => $articlesPag,
            "categories" => $this->repoCategory->findAll(),
        ]);
    }

    /**
     * @Route("/recherche", name="recherche")
     */
    public function recherche(Request $request, PaginatorInterface $paginator): Response
    {
        $date = \DateTime::createFromFormat("Y-m-d", date($request->request->get('date')));
        //dd($date);

        $session = new Session();
        if (!$session->get('articles')) {
            $session->start();
        }

        if ($request->request->get('title')) {
            $articles = $this->repoArticle->findByTitleLike($request->request->get('title'), $date);
            $session->set('articles', $articles);
        }

        $articlesPag = $paginator->paginate(
            $session->get('articles'), // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6// Nombre de résultats par page
        );

        return $this->render('home/index.html.twig', [
            "articles" => $articlesPag,
            "categories" => $this->repoCategory->findAll(),
        ]);
    }
}
