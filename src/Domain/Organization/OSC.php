<?php
final class OSC
{
    public const IDENTIFIER = '69f88cdcd1dd355cb895ded2';

    private const OFFICES = [
        'osc_president_office' => true,
        'osc_ivp_office' => true,
        'osc_evp_office' => true,
        'osc_gensec_office' => true,
        'osc_genaud_office' => true,
        'osc_gentreas_office' => true,
        'osc_genpio_office' => true
    ];

    private const EXECUTIVES = [
        'osc_president' => true,
        'osc_ivp' => true,
        'osc_evp' => true,
        'osc_gensec' => true,
        'osc_genaud' => true,
        'osc_gentreas' => true,
        'osc_genpio' => true
    ];

    public const EXECUTIVE_MAP = [
        'osc_president_office' => 'osc_president',
        'osc_ivp_office' => 'osc_ivp',
        'osc_evp_office' => 'osc_evp',
        'osc_gensec_office' => 'osc_gensec',
        'osc_genaud_office' => 'osc_genaud',
        'osc_gentreas_office' => 'osc_gentreas',
        'osc_genpio_office' => 'osc_genpio'
    ];
}
?>