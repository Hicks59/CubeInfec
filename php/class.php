<?php
ini_set('max_execution_time', 240);
// classes et méthodes du cubes regroupants les petites cubes
class BigCube{	
	public function __construct(int $nb_ele,int $x_start,int $y_start,int $z_start,int $max_cube) {	
		$this->cubes =  array();
		$this->cubesConta = array();
		$this->histoConta = array();
		$this->nbEle = $nb_ele;
		$this->day = 0;
		$this->nbCubeConta = 0;
		$this->nbCubeSain = 0;
		$this->pourcentConta = 0;
		$this->pourcentSain = 100;
		$this->maxCube = $max_cube;
		$this->axes = array('z','y','x');
		$this->graphAxeX = array();
		$this->graph1 = array();
		$this->graph2 = array();
		$this->graph3 = array();
		$this->pourcentNewconta = array();

		$nbEleArray = $this->nbEle - 1;
		//Génération des petits cubes
		for ($x = 0; $x <= $nbEleArray; $x++) {
			for ($y = 0; $y <= $nbEleArray; $y++) {
				for ($z = 0; $z <= $nbEleArray; $z++) {
					$smallCube = new SmallCube($x,$y,$z);				
				
					//ajout dans le bon tableau de l'objet en fonction du premier cube contaminé issu des paramètres de saisie
					if(($smallCube->x === $x_start)&&($smallCube->y === $y_start)&&($smallCube->z === $z_start))
					{							
						$smallCube->setState();
						$smallCube->setAttr('day_conta',2);
						
						array_push($this->histoConta,$smallCube);									
					} else {						
						array_push($this->cubes,$smallCube);						
					}				
				}
			}
		}	
		$this->logStat();		
	}
	
	private function logStat(){
		//Nombre de petits cubes sain
		$this->nbCubeSain = count($this->cubes);
		//Nombre total de petits cubes contaminés
		$this->nbCubeConta = count($this->histoConta) + count($this->cubesConta);
		//calcul du nombre de petits cubes générés pour vérifier avec l'attente joueur
		$this->nbSmallCube = $this->nbCubeSain + $this->nbCubeConta;

		//calcul du pourcentage de contamination		
		if($this->nbCubeConta !== 0)
		{
			$this->pourcentSain = $this->nbCubeSain * 100 / $this->nbSmallCube;
			$this->pourcentConta  = $this->nbCubeConta * 100 / $this->nbSmallCube;
			$this->pourcentNewconta = count($this->histoConta) * 100 / $this->nbSmallCube;
		}	
		
		//Ajout dans les tableaux de statistiques
		array_push($this->graphAxeX, 'Jour '.$this->day);
		array_push($this->graph1, $this->pourcentSain);		
		array_push($this->graph2, $this->pourcentConta);	
		array_push($this->graph3, $this->pourcentNewconta);
		
		echo 'Etat de contamination:<br>'; 
		echo $this->pourcentConta.'% Jour '. $this->day.'<br>';		
		echo 'Nombre de Cubes sains:'.$this->nbCubeSain.'<br>';
		echo 'Nombre de Cubes contaminés:'.$this->nbCubeConta.'<br>'; 	
		echo '----------------------------------------<br>';	
	}
	
	public function coronavirus() {		
		while(count($this->cubes)> 0){			
			
			foreach($this->histoConta as $key) {
				
				foreach ($this->axes as $axe) {				
					$newValue = $key->$axe - 1;
				
					if(($key->$axe - 1)>= 0){
						$this->evoConta($newValue,$axe,$key);						
					}	
					
					$newValue = $key->$axe + 1;
					
					if(($newValue + 1) <= $this->nbEle){
						$this->evoConta($newValue,$axe,$key);	
					}				
				}
				
				array_push($this->cubesConta,$key); 
				unset($this->histoConta[array_search($key, $this->histoConta)]);
			}
			
			$this->day++;
			if($this->day === 1000)
			{
				echo 'Erreur: Traitement stoppé, nombre de jour de traitement trop élevé ';
				break;
			}
			$this->logStat();				
		}
	}
	
	private function evoConta(int $value,string $axe,object $key){
	
		switch($axe) {
			case 'x':
				$idCube = $this->searchObjet($value,$key->y,$key->z);				
			break;
			case 'y':
				$idCube = $this->searchObjet($key->x,$value,$key->z);				
			break;
			case 'z':
				$idCube =  $this->searchObjet($key->x,$key->y,$value);				
			break;
		}	
		
		if(isset($idCube))
		{
			$this->cubes[$idCube]->setState();
			$this->cubes[$idCube]->setAttr('day_conta',2);	
			
			array_push($this->histoConta,$this->cubes[$idCube]);
			unset($this->cubes[$idCube]);	
		}
	}
	
	private function searchObjet(int $x,int $y,int $z){
		foreach ($this->cubes as $index => $object) {
			  if (($object->x === $x)&&($object->y === $y)&&($object->z === $z)){
					return $index;
			  }
		}
	}
}
 
// classes et méthodes des petites cubes
class SmallCube {
	public function __construct(int $x,int $y,int $z) {
		$this->x = $x;	
		$this->y = $y;
		$this->z = $z;
		$this->state = 0;
		$this->day_conta = null;
	}
	
	public function setAttr(string $attr,int $value) {
		$this->$attr = $value;
	}

	public function setState() { 
        $this->state = 1;
    }
}
?>