<?php

namespace Blogger\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Blogger\BlogBundle\Controller\BaseController;

class IndexController extends BaseController
{
	public function indexAction()
    {
		if(self::is_auth())
		{
			if($this->session->has("delete_cookies"))
			{
				$response =  self::deleteCookies();
				$this->session->remove("delete_cookies");
				$response->headers->set('Refresh', '0.001; url=' . $this->generateUrl("blogger_blog_homepage"));
				return $response;
			}
			else
			{
				return $this->redirect($this->generateUrl("blogger_blog_homepage"));
			}
		}
		else
		{
			return $this->redirect($this->generateUrl("login"));
		}
    }
	
	public function loginAction(Request $request){
		if(self::is_auth())
		{
			return $this->redirect($this->generateUrl("index"));
		}
		else
		{
			if($request->getMethod() == "GET")
			 {
				if ($this->session->has("error_msg")) {
					$this->data["error_msg"] = $this->session->get("error_msg");
					$this->session->remove("error_msg");
				}
				$user_data = self::checkCookies();
				$this->data["user_data"] = $user_data;
				return $this->render('BloggerBlogBundle:index:login.html.twig', $this->data);
			 }
			 elseif($request->getMethod() == "POST")
			 {
				$data = $request->request->all();
				if(isset($data["username"]) && strlen($data["username"]) > 0 && isset($data["password"]) && strlen($data["password"]) > 0)
				{
					$repository = $this->getDoctrine()
						->getRepository('BloggerBlogBundle:User');

					$query = $repository->createQueryBuilder('p')
						->where('p.Username = :Username')
						->setParameter('Username', $data["username"])
						->getQuery();

					$users = $query->getResult();
					if(count($users) > 0)
					{
						if($data["password"] == $users["0"]->getPassword())
						{
							$this->session->set("user_data" , $users["0"]);
							if(isset($data["remember"]))
							{
								$response = self::saveCookies($data["username"] , $data["password"]);
								$response->headers->set('Refresh', '0.001; url=' . $this->generateUrl("login"));
								return $response;
							}
							else
							{
								$this->session->set("delete_cookies" , true);
							}
						}
						else
						{
							$this->session->set("error_msg", "User Name Or Password Incorrect");
						}
					}
					else
					{
							$this->session->set("error_msg", "User Name Or Password Incorrect");
							
					}
					return $this->redirect($this->generateUrl("login"));
				}
				else
				{
					return $this->redirect($this->generateUrl("login"));
				}
			 }
		}
    }
	
	public function logoutAction()
	{
		$this->session->remove("user_data");
		return $this->redirect($this->generateUrl("login"));
	}
	
	 private function hashPassword($password){
        $security = new Security();
        return $security->hash($password);
    }
}