<?php

namespace Blogger\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends BaseController
{
    public function indexAction()
    {
		if(self::is_auth())
		{
			if(self::is_admin())
			{
				return $this->render('BloggerBlogBundle:Default:index.html.twig', $this->data);
			}
			else
			{
				$posts = $this->get('doctrine')->getEntityManager()
				->createQuery('SELECT p FROM BloggerBlogBundle:Post p where p.status = 1')
				->getResult()
				;
				$this->data["posts"] = $posts;
				return $this->render('BloggerBlogBundle:Default:user.html.twig', $this->data);
			}
		}
		else
		{
			return $this->redirect($this->generateUrl("login"));
		}
    }
}
