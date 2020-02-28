<?php
	$debut = microtime(true);

	require_once('./php/class.php');
	if((isset($_POST['nb_el']))&&(is_numeric($_POST['nb_el']))) {	
		$nb_ele = $_POST['nb_el'];
	} else {	
		$nb_ele = 10;
	}

	$max_cube = pow($nb_ele,3);

	if(isset($_POST['x_start'])) { 
		$x_start = checkPost($_POST['x_start'],$nb_ele);
	}else{
		$x_start = 0;
	}
	
	if(isset($_POST['y_start'])) { 
		$y_start = checkPost($_POST['y_start'],$nb_ele);
	}else{
		$y_start = 0;
	}
	
	if(isset($_POST['z_start'])) { 
		$z_start = checkPost($_POST['z_start'],$nb_ele);
	}else{
		$z_start = 0;
	}

	function checkPost($value,$nbEle){
		$value = strip_tags($value);
		if(is_numeric($value)) {

			if($value > $nbEle) {
				return 0;
			} else {
				return $value;
			}			
		} else {			
			return 0;
		}
	}
	
?>
<html>
	<head>
	<style>
		#myChart,fieldset{max-width:960px}
		h2{cursor:pointer}
		fieldset > button {margin:10px 0px;}
	</style>
	</head>
	<body>
		<form method="post" action="index.php" >
			<fieldset>
			<legend><h2>Settings :</h2></legend>
			<P>
				<i>
				Les valeurs ci-dessous sont utilisées en cas d'erreur(s) ou d'absence de saisie !
				<br>
				Valeur par défaut: 10 éléments par axe
				<br>
				Premier cube contaminé X0,Y0,Z0
				</i>
			</P>
			<p>
				<input type="text" name="nb_el" value="" maxlength="2" placeholder="nombre de cubes par axe" required> (2 chiffres max)
			</p>
			<p>
				<input type="text" name="x_start" value="" maxlength="2" placeholder="X start" required>
				<input type="text" name="y_start" value="" maxlength="2" placeholder="Y start" required>
				<input type="text" name="z_start" value="" maxlength="2" placeholder="Z start" required>
			</p>
			<button>Valider</button>
			</fieldset>
		</form>
		<div>
			<h2>Paramètres d'entrée</h2>
			<p>
				<?php
					echo 'Nombre de cubes par rangée: '.$nb_ele.'<br>';
					echo 'Coordonnées du premier cube contaminé: X'.$x_start.' Y'.$y_start.' Z'.$z_start;		
				?>
			</p>
		</div>
		<div>			
			<fieldset>
			<button onclick="toggleAff('log')"><h2>Logs</h2></button>
				<div id="log">
					<?php	
						$cube = new BigCube($nb_ele,$x_start,$y_start,$z_start,$max_cube);					
						$cube->coronavirus();	
					?>
				
				</div>
			</fieldset>
		</div>
		<div id="graph">
			<h2>Graphique</h2>
			<canvas id="myChart"></canvas>
		</div>	
		<script src="./js/chart.js"></script>
		<script>
			document.getElementById('log').style.display = "none";
			
			function toggleAff(id) {
				if (document.getElementById(id).style.display === "block") {
					document.getElementById(id).style.display = "none";
				} else {
					document.getElementById(id).style.display = "block";
				}
			}			
			
			var ctx = document.getElementById('myChart').getContext('2d');
			var chart = new Chart(ctx, {
				// The type of chart we want to create
				type: 'bar',

				// The data for our dataset
				data: {
					labels: <?php echo json_encode($cube->graphAxeX); ?>,
					datasets: [ {
						label: '% de nouvelles contaminations',
						data: <?php echo json_encode($cube->graph3); ?>,
						borderColor: 'rgb(0, 0, 0)',
						datasetFill:false,
						// Changes this dataset to become a line
						type: 'line'
					},{
						label: '% de cubes sains',
						backgroundColor: 'rgb(127,191,63)',
						borderColor: 'rgb(127,191,63)',
						data: <?php echo json_encode($cube->graph1); ?>
					},{
						label: '% de cubes contaminés',
						backgroundColor: 'rgb(255, 100, 0)',
						borderColor: 'rgb(255, 100, 0)',
						data: <?php echo json_encode($cube->graph2); ?>
					}]
				},

				// Configuration options go here
				options: {}
			});
		</script>
	</body>
</html>
<?php
	$fin = microtime(true);
	$delai = $fin - $debut;
	echo 'Le temps écoulé est de '.$delai.' millisecondes';
?>
