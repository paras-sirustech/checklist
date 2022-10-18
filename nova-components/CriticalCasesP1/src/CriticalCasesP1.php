<?php

namespace Ooredoo\CriticalCasesP1;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class CriticalCasesP1 extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('critical-cases-p1', __DIR__.'/../dist/js/tool.js');
        Nova::style('critical-cases-p1', __DIR__.'/../dist/css/tool.css');
    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return \Illuminate\View\View
     */
    public function renderNavigation()
    {
        return view('critical-cases-p1::navigation');
    }
}
