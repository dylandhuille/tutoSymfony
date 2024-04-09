<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
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
}
