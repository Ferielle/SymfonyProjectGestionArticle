<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\THEME;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


use function PHPSTORM_META\map;

class WebArticleController extends AbstractController
{

/**
     * @Route("/article/{id}", name="article_details")
     */
    public function showDetails(Article $article, ArticleRepository $articleRepository): Response
    {
        
        if ($article) {
            $article->setImage(stream_get_contents($article->getImage()));
        }
        return $this->render('web_article/detail.html.twig', [
            'articleDetails' => $article,
            'theme_articles' =>$article->getThemeArticles()
        ]);
    }
    


    /**
    * @Route("/create-article", name="web_create_article")
    */
    public function createArticle(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['image']->getData();
            if ($uploadedFile) {
                $encodedImage = $this->encodeImageToBase64($uploadedFile);
                    $article->setImage($encodedImage);
            }
            $entityManager = $this->getDoctrine()->getManager();
            foreach ($form['theme_articles']->getData() as $value ){
                $theme = new THEME();
                $theme->setArticle($article);
                $theme->setLabeltheme($value->getLabeltheme());
                $theme->setSlug($value->getSlug());

                $entityManager->persist($theme);
                $entityManager->flush();
            }
            
            $entityManager->persist($article);
            $entityManager->flush();
            return $this->redirectToRoute('web_article_list');
        }
    
        // Render the form template
        return $this->render('web_article/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    private function encodeImageToBase64($uploadedFile): string
    {
        $fileContent = file_get_contents($uploadedFile->getPathname());
        $encodedImage = base64_encode($fileContent);

        return $encodedImage;
    }
    /**
     * @Route("/", name="web_article_list")
     */
    public function listArticles(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAll();
        // Use array_map to modify each article's image data
        $articles = array_map(function ($article) {
            $article->setImage(stream_get_contents($article->getImage()));
            return $article; // Return the modified article
        }, $articles);
    
        return $this->render('web_article/list.html.twig', [
            'articles' => $articles,
        ]);
    }

 /**
 * @Route("/article/{id}/edit", name="web_article_edit", methods={"GET", "POST"})
 */
public function editArticle(Request $request): Response
{
    $id = $request->get('id');
    
    // Obtenez l'article à éditer en fonction de l'ID
    $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

    

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
