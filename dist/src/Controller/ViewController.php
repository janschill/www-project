<?php

namespace Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ViewController extends Controller
{
  public function __construct($container)
  {
    parent::__construct($container);
  }

  public function showIndex($request)
  {
    $user = $request->attributes->get('user');
    $html = $this->container['twig']->render('index.html.twig', ['user' => $user]);
    return new Response($html);
  }

  public function showAbout($request)
  {
    $user = $request->attributes->get('user');
    $html = $this->container['twig']->render('about.html.twig', ['user' => $user]);
    return new Response($html);
  }

  public function showProjects($request)
  {
    $user = $request->attributes->get('user');
    $id = $request->attributes->get('id');

    if (!isset($id)) {
      $html = $this->container['twig']->render('projects.html.twig', ['user' => $user]);
      return new Response($html);
    } else {
      echo "Project";
    }
  }

  public function showBlog($request)
  {
    $user = $request->attributes->get('user');
    $posts = $this->blogModel->getAllPosts($request);
    $categories = $this->blogModel->getAllCategories();
    $tags = $this->blogModel->getAllTags();
    $html = $this->container['twig']->render('blog.html.twig', [
      'posts' => $posts,
      'user' => $user,
      'tags' => $tags,
      'categories' => $categories
    ]);
    return new Response($html);
  }

  public function showConf($request)
  {
    $user = $request->attributes->get('user');
    $categories = $request->attributes->get('categories');
    $html = $this->container['twig']->render('conf.html.twig', [
      'user' => $user,
      'categories' => $categories
    ]);
    return new Response($html);
  }
}
