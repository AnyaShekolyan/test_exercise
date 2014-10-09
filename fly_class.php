<?php
	class flying
	{		
		//получаем двумерный массив, и проверяем - прислали ли нам вторую переменную или нет
		//если не прислали, тогда считаем длинну всего маршрута, как сумму расстояний между точками
		//если вторая переменная прислана, то ищем расстояние между точками указанного сегмента
		function getPartDistance($route, $n)
		{
			if (!$n){
				$distance = $this -> getDistance($route[0], $route[1]);
				$distance = $distance + $this -> getDistance($route[1], $route[2]);
			}else{
				$distance = $this -> getDistance($route[$n], $route[$n-1]);
			}
			return $distance;
		} 
		//функция которая собственно считает расстояние между точками, как корень суммы квадратов разностей координатов конца и начала
		function getDistance($start, $end)
		{
			$distance = pow(pow($end[0] - $start[0], 2) + pow($end[1] - $start[1], 2),0.5);
			return $distance;
		}
		//функция рассчета времени прибытия, ищется по простой формцле s = v*t
		function getTimeArrival($date, $speed, $distance)
		{
			$timearrival = date('Y-m-d H:i:s',$date + ($distance/$speed)*3600);
			return $timearrival;
		}
		//и функция которая считает в зависимости от нынешнего времени, сколько процентов отведенного ему пути пролетел самолет
		function play($data, $src){
			$fp = fopen($src."file.txt","r");
			while (!feof($fp)){
				$start_time = fgets($fp);
			}
			fclose($fp);
			$date = date_create();
			$distance = $this -> getPartDistance($data[tr]);
			$progress = (((date_timestamp_get(date_create())-$start_time)/3600)*100*$data[speed])/$distance;
			return $progress;			
		}

	}

?>