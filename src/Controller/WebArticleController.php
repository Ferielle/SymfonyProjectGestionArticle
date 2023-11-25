<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function PHPSTORM_META\map;

class WebArticleController extends AbstractController
{
    
    /**
    * @Route("/create-article", name="web_create_article")
    */
    public function createArticle(Request $request): Response
    {
        $article = new Article();
    
        // Create the form using ArticleType
        $form = $this->createForm(ArticleType::class, $article);
    
        // Handle the form submission
        $form->handleRequest($request);
    
        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $uploadedFile = $form['image']->getData();
            $encodedImage = $this->encodeImageToBase64($uploadedFile);

            // Set the base64-encoded image in the article entity
            $article->setImage($encodedImage);

    
            // Add your logic for persisting to the database if needed
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
    
            // Redirect to the desired page after successful form submission
            return $this->redirectToRoute('web_article_list');
        }
    
        // Render the form template
        return $this->render('web_article/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    private function encodeImageToBase64($uploadedFile): string
    {
        // Get the binary content of the file
        $fileContent = file_get_contents($uploadedFile->getPathname());

        // Encode the binary content to base64
        $encodedImage = base64_encode($fileContent);

        return $encodedImage;
    }
    /**
     * @Route("/article-list", name="web_article_list")
     */
    public function listArticles(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAll();

        // Use array_map to modify each article's image data
        $articles = array_map(function ($article) {
            $article->setImage(stream_get_contents($article->getImage()));
            return $article; // Return the modified article
        }, $articles);
    
        return $this->render('home_page/index.html.twig', [
            'articles' => $articles,
        ]);
    }
     /**
     * @Route("/article/{id}/edit", name="web_article_edit", methods={"GET", "POST"})
     */
    public function editArticle(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['image']->getData();
            $encodedImage = $this->encodeImageToBase64($uploadedFile);
            // Set the base64-encoded image in the article entity
            $article->setImage($encodedImage);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('web_article_list');
        }

        return $this->render('web_article/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/article/{id}/delete", name="web_article_delete", methods={"POST"})
     */
    public function deleteArticle(Request $request, Article $article): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('web_article_list');
    }
}
