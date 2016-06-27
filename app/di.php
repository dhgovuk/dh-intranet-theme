<?php

$registrar->addInstance(\Dxw\Iguana\Theme\Helpers::class, new \Dxw\Iguana\Theme\Helpers());
// $registrar->addInstance(\Dxw\Iguana\Theme\LayoutRegister::class, new \Dxw\Iguana\Theme\LayoutRegister(
//     $registrar->getInstance(\Dxw\Iguana\Theme\Helpers::class)
// ));
$registrar->addInstance(\Dxw\Iguana\Extras\UseAtom::class, new \Dxw\Iguana\Extras\UseAtom());

$registrar->addInstance(\DHIntranet\LoginCookieDuration::class, new \DHIntranet\LoginCookieDuration());
$registrar->addInstance(\DHIntranet\DisableNicknameChecking::class, new \DHIntranet\DisableNicknameChecking());

$registrar->addInstance(\DHIntranet\Theme\Scripts::class, new \DHIntranet\Theme\Scripts(
    $registrar->getInstance(\Dxw\Iguana\Theme\Helpers::class)
));
$registrar->addInstance(\DHIntranet\Icons::class, new \DHIntranet\Icons(
    $registrar->getInstance(\Dxw\Iguana\Theme\Helpers::class)
));
