<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('attachment', FileType::class, [
                'multiple' => true,
                'constraints' => [
                        new File([
                            'maxSize' => '50k',
                            'maxSizeMessage' => 'Votre fichier est trop gros.'
                        ])
                    ]
                ])
            ->getForm();


        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $attachments = $form->get('attachment')->getData();
            // $filename = bin2hex(random_bytes(10));

            foreach ($attachments as $attachment) {
                $filename = pathinfo($attachment->getClientOriginalName(), PATHINFO_FILENAME) . '-' . bin2hex(random_bytes(10));
                $extension = $attachment->guessExtension() ?? 'bin';
                $attachment->move('images', $filename . '.' . $extension);
            }
        }

        return $this->render('home/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

