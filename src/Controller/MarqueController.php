<?php

namespace App\Controller;

use App\Entity\Marque;
use App\Form\MarqueType;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Filesystem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/marque')]
class MarqueController extends AbstractController
{
    #[Route('/', name: 'app_marque')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {

        $marque = new Marque();
        $form = $this->createForm(MarqueType::class, $marque);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $imageFile = $form->get('logo')->getData();

            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'import de votre logo');
                }
                $marque->setLogo($newFilename);
            }

            $em->persist($marque);
            $em->flush();

            $this->addFlash('success', 'Marque ajoutée');
        }

        $marques = $em->getRepository(Marque::class)->findAll();
        return $this->render('marque/index.html.twig', [
            'marques' => $marques,
            'ajout' => $form->createView()
        ]);
    }

    #[Route('/{id}', name: 'app_marque_show')]
    public function show(Marque $marque = null) : Response
    {

        if ($marque == null){
            $this->addFlash('danger', 'Marque introuvable');
            return $this->redirectToRoute('app_marque');
        }

        return $this->render('marque/show.html.twig', [
            'marque' => $marque,
        ]);
    }

        #[Route('/edit/{id}', name: 'app_marque_edit')]
        public function editCat(EntityManagerInterface $em, Marque $marque, Request $request)
        {
            $form = $this->createForm(MarqueType::class, $marque);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()){
                $imageFile = $form->get('logo')->getData();

                if ($imageFile) {
                    $newFilename = uniqid().'.'.$imageFile->guessExtension();

                    try {
                        $imageFile->move(
                            $this->getParameter('upload_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $this->addFlash('danger', 'Erreur lors de l\'import de votre logo');
                    }
                    $marque->setLogo($newFilename);
                }
                $em->persist($marque);
                $em->flush();

                $this->addFlash('success', 'Marque editée');
                return $this->redirectToRoute('app_marque');
            }
            return $this->render('marque/edit.html.twig', [
                'edit' => $form->createView(),
            ]);
        }


        #[Route('/delete/{id}', name: 'app_marque_delete')]
        public function delete(Marque $marque = null, EntityManagerInterface $entityManager)
        {
            if ($marque == null){
                $this->addFlash('danger', 'Marque introuvable');
                return $this->redirectToRoute('app_marque');
            }

            $entityManager->remove($marque);
            $entityManager->flush();

            $this->addFlash('success', 'Marque supprimée');
            return $this->redirectToRoute('app_marque');
        }
}
