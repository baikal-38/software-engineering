## Описание
Приложение для просмотра прогноза погоды. 
Оно состоит из двух микросервисов:
Client - отвечает за работу с пользователем, 
Server - составление графика температуры в городе, выбранном пользователем.  
При работе с приложением пользователю необходимо выбрать город. Для получения списка доступнвх городов Client взаимодействует с базой данных. 
По запросу пользователя клиентский сервис отправляет id города в приложение Server, которое взаимодействуя с api.open-meteo.com (прогноз погоды) и quickchart.io (построение графика), 
формирует график дневной и ночной температуры на предстоящие 16 дней. 

____
### Запуск.
Клонировать репозиторий и, находясь в терминале, перейти в папку, где находится файл **docker-compose.yml** для запуска приложения в Docker. После чего выполнить команду:
```
docker compose up -d
```

____
### Результат
После успешного запуска всех контейнеров необходимо подождать 5-10 секунд (пока закончится установка необходимых библиотек) и перейти на [http://localhost/](http://localhost/).
###