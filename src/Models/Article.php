<?php 
	class Article{
		private $idArticle;
		private $titre;
		private $contenuArticle;
		private $tag;
		private $lienPhoto;

		public function __construct($_idArticle, $_titre, $_contenuArticle, $_tag, $_lienPhoto){
			$this->idArticle = $_idArticle;
			$this->titre = $_titre;
			$this->contenuArticle = $_contenuArticle;
			$this->tag = $_tag;
			$this->lienPhoto = $_lienPhoto;
		}

		public function getIdArticle(){
			return $this->idArticle;
		}

		public function getTitre(){
			return $this->titre;
		}

		public function getContenuArticle(){
			return $this->contenuArticle;
		}

		public function getTag(){
			return $this->tag;
		}

		public function getLienPhoto(){
			return $this->lienPhoto;
		}

		public function setIdArticle($_idArticle){
			$this->idArticle = $_idArticle;
		}

		public function setTitre($_titre){
			$this->titre = $_titre;
		}

		public function setContenuArticle($_contenuArticle){
			$this->contenuArticle = $_contenuArticle;
		}

		public function setTag($_tag){
			$this->tag = $_tag;
		}

		public function setLienPhoto($_lienPhoto){
			$this->lienPhoto = $_lienPhoto;
		}
	}
?>