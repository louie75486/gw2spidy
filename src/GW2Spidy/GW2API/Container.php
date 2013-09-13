<?php

namespace GW2Spidy\GW2API;

class Container extends API_Item {
    
    public function __construct($API_Item) {
        parent::__construct($API_Item);
        
        $this->sub_type = $API_Item['container']['type'];
    }
    
    public function getTooltipDescription() {
        $tooltip = <<<HTML
        <div class="p-tooltip-description db-description">
            <dl class="db-summary">
                <dt class="db-title gwitem-{$this->getRarityLower()}">{$this->getHTMLName()}</dt>
                <dd class="db-consumableType">{$this->getSubType()}</dd>
                <dd class="db-itemDescription">{$this->getHTMLDescription()}</dd>
            </dl>
        </div>
HTML;
        return $tooltip;
    }
}