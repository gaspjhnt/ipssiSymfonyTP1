<?php

namespace App\Controller;

use App\Entity\Modele;
use App\Form\ModeleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/modele')]
class ModeleController extends AbstractController
{

    #[Route('/', name: 'app_modele')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {

        $modele = new Modele();
        $form = $this->createForm(ModeleType::class, $modele);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $em->persist($modele);
            $em->flush();

            $this->addFlash('success', 'Modèle ajouté');
        }

        $modeles = $em->getRepository(Modele::class)->findAll();
        return $this->render('modele/index.html.twig', [
            'modeles' => $modeles,
            'ajout' => $form->createView()
        ]);
    }


    #[Route('/{id}', name: 'app_modele_show')]
    public function category(Modele $modele = null) : Response
    {

        if ($modele == null){
            $this->addFlash('danger', 'Modèle introuvable');
            return $this->redirectToRoute('app_modele');
        }

        return $this->render('modele/show.html.twig', [
            'modele' => $modele,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_modele_edit')]
    public function editCat(EntityManagerInterface $em, Modele $modele, Request $request)
    {
        $form = $this->createForm(ModeleType::class, $modele);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $em->persist($modele);
            $em->flush();

            $this->addFlash('success', 'Modèle edité');
            return $this->redirectToRoute('app_modele');
        }
        return $this->render('modele/edit.html.twig', [
            'edit' => $form->createView(),
        ]);
    }

        #[Route('/delete/{id}', name: 'app_modele_delete')]
        public function delete(Modele $modele = null, EntityManagerInterface $entityManager)
        {
            if ($modele == null){
                $this->addFlash('danger', 'Modèle introuvable');
                return $this->redirectToRoute('app_modele');
            }

            $entityManager->remove($modele);
            $entityManager->flush();

            $this->addFlash('success', 'Modèle supprimé');
            return $this->redirectToRoute('app_modele');
        }
}
