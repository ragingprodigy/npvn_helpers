<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
</head>
<body>
<div>


    <ul>
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>

    {!! Form::open(['url' => 'bvn_check', 'enctype'=>'multipart/form-data']) !!}

        {!! Form::label('theFile', 'Select Excel File') !!}

        {!! Form::file('theFile') !!}

        {!! Form::submit('Process File') !!}
    <br>

    {!! Form::close() !!}
</div>
</body>
</html>
