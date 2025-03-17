<!DOCTYPE html>
<html>
<head>
    <title>{{ trans('DBT/configuration.send.mail.mail_sent') }}</title>
</head>
<body>

<p>{!! $emailContent !!}</p>
<i>Lo Staff di {{ config('app.name') }}</i>

</body>
</html>
