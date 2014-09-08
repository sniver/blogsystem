<?php

namespace Blogger\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Blogger\BlogBundle\Controller\BaseController;
use Blogger\BlogBundle\Entity\Post;
use Blogger\BlogBundle\Entity\PostCategories;

class PostsController extends BaseController {
	
	public function getAction()
	{
		if(self::is_auth() && self::is_admin())
		{
			$posts = $this->get('doctrine')->getEntityManager()
				->createQuery('SELECT p FROM BloggerBlogBundle:Post p')
				->getResult()
			;
			$this->data["posts"] = $posts;
			return $this->render('BloggerBlogBundle:posts:index.html.twig', $this->data);
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
			return $this->render('BloggerBlogBundle:posts:add.html.twig', $this->data);
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
				$post = new Post();
				$post->setTitle($params["title"]);
				$post->setContent($params["content"]);
				$post->setStatus($params["status"]);
				$post->setAuthor("admin");
				$em = $this->getDoctrine()->getManager();
				$em->persist($post);
				$em->flush();
				$this->data["status"] = "OK";
				$this->data["added_id"] = $post->getId();
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
	
	public function editAction($PostId)
	{
		if(self::is_auth() && self::is_admin())
		{
			$em = $this->getDoctrine()->getManager();
			$posts = $em->getRepository('BloggerBlogBundle:Post')->find($PostId);

			if (!$posts) {
				return $this->redirect($this->generateUrl("admin_posts"));
			}
			$this->data["posts"] = $posts;
			return $this->render('BloggerBlogBundle:posts:edit.html.twig', $this->data);
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
				$post = $em->getRepository('BloggerBlogBundle:Post')->find($params["id"]);

				if (!$post) {
					$this->data["status"] = "Failed";
					$this->data["error_msg"] = "Post Does't exist";
				}
				else
				{
					$post->setTitle($params["title"]);
					$post->setContent($params["content"]);
					$post->setStatus($params["status"]);
					$post->setAuthor("admin");
					$em = $this->getDoctrine()->getManager();
					$em->persist($post);
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
	
	public function deleteAction(Request $request , $PostId)
	{
		if(self::is_auth() && self::is_admin())
		{
			$em = $this->getDoctrine()->getManager();
			$post = $em->getRepository('BloggerBlogBundle:Post')->find($PostId);

			if (!$post) {
				if($request->getMethod() == 'GET' && $request->isXmlHttpRequest())
				{
					$this->data["status"] = "Failed";
					$this->data["error_msg"] = "Post Does't exist";
					return new response(json_encode($this->data));
				}
				else
				{
					return $this->redirect($this->generateUrl("admin_posts"));
				}
			}
			else
			{
				$em->remove($post);
				$em->flush();
				
			}
			
			if($request->getMethod() == 'GET' && $request->isXmlHttpRequest())
			{
				$this->data["status"] = "OK";
				return new response(json_encode($this->data));
			}
			else
			{
				return $this->redirect($this->generateUrl("admin_posts"));
			}
		}
		else
		{
			return $this->redirect($this->generateUrl("logout"));
		}
	}
	
	public function categoriesAction($PostId)
	{
		if(self::is_auth() && self::is_admin())
		{
			$this->data["Post_id"] = $PostId;
			$categories = $this->get('doctrine')->getEntityManager()
				->createQuery('SELECT p FROM BloggerBlogBundle:Category p')
				->getResult()
			;
			$this->data["categories"] = $categories;
			
			$post_categories = $this->get('doctrine')->getEntityManager()
				->createQuery('SELECT p FROM BloggerBlogBundle:PostCategories p where p.Post_id = ' . $PostId)
				->getResult()
			;
			
			$this->data["post_categories"] = $post_categories;
			return $this->render('BloggerBlogBundle:posts:categories.html.twig', $this->data);
		}
		else
		{
			return $this->redirect($this->generateUrl("logout"));
		}
	}
	
	public function assignCategoriesAction(Request $request)
	{
		if(self::is_auth() && self::is_admin())
		{
			if($request->getMethod() == 'POST' && $request->isXmlHttpRequest())
			{
				$params = $request->request->all();
				if(!empty($params["Post_id"]) && isset($params["categories"]))
				{
					$result = $this->get('doctrine')->getEntityManager()
						->createQuery("DELETE FROM BloggerBlogBundle:PostCategories p where p.Post_id = " . $params["Post_id"])
						->getResult()
					;
					
					$arr_categories = explode(',', $params["categories"]);
					for($i = 0 ; $i<count($arr_categories) ; $i++)
					{
						$post_category = new PostCategories();
						$post_category->setPostId($params["Post_id"]);
						$post_category->setCategoryId($arr_categories[$i]);
						$em = $this->getDoctrine()->getManager();
						$em->persist($post_category);
						$em->flush();
					}
					
					$this->data["status"] = "OK";
				}
				else
				{
					$this->data["status"] = "Failed";
					$this->data["error_msg"] = "Post id must provide , and check some categories";
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
	
}