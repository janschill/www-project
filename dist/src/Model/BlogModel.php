<?php

namespace Model;

class BlogModel extends Model
{
  public function __construct($db)
  {
    parent::__construct($db);
  }

  public function addPost($post, $id)
  {
    if ($this->getOnePost($id) != null) {
      $queryDelete = "DELETE FROM posts WHERE id = :id";
      $sqlDelete = $this->db->prepare($queryDelete);
      $sqlDelete->bindParam(':id', $id);
      $sqlDelete->execute();
    }

    $sql = $this->db->prepare("INSERT INTO posts (title, text, created, author, cover) VALUES (:title, :text, :created, :author, :cover)");

    $sql->bindParam(':title', $post['title']);
    $sql->bindParam(':text', $post['text']);
    $sql->bindParam(':created', $post['created']);
    $sql->bindParam(':author', $post['author']);
    $sql->bindParam(':cover', $post['cover']);

    $sql->execute();

    $lastId = $this->db->lastInsertId();

    $this->addTagToPost($lastId, $post['tags']);
    $this->addCategoryToPost($lastId, $post['category']);
  }

  protected function addTagToPost($lastId, $tags)
  {
    // clear all entries before adding the new set
    $query = "DELETE FROM tag2post WHERE postsid = :postid";
    $sql = $this->db->prepare($query);
    $sql->bindParam(':postid', $lastId);
    $sql->execute();

    foreach ($tags as $tag) {
      $tagid = $this->getTagByName($tag);
      $query = "INSERT INTO tag2post (tagsid, postsid) VALUES (:tagid, :postid)";

      $sql = $this->db->prepare($query);
      $sql->bindParam(':tagid', $tagid['id']);
      $sql->bindParam(':postid', $lastId);

      $sql->execute();
    }
  }

  protected function addCategoryToPost($lastId, $categories)
  {
    // clear all entries before adding the new set
    $query = "DELETE FROM category2post WHERE postsid = :postid";
    $sql = $this->db->prepare($query);
    $sql->bindParam(':postid', $lastId);
    $sql->execute();

    foreach ($categories as $category) {
      $categoryid = $this->getCategoryByName($category);
      $query = "INSERT INTO category2post (categoriesid, postsid) VALUES (:categoryid, :postid)";

      $sql = $this->db->prepare($query);
      $sql->bindParam(':categoryid', $categoryid['id']);
      $sql->bindParam(':postid', $lastId);

      $sql->execute();
    }
  }

  public function getOnePost($id)
  {
    $sql = $this->db->prepare("SELECT * FROM posts WHERE id = :id");
    $sql->bindParam(':id', $id);
    $sql->execute();
    $row = $sql->fetch(\PDO::FETCH_ASSOC);
    return $row;
  }

  public function getFewPosts()
  {
    return $this->getRow("SELECT * FROM posts ORDER BY created desc LIMIT 3");
  }

  public function getAllPosts($instance)
  {
    return $this->getRow("SELECT * FROM $instance ORDER BY created desc");
  }

  public function getCategoryById($id)
  {
    $query = "SELECT categories.name FROM categories 
    JOIN category2post ON categories.id = category2post.categoriesid
    JOIN posts ON posts.id = category2post.postsid
    WHERE posts.id = :id";

    $sql = $this->db->prepare($query);
    $sql->bindParam(':id', $id);
    $sql->execute();
    $row = $sql->fetchAll(\PDO::FETCH_COLUMN, 0);

    return $row;
  }

  public function getTagsById($id)
  {
    $query = "SELECT tags.name FROM tags
    JOIN tag2post ON tags.id = tag2post.tagsid
    JOIN posts ON posts.id = tag2post.postsid
    WHERE posts.id = :id";

    $sql = $this->db->prepare($query);
    $sql->bindParam(':id', $id);
    $sql->execute();
    /* to format the return array – fetches first item from every row – would otherwise return 2d array */
    $row = $sql->fetchAll(\PDO::FETCH_COLUMN, 0);

    return $row;
  }

  public function deletePost($id)
  {
    $sql = $this->db->prepare("DELETE FROM posts WHERE id = :id");
    $sql->bindParam(':id', $id);
    $sql->execute();

    return $sql;
  }
}