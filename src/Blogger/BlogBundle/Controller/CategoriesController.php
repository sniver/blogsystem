<?php

namespace Blogger\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Blogger\BlogBundle\Controller\BaseController;
use Blogger\BlogBundle\Entity\Category;

class CategoriesController extends BaseController {
	
	public function getAction()
	{
		if(self::is_auth() && self::is_admin())
		{
			$categories = $this->get('doctrine')->getEntityManager()
				->createQuery('SELECT p FROM BloggerBlogBundle:Category p')
				->getResult()
			;
			$this->data["categories"] = $categories;
			return $this->render('BloggerBlogBundle:categories:index.html.twig', $this->data);
		}
		else
		{
			return $this->redirect($this->generateUrl("logout"));
		}
	}
	
	public function addAction()
	{
		if(self::is_auth() && self::is_admin())
		{
			return $this->render('BloggerBlogBundle:categories:add.html.twig', $this->data);
		}
		else
		{
			return $this->redirect($this->generateUrl("logout"));
		}
	}
	
	public function saveAction(Request $request)
	{	
		if(self::is_auth() && self::is_admin())
		{
			if($request->getMethod() == 'POST' && $request->isXmlHttpRequest())
			{
				$params = $request->request->all();
				if(isset($params["name"]) && strlen($params["name"]) > 0)
				{
					$category = new Category();
					$category->setName($params["name"]);
					$em = $this->getDoctrine()->getManager();
					$em->persist($category);
					$em->flush();
					$this->data["status"] = "OK";
					$this->data["added_id"] = $category->getId();
				}
				else
				{
					$this->data["status"] = "Failed";
					$this->data["error_msg"] = "SomeThing Wrong";
				}
				return new response(json_encode($this->data));
			}
			else
			{
				echo 'Access Denied';
				exit();
			}
		}
		else
		{
			return $this->redirect($this->generateUrl("logout"));
		}
	}
	
	public function editAction($CategoryId)
	{
		if(self::is_auth() && self::is_admin())
		{
			$em = $this->getDoctrine()->getManager();
			$categories = $em->getRepository('BloggerBlogBundle:Category')->find($CategoryId);

			if (!$categories) {
				return $this->redirect($this->generateUrl("admin_categories"));
			}
			$this->data["categories"] = $categories;
			return $this->render('BloggerBlogBundle:categories:edit.html.twig', $this->data);
		}
		else
		{
			return $this->redirect($this->generateUrl("logout"));
		}
	}
	
	public function updateAction(Request $request)
	{	
		if(self::is_auth() && self::is_admin())
		{
			if($request->getMethod() == 'POST' && $request->isXmlHttpRequest())
			{
				$params = $request->request->all();
				$em = $this->getDoctrine()->getManager();
				$category = $em->getRepository('BloggerBlogBundle:Category')->find($params["id"]);

				if (!$category) {
					$this->data["status"] = "Failed";
					$this->data["error_msg"] = "Category Does't exist";
				}
				else
				{
					$category->setName($params["name"]);
					$em = $this->getDoctrine()->getManager();
					$em->persist($category);
					$em->flush();
					$this->data["status"] = "OK";
				}
				return new response(json_encode($this->data));
			}
			else
			{
				echo 'Access Denied';
				exit();
			}
		}
		else
		{
			return $this->redirect($this->generateUrl("logout"));
		}
	}
	
	public function deleteAction(Request $request , $CategoryId)
	{
		if(self::is_auth() && self::is_admin())
		{
			$em = $this->getDoctrine()->getManager();
			$category = $em->getRepository('BloggerBlogBundle:Category')->find($CategoryId);

			if (!$category) {
				if($request->getMethod() == 'GET' && $request->isXmlHttpRequest())
				{
					$this->data["status"] = "Failed";
					$this->data["error_msg"] = "Category Does't exist";
					return new response(json_encode($this->data));
				}
				else
				{
					return $this->redirect($this->generateUrl("admin_categories"));
				}
			}
			else
			{
				$em->remove($category);
				$em->flush();
				
			}
			
			if($request->getMethod() == 'GET' && $request->isXmlHttpRequest())
			{
				$this->data["status"] = "OK";
				return new response(json_encode($this->data));
			}
			else
			{
				return $this->redirect($this->generateUrl("admin_categories"));
			}
		}
		else
		{
			return $this->redirect($this->generateUrl("logout"));
		}
	}
}