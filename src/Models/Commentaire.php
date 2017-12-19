<?php
	namespace DUT\Models;
	
	class Commentaire 
	{
		private $idCommentaire;
		private $idArticle;
		private $nomEditeur;
		private $contenuCommentaire;

		function __construct($_idCommentaire,$_idArticle,$_nomEditeur,$_contenuCommentaire)
		{
			$this->idCommentaire=$_idCommentaire;
			$this->idArticle=$_idArticle;
			$this->nomEditeur=$_nomEditeur;
			$this->contenuCommentaire=$_contenuCommentaire;
			
		}

		public function getIdCommentaire(){
			return $this->idCommentaire;
		}

		public function getIdArticle(){
			return $this->idArticle;
		}

		public function getNomEditeur(){
			return $this->nomEditeur;
		}

		public function getContenuCommentaire(){
			return $this->contenuCommentaire;
		}


		public function setIdCommentaire($idCommentaire){
			 $this->idCommentaire=$idCommentaire;
		}

		public function setIdArticle($idArticle){
			$this->idArticle=$idArticle;
		}

		public function setNomEditeur($nomEditeur){
			 $this->nomEditeur=$nomEditeur;
		}

		public function setContenuCommentaire($contenuCommentaire){
			 $this->contenuCommentaire=$contenuCommentaire;
		}
	}
  ?>