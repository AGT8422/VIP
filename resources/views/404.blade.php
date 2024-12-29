<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>404 - NOT FOUND</title>
</head>
<style>
    .ERROR_404{
        position: fixed;
        top: 50%;
        left: 50%; 
        width: 500px;
        text-align: center;
        font-weight: bolder;
        font-size: 30px;
        padding:50%;
        border:1px solid black;
        transform: translate(-50%,-50%);
        background-color: #ececec;
        color: #3a3a3a33;
    }
    .body_page{
        position: fixed;
        width: 100%;
        height: 100%;
        background-color: #ececec;
    }
</style>
<body>
    <div class="body_page">
        <div class="ERROR_404">
            <h1 class="font_number"  >{{ "404 Not Found." }}</h1>
        </div>
    </div>
</body>
</html>