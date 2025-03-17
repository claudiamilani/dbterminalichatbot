<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Auth\SocialiteProviders\Medialogic;

use SocialiteProviders\Manager\SocialiteWasCalled;

class MdlAzureExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param \SocialiteProviders\Manager\SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('mdl-azure', MdlAzureProvider::class);
    }
}
