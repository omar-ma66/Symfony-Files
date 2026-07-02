<?php

namespace App\Controller;

use App\Entity\Fichier;
use App\Form\FichierType;
use App\Repository\FichierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class FichierController extends AbstractController
{
     #[Route('/fichier/nouveau', name: 'app_fichier_new')]
  
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $fichier = new Fichier();
        $form = $this->createForm(FichierType::class, $fichier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('nom')->getData();

            // Comme l'option 'mapped' => false est activée, on gère le fichier manuellement
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // On sécurise le nom du fichier (slug)
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // On déplace le fichier dans le dossier configuré (ex: %kernel.project_dir%/public/uploads)
                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... gérer l'erreur si le fichier ne se sauvegarde pas
                }

                // On met à jour l'entité avec le nouveau nom de fichier sécurisé
                $fichier->setNom($newFilename);
            }

            $entityManager->persist($fichier);
            $entityManager->flush();

            return $this->redirectToRoute('app_fichier_success');
        }

        return $this->render('fichier/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


#[route('/fichier/success',name:'app_fichier_success')]
public function success(Request $request,FichierRepository $fichierRepository)
 {           
    $data = $fichierRepository->findAll();
        // $objFile = new Fichier();
//             $form = $this->createForm(FichierType::class,$objFile);
//             $form->handleRequest($request);


    return $this->render('fichier/success.html.twig',["data"=>$data]);
}


}