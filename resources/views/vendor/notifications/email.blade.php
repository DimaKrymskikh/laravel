<x-mail.layout>
    <div class="main">
        <div>
            <div class="h1">Привет</div>
        </div>
        
        <p>
            Нажмите кнопку ниже, чтобы подтвердить свой адрес электронной почты.
        </p>
        
        <div style="text-align: center; margin: 2rem 0">
            <a class="button" href="{{ $actionUrl }}" target="_blank" rel="noopener">Подтвердение адреса эл. почты</a>
        </div>
        
        <p>
            Если вы не создавали учетную запись, никаких дальнейших действий не требуется.
        </p>
        
        <p style="font-size: 1.125rem">
            С уважением,<br>
            {{ config('app.name') }}
        </p>
        
        <p>
            Если у Вас возникли проблемы с нажатием кнопки "Подтвердение адреса эл. почты", скопируйте ссылку и вставьте её в ваш веб-браузер: <br>
            <a href="{{ $actionUrl }}">{{ $displayableActionUrl }}</a>
        </p>
    </div>
</x-mail.layout>
