<?php

class Game {
    
    private $gameID;
    private $title;
    private $genre;
    private $platform;
    private $releaseYear;
    private float $rating;
    private $imageName;

    public function __construct($data) {
        $this->setId($data['id']);
        $this->setTitle($data['title']);
        $this->setGenre($data['genre']);
        $this->setPlatform($data['platform']);
        $this->setReleaseYear($data['release_year']);
        $this->setRating($data['rating']);
        $this->setImageName($data['imagename']);
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getGenre() {
        return $this->genre;
    }

    public function setGenre($genre) {
        $this->genre = $genre;
    }

    public function getPlatform() {
        return $this->platform;
    }

    public function setPlatform($platform) {
        $this->platform = $platform;
    }

    public function getReleaseYear() {
        return $this->releaseYear;
    }

    public function setReleaseYear($releaseYear) {
        $this->releaseYear = $releaseYear;
    }

    public function getRating() {
        return $this->rating;
    }

    public function setRating(float $rating) {
        $this->rating = $rating;
    }
    public function getId() {
        return $this->id;
    }

    public function setId($gameID) {
        $this->id = $gameID;
    }

    public function setImageName($imageName) {
        $this->imageName = $imageName;
    }

    public function getImageName() {
        return $this->imageName;
    }
}
?>