<?php

namespace App\Controller;

use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use App\Entity\User;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    /**
     * @Route("/reset_password/{token}", name="app_reset_password")
     */
    public function resetPassword(User $user,Request $request,UserPasswordEncoderInterface $encoder)
    {
//        $user = $this->getUser();
        $form = $this->createForm(ResetPasswordType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user->onPreUpdate();
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);
            $user->setToken(null);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('app_login');
        }
        return $this->render('user/reset_password.html.twig',array(
            'form'=>$form->createView()
        ));
    }

    /**
     * @Route("/forget_password",name="forget_password",methods={"GET","POST"})
     */
    public function forgetPassword(Request $request, UserRepository $userRepository, \Swift_Mailer $mailer){
        $random_nb = mt_rand(10,100);
        $email_error=null;
        if($this->isCsrfTokenValid('forget_password'.$request->request->get('id'), $request->request->get('_token')) && $request->request->get('email')){
            $email = $request->request->get('email');
            $token = $request->request->get('_token');
            $user = $userRepository->findOneBy(['email'=>$email]);
            if($user){
                //send Email
                $message = (new \Swift_Message('hello Email'))
                    ->setFrom('achour@mingers-kreuzer.de')
                    ->setTo('mouhcine.new@gmail.com')
                    ->setBody(
                        $this->renderView(
                        // templates/emails/registration.html.twig
                            'emails/reset_password.html.twig',
                            ['token' => $token]
                        ),
                        'text/html'
                    );
                $mailer->send($message);
                $user->setToken($token);
                $user->onPreUpdate();
                $this->getDoctrine()->getManager()->flush();
                return $this->render('security/forget_password.html.twig',['email'=>$email]);
            }else{
                $email_error = 'No User found for this Email '.$email;
            }
        }
        return $this->render('security/forget_password.html.twig',['random_nb'=>$random_nb,'email_error'=>$email_error]);

    }
}
