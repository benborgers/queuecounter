<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>queuecounter</title>
        <link rel="icon" href="https://emojicdn.elk.sh/📟" />

        <link rel="stylesheet" href="https://api.fontshare.com/v2/css?f[]=satoshi@400,401,500,501,700,701,900,901&display=swap" />

        @fluxAppearance

        @vite('resources/css/app.css')
    </head>
    <body class="antialiased bg-zinc-50">
        {{ $slot }}

        @fluxScripts
    </body>
</html>
