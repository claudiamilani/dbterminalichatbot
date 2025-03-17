@if(!empty($configuration->pwdr_mail_body_u))
    {!! str_replace('%RESET_URL%', 'http://'.$host.(!in_array($_SERVER['SERVER_PORT'] ?? 80,[80,443])?(':'.$_SERVER['SERVER_PORT'] ?? 80):'').route(($is_admin_route ? \App\LftRouting\RoutingManager::adminPwdResetRoute(): \App\LftRouting\RoutingManager::pwdResetRoute()), $user->passwordRecovery->token, false), $configuration->pwdr_mail_body_u) !!}
@else
    <p>Gentile utente, abbiamo ricevuto una richiesta di reset della tua password. Per completare la
        procedura clicca su link riportato di seguito o copialo nella barra degli indirizzi del tuo browser e premi
        invio.</p>
    <p>
        <a href="{{ 'http://'.$host.(!in_array($_SERVER['SERVER_PORT'] ?? 80,[80,443])?(':'.$_SERVER['SERVER_PORT'] ?? 80):'').route(($is_admin_route ? \App\LftRouting\RoutingManager::adminPwdResetRoute() : \App\LftRouting\RoutingManager::pwdResetRoute()), $user->passwordRecovery->token, false) }}">{{ 'http://'.$host.(!in_array($_SERVER['SERVER_PORT'] ?? 80,[80,443])?(':'.$_SERVER['SERVER_PORT'] ?? 80):'').route(($is_admin_route ? \App\LftRouting\RoutingManager::adminPwdResetRoute() : \App\LftRouting\RoutingManager::pwdResetRoute()), $user->passwordRecovery->token, false) }}</a>
    </p>
    <p>Il link ha una validità di 24h.</p>
    <br><br>
    Saluti<br>
    <i>Lo Staff di {{config('app.name')}}</i>
@endif