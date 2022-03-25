
<!DOCTYPE html>
<html>
<head>
    <title>Оптимальное добавление множества меток</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!--
        Укажите свой API-ключ. Тестовый ключ НЕ БУДЕТ работать на других сайтах.
        Получить ключ можно в Кабинете разработчика: https://developer.tech.yandex.ru/keys/
    -->
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru-RU&amp;apikey=86009da9-60bd-4109-ba53-cf724a2a8e25" type="text/javascript"></script>
    <script src="https://yandex.st/jquery/2.2.3/jquery.min.js" type="text/javascript"></script>
    <style>
        html, body, #map {
            width: 100%;
            height: 100%;
            padding: 0;
            margin: 0;
        }
        #map {
            height: calc(100% - 38px);
        }
        a {
            color: #04b; /* Цвет ссылки */
            text-decoration: none; /* Убираем подчеркивание у ссылок */
        }
        a:visited {
            color: #04b; /* Цвет посещённой ссылки */
        }
        a:hover {
            color: #f50000; /* Цвет ссылки при наведении на нее курсора мыши */
        }
    </style>
</head>
<body>

<div class="d-grid gap-2">
    <a class="btn btn-primary" href="/login">Войти/Зарегитрироваться</a>
</div>

<div id="map"></div>


<script>
    ymaps.ready(init);

    function init() {

        var myMap = new ymaps.Map('map', {
                center: [55.76, 37.64],
                zoom: 10
            }, {
                searchControlProvider: 'yandex#search'
            });

        var objectManager = new ymaps.LoadingObjectManager('/dtp?bbox=%b', {
            // Включаем кластеризацию.
            clusterize: true,
            // Опции кластеров задаются с префиксом 'cluster'.
            clusterHasBalloon: true,
            // Опции геообъектов задаются с префиксом 'geoObject'.
            geoObjectOpenBalloonOnClick: true
        });

        // Опции можно задавать напрямую в дочерние коллекции.
        // objectManager.clusters.options.set({
        //     preset: 'islands#grayClusterIcons',
        //     hintContentLayout: ymaps.templateLayoutFactory.createClass('Группа объектов')
        // });
        // objectManager.objects.options.set('preset', 'islands#grayIcon');

        myMap.geoObjects.add(objectManager);
    }

</script>



{{--<script>--}}
{{--    ymaps.ready(init);--}}

{{--    function init () {--}}
{{--        var myMap = new ymaps.Map('map', {--}}
{{--                center: [55.76, 37.64],--}}
{{--                zoom: 10--}}
{{--            }, {--}}
{{--                searchControlProvider: 'yandex#search'--}}
{{--            }),--}}
{{--            objectManager = new ymaps.ObjectManager({--}}
{{--                // Чтобы метки начали кластеризоваться, выставляем опцию.--}}
{{--                clusterize: true,--}}
{{--                // ObjectManager принимает те же опции, что и кластеризатор.--}}
{{--                gridSize: 64,--}}
{{--                clusterDisableClickZoom: true--}}
{{--            });--}}

{{--        // Чтобы задать опции одиночным объектам и кластерам,--}}
{{--        // обратимся к дочерним коллекциям ObjectManager.--}}
{{--        objectManager.objects.options.set('preset', 'islands#greenDotIcon');--}}
{{--        objectManager.clusters.options.set('preset', 'islands#greenClusterIcons');--}}
{{--        myMap.geoObjects.add(objectManager);--}}

{{--        $.ajax({--}}
{{--            url: "/dtp"--}}
{{--        }).done(function(data) {--}}
{{--            objectManager.add(data);--}}
{{--        });--}}

{{--    }--}}
{{--</script>--}}
</body>
</html>