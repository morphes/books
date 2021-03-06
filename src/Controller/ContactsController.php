<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Repository\BlockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ContactsController
 *
 * @package App\Controller
 */
class ContactsController extends AbstractController
{
    /**
     * @Route("/contacts", name="contacts")
     */
    public function index(Request $request, TranslatorInterface $translator, BlockRepository $blockRepository)
    {
        $feedback = new Feedback();

        $form = $this->createFormBuilder($feedback)
            ->add('firstname', TextType::class, [
                'label' => 'Имя',
                'attr' => [
                    'class' => 'form form-control'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Фамилия',
                'attr' => [
                    'class' => 'form form-control'
                ]
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'form form-control'
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => 'Телефон',
                'attr' => [
                    'class' => 'form form-control'
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Сообщение',
                'attr' => [
                    'class' => 'form form-control'
                ]
            ])
            ->add('save', SubmitType::class, ['label' => 'Отправить', 'attr' => ['class' => 'btn btn-lg btn-block btn-primary']])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $feedback = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($feedback);
            $entityManager->flush();

            $this->addFlash('success', 'Спасибо за Ваше обращение! Мы с вами свяжемся в ближайшее время');

            return $this->redirectToRoute('contacts');
        }
        $contacts_block = $blockRepository->findBy(['name' => 'contacts_page_content']);
        return $this->render('contacts.html.twig', [
            'form' => $form->createView(),
            'contacts_block' => $contacts_block[0]
        ]);
    }
}
