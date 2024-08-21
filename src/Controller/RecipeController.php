<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{

    #[Route('/recettes', name: 'recipe.index')]
    public function index(Request $request, RecipeRepository $repository, EntityManagerInterface $em): Response
    {
        $recipes = $repository->findWithDurationLowerThan(20);
        $totalTiemsRecipes = $repository->findTotalDuration();
        $totalNumberRecipes = $repository->findTotalRecipes();


        // cree une nouvelle recette
        // $recipe = new Recipe();
        // $recipe->setTitle('Barbe a papa')
        //        ->setSlug('barbe-a-papa')
        //        ->setContent('Mettez du sucre')
        //        ->setDuration(2)
        //        ->setCreatedAt(new DateTimeImmutable())
        //        ->setUpdateAt(new DateTimeImmutable());
        // $em->persist($recipe);
        // $em->flush();

        // supprimer une recette
        //$en->remove($recipes[0]);
        //$em->flush();

        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
            'totalTiemsRecipes' => $totalTiemsRecipes,
            'totalNumberRecipes' => $totalNumberRecipes
        ]);
    }

    #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    public function show(Request $request, string $slug, int $id, RecipeRepository $repository): Response
    {
        $recipe = $repository->find($id);
        if ($recipe->getSlug() != $slug) {
            return $this->redirectToRoute('recipe.show', ['slug' => $recipe->getSlug(), 'id' => $recipe->getId()]);
        }
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe
        ]);
    }
    // methods permet de "securiser" en acceptant que les mathode post et get pour cette page
    #[Route('/recettes/{id}/edit', name: 'recipe.edit', methods: ['GET','POST'])]
    // Recipe $recipe va chercher le recette don l'id est passer dans l'url évite de faire $recipe = $repository->find($id);
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em) {
        // ici on créé le formulaire a l'aide de la class de la recette RecipeType avec les données de la base de donnée $recipe
        $form = $this->createForm(RecipeType::class, $recipe);
        // éffectue un setTitle etSlug ... avec les nouveaux champs
        $form->handleRequest($request);
        // on verifi si le formulaire est soumit et valide
        if($form->isSubmitted() && $form->isValid()){
            // $recipe->setUpdateAt(new \DateTimeImmutable());
            //on envoie les modif en base de donnée
            $em->flush();
            $this->addFlash('success', 'La recette a bien été modifiée');
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('recipe/edit.html.twig', [
            'recipe' =>$recipe,
            'form' =>$form
        ]);
    }

    #[Route('/recettes/create', name: 'recipe.create')]
    public function create(Request $request, EntityManagerInterface $em) {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        //
        // $recipe->setCreatedAt(new \DateTimeImmutable());
        // $recipe->setUpdateAt(new \DateTimeImmutable());
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'La recette a bien été créée');
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('recipe/create.html.twig', [
            'form' => $form
        ]);
    }
    // methods permet de "securiser" en acceptant que la methode delete pour cette page
    #[Route('/recettes/{id}', name: 'recipe.delete', methods: ['DELETE'])]
    public function remove(Recipe $recipe, EntityManagerInterface $em){
        $em->remove ($recipe);
        $em->flush();
        $this->addFlash('success', 'La recette a bien été supprimée');
        return $this->redirectToRoute('recipe.index');
    }
}