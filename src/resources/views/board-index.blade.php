<!doctype html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>게시판</title>
</head>
<body>
    <div id="app"></div>
    <script src="{{asset("/js/bootstrap.js")}}"></script>
    <script src="{{asset("/js/gnu.js")}}"></script>

    <script>
        window.user = {!! json_encode(auth()->user()) !!};
    </script>

</body>
</html>