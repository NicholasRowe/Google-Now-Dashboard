
function getweather()
{
	var script2 = document.createElement("script");
	script2.src = "weather_json.php";
	document.getElementsByTagName("head")[0].appendChild(script2);
}
function weather(json)
{
	document.getElementById('weather_main_img').src = 'img/weather/'+json.current_conditions.icon+'.png';
	document.getElementById('weather_img_current_cond').innerHTML = json.current_conditions.weather;
	document.getElementById('weather_main_current_temp').innerHTML = json.current_conditions.temperature;
	document.getElementById('weather_main_windy').innerHTML = json.current_conditions.wind;
	document.getElementById('weather_main_rainy').innerHTML = json.current_conditions.precip;
	document.getElementById('weather_main_name_place').innerHTML = 'UCA Campus';
	
	document.getElementById('day_1').innerHTML = ''+
				'<span class="forecast_day_name">'+(json.forecast[0].day).toUpperCase()+'</span>'+
                '<img  class="forecast_day_icon" src="img/weather/'+json.forecast[0].icon+'.png" />'+
                '<span class="forecast_day_high">'+json.forecast[0].high+'</span>'+
                '<span class="forecast_day_low">'+json.forecast[0].low+'</span>';
				
	document.getElementById('day_2').innerHTML = ''+
				'<span class="forecast_day_name">'+(json.forecast[1].day).toUpperCase()+'</span>'+
                '<img  class="forecast_day_icon" src="img/weather/'+json.forecast[1].icon+'.png" />'+
                '<span class="forecast_day_high">'+json.forecast[1].high+'</span>'+
                '<span class="forecast_day_low">'+json.forecast[1].low+'</span>';
				
				
	document.getElementById('day_3').innerHTML = ''+
				'<span class="forecast_day_name">'+(json.forecast[2].day).toUpperCase()+'</span>'+
                '<img  class="forecast_day_icon" src="img/weather/'+json.forecast[2].icon+'.png" />'+
                '<span class="forecast_day_high">'+json.forecast[2].high+'</span>'+
                '<span class="forecast_day_low">'+json.forecast[2].low+'</span>';
				
	document.getElementById('day_4').innerHTML = ''+
				'<span class="forecast_day_name">'+(json.forecast[3].day).toUpperCase()+'</span>'+
                '<img  class="forecast_day_icon" src="img/weather/'+json.forecast[3].icon+'.png" />'+
                '<span class="forecast_day_high">'+json.forecast[3].high+'</span>'+
                '<span class="forecast_day_low">'+json.forecast[3].low+'</span>';
}
//getweather();


