<html>
<head>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
	<script>
		$(function() {
			$(".meter > span").each(function() {
				$(this)
					.data("origWidth", $(this).width())
					.width(0)
					.animate({
						width: $(this).data("origWidth")
					}, 1200);
			});
		});
	</script>
	
	<style>
		.meter { 
			height: 15px;  
			width: 100%;
			margin: 60px 0 20px 0; 
			background: #555;
			border-radius: 25px;
			padding: 10px;
			box-shadow        : inset 0 -1px 1px rgba(255,255,255,0.3);
		}
		.meter > span {
			display: block;
			height: 100%;
			border-top-right-radius: 8px;
			border-bottom-right-radius: 8px;
			border-top-left-radius: 20px;
			border-bottom-left-radius: 20px;
			background-color: rgb(43,194,83);
			box-shadow: 
			  inset 0 2px 9px  rgba(255,255,255,0.3),
			  inset 0 -2px 6px rgba(0,0,0,0.4);
			position: relative;
			overflow: hidden;
		}

	</style>
</head>
</body>

<?php
	//подключаем баблиотеку функций
	include_once('fly_class.php');
	$flying = new flying();

	$src = "http://localhost/wp-admin/test/";

	//подклчаем файл формата .json
	$str_data = file_get_contents($src."document.json");
	//if($str_data){echo 'ok';}else{echo 'no';}
	$data = json_decode($str_data,true);	

	//Обращение к функции getPartDistance(, она считает километраж маршрута, для удобства - функции getDistance() и getPartDistance() 
	//я сделала одинаковыми для вызова при подсчете расстояния всего маршрута и расстояния между точками. В функцию передается 2 параметра:
	//это двумерный массив $dada, полученый из внешнего файла json с координатами точек маршрута
	//и необязательный параметр - номер сегмента маршрута.

	//Ниже, по очереди вызывается функция getPartDistance(), для рассчета всего маршрута, первого и второго его сегмента

	$distance = $flying -> getPartDistance($data[tr]);
	$first_segment = $flying -> getPartDistance($data[tr], 1);
	$second_segment = $flying -> getPartDistance($data[tr], 2);

	echo 	'<br><center>
			<table width = 75%>
				<tr>
					<td align = "center">	Из файла ***.json к нам поступила информация, о том, что некий самолет пролетит со скоростью: '.$data[speed].', 
							из пункта "А": координаты которого ('.$data[tr][0][0].', '.$data[tr][0][1].'), в пункт В: ('.$data[tr][1][0].', '.$data[tr][1][1].')
							по пути посещая авиа-заправку с координатами: ('.$data[tr][2][0].', '.$data[tr][2][1].')<br><br><br>
					</td>
				</tr>
				<tr>
					<td>	Из рассчетов полуим, что путь, который необходимо пролететь самолету: <b>'.$distance.'</b> км
					</td>
				</tr>
				<tr>
					<td>	Расстояние от пункта вылета до промежуточного: <b>'.$first_segment.'</b> км, от промежуточного до конца пути: <b>'.$second_segment.'</b> км
					</td>
				</tr>';


	echo '<tr><td><form action="'.$src.'avia.php" method="get">';
	echo 'Вычислим в какое время самолет долетит из пункта А в пункт В, если вылетит в установленную дату: <select name="day">';
	for ($i = 1; $i<=30; $i++){
		echo '<option value="'.$i.'">'.$i.'</option>';	
	}
	echo '<select>';
	echo '<select name="month">';
	echo '<option value="1">Январь</option>';	
	echo '<option value="2">Февраль</option>';	
	echo '<option value="3">Март</option>';	
	echo '<option value="4">Апрель</option>';	
	echo '<option value="5">Май</option>';	
	echo '<option value="6">Июнь</option>';	
	echo '<option value="7">Июль</option>';	
	echo '<option value="8">Август</option>';	
	echo '<option value="9">Сентябрь</option>';	
	echo '<option value="10">Октябрь</option>';	
	echo '<option value="11">Ноябрь</option>';	
	echo '<option value="12">Декабрь</option>';	
	echo '<select>';

	echo '<select name="year">';
	for ($i = 2014; $i<=2018; $i++){
		echo '<option value="'.$i.'">'.$i.'</option>';	
	}
	echo '<select>';

	echo '<select name="hour">';
	for ($i = 0; $i<=23; $i++){
		echo '<option value="'.$i.'">'.$i.'</option>';	
	}
	echo '<select>';

	echo '<select name="minute">';
	for ($i = 0; $i<=59; $i++){
		echo '<option value="'.$i.'">'.$i.'</option>';	
	}
	echo '<select>';
	
	echo '<input type = "submit" name="arrive" value = "ok">';
	echo '</form></td></tr>';

	if (isset($_GET['arrive'])){
		//Рассчет времени прибытия в конечную точку, 
		//строится на основе прибалвения к нынешнему времени ориентироваогого времени полета (расстояние/скорость)

		$unixtime = mktime ($_GET['hour'], $_GET['minute'], 0, $_GET['month'], $_GET['day'], $_GET['year']);
		$first_segment = $flying -> getTimeArrival($unixtime, $data[speed], $flying -> getPartDistance($data[tr], 1));
		$second_segment = $flying -> getTimeArrival($unixtime, $data[speed], $flying -> getPartDistance($data[tr]));

		echo '	<tr><td>В промежуточной точке маршрута самолет появится в: <b>'.$first_segment.'</b>, окончит маршрут самолет в:  <b>'.$second_segment.'</b></td></tr>';

		//Запись данных о времени вылета, для симуляции передвижения по маршруту
		$date = date_create();
		$fp = fopen("file.txt", "w"); 
		if ($fp){
			fwrite($fp, date_timestamp_get($date)); 
		}
		fclose($fp);
		echo '<tr><td><form action="'.$src.'avia.php" method="get">';
		echo '<input type = "submit" name="simulate" value = "Полетели!">';
		echo '</form></td></tr>';
	}

	//Костыльный метод "прогресс бара", к сожалению. В виду чего, бновление прогресса полета самолета происходит по кнопке.
	if (isset($_GET['simulate'])){
		echo '<form action="'.$src.'avia.php" method="get">';
		$progress = $flying -> play($data, $src); 
		if ($progress < 100){
			echo '<tr><td><br><br>Следим за полетом самолета! Самолет вылетел, на данный момент от всего маршрута пройдено '.$progress.'%</td></tr>';
			echo '<tr><td><body><div class="meter" width="490px"><span style="width: '.$progress.'%"></span></div>';
			echo '<input type = "submit" name="simulate" value = "Обновить"></td></tr>';
		}else{
			echo '<tr><td>Долетели! Всем спасибо за внимание. (Если я забыла что-то сделать, прошу сообщить. Всего доброго.</td></tr></table>';
		}
		echo '</form>'; 
	}


/*
Необходимо написать библиотеку на php, которая позволяет эмулировать полет самолета по заданной траектории. Траектория полета и скорость задается с помощью файла настроек в формате JSON:

{
   "tr": [
       [ 33, 33 ],   //  начальная точка [ широта, долгота ]
       [ 37, 24 ],   //  промежуточная точка [ широта, долгота ]
       [ 43, 45 ],   //  конечная точка [ широта, долгота ]
   ],
   “speed”: “400”  // скорость в км/ч
}

Необходимо чтобы библиотека умела:
высчитывать расстояние в километрах маршрута getDistance();
высчитывать ориентировочное время прибытия для указанной даты и времени отправления getTimeArrival(DateTime $data) ;
высчитывать расстояние в километрах для каждого участка маршрута getPartDistance(int $n), где $n - номер участка между точками в файле настроек $n-1 и $n;
высчитывать ориентировочное время прибытия для указанной даты и времени отправления getPartTimeArrival(DateTime $data, $n), где $n точка в файле настроек;
при вызове метода play(), необходимо симулировать передвижение по маршруту. Вызов метода getPlace() должен вернуть текущее местоположение. Время симуляции совпадает с реальным временем. Для того, чтобы не потерять данные о времени запуска, координатах и скорости рекомендуется хранить эти данные в файле или куках.

Если для последнего пункта у вас возникли сложности с поиском формулы, то вы можете вместо координат текущего местоположения возвращять процент от пройденного расстояния.

Тестовое задание должно быть залито на github, в письме с ссылкой на залитый проект необходимо указать как запустить проект для проверки.

*/


?>

</body>
</html>