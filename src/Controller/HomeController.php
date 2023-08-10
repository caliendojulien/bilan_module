<?php

namespace App\Controller;

use App\Entity\Module;
use App\Form\ModuleType;
use App\Repository\ModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home_index')]
    public function index(
        ModuleRepository $moduleRepository
    ): Response
    {
        $modules = $moduleRepository->findAll();
        return $this->render('home/index.html.twig', compact('modules'));
    }

    #[Route('/modif_etoile/{module}/{etoile}', name: 'home_modif_etoile')]
    public function modif_etoile(
        Module $module,
        int $etoile,
        EntityManagerInterface $entityManager
    ): Response
    {
       if ($module->getNote() != $etoile) {
           $module->setNote($etoile);
           $entityManager->persist($module);
           $entityManager->flush();
       }
       return $this->redirectToRoute('home_index');
    }

    #[Route('/ajout_module', name: 'home_ajout_module')]
    public function ajout_module(
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        $module = new Module();
        $form = $this->createForm(ModuleType::class, $module);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($module);
            $entityManager->flush();
            return $this->redirectToRoute('home_index');
        }

        return $this->render('home/ajout_module.html.twig', compact('form'));
    }
}
