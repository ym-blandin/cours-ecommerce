<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function renderMenuList()
    {
        $categories = $this->categoryRepository->findAll();

        return $this->render('category/_menu.html.twig', [
            'categories' => $categories
        ]);
    }


    #[Route('/admin/category/create', name: 'category_create')]
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        // On crée une catégorie "vide" afin de la passer au "createForm"
        $category = new Category;

        $form = $this->createForm(CategoryType::class, $category);

        // On écoute si le formulaire est soumis
        $form->handleRequest($request);

        // S'il est soumis on enregistre la catégorie dans la base de données et on redigire vers la page d'accueil
        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($slugger->slug($category->getName())));

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        // On ne récupère que la partie permettant l'affichage du formulaire
        $formView = $form->createView();

        return $this->render('category/create.html.twig', [
            'formView' => $formView
        ]);
    }


    #[Route('/admin/category/{id}/edit', name: 'category_edit')]
    public function edit($id, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $category = $categoryRepository->find($id);

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($slugger->slug($category->getName())));

            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        $formView = $form->createView();

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'formView' => $formView
        ]);
    }
}
