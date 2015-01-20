<?php
/**
 * Default age-restricted template.
 * 
 * Strictly speaking this isn't an integration template. This
 * template loads whenever a reader reaches an age-restricted
 * webcomic they can't access and an appropriate template can't be
 * found in the current theme.
 * 
 * @package Webcomic
 * @uses verify_webcomic_age()
 */

wp_die(
    is_null(verify_webcomic_age())
        ? sprintf("
            <form method='post'>
                %s<br>
                <button type='submit' name='webcomic_birthday' value='1'>%s</button>
                <button type='submit' name='webcomic_birthday' value='0'>%s</button>
            </form>",
            sprintf(__('Are you %s years or older?', 'webcomic'), WebcomicTag::get_verify_webcomic_age()),
            __('Yes', 'webcomic'),
            __('No', 'webcomic')
        )
        : __("You don't have permission to view this content.", 'webcomic'),
    __('Restricted Content | Webcomic', 'webcomic'), 401
);