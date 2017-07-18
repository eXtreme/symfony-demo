<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/profile")
 * @Security("has_role('ROLE_USER')")
 */
class ProfileController extends Controller
{
    /**
     * @Route("/change-password", name="profile_change_password")
     * @Method({"GET", "POST"})
     */
    public function changePasswordAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm('AppBundle\Form\UserPasswordType');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encoder = $this->get('security.password_encoder');
            $encodedPassword = $encoder->encodePassword($user, $form->get('new_password')->getData());

            $user->setPassword($encodedPassword);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'profile.password_changed');

            return $this->redirectToRoute('profile_change_password');
        }

        return $this->render('profile/change_password.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
