<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\THEME;
use App\Entity\Article;
use Symfony\Component\Validator\Constraints\DateTime;
class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="app_article")
     */
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }
    /**
     * @Route("/api/addarticles", name="api_article_add", methods={"POST"})
     */
    public function addArticle(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $entityManager = $this->getDoctrine()->getManager();

        $article = new Article();
        $article->setTitle($data['title']);
        $article->setDescription($data['description']);
        $article->setImage($data['image']);
        $date = new \DateTime($data['date']);
        $article->setDate($date);
        foreach ($data['theme_article'] as $themeData) {
            $theme = new THEME();
            $theme->setLabeltheme($themeData['labeltheme']);
            $theme->setSlug($themeData['slug']);
            $theme->setArticle($article); // Set the association
            $entityManager->persist($theme);
        }
        $entityManager->persist($article);
        $entityManager->flush();

        return $this->json(['message' => 'Article added successfully'], 201);
    }

    /**
 * @Route("/api/articles", name="api_article_list", methods={"GET"})
 */
public function getAllArticles(): JsonResponse
{
    $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();

    $formattedArticles = [];
    foreach ($articles as $article) {
        $formattedArticles[] = [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'description' => $article->getDescription(),
            'image' => base64_encode(stream_get_contents($article->getImage())),
            'date' => $article->getDate()->format('Y-m-d'),
            'themes' => $this->getThemesForArticle($article),
        ];
    }

    return $this->json($formattedArticles);
}

/**
 * Helper method to get themes for a given article.
 */
private function getThemesForArticle(Article $article): array
{
    $themes = [];
    foreach ($article->getThemeArticle() as $theme) {
        $themes[] = [
            'labeltheme' => $theme->getLabeltheme(),
            'slug' => $theme->getSlug(),
        ];
    }

    return $themes;
}
/**
 * @Route("/api/articles/{id}", name="api_article_get", methods={"GET"})
 */
public function getArticleById(int $id): JsonResponse
{
    $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

    if (!$article) {
        return $this->json(['message' => 'Article not found'], 404);
    }

    $formattedArticle = [
        'id' => $article->getId(),
        'title' => $article->getTitle(),
        'description' => $article->getDescription(),
        'image' => base64_encode(stream_get_contents($article->getImage())),
        'date' => $article->getDate()->format('Y-m-d'),
        'themes' => $this->getThemesForArticle($article),
    ];

    return $this->json($formattedArticle);
}
/**
 * @Route("/api/articles/delete/{id}", name="api_article_delete", methods={"DELETE"})
 */
public function deleteArticleById(int $id): JsonResponse
{
    $entityManager = $this->getDoctrine()->getManager();
    $article = $entityManager->getRepository(Article::class)->find($id);

    if (!$article) {
        return $this->json(['message' => 'Article not found'], 404);
    }

    $entityManager->remove($article);
    $entityManager->flush();

    return $this->json(['message' => 'Article deleted successfully'], 200);
}
 /**
     * @Route("/api/articles/update/{id}", name="article_update", methods={"PUT"})
     */
    public function updateArticle(Request $request, $id): Response
    {
        // Assuming the request contains JSON data
        $data = json_decode($request->getContent(), true);

        // Validate or process the data as needed
        // ...

        // Retrieve the article from the database
        $entityManager = $this->getDoctrine()->getManager();
        $article = $entityManager->getRepository(Article::class)->find($id);

        // Check if the article exists
        if (!$article) {
            return new Response('Article not found', Response::HTTP_NOT_FOUND);
        }

        // Update the article properties
        $article->setTitle($data['title']);
        $article->setDescription($data['description']);
        $article->setImage($data['image']);
        $date = new \DateTime($data['date']);
        $article->setDate($date);
        foreach ($data['theme_article'] as $themeData) {
            $theme = new THEME();
            $theme->setLabeltheme($themeData['labeltheme']);
            $theme->setSlug($themeData['slug']);
            $theme->setArticle($article); // Set the association
        }



        $entityManager->flush();

        // Optionally, return a success response
        return new Response('Article updated successfully', Response::HTTP_OK);
    }
}

