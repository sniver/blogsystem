<?php
namespace Blogger\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;

class BaseController extends Controller {
	
	protected $data = array();
	protected $session = NULL;
	protected $cookies = NULL;
	protected $request = NULL;
	public function __construct() {
		$this->init(TRUE);
	}
	
	public function init($notDirect = FALSE) {
	
		if(!isset($this->session)) {
			$this->session = new Session();
		} else {
			if(!$this->session->has("user_data"))
			{
				$this->session = new Session();
			}
			else
			{
				$this->session = $this->getRequest()->getSession();
			}
		}
		$request = Request::createFromGlobals();
		$this->cookies = $request->cookies;
	}
	
	public function is_auth()
	{
		if ($this->session->has("user_data")) {
			return true;
		}
		else
		{
			return false;
		}
	}
	public function is_admin()
	{
		if ($this->session->get("user_data")->getIsAdmin()) {
			return true;
		}
		else
		{
			return false;
		}
	}
	public function checkCookies()
	{
		$user_data = array();
		if($this->cookies->has('user_name') && $this->cookies->has('password'))
		{
			$user_data["user_name"] = trim($this->cookies->get('user_name'));
			$user_data["password"] = trim($this->cookies->get('password'));
			
		}
		return $user_data;
	}
	
	public function saveCookies($user_name , $password)
    {
		$username = new Cookie('user_name', $user_name);
		$password = new Cookie('password', $password);
		$response = new Response();
		$response->headers->setCookie($username);
		$response->headers->setCookie($password);
		return $response;
    }
	
	public function deleteCookies()
	{
		$response = new Response();
		$response->headers->clearCookie('user_name');
		$response->headers->clearCookie('password');
		return $response;
	}
}