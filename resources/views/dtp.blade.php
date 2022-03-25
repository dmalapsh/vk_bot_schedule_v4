
<!DOCTYPE html>
<html>
<head>
    <style>
        iframe{
            width: 100%;
            height: 500px;
        }
    </style>
    <link href="https://use.fontawesome.com/releases/v5.15.0/css/all.css" rel="stylesheet">
    <title>Оптимальное добавление множества меток</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">.
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://yandex.st/jquery/2.2.3/jquery.min.js" type="text/javascript"></script>
    <style>
        html, body, #map {
            width: 100%; height: 100%; padding: 0; margin: 0;
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
<div class="container">
    <h2>{{$dtp->title}}</h2>
    <div class="text-muted fs-13 mb-3 " data-time="{{$dtp->date}}">
        <span title="Дата" class="me-3">
            <i  class="far fa-calendar"></i>
            {{$dtp->date}}
        </span>
        <span title="Погибло" class="me-3">
            <i class="fas fa-skull-crossbones"></i>
            @if($dtp->dead !== null)
                {{$dtp->dead}}
            @else
                Незивестно
            @endif
        </span>
        <span title="Пострадало" class="me-3">
            <i class="fas fa-user-injured"></i>
            @if($dtp->injured !== null)
                {{$dtp->injured}}
            @else
                Незивестно
            @endif
        </span>
        <span title="Загрузивший ДТП пользователь" class="me-3">
            @if($dtp->user_id)
                <i class="fas fa-user-edit"></i>
                {{$dtp->user_id}}
            @endif
        </span>
    </div>

    <p class="mt-3">
        {{$dtp->description}}
    </p>
    @foreach(explode(",",$dtp->videos_url) as $video)
        <iframe allowfullscreen src="{{$video}}" frameborder="0"></iframe>
    @endforeach

</div>


</body>
</html>