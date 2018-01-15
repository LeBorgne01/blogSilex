<?php
	namespace DUT\Models;

	class Citation{
		private $idCitation;
		private $contenuCitation;
		private $lienVideo;
		private $nombreAime;

		public function __construct($_idCitation, $_contenuCitation, $_lienVideo, $_nombreAime){
			$this->idCitation = $_idCitation;
			$this->contenuCitation = $_contenuCitation;
			$this->lienVideo = $_lienVideo;
			$this->nombreAime = $_nombreAime;
		}


		// Getters
		public function getIdCitation(){
			return $this->idCitation;
		}

		public function getContenuCitation(){
			return $this->contenuCitation;
		}

		public function getLienVideo(){
			return $this->lienVideo;
		}

		public function getNombreAime(){
			return $this->nombreAime;
		}

		//fin getters

		// Setters
		public function setIdCitation($_idCitation){
			$this->idCitation = $_idCitation;
		}

		public function setContenuCitation($_contenuCitation){
			$this->contenuCitation = $_contenuCitation;
		}

		public function setLienVideo($_lienVideo){
			$this->lienVideo = $_lienVideo;
		}

		public function setNombreAime($_nombreAime){
			$this->nombreAime = $_nombreAime;
		}

		//fin setters

		//Fonction pour ajouter un j'aime sur la vidéo
		public function ajouterUnAime(){
			$this->nombreAime++;
		}
	}
?>